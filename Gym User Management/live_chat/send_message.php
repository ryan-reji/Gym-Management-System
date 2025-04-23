<?php
// Include authentication check and configuration
require_once "auth_check.php";
require_once "config.php";

// Make sure this is a POST request
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

// Validate the message
if (!isset($_POST["message"]) || empty(trim($_POST["message"]))) {
    echo json_encode(["success" => false, "error" => "Message cannot be empty"]);
    exit;
}

// Sanitize the message
$message = trim($_POST["message"]);
$user_id = $_SESSION["id"];

try {
    // Create database connection
    $conn = mysqli_connect($sname, $username, $password, $db_name, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to save message: " . $stmt->error);
    }
    
    // Get the message ID
    $message_id = $conn->insert_id;
    
    // Get user information for the response
    $user_query = "SELECT username, COALESCE(display_name, username) as display_name
                  FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_row = $user_result->fetch_assoc()) {
        // Create initials
        $display_name = $user_row['display_name'];
        $initials = substr($display_name, 0, 1);
        if(strpos($display_name, ' ') !== false) {
            $name_parts = explode(' ', $display_name);
            $initials = substr($name_parts[0], 0, 1) . substr($name_parts[count($name_parts)-1], 0, 1);
        }
        
        // Format the response
        $formatted_time = date('g:i A');
        $timestamp = time();
        
        $response = [
            "success" => true,
            "message" => [
                "id" => $message_id,
                "message" => $message,
                "timestamp" => $timestamp,
                "formatted_time" => $formatted_time,
                "user_id" => $user_id,
                "username" => $user_row['username'],
                "display_name" => $display_name,
                "initials" => strtoupper($initials),
                "user_type" => 'member',
                "is_current_user" => true
            ]
        ];
        
        echo json_encode($response);
    } else {
        throw new Exception("Failed to retrieve user information");
    }
    
    $user_stmt->close();
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>