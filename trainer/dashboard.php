<?php
session_start();
include 'db_config.php'; // Ensure this file contains database connection

// Get the current week's start and end date
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

// Fetch trainer sessions for the current week, filtering only 'Scheduled' ones
$query = "SELECT session_date, session_time, session_status FROM trainer_sessions 
          WHERE session_date BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $startOfWeek, $endOfWeek);
$stmt->execute();
$result = $stmt->get_result();

$schedule = [];
$cancelledDays = [];

while ($row = $result->fetch_assoc()) {
    $date = $row['session_date'];
    $time = (int)date('H', strtotime($row['session_time'])); // Extract hour as integer
    $dayOfWeek = (int)date('w', strtotime($date)); // 0 (Sunday) - 6 (Saturday)
    $status = $row['session_status'];

    if ($status === 'cancelled') {
        $cancelledDays[$dayOfWeek] = true; // Mark entire day as cancelled
        continue; // Skip processing this session
    }

    if (!isset($schedule[$time])) {
        $schedule[$time] = array_fill(0, 7, 0); // Initialize array for each hour
    }

    $schedule[$time][$dayOfWeek] = 1; // Mark as booked
}

$stmt->close();
$conn->close();

// Define time slots (can be adjusted as needed)
$timeSlots = range(6, 18); // 6 AM to 6 PM
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .time-slot-blue { background-color: #60a5fa; } /* Cancelled */
        .time-slot-green { background-color: #34d399; } /* Available */
        .time-slot-red { background-color: #f87171; } /* Booked */
        .time-slot { text-align: center; padding: 8px 4px; color: white; font-weight: 500; }
        .time-header { background-color: #dbeafe; padding: 8px 4px; text-align: center; font-weight: 500; }
        .day-header { background-color: #dbeafe; padding: 8px 4px; text-align: center; font-weight: 500; }
        .schedule-container { position: sticky; top: 0; background-color: white; z-index: 10; }
        .schedule-table-container { max-height: 70vh; overflow-y: auto; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'header.php'; ?>
    <div class="flex flex-col md:flex-row">
        <?php include 'sidebar.php'; ?>
        <div class="w-full md:ml-64 p-4">
            <main class="p-2 md:p-6">
                <div class="mb-6">
                    <h2 class="text-3xl font-medium mb-4">Home Page</h2>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="bg-white p-4 rounded shadow-md">
                            <h3 class="text-xl font-medium mb-4 sticky top-0 bg-white z-10 py-2">Weekly Booking Schedule</h3>
                            
                            <div class="schedule-table-container">
                                <table class="w-full border-collapse table-fixed">
                                    <thead class="schedule-container">
                                        <tr>
                                            <th class="border border-gray-300 day-header w-20">Time</th>
                                            <?php
                                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                            foreach ($days as $index => $day) {
                                                $headerClass = isset($cancelledDays[$index]) ? 'bg-blue-400' : '';
                                                echo '<th class="border border-gray-300 day-header ' . $headerClass . '">' . $day . '</th>';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php
                                        foreach ($timeSlots as $hour) {
                                            echo '<tr>';
                                            echo '<td class="border px-4 py-2 text-center font-medium bg-gray-50">' . sprintf("%02d:00", $hour) . '</td>';
                                            
                                            for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
                                                if (isset($cancelledDays[$dayOfWeek])) {
                                                    echo '<td class="border px-4 py-2 text-center bg-blue-400"></td>';
                                                } else {
                                                    $isBooked = isset($schedule[$hour][$dayOfWeek]) && $schedule[$hour][$dayOfWeek] == 1;
                                                    $cellClass = $isBooked ? 'bg-red-400' : 'bg-green-400';
                                                    echo '<td class="border px-4 py-2 text-center ' . $cellClass . '"></td>';
                                                }
                                            }
                                            
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-end space-x-4">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-blue-400 mr-2"></div>
                                    <span class="text-sm">Non-working Hours</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-400 mr-2"></div>
                                    <span class="text-sm">Available</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-red-400 mr-2"></div>
                                    <span class="text-sm">Booked</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
