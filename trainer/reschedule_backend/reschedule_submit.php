<?php
session_start();
require_once '../db_config.php';

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];
$request_type = $_POST['request_type'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$new_start_time = $_POST['new_start_time'] ?? null;
$new_end_time = $_POST['new_end_time'] ?? null;

// Check if a request already exists for the same date range
$check_query = "SELECT COUNT(*) FROM trainer_reschedules 
                WHERE trainer_id = ? 
                AND (start_date <= ? AND end_date >= ?)";

$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("sss", $trainer_id, $end_date, $start_date); // FIXED: changed "iss" to "sss"
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count > 0) {
    $_SESSION['error'] = "You have already submitted a request for the selected dates.";
    header("Location: ../reschedule.php");
    exit;
}

// Insert the new request
$query = "INSERT INTO trainer_reschedules (trainer_id, request_type, start_date, end_date, new_start_time, new_end_time, status, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";

$stmt = $conn->prepare($query);
$stmt->bind_param("isssss", $trainer_id, $request_type, $start_date, $end_date, $new_start_time, $new_end_time);

if ($stmt->execute()) {
    $_SESSION['success'] = "Your reschedule request has been submitted for approval.";
} else {
    $_SESSION['error'] = "There was an error submitting your request. Please try again.";
}

$stmt->close();
$conn->close();

header("Location: ../reschedule.php");
exit;
?>
