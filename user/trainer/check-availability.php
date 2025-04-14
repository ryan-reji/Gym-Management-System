<?php
require_once 'config/db.php';

// Check if user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

// Get JSON data from POST request
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

if (!$data || !isset($data['trainer_id']) || !isset($data['dates']) || !isset($data['time_slot'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request data']);
    exit;
}

$trainer_id = intval($data['trainer_id']);
$dates = $data['dates'];
$time_slot = $data['time_slot'];

// Prepare the response
$response = [
    'available' => true,
    'booked_dates' => []
];

// Check each date for the specified time slot
foreach ($dates as $date) {
    $query = "SELECT * FROM trainer_bookings 
              WHERE trainer_id = ? 
              AND session_date = ? 
              AND default_session_time = ? 
              AND status = 'active'";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $trainer_id, $date, $time_slot);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $response['available'] = false;
        $response['booked_dates'][] = $date;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);