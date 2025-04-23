<?php
// Include database connection
require_once "config.php";

// This script will be run by a cron job or similar scheduler
// to delete messages older than 30 hours

// Calculate the timestamp for 30 hours ago
$thirty_hours_ago = date('Y-m-d H:i:s', strtotime('-30 hours'));

// Prepare delete statement
$sql = "DELETE FROM messages WHERE created_at < ?";

if($stmt = mysqli_prepare($conn, $sql)) {
    // Bind the timestamp parameter
    mysqli_stmt_bind_param($stmt, "s", $thirty_hours_ago);
    
    // Execute the statement
    if(mysqli_stmt_execute($stmt)) {
        $deleted_count = mysqli_stmt_affected_rows($stmt);
        echo "Successfully deleted $deleted_count old messages.";
    } else {
        echo "Error deleting old messages: " . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing query: " . mysqli_error($conn);
}

mysqli_close($conn);
?>