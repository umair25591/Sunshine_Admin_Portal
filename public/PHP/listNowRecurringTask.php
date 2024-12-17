<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['recurringId'])){

        $recurringId = $_POST['recurringId'];

        $getRecurringTask = "SELECT * FROM recurringTasks WHERE id = {$recurringId};";
        $runGetRecurringTask = mysqli_query($conn, $getRecurringTask);

        if($runGetRecurringTask){
            $resultGetRecurringTask = mysqli_fetch_assoc($runGetRecurringTask);
            $taskId = $resultGetRecurringTask['task'];
            $driverId = $resultGetRecurringTask['driver'];
            $priority = $resultGetRecurringTask['priority'];

            if($driverId){

                $listTaskNow = "INSERT INTO yardDuties(task, driver, status, priority) VALUES({$taskId}, {$driverId}, 'Pending', '{$priority}')";
                $runListTaskNow = mysqli_query($conn, $listTaskNow);

                if($runListTaskNow){
                    echo json_encode(['status' => 'success', 'message' => 'Task is listed Successfully']);
                }
            }
            else{

                $listTaskNow = "INSERT INTO yardDuties(task, status, priority) VALUES({$taskId}, 'Pending', '{$priority}')";
                $runListTaskNow = mysqli_query($conn, $listTaskNow);

                if($runListTaskNow){
                    echo json_encode(['status' => 'success', 'message' => 'Task is listed Successfully']);
                }
            }


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