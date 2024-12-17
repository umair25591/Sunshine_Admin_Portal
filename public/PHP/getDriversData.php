<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $getDriversData = "WITH LatestShifts AS (
    SELECT *, 
        ROW_NUMBER() OVER (PARTITION BY driver ORDER BY createdAt DESC) AS rn
    FROM driverShifts
)
SELECT 
    d.id AS driverId,
    d.firstName,
    d.lastName,
    d.mobile,
    ds.clockIn,
    ds.clockOut,
    ds.status AS clockStatus,  -- Status from driverShifts table
    ds.duration,
    ds.clockInStatus,
    va.numberPlate AS vehicleDriving,
    j.numberPlate AS jobVehicle,
    vj.status AS jobVehicleStatus,  -- Status of the job vehicle from vehicles table
    j.run,
    j.date AS jobDate,
    j.start AS jobStart,
    j.finish AS jobFinish
FROM drivers d
LEFT JOIN (
    SELECT * 
    FROM LatestShifts 
    WHERE rn = 1
) ds ON d.id = ds.driver
LEFT JOIN (
    SELECT va.*, v.numberPlate
    FROM vehicleAllocation va
    JOIN vehicles v ON va.vehicle = v.id AND v.status = 'Active' AND v.isDeleted = 0
    WHERE va.status = 'Active'
) va ON d.id = va.driver
LEFT JOIN (
    SELECT j.*, v.numberPlate, v.status AS vehicleStatus  -- Renaming status to vehicleStatus
    FROM jobs j
    LEFT JOIN vehicles v ON j.vehicle = v.id AND (v.isDeleted = 0 OR j.vehicle IS NULL)  -- Allow null vehicles
    WHERE j.status != 'Done'
) j ON d.id = j.driver
LEFT JOIN vehicles vj ON j.vehicle = vj.id  -- Join to get the status of the job vehicle
WHERE 
    d.status = 'Active'
    AND d.isDeleted = 0
ORDER BY 
    d.firstName ASC, 
    d.lastName ASC;
";


    $runGetDriversData = mysqli_query($conn, $getDriversData);
    $rowGetDriversData = mysqli_num_rows($runGetDriversData);

    if($rowGetDriversData > 0){
        $data = [];

        while($result = mysqli_fetch_assoc($runGetDriversData)){
            $data[] = $result;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    else{
        echo json_encode(['status' => 'warning', 'message' => 'No driver found']);
    }

    }
    else{
        echo json_encode(['status' => 'error', 'message' => 'Please Login First']);
    }
?>