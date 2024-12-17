<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    if(isset($_POST['shift_id']) && isset($_POST['clockIn']) && isset($_POST['clockOut'])){
        $shiftId = $_POST['shift_id'];
        $clockIn = $_POST['clockIn'];
        $clockOut = $_POST['clockOut'];
        $breaks = json_decode($_POST['breaks'], true);

        $clockInDateTime = new DateTime($clockIn);
        $clockOutDateTime = new DateTime($clockOut);
        $interval = $clockInDateTime->diff($clockOutDateTime);

        $hours = $interval->h + ($interval->days * 24);
        $minutes = $interval->i;
        $duration = sprintf('%02d:%02d', $hours, $minutes);

        $editShiftData = "UPDATE driverShifts SET clockIn = '{$clockIn}', clockOut = '{$clockOut}', duration = '{$duration}' WHERE id = {$shiftId};";
        $runEditShiftData = mysqli_query($conn, $editShiftData);

        if($runEditShiftData){
            foreach ($breaks as $break) {
                $breakId = $break['break_id'];
                $breakStart = $break['break_start'];
                $breakEnd = $break['break_end'];

                $breakStartDateTime = new DateTime($breakStart);
                $breakEndDateTime = new DateTime($breakEnd);
                $breakInterval = $breakStartDateTime->diff($breakEndDateTime);

                $breakHours = $breakInterval->h + ($breakInterval->days * 24);
                $breakMinutes = $breakInterval->i;
                $breakDuration = sprintf('%02d:%02d', $breakHours, $breakMinutes);

                $updateBreak = "UPDATE breaks SET start = '{$breakStart}', finish = '{$breakEnd}', duration = '{$breakDuration}' WHERE id = {$breakId};";
                $runUpdateBreak = mysqli_query($conn, $updateBreak);

                if (!$runUpdateBreak) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update breaks.']);
                    exit();
                }

                echo json_encode(['status' => 'success', 'message' => 'Edit Successfully']);
            }
        }
        else{
            echo json_encode(['status' => 'error', 'message' => 'Update Shift Query Error']);
        }
    }
    else{
        echo json_encode(['status' => 'error', 'message' => 'Invalid Parameter']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Loginn First']);
}

?>