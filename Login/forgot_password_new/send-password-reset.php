<?php
/* send-password-reset.php */
$email = $_POST["email"];
$username = $_POST["username"];

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT id FROM users WHERE email = ? AND username = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);
    
    $new_password = bin2hex(random_bytes(4)); // Generate random password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    $sql = "UPDATE users SET password = ?, reset_token_hash = ?, reset_token_expires_at = ? WHERE email = ? AND username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssss", $hashed_password, $token_hash, $expiry, $email, $username);
    $stmt->execute();
    
    if ($mysqli->affected_rows) {
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@example.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        
        $mail->Body = <<<END
        Click <a href="http://localhost/MINI_PROJECT2/Login/forgot_password/reset-password.php?token=$token">here</a> to reset your password.
        Your new temporary password is: <b>$new_password</b>
        END;
        
        try {
            $mail->send();
            echo "Password reset link and new password sent. Please check your inbox.";
        } catch (Exception $e) {
            error_log("Failed to send password reset email: " . $e->getMessage());
            echo "Failed to send password reset email. Please try again later or contact support.";
        }
    }
} else {
    echo "No email for current username found. Please enter valid credentials and try again.";
}