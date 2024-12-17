<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['recurringId'])){
        $recurringId = $_POST['recurringId'];

        $pauseRecurringTask = "UPDATE recurringTasks SET status = 'Pause' WHERE id = {$recurringId};";
        $runPauseRecurringTask = mysqli_query($conn, $pauseRecurringTask);

        if($runPauseRecurringTask){
            echo json_encode(['status' => 'success', 'message' => 'Task is Paused']);
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