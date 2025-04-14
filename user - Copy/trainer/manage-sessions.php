<!-- manage-sessions.php -->
<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$user_id = $_SESSION['user_id'];

// Verify booking belongs to this user
$booking_query = "SELECT b.*, t.FirstName, t.LastName, t.id as trainer_id 
                 FROM trainer_bookings b 
                 JOIN trainers t ON b.trainer_id = t.id 
                 WHERE b.id = $booking_id AND b.user_id = $user_id";
$booking_result = mysqli_query($conn, $booking_query);

if (mysqli_num_rows($booking_result) == 0) {
    header('Location: my-bookings.php');
    exit;
}

$booking = mysqli_fetch_assoc($booking_result);

// Get all sessions for this booking
$sessions_query = "SELECT * FROM trainer_sessions 
                  WHERE booking_id = $booking_id 
                  ORDER BY session_date ASC";
$sessions_result = mysqli_query($conn, $sessions_query);

// If no sessions exist yet (first view after payment), create them
if (mysqli_num_rows($sessions_result) == 0 && $booking['payment_status'] == 'paid') {
    // Create sessions for each day in the booking period
    $start_date = new DateTime($booking['booking_start_date']);
    $end_date = new DateTime($booking['booking_end_date']);
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));
    
    foreach ($date_range as $date) {
        $session_date = $date->format('Y-m-d');
        $insert_query = "INSERT INTO trainer_sessions 
                        (booking_id, session_date, session_time, status) 
                        VALUES 
                        ($booking_id, '$session_date', '" . $booking['default_session_time'] . "', 'scheduled')";
        mysqli_query($conn, $insert_query);
    }
    
    // Refresh sessions query
    $sessions_result = mysqli_query($conn, $sessions_query);
}

// Get available time slots for this trainer (for rescheduling)
$slots_query = "SELECT time_from, time_to FROM trainer_availability 
               WHERE trainer_id = " . $booking['trainer_id'] . " 
               GROUP BY time_from, time_to";
$slots_result = mysqli_query($conn, $slots_query);
$time_slots = [];

while ($slot = mysqli_fetch_assoc($slots_result)) {
    $start = strtotime($slot['time_from']);
    $end = strtotime($slot['time_to']);
    
    // Create one-hour slots
    for ($time = $start; $time < $end; $time += 3600) {
        $time_slots[] = date('H:i:00', $time);
    }
}

$time_slots = array_unique($time_slots);
sort($time_slots);
?>

<div class="container mt-5">
    <h2>Manage Training Sessions</h2>
    <h5>Trainer: <?php echo $booking['FirstName'] . ' ' . $booking['LastName']; ?></h5>
    
    <div class="card mb-4">
        <div class="card-body">
            <p>You can reschedule or cancel individual training sessions below. Please note that:</p>
            <ul>
                <li>Rescheduling must be done at least 24 hours in advance</li>
                <li>Cancellations must be done at least 24 hours in advance</li>
                <li>Your default time is <?php echo date('g:i A', strtotime($booking['default_session_time'])); ?></li>
            </ul>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($session = mysqli_fetch_assoc($sessions_result)) { 
                    $session_date = strtotime($session['session_date']);
                    $today = strtotime('today');
                    $is_past = $session_date < $today;
                    $is_today = $session_date == $today;
                    $is_editable = !$is_past && $session['status'] != 'completed' && $session['status'] != 'cancelled';
                ?>
                    <tr class="<?php echo $is_past ? 'text-muted' : ''; ?>">
                        <td><?php echo date('D, M d, Y', strtotime($session['session_date'])); ?></td>
                        <td><?php echo date('g:i A', strtotime($session['session_time'])); ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo ($session['status'] == 'scheduled') ? 'primary' : 
                                     (($session['status'] == 'completed') ? 'success' : 
                                     (($session['status'] == 'rescheduled') ? 'info' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($session['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($is_editable) { ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        data-toggle="modal" data-target="#rescheduleModal" 
                                        data-session-id="<?php echo $session['id']; ?>"
                                        data-session-date="<?php echo $session['session_date']; ?>"
                                        data-session-time="<?php echo $session['session_time']; ?>">
                                    Reschedule
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        data-toggle="modal" data-target="#cancelModal" 
                                        data-session-id="<?php echo $session['id']; ?>"
                                        data-session-date="<?php echo date('D, M d, Y', strtotime($session['session_date'])); ?>">
                                    Cancel
                                </button>
                            <?php } else { ?>
                                <small class="text-muted">No actions available</small>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Session</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="process-reschedule.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="session_id" name="session_id">
                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                    
                    <div class="form-group">
                        <label>Session Date:</label>
                        <p id="reschedule_date" class="font-weight-bold"></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_time">Select New Time:</label>
                        <select id="new_time" name="new_time" class="form-control" required>
                            <?php foreach ($time_slots as $time) { 
                                $display_time = date('g:i A', strtotime($time));
                                $end_time = date('g:i A', strtotime($time) + 3600);
                            ?>
                                <option value="<?php echo $time; ?>"><?php echo $display_time . ' - ' . $end_time; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="reschedule_reason">Reason for Rescheduling:</label>
                        <textarea id="reschedule_reason" name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fa fa-info-circle"></i> Rescheduling will notify your trainer. 
                            Please only reschedule sessions if necessary.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reschedule Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Session</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="process-cancel.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="cancel_session_id" name="session_id">
                    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                    
                    <div class="alert alert-warning">
                        <p>Are you sure you want to cancel your training session on <strong id="cancel_date"></strong>?</p>
                        <p><i class="fa fa-exclamation-triangle"></i> Cancellations cannot be undone.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="cancel_reason">Reason for Cancellation:</label>
                        <textarea id="cancel_reason" name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Keep Session</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for modals -->
<script>
$(document).ready(function() {
    // Populate reschedule modal data
    $('#rescheduleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var sessionId = button.data('session-id');
        var sessionDate = button.data('session-date');
        var sessionTime = button.data('session-time');
        
        var modal = $(this);
        modal.find('#session_id').val(sessionId);
        modal.find('#reschedule_date').text(formatDate(sessionDate) + ' at ' + formatTime(sessionTime));
        
        // Pre-select the current time slot
        modal.find('#new_time').val(sessionTime);
    });
    
    // Populate cancel modal data
    $('#cancelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var sessionId = button.data('session-id');
        var sessionDate = button.data('session-date');
        
        var modal = $(this);
        modal.find('#cancel_session_id').val(sessionId);
        modal.find('#cancel_date').text(sessionDate);
    });
    
    function formatDate(dateString) {
        var date = new Date(dateString);
        var options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    }
    
    function formatTime(timeString) {
        var time = new Date('2000-01-01T' + timeString);
        return time.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>