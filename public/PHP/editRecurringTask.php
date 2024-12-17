<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['recurringTaskId']) && isset($_POST['taskId'])){

        $taskName = $_POST['task'];
        $when = $_POST['when'];
        $recurringDays = isset($_POST['recurringDays']) ? $_POST['recurringDays'] : [];
        $assignToPerson = isset($_POST['assignToPerson']) ? $_POST['assignToPerson'] : NULL;
        $cancelIfNotCompletedBy = isset($_POST['cancelIfNotCompletedBy']) ? $_POST['cancelIfNotCompletedBy'] : 0;
        $priority = isset($_POST['priority']) ? $_POST['priority'] : 'Normal';
        $tomorrowDateTime = $_POST['tomorrowDateTime'];
        $taskId = $_POST['taskId'];
        $recurringTaskId = $_POST['recurringTaskId'];
        $recurringDaysStr = implode(',', $recurringDays);


        $updateTask = "UPDATE tasks SET name = '{$taskName}' WHERE id = {$taskId};";
        $runUpdateTask = mysqli_query($conn, $updateTask);

        if($when == "recurring"){

            if($assignToPerson){
                $updateRecurring = "UPDATE recurringTasks SET days = '{$recurringDaysStr}', cancelNotCompleted = {$cancelIfNotCompletedBy}, priority = '{$priority}', driver = {$assignToPerson} WHERE id = {$recurringTaskId};";
                $runUpdateRecurring = mysqli_query($conn, $updateRecurring);
                if($runUpdateRecurring){
                    echo json_encode(['status' => 'success', 'message' => 'Task Edited Successfully']);
                }
            }
            else{
                $updateRecurring = "UPDATE recurringTasks SET days = '{$recurringDaysStr}', cancelNotCompleted = {$cancelIfNotCompletedBy}, priority = '{$priority}' WHERE id = {$recurringTaskId};";
                $runUpdateRecurring = mysqli_query($conn, $updateRecurring);
                if($runUpdateRecurring){
                    echo json_encode(['status' => 'success', 'message' => 'Task Edited Successfully']);
                }
            }

            
        }
        else if($when == "tomorrow"){
            if($assignToPerson){
                $updateRecurring = "UPDATE recurringTasks SET date = '{$tomorrowDateTime}', cancelNotCompleted = {$cancelIfNotCompletedBy}, priority = '{$priority}', driver = {$assignToPerson} WHERE id = {$recurringTaskId};";
                $runUpdateRecurring = mysqli_query($conn, $updateRecurring);
                if($runUpdateRecurring){
                    echo json_encode(['status' => 'success', 'message' => 'Task Edited Successfully']);
                }
            }
            else{
                $updateRecurring = "UPDATE recurringTasks SET date = '{$tomorrowDateTime}', cancelNotCompleted = {$cancelIfNotCompletedBy}, priority = '{$priority}' WHERE id = {$recurringTaskId};";
                $runUpdateRecurring = mysqli_query($conn, $updateRecurring);
                if($runUpdateRecurring){
                    echo json_encode(['status' => 'success', 'message' => 'Task Edited Successfully']);
                }
            }
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