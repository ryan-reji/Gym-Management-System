<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Start session

$user_id = $_SESSION['id'] ?? 0; // Assuming user is logged in

// Get trainer details
$trainer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM trainers WHERE trainer_id = $trainer_id";
$result = mysqli_query($conn, $query);
$trainer = mysqli_fetch_assoc($result);

// Get trainer availability
$avail_query = "SELECT * FROM trainer_availability WHERE trainer_id = $trainer_id ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$avail_result = mysqli_query($conn, $avail_query);

// Check user's membership validity
$membership_query = "SELECT end_date FROM plan_bookings WHERE user_id = $user_id";
$membership_result = mysqli_query($conn, $membership_query);
$membership = mysqli_fetch_assoc($membership_result);

$can_book = true;
$remaining_days = 0;
$error_message = "";

if ($membership) {
    $end_date = new DateTime($membership['end_date']);
    $current_date = new DateTime();
    $remaining_days = $current_date->diff($end_date)->days;

    if ($end_date < $current_date) {
        $can_book = false;
        $error_message = "Your membership has expired. Please renew your membership to book a trainer.";
    } elseif ($remaining_days < 30) {
        $can_book = false;
        $error_message = "You cannot book a trainer as your membership has less than 30 days remaining.";
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?php echo $trainer['FirstName'] . ' ' . $trainer['LastName']; ?></h2>
            <p class="lead"><?php echo $trainer['specialization']; ?></p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5>About</h5>
                   
                    
                    <h5>Experience</h5>
                    <p><?php echo $trainer['experience']; ?> years</p>
                    
                    <h5>Rate</h5>
                    <p>$<?php echo $trainer['hourly_rate']; ?> per hour</p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Weekly Availability</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($avail = mysqli_fetch_assoc($avail_result)) { ?>
                                <tr>
                                    <td><?php echo $avail['day_of_week']; ?></td>
                                    <td><?php echo date('g:i A', strtotime($avail['time_from'])) . ' - ' . date('g:i A', strtotime($avail['time_to'])); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Book This Trainer</h5>
                </div>
                <div class="card-body">
                    <?php if (!$can_book): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="booking-calendar.php" method="GET">
                        <input type="hidden" name="trainer_id" value="<?php echo $trainer_id; ?>">
                        <button type="submit" class="btn btn-primary btn-block" <?php echo !$can_book ? 'disabled' : ''; ?>>Select Booking Period</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
