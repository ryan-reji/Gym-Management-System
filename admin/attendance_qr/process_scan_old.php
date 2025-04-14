<?php
session_start();
include 'db_config.php'; // Ensure this file connects to your database

// Get and decode input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Initialize response
$response = ['status' => 'error', 'message' => 'Invalid request'];

// Get QR data
$username = isset($data['username']) ? trim($data['username']) : '';
$action = isset($data['action']) ? trim($data['action']) : '';

// Log received data for debugging
error_log("Received data: " . $input);

if (!empty($username)) {
    // Check if the user exists
    $userQuery = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Check if the user has an active plan
        $planQuery = "SELECT plan_id, end_date FROM plan_bookings WHERE user_id = ? AND end_date >= CURDATE()";
        $stmt = $conn->prepare($planQuery);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($plan_id, $end_date);
            $stmt->fetch();
            $stmt->close();
            
            // Process based on whether this is a check-in/check-out action or just a scan
            if (!empty($action)) {
                if ($action == "check_in") {
                    // Allow multiple check-ins - simply insert a new record
                    $insertQuery = "INSERT INTO gym_attendance (user_id, date, check_in_time) VALUES (?, CURDATE(), NOW())";
                    $stmt = $conn->prepare($insertQuery);
                    $stmt->bind_param("i", $user_id);
                    if ($stmt->execute()) {
                        $response = ['status' => 'success', 'message' => 'Check-in recorded successfully!'];
                    } else {
                        $response = ['status' => 'error', 'message' => 'Failed to record check-in: ' . $conn->error];
                    }
                    $stmt->close();
                } 
                else if ($action == "check_out") {
                    // Find the most recent check-in without a checkout
                    $updateQuery = "UPDATE gym_attendance 
                                   SET check_out_time = NOW() 
                                   WHERE user_id = ? 
                                   AND check_out_time IS NULL 
                                   ORDER BY check_in_time DESC 
                                   LIMIT 1";
                    $stmt = $conn->prepare($updateQuery);
                    $stmt->bind_param("i", $user_id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        $response = ['status' => 'success', 'message' => 'Check-out recorded successfully!'];
                    } else {
                        $response = ['status' => 'error', 'message' => 'No active check-in found to check out from.'];
                    }
                    $stmt->close();
                }
            } 
            else {
                // Just a scan - always offer check-in option
                // Check if there's an active session without checkout
                $activeQuery = "SELECT COUNT(*) as active_count FROM gym_attendance 
                               WHERE user_id = ? 
                               AND check_out_time IS NULL";
                $stmt = $conn->prepare($activeQuery);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $activeData = $result->fetch_assoc();
                $stmt->close();
                
                if ($activeData && $activeData['active_count'] > 0) {
                    // User has active sessions
                    $response = [
                        'status' => 'both', 
                        'message' => 'What would you like to do?',
                        'active_sessions' => $activeData['active_count'],
                        'show_buttons' => true
                    ];
                } else {
                    // User has no active sessions
                    $response = [
                        'status' => 'check-in', 
                        'message' => 'Welcome! Would you like to check in?',
                        'show_buttons' => true
                    ];
                }
            }
        } else {
            $response = ['status' => 'error', 'message' => 'No active membership plan found.'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Invalid QR: Member not found.'];
    }
} else {
    $response = ['status' => 'error', 'message' => 'Username is missing or invalid.'];
}

// Set proper content type header
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>