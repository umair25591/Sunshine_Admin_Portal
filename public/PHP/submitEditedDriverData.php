<?php
require_once '../../db-connection.php';
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $driverId = isset($_POST['driverId']) ? trim($_POST['driverId']) : '';
    $firstName = isset($_POST['firstName']) ? trim($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? trim($_POST['lastName']) : '';
    $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
    $employeeType = isset($_POST['employeeType']) ? trim($_POST['employeeType']) : '';
    $payStructure = isset($_POST['payStructure']) ? trim($_POST['payStructure']) : '';
    $uniqueId = isset($_POST['uniqueId']) ? trim($_POST['uniqueId']) : '';
    $pin = isset($_POST['pin']) ? trim($_POST['pin']) : '';

    if (empty($driverId) || empty($firstName) || empty($lastName) || empty($mobile) || empty($employeeType) || empty($payStructure) || empty($uniqueId)) {
        $response['status'] = 'error';
        $response['message'] = 'All fields except pin are required';
    } 
    else {
        if (!empty($pin)) {
            $hashedPin = password_hash($pin, PASSWORD_DEFAULT);
            $sql = "UPDATE drivers SET
                        firstName = ?,
                        lastName = ?,
                        mobile = ?,
                        employeeType = ?,
                        payStructure = ?,
                        keyPayUniqueID = ?,
                        pin = ?
                    WHERE id = ?";
            $params = [$firstName, $lastName, $mobile, $employeeType, $payStructure, $uniqueId, $hashedPin, $driverId];
            $types = 'sssiissi';
        } else {
            $sql = "UPDATE drivers SET
                        firstName = ?,
                        lastName = ?,
                        mobile = ?,
                        employeeType = ?,
                        payStructure = ?,
                        keyPayUniqueID = ?
                    WHERE id = ?";
            $params = [$firstName, $lastName, $mobile, $employeeType, $payStructure, $uniqueId, $driverId];
            $types = 'sssiisi';
        }

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Driver data has been updated successfully';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to update driver data';
            }
            
            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Database error: Unable to prepare statement';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method';
}

$conn->close();
echo json_encode($response);
?>
