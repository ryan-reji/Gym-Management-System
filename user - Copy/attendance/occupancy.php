<?php
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response headers
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Invalid request method");
    }

    $gym_id = filter_input(INPUT_GET, 'gym_id', FILTER_VALIDATE_INT);
    
    if (!$gym_id) {
        throw new Exception("Invalid or missing gym_id");
    }

    $stmt = $conn->prepare("SELECT current_occupancy, max_capacity FROM gym WHERE gym_id = ?");
    $stmt->bind_param("i", $gym_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "current_occupancy" => $row['current_occupancy'],
            "max_capacity" => $row['max_capacity']
        ]);
    } else {
        throw new Exception("Gym not found");
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>