<?php
include '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reschedule_id = $_POST['trainer_reschedule_id'];
    $status = $_POST['status'];

    // Fetch details of the reschedule request
    $query = "SELECT trainer_id, new_start_time, new_end_time, start_date, end_date, request_type 
              FROM trainer_reschedules 
              WHERE trainer_reschedule_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $reschedule_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Reschedule request not found."]);
        exit;
    }

    $request = $result->fetch_assoc();
    $trainer_id = $request['trainer_id'];
    $start_date = $request['start_date'];
    $end_date = $request['end_date'];
    $request_type = $request['request_type'];

    // Start Transaction
    $conn->begin_transaction();

    // Update the reschedule request status
    $updateQuery = "UPDATE trainer_reschedules SET status = ? WHERE trainer_reschedule_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $status, $reschedule_id);
    
    if (!$stmt->execute()) {
        $conn->rollback();
        echo json_encode(["error" => "Failed to update reschedule status."]);
        exit;
    }

    // If Approved, update trainer_sessions
    if ($status === 'approved') {
        if ($request_type === 'leave') {
            // Cancel sessions for leave request
            $updateSessionQuery = "UPDATE trainer_sessions 
                                   SET session_status = 'cancelled' 
                                   WHERE trainer_id = ? 
                                   AND session_date BETWEEN ? AND ?";
            $stmt = $conn->prepare($updateSessionQuery);
            $stmt->bind_param("iss", $trainer_id, $start_date, $end_date);

            if (!$stmt->execute()) {
                $conn->rollback();
                echo json_encode(["error" => "Failed to cancel sessions: " . $stmt->error]);
                exit;
            }

            // Commit the transaction if everything is successful
            $conn->commit();
            echo json_encode(["success" => "Sessions cancelled successfully."]);
            exit;
        } else {
            // Reschedule request: Update time
            $updateSessionQuery = "UPDATE trainer_sessions 
                                   SET session_time = ? 
                                   WHERE trainer_id = ? 
                                   AND session_date BETWEEN ? AND ?";
            $stmt = $conn->prepare($updateSessionQuery);
            $stmt->bind_param("siss", $new_start_time, $trainer_id, $start_date, $end_date);

            if (!$stmt->execute()) {
                $conn->rollback();
                echo json_encode(["error" => "Failed to update session times: " . $stmt->error]);
                exit;
            }

            // Commit the transaction
            $conn->commit();
            echo json_encode(["success" => "Session times updated successfully."]);
            exit;
        }
    }

    // If status updated but not approved
    $conn->commit();
    echo json_encode(["success" => true]);
}
?>
