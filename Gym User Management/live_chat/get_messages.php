<?php
// Include authentication check and configuration
require_once "auth_check.php";
require_once "config.php";

// Get the last timestamp parameter
$last_timestamp = isset($_GET['last_timestamp']) ? intval($_GET['last_timestamp']) : 0;

try {
    // Create database connection
    $conn = mysqli_connect($sname, $username, $password, $db_name, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Convert timestamp to MySQL datetime format for comparison
    $last_date = date('Y-m-d H:i:s', $last_timestamp);

    $sql = "SELECT 
    m.id, 
    m.message, 
    m.created_at, 
    m.user_id, 
    u.username, 
    COALESCE(u.display_name, u.username) AS display_name
FROM messages m
JOIN users u ON m.user_id = u.id
ORDER BY m.created_at DESC
LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $last_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    
    while ($row = $result->fetch_assoc()) {
        // Create initials
        $display_name = $row['display_name'];
        $initials = substr($display_name, 0, 1);
        if (strpos($display_name, ' ') !== false) {
            $name_parts = explode(' ', $display_name);
            $initials = substr($name_parts[0], 0, 1) . substr($name_parts[count($name_parts) - 1], 0, 1);
        }

        $message_timestamp = strtotime($row['created_at']);
        $formatted_time = date('g:i A', $message_timestamp);

        $messages[] = [
            'id' => $row['id'],
            'message' => $row['message'],
            'timestamp' => $message_timestamp,
            'formatted_time' => $formatted_time,
            'user_id' => $row['user_id'],
            'username' => $row['username'],
            'display_name' => $display_name,
            'initials' => strtoupper($initials),
            'user_type' => 'member', // ensure it's always 'trainer' or 'member'
            'is_current_user' => ($_SESSION['id'] == $row['user_id'])
        ];
    }

    echo json_encode(["success" => true, "messages" => $messages]);

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
