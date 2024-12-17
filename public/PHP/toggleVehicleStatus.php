<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId']) && isset($_POST['vehicleId']) && isset($_POST['status'])) {
    $vehicleId = $_POST['vehicleId'];
    $status = $_POST['status'];

    $setVehicleStatus = "UPDATE vehicles SET status = '{$status}' WHERE id = {$vehicleId};";
    $runSetVehicleStatus = mysqli_query($conn, $setVehicleStatus);

    if($runSetVehicleStatus){
        echo json_encode(['status' => 'success']);
    }
    else{
        echo json_encode(['status' => 'error']);
    }
}


?>