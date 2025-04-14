<?php
session_start(); // Start the session first

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    die('Please log in first');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym QR Check-In/Out</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        .response {
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: #ddd;
            margin-right: 5px;
        }
        .tab.active {
            background: #4CAF50;
            color: white;
        }
        #status {
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Gym QR Check-In/Out</h1>
    
    <div class="section">
        <div class="tabs">
            <button class="tab active" data-mode="checkin">Check-In</button>
            <button class="tab" data-mode="checkout">Check-Out</button>
        </div>
        
        <div id="reader"></div>
        <div id="status"></div>
        <div id="response" class="response"></div>
    </div>

    <script>
        // Debug information
        console.log('Script starting...');
        
        let currentMode = 'checkin';
        const user_id = <?php echo $_SESSION['id']; ?>;
        
        console.log('User ID:', user_id);
        
        let html5QrcodeScanner = null;

        // Initialize scanner with proper error handling
        function initializeScanner() {
            try {
                console.log('Initializing scanner...');
                
                // Clear previous instance if exists
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear();
                }
                
                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader",
                    { 
                        fps: 10, 
                        qrbox: 250,
                        showTorchButtonIfSupported: true
                    }
                );
                
                console.log('Scanner initialized, rendering...');
                html5QrcodeScanner.render(onScanSuccess, onScanError);
                
                $('#status').text(`Ready to ${currentMode}`).css('color', 'black');
            } catch (error) {
                console.error('Scanner initialization error:', error);
                $('#status').text('Error initializing scanner').css('color', 'red');
            }
        }

        // Handle scan errors
        function onScanError(errorMessage) {
            console.error('Scan error:', errorMessage);
        }

        // Handle successful scans
        function onScanSuccess(decodedText) {
            console.log('QR Code scanned:', decodedText);
            
            try {
                const qrData = JSON.parse(decodedText);
                processQRCode(qrData);
            } catch (e) {
                console.error('QR Code parsing error:', e);
                $('#status').text('Invalid QR Code').css('color', 'red');
            }
        }

        // Process QR code data
        function processQRCode(qrData) {
            console.log('Processing QR data:', qrData);
            
            if (!qrData.gym_id) {
                $('#status').text('Invalid QR Code format').css('color', 'red');
                return;
            }

            const endpoint = currentMode === 'checkin' ? 'checkin.php' : 'checkout.php';
            
            const data = {
                user_id: user_id,
                gym_id: qrData.gym_id
            };

            console.log('Sending request to:', endpoint, 'with data:', data);

            $.ajax({
                url: endpoint,
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    console.log('Server response:', response);
                    
                    if (response.success) {
                        $('#status').text(response.message).css('color', 'green');
                        
                        // Stop current scanner
                        if (html5QrcodeScanner) {
                            html5QrcodeScanner.clear();
                        }
                        
                        // Restart scanner after 3 seconds
                        setTimeout(() => {
                            initializeScanner();
                        }, 3000);
                    } else {
                        $('#status').text(response.message).css('color', 'red');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', {xhr, status, error});
                    $('#status').text('Error processing request').css('color', 'red');
                }
            });
        }

        // Handle tab switching
        $(document).ready(function() {
            console.log('Document ready');
            
            $('.tab').click(function() {
                console.log('Tab clicked:', $(this).data('mode'));
                
                $('.tab').removeClass('active');
                $(this).addClass('active');
                currentMode = $(this).data('mode');
                
                $('#status').text(`Ready to ${currentMode}`).css('color', 'black');
                
                // Reinitialize scanner when switching modes
                initializeScanner();
            });

            // Initialize scanner on page load
            initializeScanner();
        });
    </script>
</body>
</html>