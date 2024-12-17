<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $mobile = htmlspecialchars(trim($_POST['mobile']));
    $employeeType = htmlspecialchars(trim($_POST['employeeType']));
    $payStructure = htmlspecialchars(trim($_POST['payStructure']));
    $uniqueId = htmlspecialchars(trim($_POST['uniqueId']));
    $pin = trim($_POST['pin']);

    if (!empty($firstName) && !empty($lastName) && !empty($mobile) && !empty($employeeType) && !empty($payStructure) && !empty($uniqueId) && !empty($pin)) {
        $hashedPin = password_hash($pin, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO drivers (firstName, lastName, mobile, employeeType, payStructure, keyPayUniqueID, status, pin) VALUES (?, ?, ?, ?, ?, ?, 'Active', ?)");
        $stmt->bind_param("sssiiss", $firstName, $lastName, $mobile, $employeeType, $payStructure, $uniqueId, $hashedPin);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Data submitted successfully"]);
        } 
        else {
            echo json_encode(["status" => "error", "message" => "Database insertion failed"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Unauthorized access or invalid request"]);
}
