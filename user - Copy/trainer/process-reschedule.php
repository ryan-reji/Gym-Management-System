<?php
// process-reschedule.php - Processes session rescheduling requests

// Start session and include database connection
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: my-bookings.php');
    exit;
}

// Get form data
$session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : 0;
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$new_time = isset($_POST['new_time']) ? mysqli_real_escape_string($conn, $_POST['new_time']) : '';
$reason = isset($_POST['reason']) ? mysqli_real_escape_string($conn, $_POST['reason']) : '';

// Validate session and booking IDs
if ($session_id <= 0 || $booking_id <= 0 || empty($new_time) || empty($reason)) {
    $_SESSION['error'] = "Missing required information for rescheduling.";
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

// Verify session belongs to this user's booking
$validate_query = "SELECT s.*, b.user_id, b.trainer_id, t.email as trainer_email 
                   FROM trainer_sessions s 
                   JOIN trainer_bookings b ON s.booking_id = b.id 
                   JOIN trainers t ON b.trainer_id = t.id
                   WHERE s.id = $session_id AND b.id = $booking_id AND b.user_id = " . $_SESSION['user_id'];
$validate_result = mysqli_query($conn, $validate_query);

if (mysqli_num_rows($validate_result) == 0) {
    $_SESSION['error'] = "Invalid session. Please try again.";
    header("Location: my-bookings.php");
    exit;
}

$session_data = mysqli_fetch_assoc($validate_result);

// Check if session is eligible for rescheduling
$session_timestamp = strtotime($session_data['session_date'] . ' ' . $session_data['session_time']);
$now = time();
$hours_difference = ($session_timestamp - $now) / 3600;

if ($hours_difference < 24) {
    $_SESSION['error'] = "Sessions must be rescheduled at least 24 hours in advance.";
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

if ($session_data['status'] == 'cancelled' || $session_data['status'] == 'completed') {
    $_SESSION['error'] = "This session cannot be rescheduled because it is already " . $session_data['status'] . ".";
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

// Update session with new time
$update_query = "UPDATE trainer_sessions 
                SET session_time = '$new_time', 
                    status = 'rescheduled', 
                    last_updated = NOW(), 
                    notes = CONCAT(IFNULL(notes, ''), '\nRescheduled on " . date('Y-m-d H:i:s') . ". Reason: $reason')
                WHERE id = $session_id";

$update_result = mysqli_query($conn, $update_query);

if (!$update_result) {
    $_SESSION['error'] = "Failed to reschedule session. Please try again. Error: " . mysqli_error($conn);
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

// Get user details for notification
$user_query = "SELECT name, email FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Send email notification to trainer
$trainer_email = $session_data['trainer_email'];
$subject = "Session Rescheduled - " . date('M d, Y', strtotime($session_data['session_date']));

$message = "
<html>
<head>
  <title>Session Rescheduled</title>
</head>
<body>
  <h2>Training Session Rescheduled</h2>
  <p>A client has rescheduled their training session.</p>
  <p><strong>Client:</strong> {$user_data['name']}</p>
  <p><strong>Date:</strong> " . date('l, F d, Y', strtotime($session_data['session_date'])) . "</p>
  <p><strong>Original Time:</strong> " . date('g:i A', strtotime($session_data['session_time'])) . "</p>
  <p><strong>New Time:</strong> " . date('g:i A', strtotime($new_time)) . "</p>
  <p><strong>Reason:</strong> $reason</p>
  <p>Please log in to your trainer portal to view your updated schedule.</p>
</body>
</html>
";

// To send HTML mail, the Content-type header must be set
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: {$_SERVER['HTTP_HOST']} <noreply@{$_SERVER['HTTP_HOST']}>" . "\r\n";

// Send email
mail($trainer_email, $subject, $message, $headers);

// Log the rescheduling activity
$log_query = "INSERT INTO activity_log 
             (user_id, activity_type, related_id, details, ip_address, created_at) 
             VALUES 
             (" . $_SESSION['user_id'] . ", 'session_reschedule', $session_id, 
             'Rescheduled session from " . $session_data['session_time'] . " to $new_time. Reason: $reason', 
             '" . $_SERVER['REMOTE_ADDR'] . "', NOW())";
mysqli_query($conn, $log_query);

// Set success message and redirect
$_SESSION['success'] = "Your session has been successfully rescheduled to " . date('g:i A', strtotime($new_time)) . ".";
header("Location: manage-sessions.php?booking_id=$booking_id");
exit;
?>