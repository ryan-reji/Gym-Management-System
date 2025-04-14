<?php
$sname = "localhost"; // Keep localhost without port here
$username = "root";
$password = "";
$db_name = "miniproject_db";
$port = 3306; // Explicitly define the port

$conn = mysqli_connect($sname, $username, $password, $db_name, $port);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>
