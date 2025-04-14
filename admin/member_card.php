<?php
// member_card.php

require 'vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function generateMemberCard($memberData) {
    // Generate QR Code
    $qrCode = QrCode::create('GYMSHARK-MEMBER-' . $memberData['reg_no']);
    $writer = new PngWriter();
    $qrResult = $writer->write($qrCode);
    
    // Save QR code
    $qrPath = 'member_cards/qr_' . $memberData['reg_no'] . '.png';
    $qrResult->saveToFile($qrPath);
    
    // Calculate age from DOB
    $dob = new DateTime($memberData['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    
    // Generate HTML for ID card
    $cardHtml = '
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
                <img src="' . ($memberData['profile_pic'] ?: 'default_profile.png') . '" alt="Member Photo">
            </div>
            
            <div class="member-info">
                <div class="member-name">' . htmlspecialchars($memberData['name']) . '</div>
                
                <div class="info-grid">
                    <div class="info-label">Reg No:</div>
                    <div>' . htmlspecialchars($memberData['reg_no']) . '</div>
                    
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
    
    // Generate PDF
    require_once 'vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf(['format' => [350, 500]]);
    $mpdf->WriteHTML($cardHtml);
    
    // Save PDF
    $pdfPath = 'member_cards/' . $memberData['reg_no'] . '_id_card.pdf';
    $mpdf->Output($pdfPath, 'F');
    
    return $pdfPath;
}