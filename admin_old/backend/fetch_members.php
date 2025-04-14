<?php
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Add your password if set
$dbname = "miniproject_db"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


header('Content-Type: application/json');

$sql = "SELECT u.FirstName, u.number, pb.start_date 
        FROM plan_bookings pb
        JOIN users u ON pb.user_id = u.id
        WHERE pb.status IN ('confirmed', 'completed') 
        ORDER BY pb.start_date DESC";

$result = $conn->query($sql);

$members = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $members[] = [
            'name' => $row['FirstName'],
            'joined' => $row['start_date'],
            'phone' => $row['number']
        ];
    }
} else {
    echo json_encode(["error" => $conn->error]); // Debugging output
    exit;
}

// Check if there are any members
if (empty($members)) {
    echo json_encode(["message" => "No members found"]); // Debugging output
    exit;
}

echo json_encode($members);
?>

