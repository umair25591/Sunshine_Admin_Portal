<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $fetchRecord = "SELECT 
    d.id AS driverId, 
    d.firstName, 
    d.lastName, 
    d.mobile, 
    ds.id AS driverShiftId, 
    ds.clockIn, 
    ds.clockOut, 
    ds.duration, 
    ds.status AS shiftStatus, 
    j.id AS jobId, 
    j.date AS jobDate, 
    j.start AS jobStartTime, 
    j.finish AS jobFinishTime, 
    j.status AS jobStatus 
FROM 
    drivers d
LEFT JOIN 
    (SELECT 
         ds1.* 
     FROM 
         driverShifts ds1
     JOIN 
         (SELECT 
              driver, 
              MAX(clockIn) AS latestClockIn 
          FROM 
              driverShifts 
          GROUP BY 
              driver) ds2 
     ON 
         ds1.driver = ds2.driver 
         AND ds1.clockIn = ds2.latestClockIn) ds 
ON 
    d.id = ds.driver
LEFT JOIN 
    (SELECT 
         j1.* 
     FROM 
         jobs j1
     JOIN 
         (SELECT 
              driver, 
              MAX(date) AS latestJobDate 
          FROM 
              jobs 
          WHERE 
              status = 'Pending' 
          GROUP BY 
              driver) j2 
     ON 
         j1.driver = j2.driver 
         AND j1.date = j2.latestJobDate) j 
ON 
    d.id = j.driver 
WHERE 
    d.status = 'Active' 
    AND d.isDeleted = 0;

";
$runFetchRecord = mysqli_query($conn, $fetchRecord);
$numRowsRecord = mysqli_num_rows($runFetchRecord);

if($numRowsRecord > 0){
    $data = [];
    
    while ($result = mysqli_fetch_assoc($runFetchRecord)) {
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