<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    if(isset($_POST['query'])){

        $querys = json_decode($_POST['query']);
        mysqli_begin_transaction($conn);

        try {
            foreach($querys as $query){
                if (!mysqli_query($conn, $query)) {
                    throw new Exception("Error executing query: " . mysqli_error($conn));
                }
            }

            mysqli_commit($conn);
            echo json_encode(["status" => "success"]);

        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        mysqli_close($conn);
    }
    else {
        echo json_encode(["status" => "error", "message" => "No query provided"]);
    }
}
else {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
}
?>
