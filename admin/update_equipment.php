<?php
// update_equipment.php - New file for handling equipment updates
include('db_connect.php');

// Initialize response array
$response = array();

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (
        isset($_POST['id']) && !empty($_POST['id']) &&
        isset($_POST['equipment-name']) && !empty($_POST['equipment-name']) &&
        isset($_POST['product-number']) && !empty($_POST['product-number']) &&
        isset($_POST['purchase-date']) && !empty($_POST['purchase-date']) &&
        isset($_POST['equipment-status']) && !empty($_POST['equipment-status'])
    ) {
        // Get form data
        $id = $_POST['id'];
        $name = $_POST['equipment-name'];
        $productNumber = $_POST['product-number'];
        $purchaseDate = $_POST['purchase-date'];
        $status = $_POST['equipment-status'];
        $description = isset($_POST['equipment-description']) ? $_POST['equipment-description'] : "";
        
        // Prepare and execute SQL query using prepared statement for security
        $sql = "UPDATE equipment SET 
                name = ?, 
                product_number = ?, 
                purchase_date = ?, 
                status = ?, 
                description = ? 
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssssi", $name, $productNumber, $purchaseDate, $status, $description, $id);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Equipment updated successfully.";
            } else {
                $response['success'] = false;
                $response['message'] = "Failed to update equipment: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $response['success'] = false;
            $response['message'] = "Database error: " . $conn->error;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Missing required fields.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>