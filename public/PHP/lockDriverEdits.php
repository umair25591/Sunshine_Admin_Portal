<?php
require_once '../../db-connection.php';
session_start();

$response = [];

if(isset($_SESSION['adminId'])){
    if(isset($_POST['shiftIds'])){
        $shiftIds = json_decode($_POST['shiftIds']);
        
        if(is_array($shiftIds)){
            foreach ($shiftIds as $shiftId) {
                $Id = $shiftId; 

                $updateShiftToLock = "UPDATE driverShifts SET lockEdit = 1 WHERE id = ?";
                $stmt = $conn->prepare($updateShiftToLock);
                $stmt->bind_param("i", $Id);

                if($stmt->execute()){
                    $response[] = ['status' => 'success', 'shiftId' => $Id, 'message' => 'Shift locked successfully'];
                }
                else {
                    $response[] = ['status' => 'error', 'shiftId' => $Id, 'message' => 'Failed to lock shift'];
                }
                
                $stmt->close();
            }
        }
        else {
            $response[] = ['status' => 'error', 'message' => 'Invalid shift IDs'];
        }

    }
    else {
        $response[] = ['status' => 'error', 'message' => 'No shift IDs provided'];
    }
}
else {
    $response[] = ['status' => 'error', 'message' => 'Unauthorized access'];
}

echo json_encode($response);
$conn->close();

?>
