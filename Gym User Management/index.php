<?php
session_start(); // Start the session

require_once 'config.php'; // Ensure database connection
require_once 'utils.php'; // Include utility functions



// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    die("Error: User not logged in."); // You can also redirect to the login page
}

$user_id = $_SESSION['id']; // Get the logged-in user's ID


// Get home data
$user_id = $_SESSION['id']; // Ensure you retrieve the logged-in user's ID
$home_data = get_home_data($user_id);
$last_activity_date = $home_data['last_activity_date'];
$upcoming_class = $home_data['upcoming_class'];
$message = $home_data['message'] ?? null;

$recent_activities = $home_data['recent_activities'];
$attendance_data = get_attendance_data();

// Calculate days since last activity
$today = new DateTime();
$days_left = $last_activity_date ? $today->diff(new DateTime($last_activity_date))->days : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/script.js"></script>
</head>
<body>
    <div class="container">
        <!-- Mobile Navigation Toggle -->
        <div class="mobile-nav-toggle">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar">
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
                <li class="active">
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="../user/trainer/">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="members.php">
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
                    <a href="bmi.php">
                        <i class="fas fa-weight"></i>
                        <span>BMI Scale</span>
                    </a>
                </li>
                <li>
                    <a href="live_chat/Live_chat.php">
                        <i class="fas fa-weight"></i>
                        <span>Live Chat</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../Login/logout.php" class="logout-button">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="greeting">
                    <h1>Dashboard</h1>
                    <p>Let's crush today's goals!</p>
                </div>
                <div class="header-actions">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="date-display">
                        <i class="fas fa-calendar"></i>
                        <span id="current-date">April 5, 2025</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Last Session Card -->
                <div class="card last-session">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Last Gym Session</h3>
                    </div>
                    <div class="card-content" id="last-session-content">
                        <div class="session-details">
                            <div class="session-info">
                            <p>
    <?php 
        if ($days_left !== null) {
            if ($days_left == 0) {
                echo "Today";
            } else {
                echo "$days_left days ago";
            }
        } else {
            echo $message; // e.g., "You haven't visited the gym yet!"
        }
    ?>
