<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



require_once 'config/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Get booking details
$query = "SELECT b.*, t.FirstName, t.LastName 
          FROM trainer_bookings b 
          JOIN trainers t ON b.trainer_id = t.trainer_id 
          WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: dashboard.php');
    exit;
}

$booking = $result->fetch_assoc();
$total_cost = $booking['total_cost']; // Total cost from DB

// Razorpay API Key (Replace with your actual key)
$razorpay_api_key = 'rzp_test_mB727DyAjJMcbZ';
?>

<div class="container mt-5">
    <h2>Complete Your Payment</h2>

    <div class="card">
        <div class="card-header">
            <h5>Booking Summary</h5>
        </div>
        <div class="card-body">
            <p><strong>Trainer:</strong> <?php echo $booking['FirstName'] . ' ' . $booking['LastName']; ?></p>
            <p><strong>Period:</strong> <?php echo date('M d, Y', strtotime($booking['booking_start_date'])); ?> - 
            <?php echo date('M d, Y', strtotime($booking['booking_end_date'])); ?></p>
            <p><strong>Time Slot:</strong> <?php echo date('g:i A', strtotime($booking['default_session_time'])); ?></p>
            <p><strong>Total Cost:</strong> ₹<?php echo $total_cost; ?></p>
        </div>
    </div>

    <button id="pay-btn" class="btn btn-primary btn-lg mt-4">Pay with Razorpay</button>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('pay-btn').onclick = function () {
        var options = {
            "key": "<?php echo $razorpay_api_key; ?>",
            "amount": "<?php echo $total_cost * 100; ?>", // Amount in paise
            "currency": "INR",
            "name": "Your Gym",
            "description": "Trainer Booking Payment",
            "image": "your-logo-url",
            "order_id": "", // This will be generated dynamically
            "handler": function (response) {
                window.location.href = "process-payment.php?payment_id=" + response.razorpay_payment_id + "&booking_id=<?php echo $booking_id; ?>";
            },
            "prefill": {
                "name": "<?php echo $_SESSION['FirstName' ]; ?>",
                "email": "user@example.com",
                "contact": "9876543210"
            },
            "theme": {
                "color": "#528FF0"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    };
</script>

<?php require_once 'includes/footer.php'; ?>
