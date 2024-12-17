<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId'])) {

    $defectId = isset($_POST['defectId']) ? intval($_POST['defectId']) : null;
    $commentText = isset($_POST['commentText']) ? $conn->real_escape_string($_POST['commentText']) : null;
    $type = $_POST['type'];

    if (!$defectId || !$commentText) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $sql = "INSERT INTO defectComment (defect, note, type, status, createdAt, updatedAt) VALUES($defectId, '$commentText', '{$type}', 'Active', NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Comment submitted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error submitting comment: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}
?>
