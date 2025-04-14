<?php
$servername = "localhost";  // Added the correct port number
$username = "root";
$password = ""; 
$dbname = "miniproject_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>