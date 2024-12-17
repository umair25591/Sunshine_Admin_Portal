<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId'])) {
    
    // Get filter values from the POST request (if provided)
    $vehicleId = isset($_POST['vehicleId']) ? $_POST['vehicleId'] : null;
    $driverId = isset($_POST['driverId']) ? $_POST['driverId'] : null;
    $showCurrent = isset($_POST['showCurrent']) ? intval($_POST['showCurrent']) : 0;
    $showResolved = isset($_POST['showResolved']) ? intval($_POST['showResolved']) : 0;
    $showUnresolvable = isset($_POST['showUnresolvable']) ? intval($_POST['showUnresolvable']) : 0;

    // Start the SQL query
    $sql = "
        SELECT 
            reportDefect.id,
            vehicles.numberPlate,                             -- Get vehicle number plate from vehicles table
            CONCAT(drivers.firstName, ' ', drivers.lastName) AS driverName,  -- Get full driver name from drivers table
            reportDefect.safeToDrive,
            reportDefect.status,
            reportDefect.faultType,
            reportDefect.fault,
            reportDefect.driverDefineFault,
            reportDefect.location,
            reportDefect.createdAt,
            reportDefect.updatedAt
        FROM 
            reportDefect
        JOIN 
            vehicles ON reportDefect.vehicle = vehicles.id    -- Join vehicles table using vehicle ID
        JOIN 
            drivers ON reportDefect.driver = drivers.id       -- Join drivers table using driver ID
        WHERE 1";   // Base condition for the WHERE clause

    // Add vehicle filter if provided
    if ($vehicleId) {
        $sql .= " AND reportDefect.vehicle = " . intval($vehicleId);
    }

    // Add driver filter if provided
    if ($driverId) {
        $sql .= " AND reportDefect.driver = " . intval($driverId);
    }

    if($showResolved){
        $sql .= " AND reportDefect.status = 'Fixed'";
    }

    if($showUnresolvable){
        $sql .= " AND reportDefect.status = 'Unrepairable'";
    }

    if($showCurrent){
        $sql .= " ORDER BY reportDefect.id DESC";
    }

    // Execute the query
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No records found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}
?>
