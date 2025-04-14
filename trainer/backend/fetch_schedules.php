<?php
require '../db_config.php';

// Get the current week's start and end dates
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

// Fetch trainer sessions for the current week
$sql = "SELECT session_date, session_time FROM trainer_sessions 
        WHERE session_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startOfWeek, $endOfWeek);
$stmt->execute();
$result = $stmt->get_result();

$schedule = [];
while ($row = $result->fetch_assoc()) {
    $dayOfWeek = date('w', strtotime($row['session_date'])); // 0 (Sunday) - 6 (Saturday)
    $hour = date('H', strtotime($row['session_time'])); // Extract hour

    if (!isset($schedule[$hour])) {
        $schedule[$hour] = array_fill(0, 7, 0); // Initialize array for each hour
    }

    $schedule[$hour][$dayOfWeek] = 1; // Mark as booked
}

// Fetch trainer working hours from the database
$queryWorkingHours = "SELECT MIN(HOUR(working_hours_start)) as min_hour, 
                      MAX(HOUR(working_hours_end)) as max_hour 
                      FROM trainers";
$resultHours = $conn->query($queryWorkingHours);
$workingHours = $resultHours->fetch_assoc();

$minHour = isset($workingHours['min_hour']) ? (int)$workingHours['min_hour'] : 6;
$maxHour = isset($workingHours['max_hour']) ? (int)$workingHours['max_hour'] : 18;

// Debugging Output
header('Content-Type: application/json');
echo json_encode([
    "debug" => [
        "startOfWeek" => $startOfWeek,
        "endOfWeek" => $endOfWeek,
        "schedule" => $schedule,
        "workingHours" => [
            "minHour" => $minHour,
            "maxHour" => $maxHour
        ]
    ]
]);
exit;