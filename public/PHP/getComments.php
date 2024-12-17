<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId'])) {

    $defectId = isset($_POST['defectId']) ? intval($_POST['defectId']) : null;

    if (!$defectId) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid defect ID']);
        exit;
    }

    $sql = "SELECT *
        FROM defectComment 
        WHERE defect = $defectId
        ORDER BY createdAt DESC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $comments]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No comments found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}
?>
