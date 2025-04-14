<?php
include '../config.php';
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['id'])) {
    echo json_encode(['res' => 'error', 'info' => 'User not logged in']);
    exit;
}

if (!isset($_POST['plan_id']) || !isset($_POST['duration'])) {
    echo json_encode(['res' => 'error', 'info' => 'No plan or duration selected']);
    exit;
}

$user_id = $_SESSION['id'];
$plan_id = intval($_POST['plan_id']);
$duration = intval($_POST['duration']); // The duration the user selects

// Get plan details
$plan_query = mysqli_query($conn, "SELECT * FROM plans WHERE PlanId = $plan_id");
if (!$plan_query || mysqli_num_rows($plan_query) == 0) {
    echo json_encode(['res' => 'error', 'info' => 'Invalid plan']);
    exit;
}

$plan = mysqli_fetch_assoc($plan_query);
$plan_name = $plan['plan_type'];
$price = $plan['price'];

// Calculate total cost based on duration
$total_cost = $price * $duration;

// Fetch user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
if (!$user_query || mysqli_num_rows($user_query) == 0) {
    echo json_encode(['res' => 'error', 'info' => 'User not found']);
    exit;
}

$user = mysqli_fetch_assoc($user_query);
$name = $user['FirstName'] . ' ' . $user['LastName'];
$email = $user['email'];
$mobile = $user['number'];

// === Razorpay order create logic === //
$amount = $total_cost * 100; // Convert to paise
$receipt = "Renewal for plan ID $plan_id";

// Razorpay API keys
$razorpay_test_key = 'rzp_test_mB727DyAjJMcbZ';
$razorpay_test_secret_key = 'uthNTU4T1NonPx033KN5aJC7';

$authHeader = "Basic " . base64_encode($razorpay_test_key . ":" . $razorpay_test_secret_key);

$orderData = array(
    "amount" => $amount,
    "currency" => "INR",
    "receipt" => $receipt,
    "notes" => array(
        "plan_name" => $plan_name,
        "user_id" => $user_id
    )
);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($orderData),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: ' . $authHeader
    )
));

$response = curl_exec($curl);
if (curl_errno($curl)) {
    echo json_encode(['res' => 'error', 'info' => 'Curl error: ' . curl_error($curl)]);
    exit;
}
curl_close($curl);

$orderRes = json_decode($response, true);

if (isset($orderRes['id'])) {
    // Store info in session for later use
    $_SESSION['renew_plan_id'] = $plan_id;
    $_SESSION['renew_amount'] = $total_cost;
    $_SESSION['renew_duration'] = $duration;
    $_SESSION['renew_rpay_order'] = $orderRes['id'];    

    // Redirect to Razorpay checkout
    header("Location: razorpay_checkout.php?order_id=" . $orderRes['id']);
    exit;
} else {
    echo json_encode(['res' => 'error', 'info' => 'Razorpay failed']);
    exit;
}
?>
