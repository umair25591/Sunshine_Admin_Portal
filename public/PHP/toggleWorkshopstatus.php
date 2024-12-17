<?php
require_once '../../db-connection.php';
session_start();
ob_clean();
header('Content-Type: application/json');

$response = ['success' => false, 'error' => 'Unknown error'];

if (isset($_POST['id']) && isset($_POST['workshopStatus']) && isset($_SESSION['adminId'])) {
    $vehicleId = intval($_POST['id']);
    $workshopStatus = intval($_POST['workshopStatus']);

    $query = "UPDATE vehicles SET workshop_status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param('ii', $workshopStatus, $vehicleId);

        if ($stmt->execute()) {
            $response = ['success' => true];
        } else {
            $response['error'] = 'Failed to execute update query';
        }
        $stmt->close();
    } else {
        $response['error'] = 'Failed to prepare update statement';
    }
} else {
    $response['error'] = 'Invalid request or missing parameters';
}

echo json_encode($response);
$conn->close();
