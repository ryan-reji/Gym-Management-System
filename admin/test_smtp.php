<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure PHPMailer is installed

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'gymsharkuser@gmail.com';
    $mail->Password = 'lixc dcwh gfur ehya';
    $mail->SMTPSecure = 'tls'; 
    $mail->Port = 587;

    $mail->setFrom('gymsharkuser@gmail.com', 'Your Name');
    $mail->addAddress('receiver@example.com');
    $mail->Subject = 'Test Mail';
    $mail->Body = 'Hello, this is a test mail.';

    $mail->send();
    echo 'Mail sent successfully!';
} catch (Exception $e) {
    echo "Mail Error: {$mail->ErrorInfo}";
}

?>
