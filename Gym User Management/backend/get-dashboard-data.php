<?php
header("Content-Type: application/json");
require "../config.php"; // Adjust based on your structure

$user_id = $_SESSION['id']; // Assuming user is logged in

// Get Last Gym Session
$lastSessionQuery = "
    SELECT date, TIMESTAMPDIFF(DAY, date, CURDATE()) AS daysAgo, 'Strength Training - 45 mins' AS type
    FROM gym_attendance
    WHERE user_id = ?
    ORDER BY date DESC
    LIMIT 1
";
$stmt = $conn->prepare($lastSessionQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lastSession = $stmt->get_result()->fetch_assoc();

// Get Upcoming Class
$upcomingClassQuery = "
    SELECT ts.date, TIME_FORMAT(ts.start_time, '%h:%i %p') AS time, 
           t.name AS instructor, 'Studio B' AS location, 'HIIT Training' AS className
    FROM trainer_bookings tb
    JOIN trainer_sessions ts ON tb.booking_id = ts.booking_id
    JOIN trainers t ON tb.trainer_id = t.trainer_id
    WHERE tb.user_id = ? AND ts.date >= CURDATE()
    ORDER BY ts.date ASC
    LIMIT 1
";
$stmt = $conn->prepare($upcomingClassQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcomingClass = $stmt->get_result()->fetch_assoc();

// Format response
$response = [
    "lastSession" => $lastSession ? [
        "date" => date("M j, Y", strtotime($lastSession["date"])),
        "daysAgo" => $lastSession["daysAgo"] . " days ago",
        "type" => $lastSession["type"]
    ] : null,
    "upcomingClass" => $upcomingClass ? [
        "day" => date("D", strtotime($upcomingClass["date"])),
        "date" => date("M j", strtotime($upcomingClass["date"])),
        "time" => $upcomingClass["time"],
        "instructor" => $upcomingClass["instructor"],
        "location" => $upcomingClass["location"],
        "className" => $upcomingClass["className"]
    ] : null
];

echo json_encode($response);
?>
