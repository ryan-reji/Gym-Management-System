
<?php
// Include authentication check
require_once "auth_check.php";
require_once "config.php";

// Create database connection
$conn = mysqli_connect($sname, $username, $password, $db_name, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure required database structure exists
$conn->query("CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT(11) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

// Add missing columns if they don't exist
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS display_name VARCHAR(100) AFTER username");
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_active TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");

// Update user's last active time
$update_sql = "UPDATE users SET last_active = NOW() WHERE id = ?";
$stmt = $conn->prepare($update_sql);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$stmt->close();

// Get online users count
$online_sql = "SELECT COUNT(*) as online_count FROM users WHERE last_active > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
$online_result = $conn->query($online_sql);
$online_count = $online_result->fetch_assoc()['online_count'];

// Get latest messages
$message_sql = "SELECT 
    m.id, 
    m.message, 
    m.created_at, 
    m.user_id, 
    u.username, 
    COALESCE(u.display_name, u.username) AS display_name
FROM messages m
JOIN users u ON m.user_id = u.id
ORDER BY m.created_at DESC
LIMIT 50";

               
$message_result = $conn->query($message_sql);
$messages = [];

if ($message_result === false) {
    die("Error executing message query: " . $conn->error);
}

while ($row = $message_result->fetch_assoc()) {
    $display_name = $row['display_name'];
    $initials = substr($display_name, 0, 1);
    if (strpos($display_name, ' ') !== false) {
        $name_parts = explode(' ', $display_name);
        $initials = substr($name_parts[0], 0, 1) . substr($name_parts[count($name_parts) - 1], 0, 1);
    }

    $messages[] = [
        'id' => $row['id'],
        'message' => $row['message'],
        'timestamp' => strtotime($row['created_at']),
        'formatted_time' => date('g:i A', strtotime($row['created_at'])),
        'user_id' => $row['user_id'],
        'username' => $row['username'],
        'display_name' => $display_name,
        'initials' => strtoupper($initials),
        'role' => 'member', // Hardcoded for now
        'is_current_user' => ($_SESSION['id'] == $row['user_id'])
    ];
}



// Reverse for chronological order
$messages = array_reverse($messages);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymShark Group Chat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern CSS Reset */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Custom Properties */
        :root {
            /* Main Color Scheme */
            --primary-color: #b74b4b;
            --primary-hover: #a43a3a;
            --background-dark: #121212;
            --card-bg: rgba(30, 30, 30, 0.75);
            --card-hover: rgba(40, 40, 40, 0.8);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
            
            /* UI Elements */
            --sidebar-width: 260px;
            --sidebar-collapsed: 80px;
            --border-radius: 12px;
            --transition-speed: 0.3s;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        /* Typography */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-dark);
            background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.5;
        }

        .container {
            position: relative;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Navigation */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: rgba(20, 20, 20, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 100;
            transition: all var(--transition-speed) ease;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 0.75rem;
        }

        .sidebar-header h2 {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.25rem;
        }

        .close-sidebar {
            margin-left: auto;
            display: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: var(--text-tertiary);
        }

        .user-profile {
            display: flex;
            align-items: center;
            padding: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .avatar i {
            color: white;
        }

        .user-info h3 {
            font-size: 0.875rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .user-info p {
            font-size: 0.75rem;
            color: var(--text-tertiary);
        }

        .nav-links {
            list-style: none;
            padding: 0;
            flex: 1;
            overflow-y: auto;
        }

        .nav-links li {
            margin: 0.25rem 0.75rem;
            border-radius: var(--border-radius);
            transition: background-color var(--transition-speed);
        }

        .nav-links li a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed);
        }

        .nav-links li a i {
            font-size: 1.125rem;
            min-width: 2rem;
            transition: transform var(--transition-speed);
        }

        .nav-links li a span {
            margin-left: 0.5rem;
        }

        .nav-links li:hover a {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-links li.active {
            background: var(--primary-color);
        }

        .nav-links li.active a {
            color: white;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem;
            background-color: rgba(220, 38, 38, 0.1);
            color: #ef4444;
            border: 1px solid rgba(220, 38, 38, 0.2);
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-speed);
        }

        .logout-button:hover {
            background-color: rgba(220, 38, 38, 0.2);
        }

        .logout-button i {
            margin-right: 0.5rem;
        }

        /* Mobile Nav Toggle */
        .mobile-nav-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 99;
            background: var(--primary-color);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: margin var(--transition-speed);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .greeting h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .greeting p {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .header-actions {
            display: flex;
            align-items: center;
        }

        .notification-icon {
            position: relative;
            margin-right: 1rem;
            cursor: pointer;
        }

        .notification-icon i {
            font-size: 1.25rem;
            color: var(--text-secondary);
        }

        .notification-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .date-display {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 0.75rem;
            border-radius: var(--border-radius);
        }

        .date-display i {
            color: var(--text-secondary);
            margin-right: 0.5rem;
        }

        /* Group Chat Container */
        .group-chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 140px);
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        /* Chat Header */
        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chat-room-info {
            display: flex;
            align-items: center;
        }

        .chat-room-info i {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-right: 0.75rem;
        }

        .chat-room-details h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chat-room-details p {
            font-size: 0.75rem;
            color: var(--text-tertiary);
        }

        .online-indicator {
            display: flex;
            align-items: center;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #4caf50;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .online-count {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        /* Chat Messages */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }

        .message-info {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-tertiary);
            margin: 1rem 0;
            position: relative;
        }

        .message-info::before, .message-info::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 100px;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .message-info::before {
            right: calc(50% + 1rem);
        }

        .message-info::after {
            left: calc(50% + 1rem);
        }

        .message-group {
            display: flex;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .user-avatar-small {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            font-size: 0.75rem;
            color: white;
            margin-right: 0.75rem;
            flex-shrink: 0;
            position: relative;
        }

        .user-avatar-small.trainer {
            background-color: #4caf50;
        }

        .trainer-badge {
            position: absolute;
            bottom: -2px;
            right: -2px;
            background-color: var(--primary-color);
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            border: 2px solid var(--card-bg);
        }

        .message-content {
            flex: 1;
        }

        .message-sender {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }

        .member-tag, .trainer-tag {
            font-size: 0.65rem;
            padding: 0.1rem 0.35rem;
            border-radius: 4px;
            margin-left: 0.5rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        .member-tag {
            background-color: rgba(79, 70, 229, 0.2);
            color: #818cf8;
        }

        .trainer-tag {
            background-color: rgba(76, 175, 80, 0.2);
            color: #4caf50;
        }

        .message-bubble {
            background: rgba(50, 50, 50, 0.5);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            max-width: 80%;
            position: relative;
        }

        .message-time {
            font-size: 0.7rem;
            color: var(--text-tertiary);
            margin-top: 0.25rem;
        }

        /* Chat Input */
        .chat-input-container {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chat-input-actions {
            display: flex;
        }

        .chat-input-btn {
            color: var(--text-secondary);
            margin-right: 0.75rem;
            cursor: pointer;
            font-size: 1.25rem;
            transition: color 0.2s ease;
        }

        .chat-input-btn:hover {
            color: var(--text-primary);
        }

        .chat-input {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 0.75rem 1rem;
            margin: 0 0.75rem;
            color: var(--text-primary);
            resize: none;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .chat-input:focus {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .chat-input::placeholder {
            color: var(--text-tertiary);
        }

        .chat-send-btn {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .chat-send-btn:hover {
            background-color: var(--primary-hover);
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-nav-toggle {
                display: flex;
            }

            .close-sidebar {
                display: block;
            }

            .sidebar.active {
                transform: translateX(0);
            }
        }

        @media (max-width: 576px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                margin-top: 1rem;
            }

            .chat-input-actions {
                display: none;
            }
        }

        /* Sound Toggle Styles */
.sound-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.2s ease;
}

.sound-toggle:hover {
    background: rgba(255, 255, 255, 0.1);
}

.sound-toggle i {
    font-size: 1rem;
}


    </style>
</head>
<body>
    <!-- Mobile Navigation Toggle -->
    <div class="mobile-nav-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <div class="container">
        <!-- Sidebar Navigation -->
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-dumbbell logo-icon"></i>
                <h2>GymShark</h2>
                <div class="close-sidebar">
                    <i class="fas fa-times"></i>
                </div>
            </div>

            <div class="user-profile">
    <div class="avatar">
        <i class="fas fa-user"></i>
    </div>
    <div class="user-info">
        <h3><?php echo htmlspecialchars($_SESSION["display_name"] ?? $_SESSION["username"]); ?></h3>
        <p>Member</p> <!-- Always Member -->
    </div>
</div>

<ul class="nav-links">
                <li>
                    <a href="../index.php">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="../../user/trainer/">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="../members.php">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>
                
                <li>
                    <a href="#">
                        <i class="fas fa-tasks"></i>
                        <span>Workout Plans</span>
                    </a>
                </li>
                <li>
                    <a href="../bmi.php">
                        <i class="fas fa-weight"></i>
                        <span>BMI Scale</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#">
                        <i class="fas fa-weight"></i>
                        <span>Live Chat</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../../Login/logout.php" class="logout-button">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="header">
                <div class="greeting">
                    <h1>Group Chat</h1>
                    <p>Connect with trainers and members</p>
                </div>
                <div class="header-actions">
                    <div class="notification-icon">
                        <i class="far fa-bell"></i>
                        <div class="badge">2</div>
                    </div>
                    <div class="date-display">
                        <i class="far fa-calendar"></i>
                        <span><?php echo date("F d, Y"); ?></span>
                    </div>
                </div>
            </div>

            <div class="group-chat-container">
                <div class="chat-header">
                    <div class="chat-room-info">
                        <i class="fas fa-users"></i>
                        <div class="chat-room-details">
                            <h3>Fitness Enthusiasts</h3>
                            <p>Daily workout discussions</p>
                        </div>
                    </div>
                    <div class="online-indicator">
                        <div class="status-dot"></div>
                        <span class="online-count"><?php echo $online_count; ?> online</span>
                    </div>
                </div>

                <div class="chat-messages" id="chat-messages">
                    <div class="message-info">
                        Today
                    </div>
                    <?php foreach($messages as $message): ?>
                    <div class="message-group">
                    <div class="user-avatar-small">
    <?php echo $message['initials']; ?>
</div>
                        <div class="message-content">
                        <div class="message-sender">
    <?php echo htmlspecialchars($message['display_name']); ?>
    <span class="member-tag">Member</span>
</div>
                            <div class="message-bubble">
                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                            </div>
                            <div class="message-time">
                                <?php echo $message['formatted_time']; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="chat-input-container">
                    <div class="chat-input-actions">
                        <div class="chat-input-btn">
                            <i class="far fa-smile"></i>
                        </div>
                        <div class="chat-input-btn">
                            <i class="fas fa-paperclip"></i>
                        </div>
                    </div>
                    <input type="text" class="chat-input" id="message-input" placeholder="Type your message..." autocomplete="off">
                    <div class="chat-send-btn" id="send-button">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
   document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const closeSidebar = document.querySelector('.close-sidebar');
    const chatMessages = document.getElementById('chat-messages');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    let lastTimestamp = 0;
    let isPolling = true;
    
    // Sound notification elements
    const messageSentSound = new Audio('audio/message-sent.mp3');
    const messageReceivedSound = new Audio('audio/message-received.mp3');
    
    // Set volume for the sounds (0.0 to 1.0)
    messageSentSound.volume = 0.5;
    messageReceivedSound.volume = 0.5;

    // Function to check if user has muted sounds
    function areSoundsMuted() {
        return localStorage.getItem('chat_sounds_muted') === 'true';
    }

    // Function to play a sound if not muted
    function playSound(sound) {
        if (!areSoundsMuted()) {
            // Create a new audio element each time to allow overlapping sounds
            const soundClone = sound.cloneNode();
            soundClone.play().catch(error => {
                console.log("Sound play failed:", error);
                // This often happens due to browser autoplay policies
            });
        }
    }

    // Scroll to bottom of chat
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Initial scroll to bottom
    scrollToBottom();

    // Toggle mobile sidebar
    mobileNavToggle.addEventListener('click', function() {
        sidebar.classList.add('active');
    });

    closeSidebar.addEventListener('click', function() {
        sidebar.classList.remove('active');
    });

 // Get formatted time
 function getFormattedTime() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
        return `${hours}:${formattedMinutes} ${ampm}`;
    }

    // Create message element
    function createMessageElement(messageData) {
        // Create message group container
        const messageGroup = document.createElement('div');
        messageGroup.className = 'message-group';
        messageGroup.setAttribute('data-message-id', messageData.id);
        
        // Create avatar
        const avatar = document.createElement('div');
        avatar.className = `user-avatar-small ${messageData.user_type === 'trainer' ? 'trainer' : ''}`;
        avatar.textContent = messageData.initials;
        
        // Add trainer badge if needed
        if (messageData.user_type === 'trainer') {
            const trainerBadge = document.createElement('div');
            trainerBadge.className = 'trainer-badge';
            const starIcon = document.createElement('i');
            starIcon.className = 'fas fa-star';
            trainerBadge.appendChild(starIcon);
            avatar.appendChild(trainerBadge);
        }
        
        // Create message content container
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        
        // Create sender name with tag
        const messageSender = document.createElement('div');
        messageSender.className = 'message-sender';
        messageSender.textContent = messageData.display_name;
        
        // Add appropriate tag
        const tagSpan = document.createElement('span');
        if (messageData.user_type === 'trainer') {
            tagSpan.className = 'trainer-tag';
            tagSpan.textContent = 'Trainer';
        } else {
            tagSpan.className = 'member-tag';
            tagSpan.textContent = 'Member';
        }
        messageSender.appendChild(tagSpan);
        
        // Create message bubble
        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        messageBubble.textContent = messageData.message;
        
        // Create message time
        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        messageTime.textContent = messageData.formatted_time;
        
        // Assemble the message
        messageContent.appendChild(messageSender);
        messageContent.appendChild(messageBubble);
        messageContent.appendChild(messageTime);
        
        messageGroup.appendChild(avatar);
        messageGroup.appendChild(messageContent);
        
        return messageGroup;
    }

    // Send message
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message === '') return;
        
        // Disable send button during request
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Create form data
        const formData = new FormData();
        formData.append('message', message);
        
        // Send message via AJAX
        fetch('send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Add message to chat immediately after successful send
                const messageElement = createMessageElement(data.message);
                chatMessages.appendChild(messageElement);
                
                // Play the sent message sound
                playSound(messageSentSound);
                
                // Clear input and scroll to bottom
                messageInput.value = '';
                scrollToBottom();
                
                // Update last timestamp
                if (data.message.timestamp > lastTimestamp) {
                    lastTimestamp = data.message.timestamp;
                }
            } else {
                console.error('Failed to send message:', data.error);
                alert('Failed to send message: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    }
    
    // Event listeners for sending messages
    sendButton.addEventListener('click', sendMessage);
    
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });
    
    // Periodically check for new messages
    function getNewMessages() {
        if (!isPolling) return;
        
        fetch(`get_messages.php?last_timestamp=${lastTimestamp}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.messages.length > 0) {
                let needScroll = false;
                const isNearBottom = chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight < 100;
                
                // Check if there are any messages from other users
                const hasNewMessagesFromOthers = data.messages.some(msg => !msg.is_current_user);
                
                data.messages.forEach(message => {
                    // Check if message already exists
                    const existingMsg = document.querySelector(`[data-message-id="${message.id}"]`);
                    if (!existingMsg) {
                        const messageElement = createMessageElement(message);
                        chatMessages.appendChild(messageElement);
                        needScroll = true;
                        
                        if (message.timestamp > lastTimestamp) {
                            lastTimestamp = message.timestamp;
                        }
                    }
                });
                
                // Play received message sound if there are new messages from others
                if (hasNewMessagesFromOthers) {
                    playSound(messageReceivedSound);
                }
                
                // Only scroll if we were already at the bottom or it's our own message
                if (needScroll && isNearBottom) {
                    scrollToBottom();
                }
            }
            
            // Schedule next poll
            setTimeout(getNewMessages, 2000);
        })
        .catch(error => {
            console.error('Error getting new messages:', error);
            // Retry after a delay if there's an error
            setTimeout(getNewMessages, 5000);
        });
    }

    // Update user's online status every minute
    function updateOnlineStatus() {
        fetch('update_status.php', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update online count if provided
                if (data.online_count) {
                    document.querySelector('.online-count').textContent = `${data.online_count} online`;
                }
            }
        })
        .catch(error => {
            console.error('Error updating online status:', error);
        });
    }

    // Add sound toggle button to the chat header
    function addSoundToggleButton() {
        const chatHeader = document.querySelector('.chat-header');
        const onlineIndicator = document.querySelector('.online-indicator');
        
        const soundToggle = document.createElement('div');
        soundToggle.className = 'sound-toggle';
        soundToggle.style.marginLeft = '15px';
        soundToggle.style.cursor = 'pointer';
        
        const soundIcon = document.createElement('i');
        soundIcon.className = areSoundsMuted() ? 'fas fa-volume-mute' : 'fas fa-volume-up';
        soundIcon.style.color = 'var(--text-secondary)';
        
        soundToggle.appendChild(soundIcon);
        
        soundToggle.addEventListener('click', function() {
            const isMuted = areSoundsMuted();
            localStorage.setItem('chat_sounds_muted', !isMuted);
            soundIcon.className = !isMuted ? 'fas fa-volume-mute' : 'fas fa-volume-up';
        });
        
        onlineIndicator.appendChild(soundToggle);
    }
    
    // Add the sound toggle button
    addSoundToggleButton();

    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // Resume polling when page becomes visible
            if (!isPolling) {
                isPolling = true;
                getNewMessages();
            }
            // Update online status immediately
            updateOnlineStatus();
        } else {
            // Optionally pause polling when page is not visible
            // isPolling = false;
        }
    });

    // Handle before unload
    window.addEventListener('beforeunload', function() {
        // Optionally notify server that user is leaving
        navigator.sendBeacon('update_status.php', '');
    });

    // Initialize lastTimestamp with the newest message from current page
    const initialMessages = document.querySelectorAll('.message-group');
    if (initialMessages.length > 0) {
        // Try to get the timestamp from the last message if it has been set
        const lastMessage = initialMessages[initialMessages.length - 1];
        const lastMessageId = lastMessage.getAttribute('data-message-id');
        lastTimestamp = Math.floor(Date.now() / 1000) - 60; // Default to 1 minute ago
    } else {
        lastTimestamp = Math.floor(Date.now() / 1000) - 86400; // 24 hours ago as fallback
    }

    // Start checking for new messages
    getNewMessages();

    // Update status immediately and then periodically
    updateOnlineStatus();
    setInterval(updateOnlineStatus, 60000); // Every minute
});
</script>
</body>
</html>