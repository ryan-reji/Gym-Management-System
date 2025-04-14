<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym QR Code Generator</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .qr-container {
            text-align: center;
            margin: 20px 0;
        }
        .gym-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Gym QR Code Generator</h1>

    <div id="gym-list">
        <?php
        include '../../Login/db_config.php';
        
        $sql = "SELECT gym_id, gym_name, location FROM gym";
        $result = $conn->query($sql);
        
        while($row = $result->fetch_assoc()) {
            echo "<div class='gym-info'>";
            echo "<h3>Gym: {$row['gym_name']} (ID: {$row['gym_id']})</h3>";
            echo "<p>Location: {$row['location']}</p>";
            echo "<div class='qr-container' id='qr-{$row['gym_id']}'></div>";
            echo "<button onclick='generateQR({$row['gym_id']})'>Generate QR Code</button>";
            echo "<button onclick='downloadQR({$row['gym_id']})'>Download QR Code</button>";
            echo "</div>";
        }
        ?>
    </div>

    <script>
        function generateQR(gymId) {
            const data = {
                gym_id: gymId
            };
            
            const qr = qrcode(0, 'L');
            qr.addData(JSON.stringify(data));
            qr.make();
            
            const container = document.getElementById(`qr-${gymId}`);
            container.innerHTML = qr.createImgTag(5);
        }

        function downloadQR(gymId) {
            const img = document.querySelector(`#qr-${gymId} img`);
            if (img) {
                const link = document.createElement('a');
                link.download = `gym-${gymId}-qr.png`;
                link.href = img.src;
                link.click();
            }
        }

        // Generate QR codes for all gyms on page load
        $(document).ready(function() {
            $('.gym-info').each(function() {
                const gymId = $(this).find('button').attr('onclick').match(/\d+/)[0];
                generateQR(gymId);
            });
        });
    </script>
</body>
</html>