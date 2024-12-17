<?php
require_once '../../db-connection.php';
session_start();

if (isset($_SESSION['adminId']) && isset($_POST['vehicleId'])) {

    $vehicleId = $_POST['vehicleId'];
    
    $getVehicleHistory = "SELECT 
        va.id AS vehicleAllocationId,
        va.vehicle,
        va.driver,
        d.id,
        CONCAT(d.firstName, ' ', d.lastName) AS driverName,
        va.start,
        va.finish,
        va.status AS vehicleAllocationStatus,
        odr.meterReading AS lastOdometerReading,
        vr.id AS vehicleReturnId
    FROM vehicleAllocation va
    JOIN drivers d ON va.driver = d.id
    LEFT JOIN odometerReadings odr ON va.id = odr.vehicleAllocation
    LEFT JOIN vehiclereturns vr ON va.id = vr.vehicleAllocation
    WHERE va.status = 'Inactive'
    AND va.vehicle = {$vehicleId}
    ORDER BY va.id DESC;";

    $runGetVehicleHistory = mysqli_query($conn, $getVehicleHistory);
    $numRowsGetVehicleHistory = mysqli_num_rows($runGetVehicleHistory);

    if ($numRowsGetVehicleHistory > 0) {
        $finalData = [];

        // Loop through vehicle history
        while ($result = mysqli_fetch_assoc($runGetVehicleHistory)) {

            $vehicleAllocationId = $result['vehicleAllocationId'];
            $start = $result['start'];
            $finish = $result['finish'];
            $driverName = $result['driverName'];
            $driverId = $result['driver'];
            $lastOdometerReading = $result['lastOdometerReading'];
            $vehicleReturnId = $result['vehicleReturnId'];

            $vehicleDetails = [];

            if ($vehicleReturnId != null) {
                $getVehicleDetails = "SELECT returnChecklist, value, otherValue, fuelLitres 
                                      FROM vehicleDetails 
                                      WHERE vehicleReturn = {$vehicleReturnId};";
                $runGetVehicleDetails = mysqli_query($conn, $getVehicleDetails);
                
                if ($runGetVehicleDetails) {
                    while ($details = mysqli_fetch_assoc($runGetVehicleDetails)) {
                        $vehicleDetails[] = [
                            'returnChecklist' => $details['returnChecklist'],
                            'value' => $details['value'],
                            'otherValue' => $details['otherValue'],
                            'fuelLitres' => $details['fuelLitres']
                        ];
                    }
                }
            }

           
            $getVehicleAverage = getVehicleAverage($vehicleId, $conn);
            $driverAverage = getDriverAverage($driverId, $conn);
            

            $finalData[] = [
                'vehicleAllocationId' => $vehicleAllocationId,
                'start' => $start,
                'finish' => $finish,
                'driverName' => $driverName,
                'lastOdometerReading' => $lastOdometerReading,
                'averageVehicleEfficiency' => $getVehicleAverage,
                'averageDriverEfficiency' => $driverAverage,
                'vehicleDetails' => $vehicleDetails,
            ];
        }

        echo json_encode(['status' => 'success', 'data' => $finalData]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No History Found']); 
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Please Login']);
}


function getDriverAverage($driverId, $conn){
    // driver average calculation 
    $driverAverageQuery = "SELECT id, vehicleAllocation FROM vehiclereturns WHERE driver = {$driverId} ORDER BY id DESC LIMIT 100";
    $result = $conn->query($driverAverageQuery);
    $vehicleReturns = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if($row['vehicleAllocation'] != null){
                $vehicleReturns[] = $row;
            }
        }
    }
    
    $returnIds = array_column($vehicleReturns, 'id');
    $vehicleAllocations = array_column($vehicleReturns, 'vehicleAllocation');

    $totalLitres = 0;

    if (!empty($returnIds)) {
        $returnIdsString = implode(',', $returnIds);
        $sql = "SELECT vehicleReturn, fuelLitres FROM vehicleDetails WHERE vehicleReturn IN ($returnIdsString) AND returnChecklist = 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if($row['fuelLitres'] != null){
                    $totalLitres += $row['fuelLitres'];
                }
            }
        }
    }

    $totalMileage = 0;
    $previousReading = 0;
    $avergeFuelEfficiency = '-';

    if (!empty($vehicleAllocations)) {
        $vehicleAllocationsString = implode(',', $vehicleAllocations);
        $sql = "SELECT vehicleAllocation, meterReading FROM odometerReadings WHERE vehicleAllocation IN ($vehicleAllocationsString) ORDER BY createdAt DESC";
        $result = $conn->query($sql);
        $odometerReadings = [];
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $odometerReadings[] = $row;
            }
        }
    
        // Step 4: Calculate mileage (difference between readings)
        foreach ($odometerReadings as $reading) {
            if ($previousReading !== null) {
                $mileage = $reading['meterReading'] - $previousReading ;
                $totalMileage += $mileage;
            }
            $previousReading = $reading['meterReading']; // Update previous reading
        }
    }

    if ($totalMileage > 0 && $totalLitres > 0) {
        $fuelEfficiency = ($totalLitres / $totalMileage) * 100;
        $avergeFuelEfficiency = round($fuelEfficiency, 2) . "L/100KM";
    } else {
        $avergeFuelEfficiency = '-';
    }

    return $avergeFuelEfficiency;
}

function getVehicleAverage($vehicleId, $conn){
    $sql = "SELECT id, vehicleAllocation FROM vehiclereturns WHERE vehicle = {$vehicleId} ORDER BY id DESC LIMIT 100";
    $result = $conn->query($sql);
    $vehicleReturns = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if($row['vehicleAllocation'] != null){
                $vehicleReturns[] = $row;
            }
        }
    }
    
    $returnIds = array_column($vehicleReturns, 'id');
    $vehicleAllocations = array_column($vehicleReturns, 'vehicleAllocation');

    $totalLitres = 0;

    if (!empty($returnIds)) {
        $returnIdsString = implode(',', $returnIds);
        $sql = "SELECT vehicleReturn, fuelLitres FROM vehicleDetails WHERE vehicleReturn IN ($returnIdsString) AND returnChecklist = 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if($row['fuelLitres'] != null){
                    $totalLitres += $row['fuelLitres'];
                }
            }
        }
    }

    $totalMileage = 0;
    $previousReading = 0;
    $avergeFuelEfficiency = '-';

    if (!empty($vehicleAllocations)) {
        $vehicleAllocationsString = implode(',', $vehicleAllocations);
        $sql = "SELECT vehicleAllocation, meterReading FROM odometerReadings WHERE vehicleAllocation IN ($vehicleAllocationsString) ORDER BY createdAt DESC";
        $result = $conn->query($sql);
        $odometerReadings = [];
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $odometerReadings[] = $row;
            }
        }
    
        // Step 4: Calculate mileage (difference between readings)
        foreach ($odometerReadings as $reading) {
            if ($previousReading !== null) {
                $mileage = $reading['meterReading'] - $previousReading ;
                $totalMileage += $mileage;
            }
            $previousReading = $reading['meterReading']; // Update previous reading
        }
    }

    if ($totalMileage > 0 && $totalLitres > 0) {
        $fuelEfficiency = ($totalLitres / $totalMileage) * 100;
        $avergeFuelEfficiency = round($fuelEfficiency, 2) . "L/100KM";
    } else {
        $avergeFuelEfficiency = '-';
    }

    return $avergeFuelEfficiency;
}

?>
