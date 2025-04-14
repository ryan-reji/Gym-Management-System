<!-- my-bookings.php -->
<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all active bookings
$bookings_query = "SELECT b.*, t.FirstName, t.LastName 
                  FROM trainer_bookings b 
                  JOIN trainers t ON b.trainer_id = t.id 
                  WHERE b.user_id = $user_id AND b.booking_status != 'cancelled' 
                  ORDER BY b.booking_start_date DESC";
$bookings_result = mysqli_query($conn, $bookings_query);
?>

<div class="container mt-5">
    <h2>My Trainer Bookings</h2>
    
    <?php if (mysqli_num_rows($bookings_result) > 0) { ?>
        <?php while ($booking = mysqli_fetch_assoc($bookings_result)) { ?>
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><?php echo $booking['FirstName'] . ' ' . $booking['LastName']; ?></h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <span class="badge badge-<?php echo ($booking['booking_status'] == 'active') ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($booking['booking_status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">Booking Period:</div>
                        <div class="col-md-8">
                            <?php echo date('M d, Y', strtotime($booking['booking_start_date'])); ?> - 
                            <?php echo date('M d, Y', strtotime($booking['booking_end_date'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">Default Time:</div>
                        <div class="col-md-8">
                            <?php echo date('g:i A', strtotime($booking['default_session_time'])); ?> - 
                            <?php echo date('g:i A', strtotime($booking['default_session_time']) + 3600); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">Payment Status:</div>
                        <div class="col-md-8">
                            <span class="badge badge-<?php echo ($booking['payment_status'] == 'paid') ? 'success' : 'warning'; ?>">
                                <?php echo ucfirst($booking['payment_status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <a href="manage-sessions.php?booking_id=<?php echo $booking['id']; ?>" class="btn btn-primary">
                        Manage Sessions
                    </a>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="alert alert-info">
            <p>You don't have any active bookings yet.</p>
            <a href="trainers.php" class="btn btn-primary">Book a Trainer</a>
        </div>
    <?php } ?>
</div>

<?php require_once 'includes/footer.php'; ?>