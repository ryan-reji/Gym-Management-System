<?php
require 'vendor/autoload.php'; // Ensure composer dependencies are loaded

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

try {
    $qrCode = new QrCode('TEST-QR');
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Save QR code
    file_put_contents('test_qr.png', $result->getString());

    echo "QR code generated successfully. Check test_qr.png in your project folder.";
} catch (Exception $e) {
    echo "Error generating QR code: " . $e->getMessage();
}
?>
