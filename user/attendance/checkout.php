<?php
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response headers
header('Content-Type: application/json');

try {
    // Debug: Ensure proper database connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Validate inputs
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $gym_id = filter_input(INPUT_POST, 'gym_id', FILTER_VALIDATE_INT);

    if (!$user_id || !$gym_id) {
        throw new Exception("Invalid input: Missing or invalid user_id or gym_id");
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if user is actually checked in
        $check_stmt = $conn->prepare("SELECT * FROM attendance WHERE user_id = ? AND check_out_time IS NULL ORDER BY check_in_time DESC LIMIT 1");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("No active check-in found for this user");
        }

        $check_out_time = date('Y-m-d H:i:s');

        // Update attendance record
        $stmt = $conn->prepare("UPDATE attendance SET check_out_time = ? WHERE user_id = ? AND check_out_time IS NULL");
        $stmt->bind_param("si", $check_out_time, $user_id);

        if (!$stmt->execute()) {
            throw new Exception("Error updating attendance record: " . $stmt->error);
        }

        // Update gym occupancy (prevent negative values)
        $update_stmt = $conn->prepare("UPDATE gym SET current_occupancy = GREATEST(0, current_occupancy - 1) WHERE gym_id = ? AND current_occupancy > 0");
        $update_stmt->bind_param("i", $gym_id);

        if (!$update_stmt->execute()) {
            throw new Exception("Error updating gym occupancy: " . $update_stmt->error);
        }

        // If no rows were affected, the gym might already be at 0 occupancy
        if ($update_stmt->affected_rows === 0) {
            // Check if gym exists
            $check_gym_stmt = $conn->prepare("SELECT current_occupancy FROM gym WHERE gym_id = ?");
            $check_gym_stmt->bind_param("i", $gym_id);
            $check_gym_stmt->execute();
            $gym_result = $check_gym_stmt->get_result();
            
            if ($gym_result->num_rows === 0) {
                throw new Exception("Gym not found");
            }
            
            $gym_data = $gym_result->fetch_assoc();
            if ($gym_data['current_occupancy'] <= 0) {
                // This is not an error case, just means the gym was already at 0
                // We still want to allow the checkout to succeed
            } else {
                throw new Exception("Failed to update gym occupancy");
            }
        }

        // Commit transaction
        $conn->commit();
        
        echo json_encode([
            "success" => true, 
            "message" => "Check-out successful"
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Check-out error: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>