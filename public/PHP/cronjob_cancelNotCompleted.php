<?php
require_once '../../db-connection.php';
session_start();

$cancelYardTask = "UPDATE yardDuties SET status = 'Cancel' WHERE cancelNotCompleted = 1 AND status = 'Pending';";
$runCancelYardTask = mysqli_query($conn, $cancelYardTask);

if ($runCancelYardTask) {
    if (mysqli_affected_rows($conn) > 0) {
        echo 'Yard Task Canceled';
    }
    else {
        echo 'No tasks were updated. None matched the criteria.';
    }
}
else {
    echo 'Query error: ' . mysqli_error($conn);
}

mysqli_close($conn);
?>
