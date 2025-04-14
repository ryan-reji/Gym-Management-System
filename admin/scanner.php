<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner - GymShark</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="styles.css">
    <script src="jsQR.js"></script>
    <style>
        .scanner-container {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            margin-top: 2rem;
            box-shadow: var(--box-shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #scanner-box {
            width: 100%;
            max-width: 500px;
            height: 500px;
            background: var(--clr-light);
            border-radius: var(--border-radius-1);
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        #scanner-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 4px solid var(--clr-primary);
            pointer-events: none;
            z-index: 10;
        }

        .scan-btn {
            padding: 0.75rem 1.5rem;
            background: var(--clr-primary);
            color: var(--clr-white);
            border: none;
            border-radius: var(--border-radius-1);
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }

        .scan-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        
        .scan-mode-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .scan-mode-btn {
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            background: var(--clr-white);
            border: 1px solid var(--clr-primary);
            color: var(--clr-primary);
            border-radius: var(--border-radius-1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .scan-mode-btn.active {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        /* Result Popup Styles */
        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .popup-content {
            background: var(--clr-white);
            padding: 2rem;
            border-radius: var(--card-border-radius);
            text-align: center;
            max-width: 400px;
            width: 90%;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .popup-close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: var(--clr-danger);
        }

        .popup-success {
            color: var(--clr-success);
        }

        .popup-error {
            color: var(--clr-danger);
        }
        
        .action-buttons {
            margin-top: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-1);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .action-btn.check-in {
            background: var(--clr-success);
            color: white;
        }
        
        .action-btn.check-out {
            background: var(--clr-danger);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar (same as other pages) -->
        <aside>
           
           <div class="top">
             <div class="logo">
               <h2>GYM</h2><h2><span class="danger">SHARK</span> </h2>
             </div>
             <div class="close" id="close_btn">
              <span class="material-symbols-sharp">
                close
                </span>
             </div>
           </div>
           <!-- end top -->
           <div class="sidebar">
             <a href="index.php" >  
                <span class="material-symbols-sharp">grid_view </span>
                <h3>Dashboard</h3>
             </a>
             <a href="member.php">
                <span class="material-symbols-sharp">person_outline </span>
               <h3>Members</h3>
                <span class="msg_count">69</span>
             </a>
             <a href="add_member.php" >
                      <span class="material-symbols-sharp">person_add</span>
                      <h3>Add Member</h3>
                  </a>
                  <a href="addtrainer.php" >
                      <span class="material-symbols-sharp">person_add</span>
                      <h3>Add Trainer</h3>
                  </a>
             
             <a href="scanner.php" class="active"> >
                <span class="material-symbols-sharp">qr_code_scanner</span>
                <h3>Attendance</h3>
             </a>
             <a href="growth.php">
                <span class="material-symbols-sharp">insights </span>
                <h3>Growth</h3>
             </a>
             <a href="Trainer.php">
                <span class="material-symbols-sharp">Person </span>
                <h3>Trainers</h3>
                <span class="msg_count">14</span>
             </a>
             <a href="equipmentpage.php">
                <span class="material-symbols-sharp">receipt_long </span>
                <h3>Equipments</h3>
             </a>
              
             <a href="settings.html">
                <span class="material-symbols-sharp">settings </span>
                <h3>Settings</h3>
             </a>
             <a href="addeq.php">
                <span class="material-symbols-sharp">add </span>
                <h3>Add Equipment</h3>
             </a>
             <a href="request/reschedule_requests.php">
              <span class="material-symbols-sharp">receipt_long </span>
              <h3>Request Approval</h3>
           </a>
           <a href="../Login/logout.php">
              <span class="material-symbols-sharp">logout </span>
              <h3>logout</h3>
           </a>
            </div>
        
        </aside>
        <!-- end aside -->
  
  <!-- Main Content -->
        <main>
            <h1>QR Scanner</h1>
            
            <div class="scan-mode-container">
                <button id="member-mode-btn" class="scan-mode-btn active">Member Mode</button>
                <button id="trainer-mode-btn" class="scan-mode-btn">Trainer Mode</button>
            </div>

            <div class="scanner-container">
                <div id="scanner-box">
                    <video id="scanner-video" playsinline></video>
                    <div class="scanner-overlay"></div>
                </div>
                <button id="start-scan-btn" class="scan-btn">Start Scanning</button>
            </div>
        </main>

        <!-- Popup for Scan Results -->
        <div id="popup-overlay" class="popup-overlay">
            <div class="popup-content">
                <span id="popup-close" class="popup-close material-symbols-sharp">close</span>
                <div id="popup-result"></div>
                <div id="action-buttons" class="action-buttons" style="display: none;">
                    <button id="check-in-btn" class="action-btn check-in">Check In</button>
                    <button id="check-out-btn" class="action-btn check-out">Check Out</button>
                </div>
            </div>
        </div>

        <!-- Right Section (same as other pages) -->
        <div class="right">
            <div class="top">
                <button id="menu_bar">
                    <span class="material-symbols-sharp">menu</span>
                </button>

                <div class="theme-toggler">
                    <span class="material-symbols-sharp active">light_mode</span>
                    <span class="material-symbols-sharp">dark_mode</span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p><b>miguel</b></p>
                        <p>Admin</p>
                        <small class="text-muted"></small>
                    </div>
                    <div class="profile-photo">
                        <!-- Add profile photo here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
    </body>
    </html>
    
<script>
    let video = document.getElementById("scanner-video");
    let canvasElement = document.createElement("canvas");
    let canvas = canvasElement.getContext("2d");
    let scanning = false;
    let currentMode = "member"; // Default mode
    let popupOverlay = document.getElementById("popup-overlay");
    let popupResult = document.getElementById("popup-result");
    let actionButtons = document.getElementById("action-buttons");
    let checkInBtn = document.getElementById("check-in-btn");
    let checkOutBtn = document.getElementById("check-out-btn");
    let scanButton = document.getElementById("start-scan-btn");
    let stream = null;

    // Toggle Member/Trainer Mode
    document.getElementById("member-mode-btn").addEventListener("click", function() {
        currentMode = "member";
        this.classList.add("active");
        document.getElementById("trainer-mode-btn").classList.remove("active");
    });

    document.getElementById("trainer-mode-btn").addEventListener("click", function() {
        currentMode = "trainer";
        this.classList.add("active");
        document.getElementById("member-mode-btn").classList.remove("active");
    });

   

// Toggle Camera On/Off
scanButton.addEventListener("click", function() {
    if (!scanning) {
        startCamera();
        scanButton.textContent = "Stop Scanning";
    } else {
        stopCamera();
        scanButton.textContent = "Start Scanning";
    }
});

// Add these at the beginning of your script to create debug elements
let debugInfo = document.createElement("div");
debugInfo.style.position = "fixed";
debugInfo.style.bottom = "10px";
debugInfo.style.left = "10px";
debugInfo.style.backgroundColor = "rgba(0,0,0,0.7)";
debugInfo.style.color = "white";
debugInfo.style.padding = "10px";
debugInfo.style.borderRadius = "5px";
debugInfo.style.zIndex = "9999";
debugInfo.innerHTML = "Camera status: Not started";
document.body.appendChild(debugInfo);

// Enhanced scanner indicator
let scannerOverlay = document.createElement("div");
scannerOverlay.style.position = "absolute";
scannerOverlay.style.top = "0";
scannerOverlay.style.left = "0";
scannerOverlay.style.right = "0";
scannerOverlay.style.bottom = "0";
scannerOverlay.style.border = "2px solid red";
scannerOverlay.style.display = "none";
scannerOverlay.style.zIndex = "9998";
document.getElementById("scanner-container").appendChild(scannerOverlay);

// Modified startCamera function with enhanced debugging
function startCamera() {
    debugInfo.innerHTML = "Starting camera...";
    
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then(function(mediaStream) {
        stream = mediaStream;
        video.srcObject = stream;
        video.setAttribute("playsinline", true);
        
        // Add event listeners to detect when video is actually playing
        video.onloadedmetadata = function() {
            debugInfo.innerHTML = "Camera loaded metadata";
        };
        
        video.onplay = function() {
            debugInfo.innerHTML = "Camera active, searching for QR codes...";
            scannerOverlay.style.display = "block";
            scanning = true;
            scanQRCode();
        };
        
        video.play();
    }).catch(error => {
        debugInfo.innerHTML = "Camera error: " + error.message;
        console.error("Camera access denied or error:", error);
        scanning = false;
        scanButton.textContent = "Start Scanning";
    });
}

// Enhanced scanQRCode function with visual feedback
function scanQRCode() {
    if (!scanning) {
        debugInfo.innerHTML = "Scanning stopped";
        scannerOverlay.style.display = "none";
        return;
    }
    
    // Check if video dimensions are available
    if (video.videoWidth === 0 || video.videoHeight === 0) {
        debugInfo.innerHTML = "Waiting for video dimensions...";
        requestAnimationFrame(scanQRCode);
        return;
    }
    
    canvasElement.width = video.videoWidth;
    canvasElement.height = video.videoHeight;
    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
    
    // Blink scanner overlay to indicate active scanning
    scannerOverlay.style.borderColor = scannerOverlay.style.borderColor === "red" ? "green" : "red";
    
    try {
        let imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        let code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
        
        if (code) {
            scannerOverlay.style.borderColor = "green";
            scannerOverlay.style.borderWidth = "4px";
            debugInfo.innerHTML = "QR detected: " + code.data;
            
            // Draw the QR location on canvas
            drawQROutline(code.location);
            
            // Stop camera and process the QR code
            stopCamera();
            processScan(code.data);
        } else {
            debugInfo.innerHTML = "Scanning... (no QR detected)";
            if (scanning) {
                requestAnimationFrame(scanQRCode);
            }
        }
    } catch (error) {
        debugInfo.innerHTML = "Scan error: " + error.message;
        console.error("Error during QR scanning:", error);
        if (scanning) {
            requestAnimationFrame(scanQRCode);
        }
    }
}

// Function to draw QR code outline
function drawQROutline(location) {
    canvas.beginPath();
    canvas.moveTo(location.topLeftCorner.x, location.topLeftCorner.y);
    canvas.lineTo(location.topRightCorner.x, location.topRightCorner.y);
    canvas.lineTo(location.bottomRightCorner.x, location.bottomRightCorner.y);
    canvas.lineTo(location.bottomLeftCorner.x, location.bottomLeftCorner.y);
    canvas.lineTo(location.topLeftCorner.x, location.topLeftCorner.y);
    canvas.strokeStyle = "#FF3B58";
    canvas.lineWidth = 4;
    canvas.stroke();
}

// Enhanced processScan function with detailed logging
// Replace your current processScan function with this updated version
function processScan(qrData) {
    debugInfo.innerHTML = "Processing QR: " + qrData;
    
    // Extract the username from the QR code data (format: GYMSHARK-MEMBER-username)
    let username = "";
    try {
        let parts = qrData.split('-');
        debugInfo.innerHTML += "<br>QR parts: " + parts.join(", ");
        
        if (parts.length < 3) {
            throw new Error("QR code doesn't have enough parts");
        }
        
        username = parts[2].trim();
        debugInfo.innerHTML += "<br>Extracted username: " + username;
    } catch (error) {
        debugInfo.innerHTML += "<br>Invalid QR format: " + error.message;
        popupResult.innerHTML = "Invalid QR code format";
        popupOverlay.style.display = "flex";
        actionButtons.style.display = "none";
        scanButton.textContent = "Start Scanning";
        return;
    }

    // Send the username to the backend for processing
    debugInfo.innerHTML += "<br>Sending to server...";
    
    fetch("attendance_qr/process_scan.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ username: username, mode: currentMode })
    })
    .then(response => {
        debugInfo.innerHTML += "<br>Server responded: " + response.status;
        if (!response.ok) {
            throw new Error("Network response was not ok: " + response.status);
        }
        return response.json();
    })
    .then(data => {
        debugInfo.innerHTML += "<br>Response data: " + JSON.stringify(data);
        
        popupResult.innerHTML = data.message;
        popupOverlay.style.display = "flex";
        
        // Reset button display
        checkInBtn.style.display = "none";
        checkOutBtn.style.display = "none";
        actionButtons.style.display = "flex";
        
        if (data.status === "check-in") {
            checkInBtn.style.display = "block";
            checkInBtn.onclick = () => confirmCheckIn(username);
        } else if (data.status === "check-out") {
            checkOutBtn.style.display = "block";
            checkOutBtn.onclick = () => confirmCheckOut(username);
        } else if (data.status === "both") {
            // This is the key change - show both buttons when status is "both"
            checkInBtn.style.display = "block";
            checkOutBtn.style.display = "block";
            checkInBtn.onclick = () => confirmCheckIn(username);
            checkOutBtn.onclick = () => confirmCheckOut(username);
        } else {
            actionButtons.style.display = "none";
        }
    })
    .catch(error => {
        debugInfo.innerHTML += "<br>Error: " + error.message;
        console.error("Error processing scan:", error);
        popupResult.innerHTML = "Error processing scan: " + error.message;
        popupOverlay.style.display = "flex";
        actionButtons.style.display = "none";
    });
    
    // Reset scan button text
    scanButton.textContent = "Start Scanning";
}
// Add a button to test processing with dummy data
let testButton = document.createElement("button");
testButton.innerHTML = "Test with Dummy QR";
testButton.style.position = "fixed";
testButton.style.bottom = "10px";
testButton.style.right = "10px";
testButton.style.zIndex = "9999";
testButton.onclick = function() {
    processScan("GYMSHARK-MEMBER-testuser");
};
document.body.appendChild(testButton);

// Stop Camera
function stopCamera() {
    if (stream) {
        // Stop all tracks in the stream
        stream.getTracks().forEach(track => {
            track.stop();
        });
        stream = null;  // Clear the stream
    }
    video.srcObject = null;  // Remove the video source
    scanning = false;  // Set scanning to false
}

// QR Code scanning logic



// Replace your processScan function with this updated version


// Also update these functions to use JSON format consistently
function confirmCheckIn(username) {
    fetch("attendance_qr/process_scan.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            username: username, 
            mode: currentMode, 
            action: "check_in" 
        })
    })
    .then(response => response.json())
    .then(data => {
        popupResult.innerHTML = data.message;
        setTimeout(() => popupOverlay.style.display = "none", 2000);
    })
    .catch(error => console.error("Error checking in:", error));
}

function confirmCheckOut(username) {
    fetch("attendance_qr/process_scan.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            username: username, 
            mode: currentMode, 
            action: "check_out" 
        })
    })
    .then(response => response.json())
    .then(data => {
        popupResult.innerHTML = data.message;
        setTimeout(() => popupOverlay.style.display = "none", 2000);
    })
    .catch(error => console.error("Error checking out:", error));
}
</script>

    