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
  <link rel="stylesheet" href="addeq.css">
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
         <a href="Trainer.php">
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
         <a href="addeq.php" class="active">
            <span class="material-symbols-sharp">add </span>
            <h3>Add Equipment</h3>
         </a>
         <a href="..Login/logout.php">
            <span class="material-symbols-sharp">logout </span>
            <h3>logout</h3>
         </a>

      </div>
    </aside>

    <main>
      <h1>Add New Equipment</h1>


      <div class="add-equipment-form">
        <div class="form-card">
          <form>
            <div class="form-group">
              <label for="equipment-name">Equipment Name</label>
              <input type="text" id="equipment-name" name="equipment-name" required>
            </div>

            <div class="form-group">
              <label for="product-number">Product Number</label>
              <input type="text" id="product-number" name="product-number" required>
            </div>

            <div class="form-group">
              <label for="purchase-date">Purchase Date</label>
              <input type="date" id="purchase-date" name="purchase-date" required>
            </div>

            <div class="form-group">
              <label for="equipment-status">Status</label>
              <select id="equipment-status" name="equipment-status" required>
                <option value="active">Active</option>
                <option value="maintenance">Under Maintenance</option>
                <option value="out-of-order">Out of Order</option>
              </select>
            </div>

            <div class="form-group">
              <label for="equipment-description">Description</label>
              <textarea id="equipment-description" name="equipment-description" rows="4"></textarea>
            </div>

            <div class="form-buttons">
              <button type="submit" class="btn-primary">Add Equipment</button>
              <button type="reset" class="btn-secondary">Clear Form</button>
            </div>
          </form>
        </div>
      </div>
    </main>

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