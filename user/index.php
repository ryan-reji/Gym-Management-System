<?php
session_start(); // Start the session

require_once 'config.php'; // Ensure database connection
require_once 'utils.php'; // Include utility functions

$current_page = 'home';

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    die("Error: User not logged in."); // You can also redirect to the login page
}

$user_id = $_SESSION['id']; // Get the logged-in user's ID

$current_page = 'home';

// Get home data
$user_id = $_SESSION['id']; // Ensure you retrieve the logged-in user's ID
$home_data = get_home_data($user_id);
$last_activity_date = $home_data['last_activity_date'];
$upcoming_class = $home_data['upcoming_class'];

$recent_activities = $home_data['recent_activities'];
$attendance_data = get_attendance_data();

// Calculate days since last activity
$today = new DateTime('2025-03-22');
$last_activity = new DateTime($last_activity_date);
$days_left = $today->diff($last_activity)->days;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php render_sidebar($current_page); ?>

    <div class="main-content">
        <div class="tab-container">
            <h2>Home</h2>
            <div class="info-boxes">
                <div class="box">
                    <h3>Last Gym session</h3>
                    <p><?php echo $days_left; ?> days</p>
                </div>
                <div class="box">
                    <h3>Upcoming Class</h3>
                    <p>
    <?php 
    if ($upcoming_class && isset($upcoming_class['session_date'], $upcoming_class['session_time'])) {
        echo "Next session on " . $upcoming_class['session_date'] . " at " . $upcoming_class['session_time'];
    } else {
        echo "No upcoming sessions.";
    }
    ?>
</p>
                </div>
            </div>
            <h3>Recent Activity</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Activity</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_activities as $activity): ?>
                        <tr>
                            <td><?php echo $activity['date']; ?></td>
                            <td><?php echo $activity['activity']; ?></td>
                            <td><?php echo $activity['duration']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Attendance Heat Map (March 2025)</h3>
            <div class="heatmap">
                <?php for ($day = 1; $day <= 22; $day++): 
                    $date = "2025-03-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $attendance = isset($attendance_data[$date]) ? $attendance_data[$date] : 0; ?>
                    <div class="heatmap-day attendance-<?php echo $attendance; ?>" title="<?php echo "$date: $attendance"; ?>">
                        <?php echo $day; ?>
                    </div>
                <?php endfor; ?>
            </div>
            <a href="bookings.php" class="book-button">Book a Session</a>
        </div>
    </div>
</body>
</html>