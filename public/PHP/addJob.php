<?php
// Include database connection
require_once '../../db-connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $customer = mysqli_real_escape_string($conn, $_POST['customer']);
    $run = mysqli_real_escape_string($conn, $_POST['run']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $startTime = mysqli_real_escape_string($conn, $_POST['start_time']);
    $finishTime = mysqli_real_escape_string($conn, $_POST['finish_time']);
    $driver = mysqli_real_escape_string($conn, $_POST['driver']);
    $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
    $currentTime = $_POST['currentTime'];
    
    $status = 'Pending';

    $insertQuery = "INSERT INTO jobs (customer, run, date, start, finish, driver, vehicle, status, createdAt)
        VALUES ('$customer', '$run', '$date', '$startTime', '$finishTime', '$driver', '$vehicle', '$status', '$currentTime');";

    if (mysqli_query($conn, $insertQuery)) {
        echo json_encode(['status' => 'success', 'message' => 'Job successfully added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
mysqli_close($conn);
?>
