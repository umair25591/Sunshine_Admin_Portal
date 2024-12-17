<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId'])) {
    $query = "SELECT 
            v.id,
            v.numberPlate, 
            v.location,
            v.workshopStatus,
            v.status,
            CASE
                WHEN MAX(r.status = 'Active') THEN 'Active'
                ELSE 'Inactive'
            END AS defectStatus,
            CASE
                WHEN MAX(va.status = 'Active') THEN 'Active'
                ELSE 'Inactive'
            END AS allocationStatus,
            CASE 
                WHEN MAX(d.id) IS NOT NULL THEN CONCAT(MAX(d.firstName), ' ', MAX(d.lastName))
                ELSE 'No-Driver'
            END AS allocatedDriver,
            CASE
                WHEN MAX(l.status = 'Active') THEN 'Lockout'
                ELSE 'Not Lockout'
            END AS lockoutStatus
        FROM 
            vehicles v
        LEFT JOIN 
            reportDefect r ON v.id = r.vehicle
        LEFT JOIN 
            vehicleAllocation va ON v.id = va.vehicle AND va.status = 'Active'
        LEFT JOIN 
            drivers d ON va.driver = d.id
        LEFT JOIN
            vehicleLockouts l ON v.id = l.vehicle AND l.status = 'Active'
        GROUP BY 
            v.id, v.numberPlate, v.location, v.workshopStatus, v.status;
        ";
    
    $result = mysqli_query($conn, $query);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch data from the database.']);
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        $vehicles = [];

        while ($row = mysqli_fetch_assoc($result)) {
            // $statusDisplay = ($row['vehicleStatus'] === 'Active') ? 'Enabled' : 'Disabled';
            // $workshopDisplay = ($row['workshop_status'] == 1) ? 'Enabled' : 'Disabled'; 
            // $vehicleLocation = ($row['workshop_status'] == 1) ? 'Workshop' : 'Depot'; 
            // $vehicles[] = [
            //     'id' => $row['id'],
            //     'numberPlate' => $row['numberPlate'],
            //     'vehicle_location' => $vehicleLocation,
            //     'defectStatus' => $row['defectStatus'],
            //     'defect' => $row['defect'],
            //     'workshopStatus' => $workshopDisplay,
            //     'vehicleStatus' => $statusDisplay,
            //     'mileage' => $row['mileage'],
            //     'note'=> $row['note']
            // ];
            $vehicles[] = $row;
        }

        echo json_encode($vehicles); 
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No vehicles found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}
?>
