<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId'])) {
    $defectId = isset($_POST['defectId']) ? intval($_POST['defectId']) : null;
    $status = isset($_POST['status']) ? $conn->real_escape_string($_POST['status']) : null;

    if (!$defectId || !$status) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $sql = "UPDATE reportDefect SET status = '$status' WHERE id = $defectId";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Defect status updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating status: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Please login']);
}
?>
