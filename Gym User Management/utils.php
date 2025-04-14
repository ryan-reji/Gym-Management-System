
<?php
// Utility functions and common data



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
    $query = "
        SELECT pb.plan_duration, pb.end_date, p.plan_type 
        FROM plan_bookings pb
        JOIN plans p ON pb.plan_id = p.PlanId
        WHERE pb.user_id = ? 
        AND pb.status = 'completed' 
        ORDER BY pb.end_date DESC 
        LIMIT 1
    ";

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
    global $conn;

    // Step 1: Fetch last gym session date
    $query_last_gym_session = "
        SELECT MAX(date) AS last_gym_visit 
        FROM gym_attendance 
        WHERE user_id = ?
    ";
    $stmt = $conn->prepare($query_last_gym_session);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $last_gym_row = $result->fetch_assoc();
    $last_gym_visit = $last_gym_row['last_gym_visit'] ?? null;

    // Step 2: Check if user has trainer booking
    $check_booking_query = "SELECT COUNT(*) as count FROM trainer_bookings WHERE user_id = ?";
    $stmt = $conn->prepare($check_booking_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking_row = $result->fetch_assoc();
    $has_booking = $booking_row['count'] > 0;

    // Step 3: Fetch upcoming trainer class (if any)
    $upcoming_class = null;
    if ($has_booking) {
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
    }

    // Step 4: You can add recent_activities logic here if needed
    $recent_activities = []; // assuming this will be filled later

    return [
        'last_activity_date' => $last_gym_visit,
        'upcoming_class' => $upcoming_class,
        'recent_activities' => $recent_activities,
        'message' => $last_gym_visit ? null : "You haven't visited the gym yet!"
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