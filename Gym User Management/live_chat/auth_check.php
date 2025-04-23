<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    die("Error: User not logged in."); // You can also redirect to the login page
}

// Safely get session variables with defaults
$user_id = $_SESSION["id"] ?? null;
$username = $_SESSION["username"] ?? 'Guest';
$display_name = $_SESSION["display_name"] ?? $username;
$user_type = $_SESSION["role"] ?? "member"; // Changed from user_type to role
$initials = substr($display_name, 0, 1);

if(strpos($display_name, ' ') !== false) {
    $name_parts = explode(' ', $display_name);
    $initials = substr($name_parts[0], 0, 1) . substr($name_parts[count($name_parts)-1], 0, 1);
}
$initials = strtoupper($initials);
?>