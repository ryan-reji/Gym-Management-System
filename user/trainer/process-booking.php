<?php
require_once 'config/db.php';

// ✅ Prevent session_start() error
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['id']; 
    $trainer_id = intval($_POST['trainer_id']);
    $start_date = $_POST['start_date'];
    $time_slot = $_POST['time_slot'];

    $end_date = date('Y-m-d', strtotime($start_date . ' +29 days'));

    // ✅ Get trainer hourly rate safely
    $query = "SELECT hourly_rate FROM trainers WHERE trainer_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("i", $trainer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $trainer = $result->fetch_assoc();
    $stmt->close();

    if (!$trainer) {
        $_SESSION['error'] = "Trainer not found!";
        header('Location: booking-calendar.php?trainer_id=' . $trainer_id);
        exit;
    }

    $total_cost = $trainer['hourly_rate'] * 30;

    // ✅ Create booking record safely
    $booking_query = "INSERT INTO trainer_bookings 
                      (user_id, trainer_id, booking_start_date, booking_end_date, 
                      default_session_time, total_cost, payment_status, booking_status) 
                      VALUES (?, ?, ?, ?, ?, ?, 'pending', 'active')";

    $stmt = $conn->prepare($booking_query);
    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param("iisssd", $user_id, $trainer_id, $start_date, $end_date, $time_slot, $total_cost);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        $_SESSION['booking_id'] = $booking_id;

        // ✅ Insert 30 daily session records
        for ($i = 0; $i < 30; $i++) {
            $session_date = date('Y-m-d', strtotime($start_date . " +$i days"));
            $day_of_week = date('l', strtotime($session_date)); // Get day name

            // ✅ Check trainer availability safely
            $availability_query = "SELECT * FROM trainer_availability 
                                   WHERE trainer_id = ? 
                                   AND day_of_week = ?";
            $stmt_avail = $conn->prepare($availability_query);
            if (!$stmt_avail) {
                die("Query preparation failed: " . $conn->error);
            }
            $stmt_avail->bind_param("is", $trainer_id, $day_of_week);
            $stmt_avail->execute();
            $avail_result = $stmt_avail->get_result();

            if ($avail_result->num_rows > 0) {
                // Trainer is available, insert session
                $session_query = "INSERT INTO trainer_sessions 
                                  (booking_id, trainer_id, session_date, session_time, session_status) 
                                  VALUES (?, ?, ?, ?, 'scheduled')";
                $stmt_session = $conn->prepare($session_query);
                if (!$stmt_session) {
                    die("Query preparation failed: " . $conn->error);
                }
                $stmt_session->bind_param("iiss", $booking_id, $trainer_id, $session_date, $time_slot);
                $stmt_session->execute();
                $stmt_session->close();
            }

            $stmt_avail->close();
        }

        $stmt->close();

        // ✅ Redirect to payment
        header('Location: payment.php?booking_id=' . $booking_id);
        exit;
    } else {
        $_SESSION['error'] = "Booking failed: " . $stmt->error;
        header('Location: booking-calendar.php?trainer_id=' . $trainer_id);
        exit;
    }
}
?>
