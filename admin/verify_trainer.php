<?php
// verify_trainer.php
include('db_connect.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the response array
$response = ['valid' => false, 'message' => 'Invalid trainer ID'];

// Log function
function logTrainerVerification($message) {
    file_put_contents('trainer_verification_log.txt', date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Check if a trainer ID was provided
if (isset($_GET['trainer_id'])) {
    $trainer_id = $_GET['trainer_id'];
    logTrainerVerification("Verifying trainer ID: $trainer_id");
    
    // Clean up the trainer ID (remove any "GYMSHARK-TRAINER-" prefix if present)
    if (strpos($trainer_id, 'GYMSHARK-TRAINER-') === 0) {
        $trainer_id = substr($trainer_id, strlen(''));
        logTrainerVerification("Extracted ID: $trainer_id");
    }
    
    // Check if this is a valid trainer in the database
    $stmt = $conn->prepare("SELECT trainer_id, first_name, last_name, specialization FROM trainers WHERE trainer_id = ?");
    $stmt->bind_param("s", $trainer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $trainer = $result->fetch_assoc();
        logTrainerVerification("Found trainer: " . $trainer['first_name'] . " " . $trainer['last_name']);
        
        // Check if trainer is already checked in
        $check_in_stmt = $conn->prepare("SELECT * FROM trainer_attendance WHERE trainer_id = ? AND check_out_time IS NULL");
        $check_in_stmt->bind_param("s", $trainer_id);
        $check_in_stmt->execute();
        $check_in_result = $check_in_stmt->get_result();
        
        if ($check_in_result->num_rows > 0) {
            // Trainer is already checked in
            $response = [
                'valid' => true,
                'already_checked_in' => true,
                'name' => $trainer['first_name'] . ' ' . $trainer['last_name'],
                'trainer_id' => $trainer_id,
                'specialization' => $trainer['specialization'],
                'message' => 'Trainer is already checked in'
            ];
            logTrainerVerification("Trainer already checked in: $trainer_id");
        } else {
            // Trainer is valid and not checked in
            $response = [
                'valid' => true,
                'already_checked_in' => false,
                'name' => $trainer['first_name'] . ' ' . $trainer['last_name'],
                'trainer_id' => $trainer_id,
                'specialization' => $trainer['specialization'],
                'message' => 'Trainer verified successfully'
            ];
            logTrainerVerification("Trainer verified successfully: $trainer_id");
        }
    } else {
        $response = [
            'valid' => false,
            'message' => 'Trainer not found'
        ];
        logTrainerVerification("Trainer not found: $trainer_id");
    }
} else {
    $response = [
        'valid' => false,
        'message' => 'No trainer ID provided'
    ];
    logTrainerVerification("No trainer ID provided in request");
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>