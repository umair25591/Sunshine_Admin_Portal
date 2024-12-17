<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $getTasks = "SELECT
    rt.id AS recurringTaskID,
    rt.task AS recurringTaskTaskID,
    rt.driver AS recurringTaskDriverID,
    rt.date AS recurringTaskDate,
    rt.days AS recurringTaskDays,
    rt.status AS recurringTaskStatus,
    rt.isDeleted AS recurringTaskIsDeleted,
    rt.createdAt AS recurringTaskCreatedAt,
    rt.updatedAt AS recurringTaskUpdatedAt,
    t.name AS taskName,
    CONCAT(d.firstName, ' ', d.lastName) AS driverName
    FROM
        recurringTasks rt
    LEFT JOIN
        tasks t ON rt.task = t.id
    LEFT JOIN
        drivers d ON rt.driver = d.id
    WHERE
        rt.status != 'Inactive'
        AND rt.isDeleted = 0;";

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