<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['yardDutyId'])){
        $yardDutyId = $_POST['yardDutyId'];

        $getYardDuty = "SELECT * FROM yardDuties WHERE id = {$yardDutyId};";
        $runYardDuty = mysqli_query($conn, $getYardDuty);
        if($runYardDuty){
            $result = mysqli_fetch_assoc($runYardDuty);
            $taskId = $result['task'];
            $driverId = $result['driver'];
            $vehicleId = $result['vehicle'];

            if($vehicleId){
                $relistTask = "INSERT INTO yardDuties(task, driver, vehicle, status) VALUES({$taskId}, {$driverId}, {$vehicleId}, 'Pending');";
                $runRelistTask = mysqli_query($conn, $relistTask);
                if($relistTask){
                    echo json_encode(['status' => 'success', 'message' => 'Task Relist Successfully']);
                }
            }
            else{
                $relistTask = "INSERT INTO yardDuties(task, driver, status) VALUES({$taskId}, {$driverId}, 'Pending');";
                $runRelistTask = mysqli_query($conn, $relistTask);
                if($relistTask){
                    echo json_encode(['status' => 'success', 'message' => 'Task Relist Successfully']);
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