</p>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Upcoming Class Card -->
                <div class="card upcoming-class">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-day"></i> Upcoming Class</h3>
                    </div>
                    <div class="card-content" id="upcoming-class-content">
                        <div class="class-details">
                            <p>
                                <?php 
                                if ($upcoming_class && isset($upcoming_class['session_date'], $upcoming_class['session_time'])) {
                                    echo "Next session on " . $upcoming_class['session_date'] . " at " . $upcoming_class['session_time'];
                                } else {
                                    echo "No upcoming sessions.";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Weekly Stats Card -->
            <div class="card weekly-stats">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Weekly Stats</h3>
                </div>
                <div class="card-content stats-display">
                    <div class="stat-item">
                        <div class="stat-circle">
                            <svg width="100" height="100">
                                <circle class="bg-circle" cx="50" cy="50" r="40"></circle>
                                <circle class="progress-circle" cx="50" cy="50" r="40" 
                                    style="stroke-dashoffset: calc(251.2 - (251.2 * 65) / 100);"></circle>
                                <text x="50" y="55" class="progress-text">65%</text>
                            </svg>
                        </div>
                        <p>Goal Progress</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">4</div>
                        <p>Workouts</p>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">180</div>
                        <p>Minutes</p>
                    </div>
                </div>
            </div><br>

            <!-- Recent Activity Card -->
            <div class="card recent-activity">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> Recent Activity</h3>
                </div>
                <div class="card-content">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Apr 1, 2025</td>
                                <td>Strength Training</td>
                                <td>45 mins</td>
                            </tr>
                            <tr>
                                <td>Mar 30, 2025</td>
                                <td>Cardio</td>
                                <td>30 mins</td>
                            </tr>
                            <tr>
                                <td>Mar 28, 2025</td>
                                <td>Yoga</td>
                                <td>60 mins</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div><br>

            <!-- Attendance Heatmap Card -->
            <div class="card attendance-heatmap">
                <div class="card-header">
                    <h3><i class="fas fa-calendar-check"></i> Attendance Heat Map</h3>
                    <div class="month-selector">
                        <button class="month-nav" id="prev-month"><i class="fas fa-chevron-left"></i></button>
                        <span id="current-month">April 2025</span>
                        <button class="month-nav" id="next-month"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="card-content">
                    <div class="heatmap-legend">
                        <div class="legend-item">
                            <span class="legend-color attendance-0"></span>
                            <span>No Visit</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color attendance-1"></span>
                            <span>Light</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color attendance-2"></span>
                            <span>Medium</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color attendance-3"></span>
                            <span>Intense</span>
                        </div>
                    </div>
                    <div class="heatmap-container">
                    <div class="heatmap-weekdays">
                            <span>Sun</span>
                            <span>Mon</span>
                            <span>Tue</span>
                            <span>Wed</span>
                            <span>Thu</span>
                            <span>Fri</span>
                            <span>Sat</span>
                        </div>  
                        <div class="heatmap-grid" id="heatmap-grid">
                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="action-container">
                <a href="bookings.php" class="book-button pulse-effect">
                    <i class="fas fa-plus"></i> Book a Session
                </a>
            </div>
        </div>
    </div>

    <!-- Floating Chat System -->
    <div class="chat-system">
        <!-- Chat Toggle Button -->
        <button class="chat-toggle-btn pulse-effect" id="chatToggleBtn">
            <i class="fas fa-comments"></i>
            <span class="chat-notification-badge">2</span>
        </button>

        <!-- Chat Window -->
        <div class="chat-window glass hidden" id="chatWindow">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-title">
                    <i class="fas fa-users"></i>
                    <h3>GymShark Chat</h3>
                </div>
                <div class="chat-actions">
                    <button class="chat-action-btn" id="chatMinimizeBtn" title="Minimize">
                        <i class="fas fa-minus"></i>
                    </button>
                    <button class="chat-action-btn" id="chatCloseBtn" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Tabs -->
            <div class="chat-tabs">
                <button class="chat-tab active" data-tab="public">
                    <i class="fas fa-globe"></i> Community
                </button>
                <button class="chat-tab" data-tab="trainers">
                    <i class="fas fa-dumbbell"></i> Trainers
                </button>
                <button class="chat-tab" data-tab="direct">
                    <i class="fas fa-user"></i> Direct
                </button>
            </div>

            <!-- Chat Content -->
            <div class="chat-content" id="chatContent">
                <!-- Message loading indicator -->
                <div class="chat-loading">
                    <div class="loading-spinner"></div>
                    <p>Loading messages...</p>
                </div>

                <!-- Messages Container -->
                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be loaded here via JavaScript -->
                </div>
            </div>

            <!-- User Typing Indicator -->
            <div class="typing-indicator hidden" id="typingIndicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <span>Someone is typing...</span>
            </div>

            <!-- Chat Input -->
            <div class="chat-input-container">
                <div class="chat-toolbar">
                    <button class="toolbar-btn" title="Emoji">
                        <i class="fas fa-smile"></i>
                    </button>
                    <button class="toolbar-btn" title="Attach">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>
                <div class="chat-input-wrapper">
                    <textarea id="chatInput" placeholder="Type your message here..." rows="1"></textarea>
                    <button id="chatSendBtn" class="chat-send-btn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Toggle mobile sidebar
        document.querySelector('.mobile-nav-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.add('active');
        });
        
        document.querySelector('.close-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
        });

        // Chat system functionality
        document.getElementById('chatToggleBtn').addEventListener('click', function() {
            document.getElementById('chatWindow').classList.toggle('hidden');
        });
        
        document.getElementById('chatCloseBtn').addEventListener('click', function() {
            document.getElementById('chatWindow').classList.add('hidden');
        });
        
        document.getElementById('chatMinimizeBtn').addEventListener('click', function() {
            document.getElementById('chatWindow').classList.add('hidden');
        });
        
        // Chat tab switching
        document.querySelectorAll('.chat-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.chat-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Load messages for selected tab
                const tabType = this.getAttribute('data-tab');
                loadChatMessages(tabType);
            });
        });
        
        // Chat input handling
        const chatInput = document.getElementById('chatInput');
        const chatSendBtn = document.getElementById('chatSendBtn');
        
        chatInput.addEventListener('input', function() {
            chatSendBtn.disabled = this.value.trim().length === 0;
            
            // Auto-resize textarea
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Function to load chat messages via AJAX
        function loadChatMessages(tabType) {
            const messagesContainer = document.getElementById('chatMessages');
            const loadingIndicator = document.querySelector('.chat-loading');
            
            // Show loading indicator
            messagesContainer.classList.add('hidden');
            loadingIndicator.classList.remove('hidden');
            
            // Simulate AJAX request
            setTimeout(function() {
                // Hide loading indicator
                loadingIndicator.classList.add('hidden');
                messagesContainer.classList.remove('hidden');
                
                // In a real implementation, you'd fetch messages from the server
                // For now, just show a placeholder message
                messagesContainer.innerHTML = '<div class="message-notice">Loading messages for ' + tabType + ' chat...</div>';
            }, 1000);
        }
        
        // Initialize chat with public tab selected
        loadChatMessages('public');
        
        // Add tooltip functionality
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = this.getAttribute('data-tooltip');
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
                tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
            });
            
            element.addEventListener('mouseleave', function() {
                const tooltip = document.querySelector('.tooltip');
                if (tooltip) tooltip.remove();
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
    let currentDate = new Date();
    let selectedYear = currentDate.getFullYear();
    let selectedMonth = currentDate.getMonth() + 1; // Months are 0-based

    function fetchAttendanceData() {
        const formattedMonth = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}`;

        fetch(`backend/get_attendance.php?month=${formattedMonth}`)
            .then(response => response.json())
            .then(data => updateHeatmap(data))
            .catch(error => console.error("Error fetching attendance data:", error));
    }

    function updateHeatmap(data) {
    const heatmapGrid = document.getElementById("heatmap-grid");
    heatmapGrid.innerHTML = ""; // Clear previous grid

    const firstDay = new Date(selectedYear, selectedMonth - 1, 1); // JS months are 0-based
    const daysInMonth = new Date(selectedYear, selectedMonth, 0).getDate();
    const startWeekday = firstDay.getDay(); // 0 (Sun) to 6 (Sat)

    // ➕ Add empty placeholders before the 1st day
    for (let i = 0; i < startWeekday; i++) {
        const emptyCell = document.createElement("div");
        emptyCell.className = "heatmap-day empty-day"; // Use a different class for style
        heatmapGrid.appendChild(emptyCell);
    }

    // 🗓 Fill actual day boxes
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
        const intensityClass = data[dateStr] || "attendance-0";

        const dayCell = document.createElement("div");
        dayCell.className = `heatmap-day ${intensityClass}`;
        dayCell.textContent = day;
        dayCell.setAttribute("data-tooltip", `${dateStr}: ${intensityClass.replace("attendance-", "")} workout`);

        heatmapGrid.appendChild(dayCell);
    }

    document.getElementById("current-month").textContent = `${getMonthName(selectedMonth)} ${selectedYear}`;
}


    function getMonthName(monthIndex) {
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        return monthNames[monthIndex - 1];
    }

    document.getElementById("prev-month").addEventListener("click", function () {
        selectedMonth--;
        if (selectedMonth < 1) {
            selectedMonth = 12;
            selectedYear--;
        }
        fetchAttendanceData();
    });

    document.getElementById("next-month").addEventListener("click", function () {
        selectedMonth++;
        if (selectedMonth > 12) {
            selectedMonth = 1;
            selectedYear++;
        }
        fetchAttendanceData();
    });

    fetchAttendanceData();
});

    </script>
</body>
</html>