<?php
session_start();
include '../Login/db_config.php'; // Ensure this file exists and connects properly

// Fetch logged-in user name
$logged_in_user = $_SESSION['username'] ?? 'Guest';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Equipment - GYM SHARK</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
  <link rel="stylesheet" href="Trainer.css">
</head>
<body>
  <div class="container">
    <!-- Keeping the same sidebar from your original code -->
    <aside>
      <div class="top">
        <div class="logo">
          <h2>GYM</h2><h2><span class="danger">SHARK</span></h2>
        </div>
        <div class="close" id="close_btn">
          <span class="material-symbols-sharp">close</span>
        </div>
      </div>
      
      <div class="sidebar">
        <!-- Same sidebar content as your original -->
         
        <a href="index.php">
            <span class="material-symbols-sharp">grid_view </span>
            <h3>Dashboard</h3>
         </a>
         <a href="member.php">
            <span class="material-symbols-sharp">person_outline </span>
           <h3>Members</h3>
            <span class="msg_count">69</span>
         </a>
         <a href="growth.php">
            <span class="material-symbols-sharp">insights </span>
            <h3>Growth</h3>
         </a>
         <a href="#" class="active">
            <span class="material-symbols-sharp">Person </span>
            <h3>Trainers</h3>
            <span class="msg_count">14</span>
         </a>
         <a href="equipmentpage.php">
            <span class="material-symbols-sharp">receipt_long </span>
            <h3>Equipments</h3>
         </a>
         
         <a href="settings.php">
            <span class="material-symbols-sharp">settings </span>
            <h3>Settings</h3>
         </a>
         <a href="addeq.php">
            <span class="material-symbols-sharp">add </span>
            <h3>Add Equipment</h3>
         </a>
         <a href="..Login/logout.php">
            <span class="material-symbols-sharp">logout </span>
            <h3>logout</h3>
         </a>

      </div>
    </aside>
    <div class="trainers-container">
        <h1>Trainers</h1>
        <div class="trainer-cards">
          <div class="trainer-card">
            <h3>Nathan Johncy</h3>
            <p>Phone: 555-1234</p>
            <p>Active: 5 years</p>
            <p>Salary: $50,000</p>
          </div>
          <div class="trainer-card">
            <h3>Jhonci natan</h3>
            <p>Phone: 555-5678</p>
            <p>Active: 8 years</p>
            <p>Salary: $60,000</p>
          </div>
          <div class="trainer-card">
            <h3>johnson nathan Johncy</h3>
            <p>Phone: 555-9012</p>
            <p>Active: 3 years</p>
            <p>Salary: $45,000</p>
          </div>
        </div>
      </div>

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
                   <p><b><?php echo htmlspecialchars($logged_in_user); ?></b></p>
                   <p>Admin</p>
                   <small class="text-muted"></small>
               </div>
        </div>
      </div>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>