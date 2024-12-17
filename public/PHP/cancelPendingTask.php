<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['yardDutyId'])){
        $yardDutyId = $_POST['yardDutyId'];

        $cancelPendingTask = "UPDATE yardDuties SET status = 'Cancel' WHERE id = {$yardDutyId};";
        $runCancelPendingTask = mysqli_query($conn, $cancelPendingTask);

        if($runCancelPendingTask){
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