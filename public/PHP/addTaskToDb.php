<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $taskName = isset($_POST['task']) ? $_POST['task'] : '';
        $whenValue = isset($_POST['when']) ? $_POST['when'] : '';
        $recurringDays = isset($_POST['recurringDays']) ? $_POST['recurringDays'] : [];
        $assignToPerson = isset($_POST['assignToPerson']) ? $_POST['assignToPerson'] : null;
        $cancelIfNotCompletedBy = isset($_POST['cancelIfNotCompletedBy']) ? (int)$_POST['cancelIfNotCompletedBy'] : 0;
        $priority = isset($_POST['priority']) ? $_POST['priority'] : '';
        $tomorrowDateTime = $_POST['tomorrowDateTime'];

        $taskName = mysqli_real_escape_string($conn, $taskName);
        $priority = mysqli_real_escape_string($conn, $priority);

        if($whenValue == "now"){
            $addTask = "INSERT INTO tasks(name, status) VALUES('{$taskName}', 'Active')";
            $runTask = mysqli_query($conn, $addTask);
            if($runTask){
                $taskId = mysqli_insert_id($conn);

                if($assignToPerson){
                    $generateYardDuty = "INSERT INTO yardDuties(task, driver, priority, cancelNotCompleted, status) VALUES({$taskId}, {$assignToPerson}, '{$priority}', {$cancelIfNotCompletedBy}, 'Pending');";
                    $runGenerateYardDuty = mysqli_query($conn, $generateYardDuty);
                }
                else{
                    $generateYardDuty = "INSERT INTO yardDuties(task, priority, cancelNotCompleted, status) VALUES({$taskId}, '{$priority}', {$cancelIfNotCompletedBy}, 'Pending');";
                    $runGenerateYardDuty = mysqli_query($conn, $generateYardDuty);
                }

                if($generateYardDuty){
                    echo json_encode(['status' => 'success', 'message' => 'Task Generated Successfully']);
                }
                else{
                    echo json_encode(['status' => 'error', 'message' => 'Task Generated Failed']);
                }
            }
            else{
                echo json_encode(['status' => 'error', 'message' => 'Task Generated Failed']);
            }
        }
        else if($whenValue == "tomorrow"){
            $addTask = "INSERT INTO tasks(name, status) VALUES('{$taskName}', 'Active')";
            $runTask = mysqli_query($conn, $addTask);
            if($runTask){
                $taskId = mysqli_insert_id($conn);

                if($assignToPerson){
                   $addToRecurringTask = "INSERT INTO recurringTasks(task, driver, priority, cancelNotCompleted, date, status) VALUES({$taskId}, {$assignToPerson}, '{$priority}', {$cancelIfNotCompletedBy}, '{$tomorrowDateTime}', 'Active');";
                   $runAddToRecurringTask = mysqli_query($conn, $addToRecurringTask);
                }
                else{
                    $addToRecurringTask = "INSERT INTO recurringTasks(task, priority, cancelNotCompleted, date, status) VALUES({$taskId}, '{$priority}', {$cancelIfNotCompletedBy}, '{$tomorrowDateTime}', 'Active');";
                    $runAddToRecurringTask = mysqli_query($conn, $addToRecurringTask);
                }

                if($runAddToRecurringTask){
                    echo json_encode(['status' => 'success', 'message' => 'Tomorrow Task Generated Successfully']);
                }
                else{
                    echo json_encode(['status' => 'error', 'message' => 'Tomorrow Task Generated Failed']);
                }
            }
            else{
                echo json_encode(['status' => 'error', 'message' => 'Task Generated Failed']);
            }
        }
        else if($whenValue == "recurring"){
            $addTask = "INSERT INTO tasks(name, status) VALUES('{$taskName}', 'Active')";
            $runTask = mysqli_query($conn, $addTask);
            if($runTask){
                $taskId = mysqli_insert_id($conn);
                $recurringDaysStr = implode(',', $recurringDays);

                if($assignToPerson){
                   $addToRecurringTask = "INSERT INTO recurringTasks(task, driver, priority, cancelNotCompleted, days, status) VALUES({$taskId}, {$assignToPerson}, '{$priority}', {$cancelIfNotCompletedBy}, '{$recurringDaysStr}', 'Active');";
                   $runAddToRecurringTask = mysqli_query($conn, $addToRecurringTask);
                }
                else{
                    $addToRecurringTask = "INSERT INTO recurringTasks(task, priority, cancelNotCompleted, days, status) VALUES({$taskId}, '{$priority}', {$cancelIfNotCompletedBy}, '{$recurringDaysStr}', 'Active');";
                    $runAddToRecurringTask = mysqli_query($conn, $addToRecurringTask);
                }

                if($runAddToRecurringTask){
                    echo json_encode(['status' => 'success', 'message' => 'Recurring Task Generated Successfully']);
                }
                else{
                    echo json_encode(['status' => 'error', 'message' => 'Recurring Task Generated Failed']);
                }
            }
            else{
                echo json_encode(['status' => 'error', 'message' => 'Task Generated Failed']);
            }
        }
        
    }
    else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    }
} 
else {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
}
?>
