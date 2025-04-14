<?php
require __DIR__ . '/../vendor/autoload.php';
include('db_connect.php');

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch member details
$sql3 = "SELECT id, FirstName, LastName, username, email, dob, blood_type, profile_pic FROM users WHERE username = ?";
$stmt = $conn->prepare($sql3);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$memberData = $result->fetch_assoc();

if (!$memberData) {
    die("Error: No user found with username " . $_POST['username']);
}
$memberData['membership_type'] = $_POST['membership_type'] ?? '';
$memberData['duration'] = $_POST['duration'] ?? '';

// Function to generate member ID card
function generateMemberCard($memberData) {
    if (!file_exists('member_cards')) {
        mkdir('member_cards', 0777, true);
    }
    
    $qrCode = new QrCode('GYMSHARK-MEMBER-' . $memberData['username']);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    
    $qrPath = 'member_cards/qr_' . $memberData['id'] . '.png';
    file_put_contents($qrPath, $result->getString());
    
    $dob = new DateTime($memberData['dob']);
    $age = (new DateTime())->diff($dob)->y;
    
    $cardHtml = $cardHtml = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            .id-card {
                width: 350px;
                height: 500px;
                background: linear-gradient(145deg, #7380ec, #5a67d8);
                border-radius: 15px;
                padding: 20px;
                color: white;
                font-family: Arial, sans-serif;
                position: relative;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            
            .logo {
                text-align: center;
                margin-bottom: 15px;
                font-size: 24px;
                font-weight: bold;
            }
            
            .photo-container {
                width: 120px;
                height: 120px;
                margin: 0 auto;
                border-radius: 60px;
                overflow: hidden;
                border: 3px solid white;
            }
            
            .photo-container img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            
            .member-info {
                margin-top: 20px;
                text-align: center;
            }
            
            .member-name {
                font-size: 20px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            
            .info-grid {
                display: grid;
                grid-template-columns: auto auto;
                gap: 10px;
                margin: 15px 0;
                text-align: left;
                padding: 0 20px;
            }
            
            .info-label {
                font-weight: bold;
                color: rgba(255,255,255,0.9);
            }
            
            .qr-code {
                text-align: center;
                margin-top: 15px;
            }
            
            .qr-code img {
                width: 100px;
                height: 100px;
                background: white;
                padding: 5px;
                border-radius: 5px;
            }
            
            .valid-until {
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                color: rgba(255,255,255,0.9);
            }
        </style>
    </head>
    <body>
        <div class="id-card">
            <div class="logo">
                GYMSHARK FITNESS
            </div>
            
            <div class="photo-container">
                <img src="' . (file_exists($memberData['profile_pic']) ? $memberData['profile_pic'] : 'assets/default_profile.png') . '" alt="Member Photo">
            </div>
            
            <div class="member-info">
                <div class="member-name">' . htmlspecialchars($memberData['FirstName'] . ' ' . $memberData['LastName']) . '</div>
                
                <div class="info-grid">
                    <div class="info-label">Reg No:</div>
                    <div>' . htmlspecialchars($memberData['id']) . '</div>
                    
                    <div class="info-label">Blood Type:</div>
                    <div>' . htmlspecialchars($memberData['blood_type']) . '</div>
                    
                    <div class="info-label">Age:</div>
                    <div>' . $age . ' years</div>
                    
                    <div class="info-label">Membership:</div>
                    <div>' . ucfirst(htmlspecialchars($memberData['membership_type'])) . '</div>
                </div>
                
                <div class="qr-code">
                    <img src="' . $qrPath . '" alt="QR Code">
                </div>
                
                <div class="valid-until">
                    Valid until: ' . date('d-m-Y', strtotime('+' . $memberData['duration'] . ' months')) . '
                </div>
            </div>
        </div>
    </body>
    </html>';
    
    $mpdf = new Mpdf(['format' => [350, 500]]);
    $mpdf->WriteHTML($cardHtml);
    
    $pdfPath = 'member_cards/' . $memberData['id'] . '_id_card.pdf';
    $mpdf->Output($pdfPath, 'F');
    
    return $pdfPath;
}

// Function to send email
function sendWelcomeEmail($memberData) {
    $idCardPath = generateMemberCard($memberData);
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gymsharkuser@gmail.com';
        $mail->Password = 'lixc dcwh gfur ehya';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('gymsharkuser@gmail.com', 'GymShark Fitness');
        $mail->addAddress($memberData['email'], $memberData['FirstName'] . ' ' . $memberData['LastName']);
        $mail->addAttachment($idCardPath, 'GymShark_Member_ID_Card.pdf');
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to GymShark Fitness, ' . $memberData['FirstName'] . '!';

        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #7380ec; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #7380ec;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .section {
                    margin-top: 20px;
                    padding: 15px;
                    background-color: #f3f4f6;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Welcome to GymShark Fitness!</h1>
                </div>
                <div class="content">
                    <h2>Hello ' . htmlspecialchars($memberData['FirstName']) . ',</h2>
                    <p>Congratulations! You are now a valued member of GymShark Fitness.</p>
                    
                    <h3>Your Membership Details:</h3>
                    <ul>
                        <li><strong>Membership ID:</strong> ' . htmlspecialchars($memberData['id']) . '</li>
                        <li><strong>Plan:</strong> ' . ucfirst(htmlspecialchars($memberData['membership_type'])) . '</li>
                        <li><strong>Valid Until:</strong> ' . date('d-m-Y', strtotime('+' . $memberData['duration'] . ' months')) . '</li>
                    </ul>

                    <div class="section">
                        <h3 style="color: #7380ec;">Your Membership ID Card</h3>
                        <p>We\'ve attached your digital membership ID card to this email. Please:</p>
                        <ul>
                            <li>Download and save it on your phone.</li>
                            <li>Print a copy if needed.</li>
                            <li>Show it at the reception for entry.</li>
                        </ul>
                    </div>

                    <h3>Important Information:</h3>
                    <ul>
                        <li>🕒 <strong>Gym Timings:</strong> 5:00 AM - 11:00 PM</li>
                        <li>📍 <strong>Location:</strong> GymShark Fitness Center, Thane</li>
                        <li>📞 <strong>Contact:</strong> +91 XXXXXXXXXX</li>
                    </ul>

                    <div class="section">
                        <h3 style="color: #7380ec;">Next Steps:</h3>
                        <ol>
                            <li>Visit our gym for an orientation session.</li>
                            <li>Meet your trainer (if applicable).</li>
                            <li>Download our app to track your progress.</li>
                        </ol>
                    </div>

                    <p style="margin-top: 20px;">
                        <a href="https://gymshark.com/member-portal" class="button">Access Member Portal</a>
                    </p>
                    
                    <p>We can’t wait to see you in the gym! Stay fit, stay strong!</p>
                    
                    <p>Best regards,<br>
                    The GymShark Team</p>
                </div>
                <div class="footer">
                    <p>GymShark Fitness<br>
                    Contact: +91 XXXXXXXXXX<br>
                    Email: support@gymshark.com</p>
                </div>
            </div>
        </body>
        </html>';

        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}
function saveBase64Image($base64Data, $targetDir) {
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    if (strpos($base64Data, ',') !== false) {
        $base64Data = explode(',', $base64Data)[1];
    }
    $imageData = base64_decode($base64Data);
    $filename = uniqid() . '.png';
    $targetFile = $targetDir . $filename;
    
    return file_put_contents($targetFile, $imageData) ? $targetFile : false;
}
// Send email
if (sendWelcomeEmail($memberData)) {
    echo "Email sent successfully.";
} else {
    echo "Failed to send email.";
}
