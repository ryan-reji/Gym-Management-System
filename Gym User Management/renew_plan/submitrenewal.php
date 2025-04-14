<?php
include '../config.php';
session_start();
error_log(print_r($_POST, true));  // Log all POST data


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id'])) {
        echo json_encode(['res' => 'error', 'info' => 'User not logged in']);
        exit;
    }

    $user_id = $_SESSION['id'];

    // Get the data from POST
    $plan_id = $_POST['plan_id'] ?? null;
    $payment_id = $_POST['razorpay_payment_id'] ?? null;
    $payment_order_id = $_POST['razorpay_order_id'] ?? null;
    $payment_signature = $_POST['razorpay_signature'] ?? null;
    $total_cost = $_POST['total_cost'] ?? null;
    $duration = $_POST['duration'] ?? null; // Added duration

    if (!$plan_id || !$payment_id || !$payment_order_id || !$payment_signature || !$total_cost || !$duration) {
        echo json_encode(['res' => 'error', 'info' => 'Missing required fields']);
        exit;
    }

    // Fetch plan details
    $plan_query = mysqli_query($conn, "SELECT * FROM plans WHERE PlanId = $plan_id");
    if (!$plan_query || mysqli_num_rows($plan_query) === 0) {
        echo json_encode(['res' => 'error', 'info' => 'Plan not found']);
        exit;
    }

    $plan = mysqli_fetch_assoc($plan_query);
    $plan_name = $plan['plan_type'];

    // Calculate the start and end dates based on the selected duration
    $start_date = date('Y-m-d');  // Today's date as start date

    // Check for any existing active subscription and calculate the start date accordingly
    $latest_booking_query = mysqli_query($conn, "SELECT end_date FROM plan_bookings WHERE user_id = $user_id ORDER BY end_date DESC LIMIT 1");
    if ($latest_booking_query && mysqli_num_rows($latest_booking_query) > 0) {
        $last_booking = mysqli_fetch_assoc($latest_booking_query);
        $last_end_date = $last_booking['end_date'];

        // If the last booking hasn't ended, start the new subscription the next day
        if (strtotime($last_end_date) >= strtotime($start_date)) {
            $start_date = date('Y-m-d', strtotime($last_end_date . ' +1 day'));
        }
    }

    // Calculate the new end date based on the selected duration
    $end_date = date('Y-m-d', strtotime($start_date . " +$duration months"));

    // Insert the new plan booking into the database
    $insert = mysqli_query($conn, "INSERT INTO plan_bookings (user_id, plan_id, start_date, end_date, total_cost, status, razorpay_payment_id)
                                   VALUES ($user_id, $plan_id, '$start_date', '$end_date', $total_cost, 'completed', '$payment_id')");

    if ($insert) {
        echo json_encode(['res' => 'success', 'info' => 'Plan renewed successfully']);
    } else {
        echo json_encode(['res' => 'error', 'info' => 'Failed to insert renewal']);
    }

} else {
    echo json_encode(['res' => 'error', 'info' => 'Invalid request']);
}
?>
