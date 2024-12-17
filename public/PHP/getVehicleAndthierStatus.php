<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $getVehicles = "SELECT 
    v.id AS vehicleId,
    v.numberPlate,
    v.status AS vehicleStatus,
    
    -- Current job details
    j_current.id AS currentJobId,
    j_current.date AS currentJobDate,
    j_current.start AS currentJobStartTime,
    j_current.finish AS currentJobFinishTime,
    j_current.driver AS currentJobDriverId,
    jd_current.firstName AS currentJobDriverFirstName,
    jd_current.lastName AS currentJobDriverLastName,
    j_current.status AS currentJobStatus,
    
    -- Future job details
    j_future.id AS futureJobId,
    j_future.date AS futureJobDate,
    j_future.start AS futureJobStartTime,
    j_future.finish AS futureJobFinishTime,
    j_future.driver AS futureJobDriverId,
    jd_future.firstName AS futureJobDriverFirstName,
    jd_future.lastName AS futureJobDriverLastName,
    j_future.status AS futureJobStatus,
    
    -- Allocation details
    va.id AS allocationId,
    va.driver AS allocationDriverId,
    ad.firstName AS allocationDriverFirstName,
    ad.lastName AS allocationDriverLastName,
    va.start AS allocationStartTime,
    va.finish AS allocationFinishTime,
    va.status AS allocationStatus
FROM 
    vehicles v
LEFT JOIN 
    (
        SELECT 
            j1.*
        FROM 
            jobs j1
        JOIN 
            (SELECT 
                 vehicle, 
                 MAX(CONCAT(date, ' ', start)) AS latestJobDateTime 
             FROM 
                 jobs 
             WHERE 
                 status = 'Active' 
             GROUP BY 
                 vehicle
            ) j2 
        ON 
            j1.vehicle = j2.vehicle 
            AND CONCAT(j1.date, ' ', j1.start) = j2.latestJobDateTime
    ) j_current 
ON 
    v.id = j_current.vehicle
LEFT JOIN 
    drivers jd_current 
ON 
    j_current.driver = jd_current.id
LEFT JOIN 
    (
        SELECT 
            j1.*
        FROM 
            jobs j1
        JOIN 
            (SELECT 
                 vehicle, 
                 MIN(CONCAT(date, ' ', start)) AS earliestPendingJobDateTime 
             FROM 
                 jobs 
             WHERE 
                 status = 'Pending' 
             GROUP BY 
                 vehicle
            ) j2 
        ON 
            j1.vehicle = j2.vehicle 
            AND CONCAT(j1.date, ' ', j1.start) = j2.earliestPendingJobDateTime
    ) j_future 
ON 
    v.id = j_future.vehicle
LEFT JOIN 
    drivers jd_future 
ON 
    j_future.driver = jd_future.id
LEFT JOIN 
    (
        SELECT 
            va1.*
        FROM 
            vehicleAllocation va1
        JOIN 
            (SELECT 
                 vehicle, 
                 MAX(start) AS latestAllocationStart 
             FROM 
                 vehicleAllocation 
             WHERE 
                 status IN ('Active', 'Inactive') 
             GROUP BY 
                 vehicle
            ) va2 
        ON 
            va1.vehicle = va2.vehicle 
            AND va1.start = va2.latestAllocationStart
    ) va 
ON 
    v.id = va.vehicle
LEFT JOIN 
    drivers ad 
ON 
    va.driver = ad.id
WHERE
    v.status != 'Inactive'
    AND
    v.isDeleted = 0
ORDER BY 
    v.numberPlate DESC;
    ";

    $runVehicle = mysqli_query($conn, $getVehicles);
    $numRows  = mysqli_num_rows($runVehicle);

    if($numRows > 0){

        $data = [];
        while ($result = mysqli_fetch_assoc($runVehicle)) {
            $data[] = $result;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);

    }
    else{
        echo json_encode(['status' => 'warning', 'message' => 'No Records Found']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}

?>