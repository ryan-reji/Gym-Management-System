<?php

$email = $_POST["email"];

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$mysqli = require __DIR__ . "/database.php";

$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($mysqli->affected_rows) {
    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("noreply@example.com");  // Replace with your actual domain
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    
    // Replace example.com with your actual domain
    $mail->Body = <<<END
    Click <a href="http://localhost/MINI_PROJECT2/Login/forgot_password/reset-password.php?token=$token">
    here</a> to reset your password.
    END;
    
    try {
        $mail->send();
        echo "Password reset link sent. Please check your inbox.";
    } catch (Exception $e) {
        error_log("Failed to send password reset email: " . $e->getMessage());
        echo "Failed to send password reset email. Please try again later or contact support.";
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
} else {
    // Email not found in database
    // Using a vague message for security
    echo "If a matching account is found, a password reset link will be sent to the email address.";
}