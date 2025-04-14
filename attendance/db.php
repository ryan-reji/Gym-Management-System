<?php
// db.php - Reusable database connection file

$host = "localhost";
$username = "root";
$password = ""; // Leave blank if no password is set
$dbname = "miniproject_db";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
