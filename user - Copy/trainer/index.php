<?php
require_once 'config/db.php';


// Check if user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$user_id = intval($_SESSION['id']);

// Check if user has booked a trainer
$query = "SELECT tb.trainer_id, tb.default_session_time, t.FirstName, t.LastName, t.number
          FROM trainer_bookings tb
          JOIN trainers t ON tb.trainer_id = t.trainer_id
          WHERE tb.user_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$trainer = $result->fetch_assoc();

if (!$trainer) {
    // If no trainer is booked, redirect to trainers list
    header("Location: trainers.php");
    exit;
}

// Fetch remaining sessions from trainer_sessions table
$sessions_left = 0; // Default value
$sessions_query = "SELECT COUNT(*) AS sessions_left 
                   FROM trainer_sessions 
                   WHERE booking_id = (
                       SELECT id FROM trainer_bookings 
                       WHERE user_id = ? 
                       AND booking_status = 'active' 
                       ORDER BY created_at DESC 
                       LIMIT 1
                   ) 
                   AND session_date >= CURDATE() 
                   AND session_status != 'cancelled'";
$sessions_stmt = $conn->prepare($sessions_query);
if ($sessions_stmt) {
    $sessions_stmt->bind_param("i", $user_id);
    $sessions_stmt->execute();
    $sessions_stmt->bind_result($sessions_left);
    $sessions_stmt->fetch();
    $sessions_stmt->close();
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Your Trainer Details</h2>
    <div class="card">
        <div class="card-body">
            <h4><?php echo htmlspecialchars($trainer['FirstName'] . ' ' . $trainer['LastName']); ?></h4>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($trainer['number']); ?></p>
            <p><strong>Training Time:</strong> <?php echo date('g:i A', strtotime($trainer['default_session_time'])); ?></p>
            <p><strong>Sessions Left:</strong> <?php echo $sessions_left; ?></p>
            <!--<a href="trainers.php" class="btn btn-primary">Browse Other Trainers</a>-->
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>