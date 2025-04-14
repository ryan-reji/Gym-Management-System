<?php
session_start();
require_once 'db_config.php';

// Check if trainer is logged in
if (!isset($_SESSION['trainer_id'])) {
    header('Location: login.php?redirect=reschedule.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_type = $_POST['request_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $new_shift_start = $_POST['new_shift_start'] ?? null;
    $new_shift_end = $_POST['new_shift_end'] ?? null;

    $query = "INSERT INTO trainer_reschedules (trainer_id, request_type, requested_date_start, requested_date_end, new_shift_start, new_shift_end, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $trainer_id, $request_type, $start_date, $end_date, $new_shift_start, $new_shift_end);
    $stmt->execute();
    $stmt->close();
    
    $success_message = "Your reschedule request has been submitted for approval.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Request</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
<?php 
include 'header.php'; 
require_once 'db_config.php';

if (!isset($_SESSION['trainer_id'])) {
    header('Location: login.php');
    exit;
}

$trainer_id = $_SESSION['trainer_id'];

// Fetch existing requests
$query = "SELECT trainer_reschedule_id, request_type, start_date, end_date, new_start_time, new_end_time, status, created_at 
          FROM trainer_reschedules 
          WHERE trainer_id = ? 
          ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar bg-gray-800 text-white p-4">
        <?php include 'sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="flex-grow p-10 ml-80">
        <h2 class="text-2xl font-bold mb-4">Reschedule Request</h2>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='bg-green-500 text-white p-3 mb-4 rounded'>{$_SESSION['success']}</div>";
            unset($_SESSION['success']); 
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='bg-red-500 text-white p-3 mb-4 rounded'>{$_SESSION['error']}</div>";
            unset($_SESSION['error']); 
        }
        ?>

        <!-- Form -->
        <form action="reschedule_backend/reschedule_submit.php" method="POST" class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mb-10">
            <label class="block mb-2">Request Type:</label>
            <select name="request_type" id="request_type" class="w-full p-2 border rounded mb-4">
                <option value="leave">Leave</option>
                <option value="part-time">Part-Time Shift</option>
                <option value="full-time">Full-Time Shift</option>
            </select>

            <label class="block mb-2">Start Date:</label>
            <input type="date" name="start_date" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2">End Date:</label>
            <input type="date" name="end_date" class="w-full p-2 border rounded mb-4" required>

            <!-- Shift Details (Hidden for Leave) -->
            <div id="shift_details">
                <label class="block mb-2">New Start Time (Optional for Part-Time/Full-Time):</label>
                <input type="time" name="new_start_time" id="new_start_time" class="w-full p-2 border rounded mb-4">

                <label class="block mb-2">New End Time (Optional for Part-Time/Full-Time):</label>
                <input type="time" name="new_end_time" id="new_end_time" class="w-full p-2 border rounded mb-4">
            </div>

            <button type="submit" class="bg-blue-500 text-white p-2 rounded w-full">Submit Request</button>
        </form>

        <!-- Display Submitted Requests -->
        <h2 class="text-2xl font-bold mb-4">Submitted Requests</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <p class="font-bold text-lg">Request ID: <?php echo $row['trainer_reschedule_id']; ?></p>
                    <p><strong>Type:</strong> <?php echo ucfirst($row['request_type']); ?></p>
                    <p><strong>Submitted:</strong> <?php echo date("d M Y", strtotime($row['created_at'])); ?></p>
                    <p><strong>Start Date:</strong> <?php echo date("d M Y", strtotime($row['start_date'])); ?></p>
                    <p><strong>End Date:</strong> <?php echo date("d M Y", strtotime($row['end_date'])); ?></p>

                    <?php if ($row['request_type'] !== 'leave') { ?>
                        <p><strong>New Start Time:</strong> <?php echo $row['new_start_time'] ?: '-'; ?></p>
                        <p><strong>New End Time:</strong> <?php echo $row['new_end_time'] ?: '-'; ?></p>
                    <?php } ?>

                    <p class="mt-2"><strong>Status:</strong> 
                        <span class="px-2 py-1 rounded-lg 
                        <?php 
                            echo ($row['status'] == 'approved') ? 'bg-green-500 text-white' : 
                                 (($row['status'] == 'rejected') ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white'); 
                        ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
document.getElementById('request_type').addEventListener('change', function() {
    const shiftDetails = document.getElementById('shift_details');
    const isLeave = this.value === 'leave';

    // Hide shift details when "Leave" is selected
    shiftDetails.style.display = isLeave ? 'none' : 'block';

    // Disable Start/End Time when "Leave" is selected
    document.getElementById('new_start_time').disabled = isLeave;
    document.getElementById('new_end_time').disabled = isLeave;
});

// Trigger change event on page load to set correct initial state
document.getElementById('request_type').dispatchEvent(new Event('change'));
</script>

</body>
</html>
