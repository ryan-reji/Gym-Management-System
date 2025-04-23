<?php
// Start session
session_start();


// Add this to ensure sessions are working
if (ini_get('session.auto_start')) {
    session_write_close();
}

// Database connection settings (same as in live_chat.php)
$dbHost = "localhost";
$dbUser = "root"; // Change these credentials
$dbPass = ""; // Change these credentials
$dbName = "gymshark_chat";      // Change if needed

// Create database connection
require_once 'config.php';
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, 3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbName";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbName);

// Create users table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role VARCHAR(20) NOT NULL DEFAULT 'member',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating table: " . $conn->error);
}

// Handle login form submission
// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        
        if (!$stmt->bind_param("s", $username)) {
            die("Bind failed: " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                header("Location: live_chat.php");
                exit;
            } else {
                $error_message = "Invalid password!";
            }
        } else {
            $error_message = "Username not found!";
        }
        $stmt->close();
    } else {
        $error_message = "Please enter both username and password!";
    }
}

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
   // Change from fixed role to getting from form
$role = $_POST['role']; // Instead of $role = "member";
    
    // Basic validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // You had "FILTER_VALIDATE_EMAIL" misspelled as "FILTER_VALIDATE_EMAIL"
        $errors[] = "Valid email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username already exists";
    }
    $stmt->close();
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists";
    }
    $stmt->close();
    
     // If no errors, insert the user
     if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            
            header("Location: live_chat.php");
            exit;
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymShark - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Using the same CSS variables as the chat page for consistency */
        :root {
            --primary-color: #b74b4b;
            --primary-hover: #a43a3a;
            --background-dark: #121212;
            --card-bg: rgba(30, 30, 30, 0.75);
            --card-hover: rgba(40, 40, 40, 0.8);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-tertiary: rgba(255, 255, 255, 0.5);
            --border-radius: 12px;
            --transition-speed: 0.3s;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.15);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.25);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-dark);
            background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            width: 100%;
            max-width: 420px;
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        
        .auth-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            background: rgba(0, 0, 0, 0.2);
        }
        
        .logo-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-right: 1rem;
        }
        
        .auth-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .tab {
            flex: 1;
            text-align: center;
            padding: 1rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-secondary);
            transition: all var(--transition-speed);
            position: relative;
        }
        
        .tab.active {
            color: var(--text-primary);
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
        }
        
        .tab-content {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: inherit;
            font-size: 1rem;
            transition: border-color var(--transition-speed);
        }
        
        .form-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color var(--transition-speed);
        }
        
        .btn:hover {
            background-color: var(--primary-hover);
        }
        
        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-tertiary);
        }
        
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            margin-left: 0.25rem;
        }
        
        .error-message {
            background-color: rgba(220, 38, 38, 0.1);
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(220, 38, 38, 0.2);
        }
        
        .error-message ul {
            margin-left: 1.5rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <i class="fas fa-dumbbell logo-icon"></i>
            <h1>GymShark</h1>
        </div>
        
        <div class="tabs">
            <div class="tab active" id="login-tab">Login</div>
            <div class="tab" id="register-tab">Register</div>
        </div>
        
        <div class="tab-content">
            <!-- Login Form -->
            <form id="login-form" method="post" action="">
                <?php if (isset($error_message) && !isset($_POST['register'])): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="login-username" class="form-label">Username</label>
                    <input type="text" id="login-username" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="login-password" class="form-label">Password</label>
                    <input type="password" id="login-password" name="password" class="form-input" required>
                </div>
              
                
                <button type="submit" name="login" class="btn">Login</button>
                
                <div class="form-footer">
                    Don't have an account? <a href="#" id="show-register">Register now</a>
                </div>
            </form>
            
            <!-- Registration Form (Hidden by default) -->
            <form id="register-form" method="post" action="" style="display: none;">
                <?php if (isset($errors) && !empty($errors)): ?>
                <div class="error-message">
                    <strong>Please fix the following errors:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="register-username" class="form-label">Username</label>
                    <input type="text" id="register-username" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="register-email" class="form-label">Email</label>
                    <input type="email" id="register-email" name="email" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="register-password" class="form-label">Password</label>
                    <input type="password" id="register-password" name="password" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="register-confirm-password" class="form-label">Confirm Password</label>
                    <input type="password" id="register-confirm-password" name="confirm_password" class="form-input" required>
                </div>
                <div class="form-group">
                    
    <label for="register-role" class="form-label">Account Type</label>
    <select id="register-role" name="role" class="form-input" required>
        <option value="member">Member</option>
        <option value="trainer">Trainer</option>
    </select>
</div>
                <button type="submit" name="register" class="btn">Register</button>
                
                <div class="form-footer">
                    Already have an account? <a href="#" id="show-login">Login now</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginTab = document.getElementById('login-tab');
            const registerTab = document.getElementById('register-tab');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const showRegister = document.getElementById('show-register');
            const showLogin = document.getElementById('show-login');
            
            // Switch to register tab
            function showRegisterTab() {
                loginTab.classList.remove('active');
                registerTab.classList.add('active');
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            }
            
            // Switch to login tab
            function showLoginTab() {
                registerTab.classList.remove('active');
                loginTab.classList.add('active');
                registerForm.style.display = 'none';
                loginForm.style.display = 'block';
            }
            
            // Event listeners
            loginTab.addEventListener('click', showLoginTab);
            registerTab.addEventListener('click', showRegisterTab);
            showRegister.addEventListener('click', function(e) {
                e.preventDefault();
                showRegisterTab();
            });
            showLogin.addEventListener('click', function(e) {
                e.preventDefault();
                showLoginTab();
            });
            
            // Set initial active tab based on form submission
            <?php if (isset($_POST['register'])): ?>
            showRegisterTab();
            <?php endif; ?>
        });
    </script>
</body>
</html>