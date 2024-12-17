<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $getTasks = "SELECT
    yd.id AS yardDutyID,
    yd.task AS yardDutyTaskID,
    yd.status AS yardDutyStatus,
    yd.createdAt AS yardDutyCreatedAt,
    yd.updatedAt AS yardDutyUpdatedAt,
    t.name AS taskName,
    CONCAT(d.firstName, ' ', d.lastName) AS driverName,
    v.numberPlate AS vehicleNumberPlate,
    CASE 
        WHEN rt.id IS NOT NULL AND rt.status = 'Active' AND rt.isDeleted = 0 THEN 'Yes'
        ELSE 'No'
    END AS recurringTaskMatch
    FROM
        yardDuties yd
    LEFT JOIN
        tasks t ON yd.task = t.id
    LEFT JOIN
        drivers d ON yd.driver = d.id
    LEFT JOIN
        vehicles v ON yd.vehicle = v.id
    LEFT JOIN
        recurringTasks rt ON yd.task = rt.task
        AND rt.status = 'Active'
        AND rt.isDeleted = 0
    ORDER BY
        yd.updatedAt DESC;
    ";

    $runGetTask = mysqli_query($conn, $getTasks);
    if($runGetTask){
        $data = [];

        while ($result = mysqli_fetch_assoc($runGetTask)) {
            $data[] = $result;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    }
    else{
        echo json_encode(['status' => 'error', 'message' => 'Query Failed']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}


?>