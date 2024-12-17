<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['recurringId'])){
        $recurringId = $_POST['recurringId'];

        $pauseRecurringTask = "UPDATE recurringTasks SET status = 'Inactive', isDeleted = 1 WHERE id = {$recurringId};";
        $runPauseRecurringTask = mysqli_query($conn, $pauseRecurringTask);

        if($runPauseRecurringTask){
            echo json_encode(['status' => 'success', 'message' => 'Task is Deleted']);
        }
        else{
            echo json_encode(['status' => 'error', 'message' => 'Query Failed']);
        }
    }
    else{
        echo json_encode(['status' => 'error', 'message' => 'Parameter Not Found']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}



?>