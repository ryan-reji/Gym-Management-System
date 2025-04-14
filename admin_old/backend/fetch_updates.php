<?php
// Include database connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Add your password if set
$dbname = "miniproject_db"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch latest 3 plan updates
$sql = "SELECT pb.user_id, u.FirstName, pb.plan_duration, pb.start_date, pb.end_date, pb.status
        FROM plan_bookings pb
        JOIN users u ON pb.user_id = u.id
        WHERE pb.status IN ('confirmed', 'completed', 'cancelled')
        ORDER BY pb.start_date DESC 
        LIMIT 3;";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusText = getStatusMessage($row['status']);
        echo "<div class='update'>
                <div class='profile-photo'>
                    <img src='default-profile.png' alt='Profile' />
                </div>
                <div class='message'>
                    <p><b>" . htmlspecialchars($row['FirstName']) . "</b> $statusText for <b>" . htmlspecialchars($row['plan_duration']) . "</b> plan.</p>
                    <small class='text-muted'>" . timeAgo($row['start_date']) . "</small>
                </div>
              </div>";
    }
} else {
    echo "<p>No recent updates.</p>";
}

// Function to generate status messages
function getStatusMessage($status) {
    switch ($status) {
        case 'confirmed': return "purchased a new membership";
        case 'completed': return "renewed their plan";
        case 'cancelled': return "had their plan cancelled";
        default: return "updated their subscription";
    }
}

// Function to display time in "time ago" format
function timeAgo($datetime) {
    if (empty($datetime)) {
        return "recently";
    }
    
    // Current time
    $now = new DateTime();
    
    // Try to create DateTime from the input
    try {
        $date = new DateTime($datetime);
    } catch (Exception $e) {
        return "recently";
    }
    
    // Get the difference
    $interval = $date->diff($now);
    
    // Check if date is in the future
    if ($interval->invert === 1) {
        // This means the date is in the future
        return date("M j, Y", strtotime($datetime));
    }
    
    if ($interval->y > 0) {
        return $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ago";
    } elseif ($interval->m > 0) {
        return $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " ago";
    } elseif ($interval->d > 0) {
        if ($interval->d >= 7) {
            $weeks = floor($interval->d / 7);
            return $weeks . " week" . ($weeks > 1 ? "s" : "") . " ago";
        } else {
            return $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
        }
    } elseif ($interval->h > 0) {
        return $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
    } elseif ($interval->i > 0) {
        return $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ago";
    } else {
        return "just now";
    }
}

$conn->close();
?>