<?php
session_start();
include "db_config.php";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['username']) && isset($_POST['password'])) {
    function validate($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $user_name = validate($_POST['username']);
    $password = validate($_POST['password']);

    if (empty($user_name) || empty($password)) {
        header("Location: index.php?error=Username and Password required");
        exit();
    }

    // ✅ Check for users
    $stmt = $conn->prepare("SELECT id, FirstName, LastName, number, gender, email, dob, username, password_hash FROM users WHERE username = ?");
    if (!$stmt) {
        die("SQL Error (Users Query): " . $conn->error);
    }
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password_hash']; // ✅ Use correct column

        if (password_verify($password, $stored_password)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['FirstName'] = $row['FirstName'];
            $_SESSION['LastName'] = $row['LastName'];
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['number'] = $row['number'];

            // ✅ Check if user has an active subscription
            $user_id = $row['id'];
            $subscription_stmt = $conn->prepare("SELECT * FROM plan_bookings WHERE user_id = ? AND CURRENT_DATE BETWEEN start_date AND end_date LIMIT 1");
            if (!$subscription_stmt) {
                die("SQL Error (Plan Bookings Query): " . $conn->error);
            }
            $subscription_stmt->bind_param("i", $user_id);
            $subscription_stmt->execute();
            $subscription_result = $subscription_stmt->get_result();

            if ($subscription_result->num_rows > 0) {
                header("Location: ../Gym User Management");
            } else {
                header("Location: ../plan_section/index.html");
            }
            exit();
        }
    }
    $stmt->close();

    // ✅ Check for trainers
$stmt = $conn->prepare("SELECT trainer_id, FirstName, trainer_username, password_hash FROM trainers WHERE trainer_username = ?");
if (!$stmt) {
    die("SQL Error (Trainers Query): " . $conn->error);
}
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $stored_password = $row['password_hash'];

    // ✅ Check if stored password is hashed or plain text
    if (password_verify($password, $stored_password) || $password === $stored_password) {
        // If it's either hashed or plain text and matches, log in the trainer
        $_SESSION['trainer_id'] = $row['trainer_id'];
        $_SESSION['trainer_name'] = $row['FirstName'];
        $_SESSION['trainer_username'] = $row['trainer_username'];

        // ✅ Upgrade plain text passwords to hashed
        if ($password === $stored_password) {
            $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE trainers SET password_hash = ? WHERE trainer_id = ?");
            $update_stmt->bind_param("si", $new_hashed_password, $row['trainer_id']);
            $update_stmt->execute();
            $update_stmt->close();
        }

        header("Location: ../trainer/dashboard.php");
        exit();
    }
}
$stmt->close();



    // ✅ Check for admin
    $stmt = $conn->prepare("SELECT admin_id, name, password_hash FROM admin WHERE admin_id = ?");
    if (!$stmt) {
        die("SQL Error (Admin Query): " . $conn->error);
    }
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password_hash'];
    
        // Check if stored password is hashed (assuming hashed passwords start with "$2y$")
        if (password_needs_rehash($stored_password, PASSWORD_DEFAULT) || password_verify($password, $stored_password)) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['username'] = $row['name'];
            header("Location: ../admin/index.php");
            exit();
        } elseif ($password === $stored_password) { // Plain text password check
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['username'] = $row['name'];
            header("Location: ../admin/index.php");
            exit();
        }
    }
    $stmt->close();
    

    // ❌ Authentication failed
    header("Location: index.php?error=Incorrect Credentials");
    exit();
} else {
    header("Location: index.php?error=Missing Fields");
    exit();
}
?>
