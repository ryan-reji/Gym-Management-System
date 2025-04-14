<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection might be included here, but you have a separate db.php file
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Booking System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/MINI_PROJECT2/assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/MINI_PROJECT2/index.php">Trainer Booking</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <!--<a class="nav-link" href="/MINI_PROJECT2/user/trainer/trainers.php">Find Trainers</a>-->
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/MINI_PROJECT2/user/my-bookings.php">My Bookings</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="/MINI_PROJECT2/Login/logout.php">logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                My Account
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="/MINI_PROJECT2/user/profile.php">Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/MINI_PROJECT2/logout.php">Logout</a>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main Content Container -->
    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success']; 
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>