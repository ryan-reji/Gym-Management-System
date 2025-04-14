<?php
session_start();
include "../Login/db_config.php";
include "send_welcome_email.php"; // Include email function

if (!isset($_SESSION['username'])) {
    header("Location: ../login/index.php?error=Please log in first");
    exit();
}

// Get payment details from URL parameters
$order_id = $_GET['oid'] ?? '';
$payment_id = $_GET['rp_payment_id'] ?? '';
$signature = $_GET['rp_signature'] ?? '';

// Get session data
$user_id = $_SESSION['id'];
$plan_id = $_SESSION['temp_plan_id']; // Set in payment.php
$enum_duration = $_SESSION['temp_enum_duration']; // Set in payment.php
$duration_months = $_SESSION['temp_duration_months']; // Set in payment.php
$amount = $_SESSION['temp_amount']; // Set in payment.php

if (!empty($payment_id)) {
    // Calculate dates
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime("+$duration_months months"));

    // Insert into database using prepared statements
    $insert_query = "INSERT INTO plan_bookings (plan_id, user_id, plan_duration, start_date, end_date, status, total_cost, razorpay_payment_id)
                     VALUES (?, ?, ?, ?, ?, 'completed', ?, ?)";
    
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iisssis", $plan_id, $user_id, $enum_duration, $start_date, $end_date, $amount, $payment_id);

    if ($stmt->execute()) {
        // Fetch user details for email
        $user_query = "SELECT id, FirstName, LastName, username, email, dob, blood_type, profile_pic FROM users WHERE id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $result = $user_stmt->get_result();
        $memberData = $result->fetch_assoc();

        if ($memberData) {
            // Add membership details
            $memberData['membership_type'] = $enum_duration;
            $memberData['duration'] = $duration_months;

            // Send welcome email
            sendWelcomeEmail($memberData);
        }

        // Clear temporary session data
        unset($_SESSION['temp_plan_id'], $_SESSION['temp_enum_duration'], $_SESSION['temp_duration_months'], $_SESSION['temp_amount']);
        
        header("Location: success1.php?success=Payment successful");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    header("Location: payment.php?error=Payment failed");
}

$conn->close();
?>
