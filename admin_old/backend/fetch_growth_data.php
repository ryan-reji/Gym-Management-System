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

// Fetch monthly revenue & new members count
$sql = "SELECT 
            DATE_FORMAT(start_date, '%b') AS month, 
            SUM(total_cost) AS total_revenue, 
            COUNT(user_id) AS new_members
        FROM plan_bookings
        WHERE status IN ('confirmed', 'completed')
        GROUP BY month
        ORDER BY MIN(start_date) ASC
        LIMIT 7"; // Fetch last 7 months

$result = $conn->query($sql);

$data = ["months" => [], "revenue" => [], "members" => []];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data["months"][] = $row["month"];
        $data["revenue"][] = (int) $row["total_revenue"];
        $data["members"][] = (int) $row["new_members"];
    }
} else {
    $data["error"] = "No data available.";
}

// Return JSON response
echo json_encode($data);
?>
