<?php
// process-cancel.php - Processes session cancellation requests

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
$reason = isset($_POST['reason']) ? mysqli_real_escape_string($conn, $_POST['reason']) : '';

// Validate session and booking IDs
if ($session_id <= 0 || $booking_id <= 0 || empty($reason)) {
    $_SESSION['error'] = "Missing required information for cancellation.";
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

// Verify session belongs to this user's booking
$validate_query = "SELECT s.*, b.user_id, b.trainer_id, t.email as trainer_email, t.FirstName, t.LastName 
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

// Check if session is eligible for cancellation
$session_timestamp = strtotime($session_data['session_date'] . ' ' . $session_data['session_time']);
$now = time();
$hours_difference = ($session_timestamp - $now) / 3600;

if ($hours_difference < 24) {
    $_SESSION['error'] = "Sessions must be cancelled at least 24 hours in advance.";
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

if ($session_data['status'] == 'cancelled' || $session_data['status'] == 'completed') {
    $_SESSION['error'] = "This session cannot be cancelled because it is already " . $session_data['status'] . ".";
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

// Update session to cancelled status
$update_query = "UPDATE trainer_sessions 
                SET status = 'cancelled', 
                    last_updated = NOW(), 
                    notes = CONCAT(IFNULL(notes, ''), '\nCancelled on " . date('Y-m-d H:i:s') . ". Reason: $reason')
                WHERE id = $session_id";

$update_result = mysqli_query($conn, $update_query);

if (!$update_result) {
    $_SESSION['error'] = "Failed to cancel session. Please try again. Error: " . mysqli_error($conn);
    header("Location: manage-sessions.php?booking_id=$booking_id");
    exit;
}

// Get user details for notification
$user_query = "SELECT name, email FROM users WHERE id = " . $_SESSION['user_id'];
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Send email notification to trainer
$trainer_email = $session_data['trainer_email'];
$trainer_name = $session_data['FirstName'] . ' ' . $session_data['LastName'];
$subject = "Session Cancelled - " . date('M d, Y', strtotime($session_data['session_date']));

$message = "
<html>
<head>
  <title>Session Cancelled</title>
</head>
<body>
  <h2>Training Session Cancelled</h2>
  <p>A client has cancelled their training session.</p>
  <p><strong>Client:</strong> {$user_data['name']}</p>
  <p><strong>Date:</strong> " . date('l, F d, Y', strtotime($session_data['session_date'])) . "</p>
  <p><strong>Time:</strong> " . date('g:i A', strtotime($session_data['session_time'])) . "</p>
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

// Send confirmation email to user
$user_email = $user_data['email'];
$user_subject = "Training Session Cancelled - Confirmation";

$user_message = "
<html>
<head>
  <title>Session Cancellation Confirmation</title>
</head>
<body>
  <h2>Training Session Cancellation Confirmation</h2>
  <p>You have successfully cancelled your training session.</p>
  <p><strong>Trainer:</strong> $trainer_name</p>
  <p><strong>Date:</strong> " . date('l, F d, Y', strtotime($session_data['session_date'])) . "</p>
  <p><strong>Time:</strong> " . date('g:i A', strtotime($session_data['session_time'])) . "</p>
  <p>If you wish to book another session, please log in to your account.</p>
</body>
</html>
";

// Send email to user
mail($user_email, $user_subject, $user_message, $headers);

// Check if we need to update overall booking status
// If all sessions are cancelled, mark the booking as cancelled
$all_cancelled_query = "SELECT COUNT(*) as total, 
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                        FROM trainer_sessions 
                        WHERE booking_id = $booking_id";
$all_cancelled_result = mysqli_query($conn, $all_cancelled_query);
$all_cancelled_data = mysqli_fetch_assoc($all_cancelled_result);

if ($all_cancelled_data['total'] == $all_cancelled_data['cancelled']) {
    // All sessions are cancelled, update booking status
    $update_booking_query = "UPDATE trainer_bookings 
                            SET status = 'cancelled', 
                                last_updated = NOW()
                            WHERE id = $booking_id";
    mysqli_query($conn, $update_booking_query);
}

// Log the cancellation activity
$log_query = "INSERT INTO activity_log 
             (user_id, activity_type, related_id, details, ip_address, created_at) 
             VALUES 
             (" . $_SESSION['user_id'] . ", 'session_cancel', $session_id, 
             'Cancelled session on " . date('Y-m-d', strtotime($session_data['session_date'])) . 
             " at " . date('H:i:s', strtotime($session_data['session_time'])) . ". Reason: $reason', 
             '" . $_SERVER['REMOTE_ADDR'] . "', NOW())";
mysqli_query($conn, $log_query);

// Set success message and redirect
$_SESSION['success'] = "Your session on " . date('l, F d', strtotime($session_data['session_date'])) . 
                       " at " . date('g:i A', strtotime($session_data['session_time'])) . " has been cancelled.";
header("Location: manage-sessions.php?booking_id=$booking_id");
exit;
?>