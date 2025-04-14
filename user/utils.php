
<?php
// Utility functions and common data

// Function to get active class for sidebar menu
function is_active($page, $current_page) {
    return ($page == $current_page) ? 'active' : '';
}

// Function to render the sidebar
// Function to render the sidebar
function render_sidebar($current_page) {
    ?>
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-dumbbell"></i> GymShark
        </div>
        
        <div class="welcome">
            <div class="user-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <div>Welcome</div>
                <div>JohnDoe123</div>
            </div>
        </div>
        
        <ul>
            <li class="<?php echo is_active('home', $current_page); ?>">
                <a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a>
            </li>
            <li class="<?php echo is_active('bookings', $current_page); ?>">
                <a href="bookings.php"><i class="far fa-calendar-alt"></i> <span>Bookings</span></a>
            </li>
            <li class="<?php echo is_active('profile', $current_page); ?>">
                <a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a>
            </li>
            <li class="<?php echo is_active('statistics', $current_page); ?>">
                <a href="statistics.php"><i class="fas fa-chart-bar"></i> <span>Statistics</span></a>
            </li>
            <li class="<?php echo is_active('plans', $current_page); ?>">
                <a href="workout_plans.php"><i class="fas fa-list"></i> <span>Workout Plans</span></a>
            </li>
        </ul>
        
        <div class="logout-container">
            <a href="../Login/logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    <?php
}

// Common data sets
function get_member_data($user_id, $conn) {
    $query = "SELECT id, FirstName, LastName, number, email, dob FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fetch plan details from the database
function get_plan_details($user_id, $conn) {
    $query = "SELECT plan_duration, end_date FROM plan_bookings WHERE user_id = ? AND status = 'completed' ORDER BY end_date DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


function get_workouts() {
    return ['Weight Training', 'Cardio Session', 'Yoga Class', 'Pilates', 'Zumba'];
}

// Get attendance data
function get_attendance_data() {
    return [
        '2025-03-01' => 0,
        '2025-03-02' => 1,
        '2025-03-03' => 0,
        '2025-03-04' => 2,
        '2025-03-05' => 3,
        '2025-03-06' => 1,
        '2025-03-07' => 0,
        '2025-03-08' => 2,
        '2025-03-10' => 1,
        '2025-03-12' => 1,
        '2025-03-15' => 1,
        '2025-03-22' => 1
    ];
}

// Get home page data
function get_home_data($user_id) {
    global $conn; // Ensure database connection is available

    // Check if user has booked a trainer session
    $check_booking_query = "SELECT COUNT(*) as count FROM trainer_bookings WHERE user_id = ?";
    $stmt = $conn->prepare($check_booking_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking_row = $result->fetch_assoc();
    $has_booking = $booking_row['count'] > 0; // True if user has booked a session

    // If user has no trainer booking, return a message
    if (!$has_booking) {
        return [
            'last_activity_date' => null,
            'upcoming_class' => null,
            'recent_activities' => [],
            'message' => "You haven't booked a personal trainer yet! <a href='book_trainer.php'>Book Now</a>"
        ];
    }

    // Fetch the last activity date (last completed session)
    $query_last_activity = "
        SELECT MAX(session_date) AS last_activity_date 
        FROM trainer_sessions ts
        JOIN trainer_bookings tb ON ts.booking_id = tb.id
        WHERE tb.user_id = ? AND ts.session_status = 'completed'
    ";

    $stmt = $conn->prepare($query_last_activity);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_activity_row = $result->fetch_assoc();
    $last_activity_date = $last_activity_row['last_activity_date'] ?? null;

    // Fetch upcoming class
    $current_datetime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
    $today = $current_datetime->format('Y-m-d');
    $current_time = $current_datetime->format('H:i:s');

    $query_upcoming = "
        SELECT ts.session_date, ts.session_time
        FROM trainer_sessions ts
        JOIN trainer_bookings tb ON ts.booking_id = tb.id
        WHERE tb.user_id = ? 
        AND ts.session_status = 'scheduled'
        AND (ts.session_date > ? OR (ts.session_date = ? AND ts.session_time > ?))
        ORDER BY ts.session_date ASC, ts.session_time ASC
        LIMIT 1
    ";

    $stmt = $conn->prepare($query_upcoming);
    $stmt->bind_param("isss", $user_id, $today, $today, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();
    $upcoming_class = $result->fetch_assoc();

    return [
        'last_activity_date' => $last_activity_date,
        'upcoming_class' => $upcoming_class,
        'recent_activities' => get_recent_activities($user_id)
    ];
}

function get_recent_activities($user_id) {
    global $conn; // Use the database connection

    $query = "
        SELECT ts.session_date AS date, ts.notes AS activity, ts.session_duration AS duration 
        FROM trainer_sessions ts
        JOIN trainer_bookings tb ON ts.booking_id = tb.id
        WHERE tb.user_id = ?
        ORDER BY ts.session_date DESC, ts.session_time DESC
        LIMIT 5
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'date' => $row['date'],
            'activity' => $row['activity'] ?: 'No Notes Available',
            'duration' => $row['duration'] . ' mins'
        ];
    }

    return $activities;
}

?>