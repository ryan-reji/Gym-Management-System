<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php?redirect=booking-calendar.php?trainer_id=' . $_GET['trainer_id']);
    exit;
}

$user_id = $_SESSION['id']; // Assuming user_id is stored in session
$trainer_id = isset($_GET['trainer_id']) ? intval($_GET['trainer_id']) : 0;

// Get user's membership end date
$plan_query = "SELECT end_date FROM plan_bookings WHERE user_id = ? ORDER BY end_date DESC LIMIT 1";
$stmt = $conn->prepare($plan_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$plan_result = $stmt->get_result();
$membership = $plan_result->fetch_assoc();
$membership_end = $membership ? $membership['end_date'] : null;

// Get trainer details
$query = "SELECT * FROM trainers WHERE trainer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
$trainer = $result->fetch_assoc();

// Fetch available time slots
$avail_query = "SELECT time_from, time_to FROM trainer_availability WHERE trainer_id = ?";
$stmt = $conn->prepare($avail_query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$avail_result = $stmt->get_result();

// Check if availability exists
if ($avail_result->num_rows === 0) {
    die("No availability found for trainer ID: " . $trainer_id);
}

$time_slots = [];
while ($slot = $avail_result->fetch_assoc()) {
    $start = strtotime($slot['time_from']);
    $end = strtotime($slot['time_to']);
    for ($time = $start; $time < $end; $time += 3600) {
        $formatted_time = date('H:i:s', $time); 
        $time_slots[] = $formatted_time; 
    }
}
$time_slots = array_unique($time_slots); // Remove duplicates

// Fetch already booked time slots
$booked_query = "SELECT DISTINCT default_session_time FROM trainer_bookings WHERE trainer_id = ? AND booking_status = 'active'";
$stmt = $conn->prepare($booked_query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$booked_result = $stmt->get_result();

$booked_slots = [];
while ($row = $booked_result->fetch_assoc()) {
    $booked_slots[] = date('H:i:s', strtotime($row['default_session_time'])); 
}

// Compute available slots
$available_slots = array_values(array_diff($time_slots, $booked_slots));
sort($available_slots);

// Debugging Output

?>

<div class="container mt-5">
    <h2>Book a Month with <?php echo $trainer['FirstName'] . ' ' . $trainer['LastName']; ?></h2>

    <form action="process-booking.php" method="POST" class="card" onsubmit="return validateBooking()">
        <div class="card-body">
            <input type="hidden" name="trainer_id" value="<?php echo $trainer_id; ?>">
            <input type="hidden" id="membership_end" value="<?php echo $membership_end; ?>">

            <div class="form-group">
                <label for="start_date">Starting Date</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required 
                       min="<?php echo date('Y-m-d'); ?>" 
                       max="<?php echo date('Y-m-d', strtotime('+3 months')); ?>">
                <small class="form-text text-muted">Select any date within the next 3 months</small>
                <p id="date_warning" class="text-danger" style="display:none;">Your membership will expire before 30 days, please select a 30-day window before membership expires or extend the plan.</p>
            </div>

            <div class="form-group">
                <label for="time_slot">Select Daily Time Slot</label>
                <select id="time_slot" name="time_slot" class="form-control" required>
                    <option value="">Select a time</option>
                    <?php foreach ($available_slots as $time) { 
                        $display_time = date('g:i A', strtotime($time));
                        $end_time = date('g:i A', strtotime($time) + 3600);
                    ?>
                        <option value="<?php echo $time; ?>"><?php echo $display_time . ' - ' . $end_time; ?></option>
                    <?php } ?>
                </select>
                <small class="form-text text-muted">This will be your default time for all 30 days</small>
            </div>

            <div class="price-calculation mt-4 mb-4">
                <h4>Price Calculation</h4>
                <div class="row">
                    <div class="col-md-8">Hourly Rate</div>
                    <div class="col-md-4 text-right">₹<?php echo $trainer['hourly_rate']; ?></div>
                </div>
                <div class="row">
                    <div class="col-md-8">Number of Sessions</div>
                    <div class="col-md-4 text-right">30</div>
                </div>
                <div class="row font-weight-bold">
                    <div class="col-md-8">Total Cost</div>
                    <div class="col-md-4 text-right">₹<?php echo $trainer['hourly_rate'] * 30; ?></div>
                </div>
            </div>

            <button type="submit" id="submit_btn" class="btn btn-primary btn-lg btn-block">Proceed to Payment</button>
        </div>
    </form>
</div>

<script>
document.getElementById('start_date').addEventListener('change', validateBooking);

function validateBooking() {
    let startDate = new Date(document.getElementById('start_date').value);
    let membershipEnd = new Date(document.getElementById('membership_end').value);
    let endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + 30);

    if (membershipEnd && endDate > membershipEnd) {
        document.getElementById('date_warning').style.display = 'block';
        document.getElementById('submit_btn').disabled = true;
        return false;
    } else {
        document.getElementById('date_warning').style.display = 'none';
        document.getElementById('submit_btn').disabled = false;
        return true;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
