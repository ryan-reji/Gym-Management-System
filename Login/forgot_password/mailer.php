<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../../vendor/autoload.php";

$mail = new PHPMailer(true);

// Uncomment this line for debugging
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

$mail->Host = "smtp.gmail.com";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Replace with your Gmail address
$mail->Username = "gymsharkuser@gmail.com";

// Replace with your Gmail App Password
// Do NOT use your regular Gmail password
$mail->Password = "lixc dcwh gfur ehya";

$mail->isHtml(true);

return $mail;