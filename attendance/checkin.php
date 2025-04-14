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
        // Check if user is already checked in
        $check_stmt = $conn->prepare("SELECT user_id FROM attendance WHERE user_id = ? AND check_out_time IS NULL");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("User is already checked in");
        }

        $check_in_time = date('Y-m-d H:i:s');
        $date = date('Y-m-d');

        // Insert attendance record
        $stmt = $conn->prepare("INSERT INTO attendance (user_id, check_in_time, date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $check_in_time, $date);

        if (!$stmt->execute()) {
            throw new Exception("Error inserting attendance: " . $stmt->error);
        }

        // Update gym occupancy
        $update_stmt = $conn->prepare("UPDATE gym SET current_occupancy = current_occupancy + 1 WHERE gym_id = ? AND current_occupancy < max_capacity");
        $update_stmt->bind_param("i", $gym_id);

        if (!$update_stmt->execute() || $update_stmt->affected_rows === 0) {
            throw new Exception("Error updating occupancy: Gym may be at maximum capacity");
        }

        // Commit transaction
        $conn->commit();
        
        echo json_encode(["success" => true, "message" => "Check-in successful"]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Check-in error: " . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>