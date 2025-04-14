<?php
session_start(); // Add this at the top of the file
require_once 'db_config.php';


// Check if trainer is logged in
if (!isset($_SESSION['trainer_id'])) {
    header('Location: login.php?redirect=sessions.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Fetch active sessions for the trainer
$query = "SELECT u.id AS user_id, u.FirstName, u.LastName, u.number, 
                 b.booking_start_date, b.booking_end_date, b.default_session_time
          FROM trainer_bookings b
          JOIN users u ON b.user_id = u.id
          WHERE b.trainer_id = ? AND b.booking_status = 'active'
          ORDER BY b.booking_start_date ASC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>
<?php include 'header.php'; ?>

<div class="flex">
    <!-- Sidebar (Fixed Width) -->
    <div class="w-64 bg-gray-200 h-screen p-4">
        <?php include 'sidebar.php'; ?>
    </div>

    <!-- Main Content (Sessions) -->
    <div class="flex-1 p-6">
        <h2 class="text-2xl font-semibold mb-4">My Active Sessions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="bg-white p-4 shadow-md rounded-lg border">
                    <h3 class="text-lg font-bold"><?php echo $row['FirstName'] . ' ' . $row['LastName']; ?></h3>
                    <p class="text-gray-600 text-sm">📞 <?php echo $row['number']; ?></p>
                    <p class="text-gray-800"><strong>Start:</strong> <?php echo date('Y-m-d', strtotime($row['booking_start_date'])); ?></p>
                    <p class="text-gray-800"><strong>End:</strong> <?php echo date('Y-m-d', strtotime($row['booking_end_date'])); ?></p>
                    <p class="text-blue-500 font-semibold mt-2">⏰ <?php echo date('g:i A', strtotime($row['default_session_time'])); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

</body>
</html>


