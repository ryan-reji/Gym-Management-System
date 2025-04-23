<?php
// Include authentication check and configuration
require_once "auth_check.php";
require_once "config.php";

try {
    // Create database connection
    $conn = mysqli_connect($sname, $username, $password, $db_name, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Update user's last active time
    $update_sql = "UPDATE users SET last_active = NOW() WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    
    // Get online users count
    $online_sql = "SELECT COUNT(*) as online_count FROM users WHERE last_active > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
    $online_result = $conn->query($online_sql);
    $online_count = $online_result->fetch_assoc()['online_count'];
    
    echo json_encode(["success" => true, "online_count" => $online_count]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>