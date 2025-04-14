<?php
// delete_equipment.php - New file for handling equipment deletion
include('db_connect.php');

// Check if ID parameter exists
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    // Prepare and execute deletion query with prepared statement
    $sql = "DELETE FROM equipment WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Success - redirect back to equipment page
            header("Location: equipmentpage.php?delete=success");
            exit();
        } else {
            // Error during execution
            header("Location: equipmentpage.php?delete=error&msg=" . urlencode($stmt->error));
            exit();
        }
        
        $stmt->close();
    } else {
        // Error preparing statement
        header("Location: equipmentpage.php?delete=error&msg=" . urlencode($conn->error));
        exit();
    }
} else {
    // Invalid or missing ID
    header("Location: equipmentpage.php?delete=error&msg=invalid_id");
    exit();
}

$conn->close();
?>