<?php
// get_equipment.php - Create this new file to fetch equipment data
include('db_connect.php');

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM equipment WHERE id = ?";
    
    if($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()) {
            $result = $stmt->get_result();
            
            if($result->num_rows == 1) {
                $equipment = $result->fetch_assoc();
                // Return as JSON
                header('Content-Type: application/json');
                echo json_encode($equipment);
            } else {
                // No equipment found
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Equipment not found']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Database error']);
        }
        
        $stmt->close();
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>