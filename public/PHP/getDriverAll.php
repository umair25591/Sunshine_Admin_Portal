<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $showTerminated = isset($_POST['showTerminated']) ? $_POST['showTerminated'] === 'true' : false;

    $query = "
        SELECT 
            d.id, 
            d.firstName, 
            d.lastName, 
            d.status, 
            ds.id AS shiftId, 
            ds.job, 
            ds.clockIn, 
            ds.clockOut, 
            ds.duration, 
            ds.clockInStatus, 
            ds.status AS shiftStatus
        FROM 
            drivers d
        LEFT JOIN 
            (SELECT 
                ds1.* 
            FROM 
                driverShifts ds1
            INNER JOIN 
                (SELECT 
                    driver, 
                    MAX(clockIn) AS latestClockIn
                FROM 
                    driverShifts
                GROUP BY 
                    driver
                ) ds2 
            ON 
                ds1.driver = ds2.driver AND ds1.clockIn = ds2.latestClockIn
            ) ds 
        ON 
            d.id = ds.driver
        WHERE 
            d.isDeleted = 0";

    if ($showTerminated) {
        $query .= " AND d.status = 'Inactive'";
    } else {
        $query .= " AND d.status = 'Active'";
    }

    $runGetAllDriver = mysqli_query($conn, $query);
    $numRowsGetAllDriver = mysqli_num_rows($runGetAllDriver);

    if($numRowsGetAllDriver > 0){
        $data = [];
        while ($result = mysqli_fetch_assoc($runGetAllDriver)) {
            $data[] = $result;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    else{
        echo json_encode(['status' => 'warning', 'message' => 'No Record Found']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}



?>