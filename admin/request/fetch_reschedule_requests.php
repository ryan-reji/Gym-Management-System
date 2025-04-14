<?php
header('Content-Type: application/json');
include '../db_connect.php';

// Fetch all reschedule requests with trainer details
$query = "SELECT rr.trainer_reschedule_id, rr.request_type, rr.created_at AS requested_date, 
                 rr.start_date, rr.end_date, rr.new_start_time, rr.new_end_time, rr.status, rr.request_type, 
                 CONCAT(t.FirstName, ' ', t.LastName) AS trainer_name
          FROM trainer_reschedules rr
          JOIN trainers t ON rr.trainer_id = t.trainer_id
          ORDER BY rr.created_at DESC";

$result = $conn->query($query);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit();
}

$requests = [];
while ($row = $result->fetch_assoc()) {
    $requests[] = $row;
}

echo json_encode($requests);
?>
