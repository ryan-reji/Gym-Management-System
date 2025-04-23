<?php
// Database connection parameters
$servername = "localhost";
$username = "root";  // Change to your MySQL username
$password = "";      // Change to your MySQL password

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS gymshark_chat";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select the database
mysqli_select_db($conn, "gymshark_chat");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    user_type ENUM('member', 'trainer') NOT NULL DEFAULT 'member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

// Create messages table
$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if (mysqli_query($conn, $sql)) {
    echo "Messages table created successfully<br>";
} else {
    echo "Error creating messages table: " . mysqli_error($conn) . "<br>";
}

// Create index on created_at for better performance with cleanup queries
$sql = "CREATE INDEX idx_created_at ON messages(created_at)";
if (mysqli_query($conn, $sql)) {
    echo "Index on created_at created successfully<br>";
} else {
    echo "Note: Index might already exist: " . mysqli_error($conn) . "<br>";
}

// Insert demo users if they don't exist
$password_hash = password_hash("demo123", PASSWORD_DEFAULT);

// Insert trainer
$sql = "INSERT IGNORE INTO users (username, password, display_name, user_type) 
        VALUES ('trainer', '$password_hash', 'Mike Thompson', 'trainer')";
if (mysqli_query($conn, $sql)) {
    echo "Demo trainer created successfully<br>";
} else {
    echo "Note: Demo trainer might already exist: " . mysqli_error($conn) . "<br>";
}

// Insert member
$sql = "INSERT IGNORE INTO users (username, password, display_name, user_type) 
        VALUES ('member', '$password_hash', 'Sarah Kim', 'member')";
if (mysqli_query($conn, $sql)) {
    echo "Demo member created successfully<br>";
} else {
    echo "Note: Demo member might already exist: " . mysqli_error($conn) . "<br>";
}

echo "Database setup completed!";

mysqli_close($conn);
?>