<?php
require_once '../../db-connection.php';
session_start();

if(isset($_SESSION['adminId'])){
    $getJobs = "SELECT
    j.*,
    d.firstName,
    d.lastName,
    v.numberPlate,
    c.name AS customerName
    FROM
        jobs j
    LEFT JOIN
        drivers d ON j.driver = d.id
    LEFT JOIN
        vehicles v ON j.vehicle = v.id
    LEFT JOIN
        customers c ON j.customer = c.id
    ORDER BY
        j.id DESC;";


    $runGetJobs = mysqli_query($conn, $getJobs);
    $numRowGetJobs = mysqli_num_rows($runGetJobs);

    if($numRowGetJobs > 0){
        $data = [];
        while ($result = mysqli_fetch_assoc($runGetJobs)) {
            $data[] = $result;
        }
        echo json_encode(value: ['status' => 'success', 'data' => $data]);
    }
    else{
        echo json_encode(['status' => 'warning', 'message' => 'No Record Found']);
    }
}
else{
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}

?>