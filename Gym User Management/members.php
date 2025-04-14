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
    <link rel="stylesheet" href="styles_members.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<body>
 
<!-- Sidebar Navigation -->
<nav class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-dumbbell logo-icon"></i>
        <h2>GymShark</h2>
        <div class="close-sidebar">
            <i class="fas fa-times"></i>
        </div>
    </div>
    
    <div class="user-profile">
        <div class="avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-info">
            <h3>Welcome</h3>
            <!--<p>JohnDoe123</p>-->
        </div>
    </div>

    <ul class="nav-links">
        <li>
            <a href="index.php">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </li>
        <li>
            <a href="../user/trainer/">
                <i class="fas fa-calendar-alt"></i>
                <span>Bookings</span>
            </a>
        </li>
        <li class="active">
            <a href="members.html">
                <i class="fas fa-user-circle"></i>
                <span>Profile</span>
            </a>
        </li>
        
        <li>
            <a href="#">
                <i class="fas fa-tasks"></i>
                <span>Workout Plans</span>
            </a>
        </li>
        <li>
                    <a href="bmi.html">
                        <i class="fas fa-weight"></i>
                        <span>BMI Scale</span>
                    </a>
         </li>
    </ul>

    <div class="sidebar-footer">
        <a href="../Login/logout.php" class="logout-button">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</nav>
    <div class="main-content">
        <div class="member-card">
            <div class="background-shape"></div>
            <div class="member-header">
                <div class="member-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h2 class="member-name"><?php echo htmlspecialchars($member_data['FirstName']) . ' ' . htmlspecialchars($member_data['LastName']); ?></h2>
                    <div class="member-id">Member ID: <?php echo htmlspecialchars($member_data['id']); ?></div>
                </div>
            </div>
            
            <div class="info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-phone"></i> Phone Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($member_data['number']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($member_data['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label"><i class="fas fa-birthday-cake"></i> Date of Birth</div>
                        <div class="info-value"><?php echo htmlspecialchars($member_data['dob']); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="plan-card">
                <div class="plan-header">
                    <h3 class="plan-title"><i class="fas fa-dumbbell"></i> Membership Plan</h3>
                    <div class="time-left">
                        <i class="fas fa-clock"></i>
                        <span><?php echo htmlspecialchars($time_left); ?></span>
                    </div>
                </div>
                <div class="info-item" style="margin-bottom: 20px;">
                    <div class="info-label">Plan Type</div>
                    <div class="info-value"><?php echo htmlspecialchars($plan_data['plan_duration']); ?> <?php echo htmlspecialchars($plan_data['plan_type']); ?></div>
                </div>
                <form action="renew_plan/renew_plan.php" method="POST">
                    <button type="submit" class="renew-button">
                        <i class="fas fa-sync-alt"></i> RENEW PLAN
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>