<?php
include '../config.php';
session_start();

if (!isset($_SESSION['id']) || !isset($_GET['order_id'])) {
    echo "Invalid access.";
    exit;
}

// Get data from session (safer than $_GET)
$user_id = $_SESSION['id'];
$plan_id = $_SESSION['renew_plan_id'];
$amount = $_SESSION['renew_amount']; // In rupees
$rpay_order_id = $_SESSION['renew_rpay_order'];
$order_id = $_GET['order_id'] ?? null;

// Fetch user details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_query);

$name = $user['FirstName'] . ' ' . $user['LastName'];
$email = $user['email'];
$mobile = $user['number'];

$razorpay_key = 'razorpay_test_or_live_key'; // Or move to config/env

// Calculate total cost in case it's missing from session
$total_cost = $_SESSION['renew_amount']; // Already fetched, but ensures consistency
$duration = $_SESSION['renew_duration']; // Also ensure consistency
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<h2 style="text-align: center;">Processing Payment...</h2>

<script>
    // Pass the PHP variables directly into JavaScript
    var total_cost = <?= $total_cost ?>;
    var duration = <?= $duration ?>;
    
    console.log("Total Cost:", total_cost);
    console.log("Duration:", duration);
    
    var options = {
        "key": "<?= $razorpay_key ?>",
        "amount": total_cost * 100,  // Amount should be in paise, so multiplied by 100
        "currency": "INR",
        "name": "Gym Membership Renewal",
        "description": "Renew your gym plan",
        "order_id": "<?= $rpay_order_id ?>",
        "handler": function (response) {
            console.log("Payment successful:", response);
            fetch('submitrenewal.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature,
                    order_id: "<?= $order_id ?>",
                    plan_id: "<?= $plan_id ?>",
                    total_cost: total_cost,
                    duration: duration
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.res === 'success') {
                    alert('Payment successful and plan renewed!');
                    window.location.href = '../members.php';
                } else {
                    alert('Payment succeeded but failed to update plan: ' + data.info);
                }
            });
        },
        "prefill": {
            "name": "<?= $name ?>",
            "email": "<?= $email ?>",
            "contact": "<?= $mobile ?>"
        },
        "theme": {
            "color": "#007bff"
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
</script>

</body>
</html>
