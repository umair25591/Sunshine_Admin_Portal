<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){

    $getJobsData =  "SELECT 
    j.id AS jobId,
    j.run,
    j.date AS jobDate,
    j.start AS jobStart,
    j.finish AS jobFinish,
    j.status AS jobStatus,
    j.driver AS jobDriver,
    j.vehicle AS jobVehicle,
    v.numberPlate AS vehicleNumberPlate,
    d.firstName AS driverFirstName,
    d.lastName AS driverLastName,
    d.mobile AS driverMobile,
    c.name AS customerName
    FROM jobs j
    LEFT JOIN vehicles v ON j.vehicle = v.id
    LEFT JOIN drivers d ON j.driver = d.id
    LEFT JOIN customers c ON j.customer = c.id
    WHERE j.status != 'Done'
    AND (v.isDeleted = 0 OR j.vehicle IS NULL)  -- Allow null vehicles
    AND d.isDeleted = 0
    AND c.status = 'Active'
    ORDER BY j.id DESC;
    ";

    $runJobData = mysqli_query($conn, $getJobsData);
    $numRowsJobData = mysqli_num_rows($runJobData);

    if($numRowsJobData > 0){
        $data = [];

        while ($result = mysqli_fetch_assoc($runJobData)) {
            $data[] = $result;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    else{
        echo json_encode(['status' => 'warning', 'message' => 'No data found']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}



?>