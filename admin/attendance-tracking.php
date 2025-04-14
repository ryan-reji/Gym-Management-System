<?php
// attendance-tracking.php
include('db_connect.php');

// Enhanced logging function
function debug_log($message, $data = null) {
    $log = date('[Y-m-d H:i:s] ') . $message;
    if ($data !== null) {
        $log .= " - Data: " . json_encode($data);
    }
    $log .= "\n";
    file_put_contents('debug_attendance.log', $log, FILE_APPEND);
}

// Output JSON function to ensure proper headers
function output_json($data) {
    debug_log("Outputting JSON response", $data);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

debug_log("Script started");
debug_log("REQUEST", $_REQUEST);

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Change to 0 to prevent errors showing in output

// Logging function
function logAttendanceAction($type, $id, $action, $message = '') {
    $logEntry = date('[Y-m-d H:i:s] ') . 
                "Attendance - Type: $type, ID: $id, Action: $action, " . 
                ($message ? "Message: $message" : '') . "\n";
    
    file_put_contents('attendance_actions.log', $logEntry, FILE_APPEND);
}

// Function to check in a member
function checkInMember($reg_no) {
    global $conn;
    debug_log("Attempting to check in member", $reg_no);
    
    try {
        // Check if member is already checked in
        $check_existing = $conn->prepare("SELECT * FROM gym_attendance WHERE reg_no = ? AND check_out_time IS NULL");
        $check_existing->bind_param("s", $reg_no);
        $check_existing->execute();
        $result = $check_existing->get_result();
        
        if ($result->num_rows > 0) {
            debug_log("Member already checked in", $reg_no);
            return [
                'success' => false, 
                'already_checked_in' => true,
                'message' => 'Member already checked in. Do you want to check out?'
            ];
        }
        
        // Check daily scan limit (3 per day)
        $daily_scan_sql = "SELECT COUNT(*) as scan_count FROM gym_attendance 
                            WHERE reg_no = ? AND DATE(check_in_time) = CURDATE()";
        $daily_scan_stmt = $conn->prepare($daily_scan_sql);
        $daily_scan_stmt->bind_param("s", $reg_no);
        $daily_scan_stmt->execute();
        $daily_scan_result = $daily_scan_stmt->get_result();
        $scan_count = $daily_scan_result->fetch_assoc()['scan_count'];
        
        if ($scan_count >= 3) {
            debug_log("Member daily limit reached", $reg_no);
            return [
                'success' => false, 
                'daily_limit_reached' => true,
                'message' => 'Daily check-in limit reached (maximum 3 times per day). Please try again tomorrow.'
            ];
        }
        
        // Insert new check-in record
        $stmt = $conn->prepare("INSERT INTO gym_attendance (reg_no, check_in_time) VALUES (?, NOW())");
        $stmt->bind_param("s", $reg_no);
        
        if ($stmt->execute()) {
            // Get member name for the response
            $name_query = $conn->prepare("SELECT name FROM members WHERE reg_no = ?");
            $name_query->bind_param("s", $reg_no);
            $name_query->execute();
            $name_result = $name_query->get_result();
            $member_name = '';
            
            if ($name_result->num_rows > 0) {
                $member_name = $name_result->fetch_assoc()['name'];
            }
            
            debug_log("Member checked in successfully", $member_name);
            return [
                'success' => true, 
                'message' => 'Member checked in successfully',
                'name' => $member_name
            ];
        } else {
            debug_log("Member check-in failed", $conn->error);
            return [
                'success' => false, 
                'message' => 'Check-in failed: ' . $conn->error
            ];
        }
    } catch (Exception $e) {
        debug_log("Exception in checkInMember", $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Function to check out a member
function checkOutMember($reg_no) {
    global $conn;
    debug_log("Attempting to check out member", $reg_no);
    
    try {
        // Check if member is checked in
        $check_existing = $conn->prepare("SELECT * FROM gym_attendance WHERE reg_no = ? AND check_out_time IS NULL");
        $check_existing->bind_param("s", $reg_no);
        $check_existing->execute();
        $result = $check_existing->get_result();
        
        if ($result->num_rows == 0) {
            debug_log("Member not checked in", $reg_no);
            return ['success' => false, 'message' => 'Member is not currently checked in'];
        }
        
        // Update check-out time
        $stmt = $conn->prepare("UPDATE gym_attendance SET check_out_time = NOW() WHERE reg_no = ? AND check_out_time IS NULL");
        $stmt->bind_param("s", $reg_no);
        
        if ($stmt->execute()) {
            // Get member name for the response
            $name_query = $conn->prepare("SELECT name FROM members WHERE reg_no = ?");
            $name_query->bind_param("s", $reg_no);
            $name_query->execute();
            $name_result = $name_query->get_result();
            $member_name = '';
            
            if ($name_result->num_rows > 0) {
                $member_name = $name_result->fetch_assoc()['name'];
            }
            
            debug_log("Member checked out successfully", $member_name);
            return [
                'success' => true, 
                'message' => 'Member checked out successfully',
                'name' => $member_name
            ];
        } else {
            debug_log("Member check-out failed", $conn->error);
            return ['success' => false, 'message' => 'Check-out failed: ' . $conn->error];
        }
    } catch (Exception $e) {
        debug_log("Exception in checkOutMember", $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

// Modified function to verify trainer and automatically check in
function verifyAndProcessTrainer($trainer_id) {
    global $conn;
    debug_log("Verifying and processing trainer", $trainer_id);
    
    try {
        // Validate trainer exists
        $trainer_check = $conn->prepare("SELECT name FROM trainers WHERE trainer_id = ?");
        $trainer_check->bind_param("s", $trainer_id);
        $trainer_check->execute();
        $trainer_result = $trainer_check->get_result();
        
        if ($trainer_result->num_rows == 0) {
            logAttendanceAction('TRAINER', $trainer_id, 'VERIFICATION_FAILED', 'Trainer not found');
            debug_log("Trainer not found", $trainer_id);
            return [
                'valid' => false,
                'message' => 'Trainer not found',
                'searched_trainer_id' => $trainer_id
            ];
        }
        
        $trainer_name = $trainer_result->fetch_assoc()['name'];
        
        // Check if trainer is already checked in
        $check_existing = $conn->prepare("SELECT * FROM trainer_attendance WHERE trainer_id = ? AND check_out_time IS NULL");
        $check_existing->bind_param("s", $trainer_id);
        $check_existing->execute();
        $result = $check_existing->get_result();
        
        if ($result->num_rows > 0) {
            // Trainer is already checked in, prompt for checkout instead of auto-checkout
            logAttendanceAction('TRAINER', $trainer_id, 'ALREADY_CHECKED_IN', 'Prompting for checkout');
            debug_log("Trainer already checked in", $trainer_name);
            return [
                'valid' => true,
                'already_checked_in' => true,
                'name' => $trainer_name,
                'message' => 'Trainer already checked in. Do you want to check out?',
                'trainer_id' => $trainer_id
            ];
        } else {
            // Trainer is not checked in, so check them in
            $stmt = $conn->prepare("INSERT INTO trainer_attendance (trainer_id, check_in_time) VALUES (?, NOW())");
            $stmt->bind_param("s", $trainer_id);
            
            if ($stmt->execute()) {
                logAttendanceAction('TRAINER', $trainer_id, 'CHECK_IN', 'Successfully checked in');
                debug_log("Trainer checked in successfully", $trainer_name);
                return [
                    'valid' => true,
                    'checked_in' => true,
                    'name' => $trainer_name,
                    'message' => 'Trainer checked in successfully',
                    'trainer_id' => $trainer_id
                ];
            } else {
                logAttendanceAction('TRAINER', $trainer_id, 'CHECK_IN_FAILED', $conn->error);
                debug_log("Failed to check in trainer", $conn->error);
                return [
                    'valid' => true,
                    'checked_in' => false,
                    'name' => $trainer_name,
                    'message' => 'Failed to check in trainer: ' . $conn->error,
                    'trainer_id' => $trainer_id
                ];
            }
        }
    } catch (Exception $e) {
        debug_log("Exception in verifyAndProcessTrainer", $e->getMessage());
        return [
            'valid' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trainer_id' => $trainer_id
        ];
    }
}
// Function to check in a trainer
function checkInTrainer($trainer_id) {
    global $conn;
    debug_log("Attempting to check in trainer", $trainer_id);
    
    try {
        // Check if trainer is already checked in
        $check_existing = $conn->prepare("SELECT * FROM trainer_attendance WHERE trainer_id = ? AND check_out_time IS NULL");
        $check_existing->bind_param("s", $trainer_id);
        $check_existing->execute();
        $result = $check_existing->get_result();
        
        if ($result->num_rows > 0) {
            debug_log("Trainer already checked in", $trainer_id);
            return [
                'success' => false, 
                'already_checked_in' => true,
                'message' => 'Trainer already checked in. Do you want to check out?'
            ];
        }
        
        // Insert new check-in record
        $stmt = $conn->prepare("INSERT INTO trainer_attendance (trainer_id, check_in_time) VALUES (?, NOW())");
        $stmt->bind_param("s", $trainer_id);
        
        if ($stmt->execute()) {
            // Get trainer name for the response
            $name_query = $conn->prepare("SELECT first_name, last_name FROM trainers WHERE trainer_id = ?");
            $name_query->bind_param("s", $trainer_id);
            $name_query->execute();
            $name_result = $name_query->get_result();
            $trainer_name = '';
            
            if ($name_result->num_rows > 0) {
                $trainer_data = $name_result->fetch_assoc();
                $trainer_name = $trainer_data['first_name'] . ' ' . $trainer_data['last_name'];
            }
            
            debug_log("Trainer checked in successfully", $trainer_name);
            return [
                'success' => true, 
                'message' => 'Trainer checked in successfully',
                'name' => $trainer_name
            ];
        } else {
            debug_log("Trainer check-in failed", $conn->error);
            return [
                'success' => false, 
                'message' => 'Trainer check-in failed: ' . $conn->error
            ];
        }
    } catch (Exception $e) {
        debug_log("Exception in checkInTrainer", $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
// Function to check out a trainer
function checkOutTrainer($trainer_id) {
    global $conn;
    debug_log("Attempting to check out trainer", $trainer_id);
    
    try {
        // Check if trainer is checked in
        $check_existing = $conn->prepare("SELECT * FROM trainer_attendance WHERE trainer_id = ? AND check_out_time IS NULL");
        $check_existing->bind_param("s", $trainer_id);
        $check_existing->execute();
        $result = $check_existing->get_result();
        
        if ($result->num_rows == 0) {
            debug_log("Trainer not checked in", $trainer_id);
            return ['success' => false, 'message' => 'Trainer is not currently checked in'];
        }
        
        // Update check-out time
        $stmt = $conn->prepare("UPDATE trainer_attendance SET check_out_time = NOW() WHERE trainer_id = ? AND check_out_time IS NULL");
        $stmt->bind_param("s", $trainer_id);
        
        if ($stmt->execute()) {
            // Get trainer name for the response - more robust approach
            $trainer_name = 'Unknown';
            
            // First try to get metadata about the trainers table
            $result = $conn->query("SHOW COLUMNS FROM trainers");
            $columns = [];
            while($row = $result->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            
            debug_log("Trainers table columns", $columns);
            
            // Check which name columns exist and construct the query accordingly
            if (in_array('name', $columns)) {
                // If there's a single 'name' column
                $name_query = $conn->prepare("SELECT name FROM trainers WHERE trainer_id = ?");
                $name_query->bind_param("s", $trainer_id);
                $name_query->execute();
                $name_result = $name_query->get_result();
                
                if ($name_result->num_rows > 0) {
                    $trainer_name = $name_result->fetch_assoc()['name'];
                }
            } 
            else if (in_array('first_name', $columns) && in_array('last_name', $columns)) {
                // If there are separate first_name and last_name columns
                $name_query = $conn->prepare("SELECT first_name, last_name FROM trainers WHERE trainer_id = ?");
                $name_query->bind_param("s", $trainer_id);
                $name_query->execute();
                $name_result = $name_query->get_result();
                
                if ($name_result->num_rows > 0) {
                    $row = $name_result->fetch_assoc();
                    $trainer_name = $row['first_name'] . ' ' . $row['last_name'];
                }
            }
            else {
                // Just use the trainer ID if we can't determine the name
                $trainer_name = 'Trainer ' . $trainer_id;
            }
            
            debug_log("Trainer checked out successfully", $trainer_name);
            return [
                'success' => true, 
                'message' => 'Trainer checked out successfully',
                'name' => $trainer_name
            ];
        } else {
            debug_log("Trainer check-out failed", $conn->error);
            return ['success' => false, 'message' => 'Trainer check-out failed: ' . $conn->error];
        }
    } catch (Exception $e) {
        debug_log("Exception in checkOutTrainer", $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
// Function to get current members count
function getCurrentMembersCount() {
    global $conn;
    debug_log("Getting current members count");
    
    try {
        $query = "SELECT COUNT(DISTINCT reg_no) as current_members FROM gym_attendance 
                  WHERE check_out_time IS NULL 
                  AND check_in_time > DATE_SUB(NOW(), INTERVAL 5 HOUR)";
        $result = $conn->query($query);
        
        if ($result) {
            $row = $result->fetch_assoc();
            debug_log("Current members count", $row['current_members']);
            return $row['current_members'];
        }
        
        debug_log("Failed to get current members count");
        return 0;
    } catch (Exception $e) {
        debug_log("Exception in getCurrentMembersCount", $e->getMessage());
        return 0;
    }
}

// Function to get current trainers count
function getCurrentTrainersCount() {
    global $conn;
    debug_log("Getting current trainers count");
    
    try {
        $query = "SELECT COUNT(DISTINCT trainer_id) as current_trainers FROM trainer_attendance 
                  WHERE check_out_time IS NULL 
                  AND check_in_time > DATE_SUB(NOW(), INTERVAL 8 HOUR)";
        $result = $conn->query($query);
        
        if ($result) {
            $row = $result->fetch_assoc();
            debug_log("Current trainers count", $row['current_trainers']);
            return $row['current_trainers'];
        }
        
        debug_log("Failed to get current trainers count");
        return 0;
    } catch (Exception $e) {
        debug_log("Exception in getCurrentTrainersCount", $e->getMessage());
        return 0;
    }
}

// Function to automatically check out members after 5 hours
function autoCheckOutMembers() {
    global $conn;
    debug_log("Running auto check-out for members");
    
    try {
        $stmt = $conn->prepare("UPDATE gym_attendance 
                                SET check_out_time = NOW() 
                                WHERE check_out_time IS NULL 
                                AND check_in_time < DATE_SUB(NOW(), INTERVAL 5 HOUR)");
        $stmt->execute();
        debug_log("Auto check-out members completed", $stmt->affected_rows);
    } catch (Exception $e) {
        debug_log("Exception in autoCheckOutMembers", $e->getMessage());
    }
}

// Function to automatically check out trainers after 8 hours
function autoCheckOutTrainers() {
    global $conn;
    debug_log("Running auto check-out for trainers");
    
    try {
        $stmt = $conn->prepare("UPDATE trainer_attendance 
                                SET check_out_time = NOW() 
                                WHERE check_out_time IS NULL 
                                AND check_in_time < DATE_SUB(NOW(), INTERVAL 8 HOUR)");
        $stmt->execute();
        debug_log("Auto check-out trainers completed", $stmt->affected_rows);
    } catch (Exception $e) {
        debug_log("Exception in autoCheckOutTrainers", $e->getMessage());
    }
}

// Always run auto check-out when the script is accessed
try {
    autoCheckOutMembers();
    autoCheckOutTrainers();
} catch (Exception $e) {
    debug_log("Exception in auto check-out operations", $e->getMessage());
}

// Test endpoint for debugging
if (isset($_GET['action']) && $_GET['action'] === 'test') {
    debug_log("Test endpoint called");
    output_json(['status' => 'success', 'message' => 'Test endpoint working']);
}

// Handle different actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    debug_log("Action requested", $action);
    
    // Log the requested action for debugging
    file_put_contents('attendance_actions.log', date('[Y-m-d H:i:s] ') . "Action: $action" . PHP_EOL, FILE_APPEND);
    
    // Member actions
    if ($action === 'check_in' && isset($_GET['reg_no'])) {
        $reg_no = $_GET['reg_no'];
        $result = checkInMember($reg_no);
        output_json($result);
    } 
    elseif ($action === 'check_out' && isset($_GET['reg_no'])) {
        $reg_no = $_GET['reg_no'];
        $result = checkOutMember($reg_no);
        output_json($result);
    } 
    elseif ($action === 'get_current_members') {
        $current_members = getCurrentMembersCount();
        output_json(['current_members' => $current_members]);
    }
    
    // Trainer actions
    elseif ($action === 'trainer_check_in' && isset($_GET['trainer_id'])) {
        $trainer_id = $_GET['trainer_id'];
        $result = checkInTrainer($trainer_id);
        output_json($result);
    } 
    elseif ($action === 'trainer_check_out' && isset($_GET['trainer_id'])) {
        $trainer_id = $_GET['trainer_id'];
        $result = checkOutTrainer($trainer_id);
        output_json($result);
    } 
    elseif ($action === 'verify_trainer' && isset($_GET['trainer_id'])) {
        $trainer_id = $_GET['trainer_id'];
        $result = verifyAndProcessTrainer($trainer_id);
        output_json($result);
    }
    elseif ($action === 'get_current_trainers') {
        $current_trainers = getCurrentTrainersCount();
        output_json(['current_trainers' => $current_trainers]);
    }
    
    // If action is not recognized
    else {
        debug_log("Invalid action or missing parameters", $_GET);
        output_json(['error' => 'Invalid action or missing parameters']);
    }
}

// Trainer verification direct endpoint (similar to member verification)
if (isset($_GET['trainer_id'])) {
    $trainer_id = trim($_GET['trainer_id']);
    $trainer_id = preg_replace('/[^0-9a-zA-Z]/', '', $trainer_id);
    debug_log("Direct trainer ID request", $trainer_id);
    
    if (empty($trainer_id)) {
        logAttendanceAction('TRAINER', 'EMPTY', 'FAILED', 'Invalid trainer ID');
        debug_log("Empty trainer ID");
        
        http_response_code(400);
        output_json([
            'valid' => false, 
            'message' => 'Invalid trainer ID',
            'raw_input' => $_GET['trainer_id']
        ]);
    }
    
    $result = verifyAndProcessTrainer($trainer_id);
    output_json($result);
}

// Default response if no action specified
debug_log("No action specified");
output_json(['error' => 'No action specified']);