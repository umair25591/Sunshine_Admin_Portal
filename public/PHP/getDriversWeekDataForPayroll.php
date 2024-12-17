<?php
require_once '../../db-connection.php'; 

$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (!DateTime::createFromFormat('Y-m-d', $start_date) || !DateTime::createFromFormat('Y-m-d', $end_date)) {
    echo json_encode(['error' => 'Invalid date format.']);
    exit;
}

$start_date = $conn->real_escape_string($start_date);
$end_date = $conn->real_escape_string($end_date);

$getDriversAndShifts = "SELECT
        ds.id AS shift_id,
        ds.driver AS driver_id,
        ds.job,
        ds.clockIn,
        ds.clockOut,
        ds.duration AS shift_duration,
        ds.clockInStatus,
        ds.status AS shift_status,
        ds.requiresApproval,
        ds.isApproved,
        ds.reason,
        ds.lockEdit,
        ds.createdAt AS shift_created_at,
        ds.updatedAt AS shift_updated_at,
        b.id AS break_id,
        b.duration AS break_duration,
        b.start AS break_start,
        b.finish AS break_finish,
        b.isPaid AS break_is_paid,
        b.status AS break_status,
        d.id AS driver_id,
        d.firstName,
        d.lastName,
        d.status AS driver_status
    FROM driverShifts ds
    LEFT JOIN breaks b ON ds.id = b.driverShifts AND b.isDeleted = 0
    LEFT JOIN drivers d ON ds.driver = d.id
    WHERE (ds.clockIn BETWEEN '{$start_date}' AND '{$end_date}' 
      OR ds.clockOut BETWEEN '{$start_date}' AND '{$end_date}')
      AND ds.status = 'Inactive'
      AND d.isDeleted = 0
      AND d.status = 'Active'

    ORDER BY ds.clockIn ASC;";

$result = mysqli_query($conn, $getDriversAndShifts);

if ($result) {
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} 
else {
    echo json_encode(['error' => 'Failed to fetch data: ' . mysqli_error($conn)]);
}

mysqli_close($conn);
?>
