<?php
// verify_member.php
include('db_connect.php');

// Function to check in a member
function checkInMember($username) {
    global $conn;

    // Auto check-out before new check-in
    autoCheckOutMembers();

    // Get user_id from username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows == 0) {
        return ['success' => false, 'message' => 'User not found'];
    }

    $user_id = $user_result->fetch_assoc()['id'];

    // Check if member is already checked in
    $check_existing = $conn->prepare("SELECT id FROM gym_attendance WHERE user_id = ? AND check_out_time IS NULL LIMIT 1");
    $check_existing->bind_param("i", $user_id);
    $check_existing->execute();
    $result = $check_existing->get_result();

    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Member already checked in'];
    }

    // Check daily scan limit (3 per day)
    $daily_scan_stmt = $conn->prepare("SELECT COUNT(*) as scan_count FROM gym_attendance WHERE user_id = ? AND DATE(check_in_time) = CURDATE()");
    $daily_scan_stmt->bind_param("i", $user_id);
    $daily_scan_stmt->execute();
    $daily_scan_result = $daily_scan_stmt->get_result();
    $scan_count = $daily_scan_result->fetch_assoc()['scan_count'];

    if ($scan_count >= 3) {
        return ['success' => false, 'message' => 'Daily check-in limit reached (3 times per day)'];
    }

    // Insert new check-in record
    $stmt = $conn->prepare("INSERT INTO gym_attendance (user_id, check_in_time) VALUES (?, NOW())");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Member checked in successfully'];
    } else {
        return ['success' => false, 'message' => 'Check-in failed'];
    }
}

// Function to check out a member
function checkOutMember($username) {
    global $conn;

    // Get user_id from username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows == 0) {
        return ['success' => false, 'message' => 'User not found'];
    }

    $user_id = $user_result->fetch_assoc()['id'];

    // Check if member is checked in
    $check_existing = $conn->prepare("SELECT id FROM gym_attendance WHERE user_id = ? AND check_out_time IS NULL LIMIT 1");
    $check_existing->bind_param("i", $user_id);
    $check_existing->execute();
    $result = $check_existing->get_result();

    if ($result->num_rows == 0) {
        return ['success' => false, 'message' => 'Member is not currently checked in'];
    }

    // Update check-out time
    $stmt = $conn->prepare("UPDATE gym_attendance SET check_out_time = NOW() WHERE user_id = ? AND check_out_time IS NULL");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Member checked out successfully'];
    } else {
        return ['success' => false, 'message' => 'Check-out failed'];
    }
}

// Function to get current members count
function getCurrentMembersCount() {
    global $conn;

    $query = "SELECT COUNT(*) as current_members FROM gym_attendance WHERE check_out_time IS NULL AND check_in_time > DATE_SUB(NOW(), INTERVAL 5 HOUR)";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['current_members'];
    }

    return 0;
}

// Function to automatically check out members after 5 hours
function autoCheckOutMembers() {
    global $conn;

    $stmt = $conn->prepare("UPDATE gym_attendance SET check_out_time = NOW() WHERE check_out_time IS NULL AND check_in_time < DATE_SUB(NOW(), INTERVAL 5 HOUR)");
    $stmt->execute();
}

// Always run auto check-out when the script is accessed
autoCheckOutMembers();

// Handle different actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $username = $_GET['username'] ?? null;

    // Log received username
    file_put_contents('log.txt', "Received Username in verify_member: " . $username . "\n", FILE_APPEND);

    if ($action === 'check_in' && $username) {
        $result = checkInMember($username);
        echo json_encode($result);
        exit();
    } elseif ($action === 'check_out' && $username) {
        $result = checkOutMember($username);
        echo json_encode($result);
        exit();
    } elseif ($action === 'get_current_members') {
        $current_members = getCurrentMembersCount();
        echo json_encode(['current_members' => $current_members]);
        exit();
    }
}

// Default response for invalid requests
echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
?>
