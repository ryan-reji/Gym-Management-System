<?php
require 'db_connect.php'; // Ensure this connects to your database


header('Content-Type: application/json');

// Fetch total active members
$getMembersQuery = "SELECT COUNT(DISTINCT user_id) AS active_members 
                    FROM plan_bookings 
                    WHERE end_date >= CURDATE()";
$result = $conn->query($getMembersQuery);
$todaysMembersCount = ($result) ? $result->fetch_assoc()['active_members'] : 0;

// Fetch currently checked-in users
$getUsersInGymQuery = "SELECT COUNT(DISTINCT user_id) AS in_gym 
                       FROM gym_attendance 
                       WHERE check_out_time IS NULL 
                       AND DATE(date) = CURDATE()";
$result = $conn->query($getUsersInGymQuery);
$usersInGym = ($result) ? $result->fetch_assoc()['in_gym'] : 0;

// Calculate gym occupancy percentage
$maxCapacity = 100;
$membersGymPercentage = min(round(($usersInGym / $maxCapacity) * 100, 2), 100);

// Return JSON response
echo json_encode([
    'active_members' => (int) $todaysMembersCount,
    'checked_in_members' => (int) $usersInGym,
    'occupancy_percentage' => ($membersGymPercentage >= 100) ? "Maxed Out" : $membersGymPercentage
]);
exit;
