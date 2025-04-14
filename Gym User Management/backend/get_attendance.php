<?php
session_start(); 
include 'db_connection.php'; // Include your database connection

$user_id = $_SESSION['id']; // Assuming user is logged in and session stores user_id
$month = $_GET['month']; // Format: YYYY-MM (e.g., "2025-04")

error_log("User ID: " . $_SESSION['id']);
error_log("Month: " . $month);

$query = "
    SELECT 
        DATE(date) as attendance_date, 
        TIMESTAMPDIFF(SECOND, check_in_time, check_out_time) AS duration_seconds
    FROM gym_attendance
    WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $month);
$stmt->execute();
$result = $stmt->get_result();

$attendance_data = [];

while ($row = $result->fetch_assoc()) {
    $duration = $row['duration_seconds'] / 60; // Convert seconds to minutes (with decimals)
    $intensity = 'attendance-0'; // Default: No Visit

    if ($duration > 0 && $duration <= 60) {
        $intensity = 'attendance-1'; // Light
    } elseif ($duration > 60 && $duration <= 120) {
        $intensity = 'attendance-2'; // Medium
    } elseif ($duration > 120) {
        $intensity = 'attendance-3'; // Intense
    }

    $attendance_data[$row['attendance_date']] = $intensity;
}

echo json_encode($attendance_data);
?>
