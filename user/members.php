<?php
// Member Info page
$current_page = 'members';
require_once('utils.php');
require_once('config.php'); // Ensure database connection is available

session_start();
if (!isset($_SESSION['id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['id'];

// Fetch user and plan details
$member_data = get_member_data($user_id, $conn);
$plan_data = get_plan_details($user_id, $conn);

// Calculate time left for plan expiration
$today = new DateTime();
$end_date = new DateTime($plan_data['end_date']);
$interval = $today->diff($end_date);
$time_left = $interval->days . " days";

// Define current page for sidebar highlighting

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management - Member Info</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php render_sidebar($current_page); ?> <!-- Now $current_page is defined -->

    <div class="main-content">
        <div class="tab-container">
            <h2>Member Info</h2>
            <div class="member-info">
                <div class="info-group">
                    <label>Member ID:</label>
                    <span><?php echo htmlspecialchars($member_data['id']); ?></span>
                </div>
                <div class="info-group">
                    <label>First Name:</label>
                    <span><?php echo htmlspecialchars($member_data['FirstName']); ?></span>
                </div>
                <div class="info-group">
                    <label>Last Name:</label>
                    <span><?php echo htmlspecialchars($member_data['LastName']); ?></span>
                </div>
                <div class="info-group">
                    <label>Phone Number:</label>
                    <span><?php echo htmlspecialchars($member_data['number']); ?></span>
                </div>
                <div class="info-group">
                    <label>Email:</label>
                    <span><?php echo htmlspecialchars($member_data['email']); ?></span>
                </div>
                <div class="info-group">
                    <label>Date of Birth:</label>
                    <span><?php echo htmlspecialchars($member_data['dob']); ?></span>
                </div>
                <h3>Plan Details</h3>
                <div class="info-group">
                    <label>Type of Plan:</label>
                    <span><?php echo htmlspecialchars($plan_data['plan_duration']); ?></span>
                </div>
                <div class="info-group">
                    <label>Time Left:</label>
                    <span><?php echo htmlspecialchars($time_left); ?></span>
                </div>
                <form action="renew_plan.php" method="POST">
                    <button type="submit">RENEW PLAN</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
