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
  <title>Equipment - GYM SHARK</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
  <link rel="stylesheet" href="styles.css">
  <style>
    .equipment-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .equipment-card {
      background: var(--clr-white);
      padding: var(--card-padding);
      border-radius: var(--card-border-radius);
      box-shadow: var(--box-shadow);
      transition: all 0.3s ease;
    }

    .equipment-card:hover {
      box-shadow: none;
    }

    .equipment-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .equipment-status {
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius-1);
      font-size: 0.8rem;
    }

    .status-active {
      background: var(--clr-success);
      color: var(--clr-white);
    }

    .status-maintenance {
      background: var(--clr-warnig);
      color: var(--clr-white);
    }

    .status-outoforder {
      background: var(--clr-danger);
      color: var(--clr-white);
    }

    .equipment-info {
      margin: 1rem 0;
    }

    .equipment-info p {
      margin: 0.5rem 0;
      color: var(--clr-dark-variant);
    }

    .equipment-actions {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 1rem;
    }

    .btn {
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius-1);
      cursor: pointer;
      font-size: 0.8rem;
    }

    .btn-edit {
      background: var(--clr-primary);
      color: var(--clr-white);
    }

    .btn-delete {
      background: var(--clr-danger);
      color: var(--clr-white);
    }

    .filters {
      display: flex;
      gap: 1rem;
      margin: 1rem 0;
    }

    .filter-select {
      padding: 0.5rem;
      border-radius: var(--border-radius-1);
      background: var(--clr-white);
      color: var(--clr-dark);
      border: 1px solid var(--clr-info-light);
    }
  </style>
</head>
<body>
  <div class="container">
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
        <a href="index.php">
          <span class="material-symbols-sharp">grid_view</span>
          <h3>Dashboard</h3>
        </a>
        <a href="member.php">
          <span class="material-symbols-sharp">person_outline</span>
          <h3>Members</h3>
          <span class="msg_count">69</span>
        </a>
        <a href="growth.php">
          <span class="material-symbols-sharp">insights</span>
          <h3>Growth</h3>
        </a>
        <a href="Trainer.php">
          <span class="material-symbols-sharp">Person</span>
          <h3>Trainers</h3>
          <span class="msg_count">14</span>
        </a>
        <a href="#" class="active">
          <span class="material-symbols-sharp">receipt_long</span>
          <h3>Equipments</h3>
        </a>
        <a href="settings.php">
          <span class="material-symbols-sharp">settings</span>
          <h3>Settings</h3>
        </a>
        <a href="addeq.php">
          <span class="material-symbols-sharp">add</span>
          <h3>Add Equipment</h3>
        </a>
        <a href="..Login/logout.php">
          <span class="material-symbols-sharp">logout</span>
          <h3>logout</h3>
        </a>
      </div>
    </aside>

    <main>
      <h1>Equipment Management</h1>


      <div class="filters">
        <select class="filter-select">
          <option value="all">All Status</option>
          <option value="active">Active</option>
          <option value="maintenance">Under Maintenance</option>
          <option value="outoforder">Out of Order</option>
        </select>
      </div>

      <div class="equipment-grid">
        <!-- Example Equipment Cards -->
        <div class="equipment-card">
          <div class="equipment-header">
            <h3>Treadmill</h3>
            <span class="equipment-status status-active">Active</span>
          </div>
          <div class="equipment-info">
            <p><strong>Product Number:</strong> TM001</p>
            <p><strong>Purchase Date:</strong> 2024-01-15</p>
            <p><strong>Description:</strong> Commercial grade treadmill with incline function</p>
          </div>
          <div class="equipment-actions">
            <button class="btn btn-edit">Edit</button>
            <button class="btn btn-delete">Delete</button>
          </div>
        </div>

        <div class="equipment-card">
          <div class="equipment-header">
            <h3>Rowing Machine</h3>
            <span class="equipment-status status-maintenance">Maintenance</span>
          </div>
          <div class="equipment-info">
            <p><strong>Product Number:</strong> RM002</p>
            <p><strong>Purchase Date:</strong> 2023-11-20</p>
            <p><strong>Description:</strong> Water resistance rowing machine</p>
          </div>
          <div class="equipment-actions">
            <button class="btn btn-edit">Edit</button>
            <button class="btn btn-delete">Delete</button>
          </div>
        </div>

        <div class="equipment-card">
          <div class="equipment-header">
            <h3>Leg Press</h3>
            <span class="equipment-status status-outoforder">Out of Order</span>
          </div>
          <div class="equipment-info">
            <p><strong>Product Number:</strong> LP003</p>
            <p><strong>Purchase Date:</strong> 2023-12-05</p>
            <p><strong>Description:</strong> 45-degree leg press machine</p>
          </div>
          <div class="equipment-actions">
            <button class="btn btn-edit">Edit</button>
            <button class="btn btn-delete">Delete</button>
          </div>
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