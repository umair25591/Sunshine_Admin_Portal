<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['recurringTaskId'])){
        $recurringTaskId = $_POST['recurringTaskId'];

        $cancelRecurringTask = "UPDATE recurringTasks SET status = 'Inactive' WHERE id = {$recurringTaskId};";
        $runCancelRecurringTask = mysqli_query($conn, $cancelRecurringTask);

        if($runCancelRecurringTask){
            echo json_encode(['status' => 'success', 'message' => 'Task is Canceled']);
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