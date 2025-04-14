<?php
// log_error.php
// Ensure proper error logging for debugging

// Check if it's a POST request with JSON data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $rawInput = file_get_contents('php://input');
    
    // Try to decode the JSON
    $errorData = json_decode($rawInput, true);
    
    if ($errorData) {
        // Prepare log message
        $logMessage = date('[Y-m-d H:i:s] ') . 
                      "QR Scanner Error: " . 
                      ($errorData['message'] ?? 'Unknown error') . "\n";
        
        // Add details if available
        if (isset($errorData['details'])) {
            $logMessage .= "Details: " . json_encode($errorData['details']) . "\n";
        }
        
        // Write to error log file
        file_put_contents('qr_scanner_errors.log', $logMessage, FILE_APPEND);
        
        // Respond with success
        http_response_code(200);
        echo json_encode(['status' => 'logged']);
        exit();
    }
}

// If something goes wrong
http_response_code(400);
echo json_encode(['error' => 'Invalid input']);
exit();
?>