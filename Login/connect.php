<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start session for error handling

$FirstName = $_POST['FirstName'];
$LastName = $_POST['LastName'];
$number = $_POST['number'];
$gender = $_POST['gender'];
$email = $_POST['email'];
$dob = $_POST['dob'];
$username = $_POST['username'];
$password = $_POST['password'];

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Calculate age
$birthDate = new DateTime($dob);
$today = new DateTime();
$age = $birthDate->diff($today)->y;

// Check if user is at least 14 years old
if ($age < 14) {
    $_SESSION['error_message'] = "You must be at least 14 years old to register.";
    header("Location: register.html");
    exit();
}

// Create a connection
$conn = new mysqli('localhost', 'root', '', 'miniproject_db');

// Check the connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Check if username already exists
$check_query = "SELECT username FROM users WHERE username = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    header("Location: register.html?error=Username already exists! Please choose a different one.");
    exit();
}
$stmt->close();

// Insert user data into database
$stmt = $conn->prepare("INSERT INTO users (FirstName, LastName, number, gender, email, dob, username, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $FirstName, $LastName, $number, $gender, $email, $dob, $username, $hashed_password);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Registration successful! You can now log in.";
    header("Location: login.php"); // Redirect to login page
    exit();
} else {
    $_SESSION['error_message'] = "Something went wrong. Please try again!";
    header("Location: register.html");
    exit();
}
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Close the statement and connection
$stmt->close();
$conn->close();
?>
