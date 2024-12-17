<?php
require_once '../../db-connection.php';
session_start();
if(isset($_SESSION['adminId'])){
    if(isset($_POST['yardDutyId']) && isset($_POST['recurringDays'])){

        $yardDutyId = $_POST['yardDutyId'];
        $recurringDays = $_POST['recurringDays'];
        $recurringDaysStr = implode(',', $recurringDays);

        $getYardDuty = "SELECT * FROM yardDuties WHERE id = {$yardDutyId}";
        $runGetYardDuty = mysqli_query($conn, $getYardDuty);

        if($runGetYardDuty){
            $resultGetYardDuty = mysqli_fetch_assoc($runGetYardDuty);
            $taskId = $resultGetYardDuty['task'];
            $driverId = $resultGetYardDuty['driver'];
            $vehicleId = $resultGetYardDuty['vehicle'];

            if($driverId && $vehicleId){
                $createRecurringTask = "INSERT INTO recurringTasks(task, vehicle, driver, days, status) VALUES({$taskId}, {$vehicleId}, {$driverId}, '{$recurringDaysStr}', 'Active');";
                $runCreateRecurringTask = mysqli_query($conn, $createRecurringTask);
                if($runCreateRecurringTask){
                    echo json_encode(['status' => 'success', 'message' => 'Recurring Created Successfully']);
                }
            }
            else if($driverId){
                $createRecurringTask = "INSERT INTO recurringTasks(task, driver, days, status) VALUES({$taskId}, {$driverId}, '{$recurringDaysStr}', 'Active');";
                $runCreateRecurringTask = mysqli_query($conn, $createRecurringTask);
                if($runCreateRecurringTask){
                    echo json_encode(['status' => 'success', 'message' => 'Recurring Created Successfully']);
                }
            }
            else if($vehicleId){
                $createRecurringTask = "INSERT INTO recurringTasks(task, vehicle, days, status) VALUES({$taskId}, {$vehicleId}, '{$recurringDaysStr}', 'Active');";
                $runCreateRecurringTask = mysqli_query($conn, $createRecurringTask);
                if($runCreateRecurringTask){
                    echo json_encode(['status' => 'success', 'message' => 'Recurring Created Successfully']);
                }
            }
            else{
                $createRecurringTask = "INSERT INTO recurringTasks(task, vehicle, days, status) VALUES({$taskId}, '{$recurringDaysStr}', 'Active');";
                $runCreateRecurringTask = mysqli_query($conn, $createRecurringTask);
                if($runCreateRecurringTask){
                    echo json_encode(['status' => 'success', 'message' => 'Recurring Created Successfully']);
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