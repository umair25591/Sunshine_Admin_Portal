<?php
require_once '../../db-connection.php';

$todayDate = date('Y-m-d');

$getRecurringTasks = "SELECT * FROM recurringTasks WHERE status = 'Active' AND date = '{$todayDate}' AND days IS NULL AND isDeleted = 0;";
$runGetRecurringTasks = mysqli_query($conn, $getRecurringTasks);

while ($result = mysqli_fetch_assoc($runGetRecurringTasks)) {

    if (!empty($result['driver'])) {
        $recurringTaskID = $result['id'];
        $task = $result['task'];
        $driver = $result['driver'];
        $cancelNotCompleted = $result['cancelNotCompleted'];
        $priority = $result['priority'];

        $createYardDuty = "INSERT INTO yardDuties(task, driver, status, priority, cancelNotCompleted) VALUES({$task}, {$driver}, 'Pending', '{$priority}', {$cancelNotCompleted});";
        $runcreateYardDuty = mysqli_query($conn, $createYardDuty);

        if($runcreateYardDuty){
            $updateRecurringTask = "UPDATE recurringTasks SET status = 'Inactive' WHERE id = {$recurringTaskID}";
            $runupdateRecurringTask = mysqli_query($conn, $updateRecurringTask);
            if($runupdateRecurringTask){
                echo 'Task generated With Driver';
            }
        }

    } 
    else {
        $recurringTaskID = $result['id'];
        $task = $result['task'];

        $createYardDuty = "INSERT INTO yardDuties(task, status, priority, cancelNotCompleted) VALUES({$task}, 'Pending', '{$priority}', {$cancelNotCompleted});";
        $runcreateYardDuty = mysqli_query($conn, $createYardDuty);

        if($runcreateYardDuty){
            $updateRecurringTask = "UPDATE recurringTasks SET status = 'Inactive' WHERE id = {$recurringTaskID}";
            $runupdateRecurringTask = mysqli_query($conn, $updateRecurringTask);
            if($runupdateRecurringTask){
                echo 'Task generated';
            }
        }
    }
}

$getRecurringTaskForDay = "SELECT * FROM recurringTasks WHERE status = 'Active' AND isDeleted = 0 AND date IS NULL;";
$rungetRecurringTaskForDay = mysqli_query($conn, $getRecurringTaskForDay);

while ($resultForDay = mysqli_fetch_assoc($rungetRecurringTaskForDay)) {

    $daysArray = explode(',', $resultForDay['days']); 
    $daysArray = array_map('trim', $daysArray);
    $daysArray = array_map('strtolower', $daysArray);

    $currentDay = strtolower(date('l'));

    if (in_array($currentDay, $daysArray)) {

        $taskName = $resultForDay['task'];
        $driver = $resultForDay['driver'];
        $vehicle = $resultForDay['vehicle'];
        $priority = $resultForDay['priority'];
        $cancelNotCompleted = $resultForDay['cancelNotCompleted'];

        if($driver && $vehicle){
            $createYardDutyForDay = "INSERT INTO yardDuties(task, driver, vehicle, status, priority, cancelNotCompleted) VALUES({$taskName}, {$driver}, {$vehicle}, 'Pending', '{$priority}', {$cancelNotCompleted});";
            $runcreateYardDutyForDay = mysqli_query($conn, $createYardDutyForDay);

            if($runcreateYardDutyForDay){
                echo 'Day Task generated With Driver';
            }
        }
        else if($driver){
            $createYardDutyForDay = "INSERT INTO yardDuties(task, driver, status, priority, cancelNotCompleted) VALUES({$taskName}, {$driver}, 'Pending', '{$priority}', {$cancelNotCompleted});";
            $runcreateYardDutyForDay = mysqli_query($conn, $createYardDutyForDay);

            if($runcreateYardDutyForDay){
                echo 'Day Task generated With Driver';
            }
        }
        else if($vehicle){
            $createYardDutyForDay = "INSERT INTO yardDuties(task, vehicle, status, priority, cancelNotCompleted) VALUES({$taskName}, {$vehicle}, 'Pending', '{$priority}', {$cancelNotCompleted});";
            $runcreateYardDutyForDay = mysqli_query($conn, $createYardDutyForDay);

            if($runcreateYardDutyForDay){
                echo 'Day Task generated With Driver';
            }
        }
        else{
            $createYardDutyForDay = "INSERT INTO yardDuties(task, status, priority, cancelNotCompleted) VALUES({$taskName}, 'Pending', '{$priority}', {$cancelNotCompleted});";
            $runcreateYardDutyForDay = mysqli_query($conn, $createYardDutyForDay);

            if($runcreateYardDutyForDay){
                echo 'Day Task generated With Driver';
            }
        }
    }
}


?>
