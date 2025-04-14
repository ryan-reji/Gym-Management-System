<?php
require_once 'config/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['payment_id']) && isset($_GET['booking_id'])) {
    $payment_id = $_GET['payment_id'];
    $booking_id = intval($_GET['booking_id']);

    // Mark payment as complete
    $query = "UPDATE trainer_bookings SET payment_status = 'completed', razorpay_payment_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $payment_id, $booking_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Payment successful!";
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['error'] = "Payment failed. Please try again.";
        header('Location: payment.php?booking_id=' . $booking_id);
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header('Location: ../index.php');
    exit;
}
?>
