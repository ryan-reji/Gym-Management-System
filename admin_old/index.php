<?php
session_start();
include '../Login/db_config.php'; // Ensure this file exists and connects properly

// Fetch logged-in user name
$logged_in_user = $_SESSION['username'] ?? 'Guest';

// Fetch total members count from the database
$result = $conn->query("
    SELECT COUNT(DISTINCT user_id) AS total_members 
    FROM plan_bookings 
    WHERE end_date >= CURDATE()
");

$total_users = $result->fetch_assoc()['total_members'] ?? 0;

// Fetch total trainers count from the database
$result = $conn->query("SELECT COUNT(*) AS total_trainers FROM trainers");
$total_trainers = $result->fetch_assoc()['total_trainers'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UI/UX</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
  <link rel="stylesheet" href="styles.css">
</head>
<body>
    <marquee style="color:white;" behaviour ="alternate" scrollamount="5" bgcolor="black" >
Welcome Admin!
    </marquee>
   <div class="container">
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

            <a href="#" class="active">
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

      <main>
           <h1>Admin Dashboard</h1>


           <div class="insights">
            <!-- Total Members -->
            <div class="members">
               <span class="material-symbols-sharp">group</span>
               <div class="middle">
                  <div class="left">
                     <h3>Total Members</h3>
                     <h1><?php echo $total_users; ?></h1>
                  </div>
                  <div class="progress">
                     <svg>
                        <circle id="members-circle" r="30" cy="40" cx="40"></circle>
                     </svg>
                     <div class="number"><p>60%</p></div>
                  </div>
               </div>
               <small>Last 24 Hours</small>
            </div>
         
            <!-- Equipment in Use -->
            <div class="Equipment">
               <span class="material-symbols-sharp">Fitness_Center</span>
               <div class="middle">
                  <div class="left">
                     <h3>Equipment In Use</h3>
                     <h1>10</h1>
                  </div>
                  <div class="progress">
                     <svg>
                        <circle id="equipment-circle" r="30" cy="40" cx="40"></circle>
                     </svg>
                     <div class="number"><p>94%</p></div>
                  </div>
               </div>
               <small>Last 24 Hours</small>
            </div>
         
            <!-- Number of Trainers -->
            <div class="income">
               <span class="material-symbols-sharp">group</span>
               <div class="middle">
                  <div class="left">
                     <h3>No Of Trainers</h3>
                     <h1><?php echo $total_trainers; ?></h1>
                  </div>
                  <div class="progress">
                     <svg>
                        <circle id="trainers-circle" r="30" cy="40" cx="40"></circle>
                     </svg>
                     <div class="number"><p>95%</p></div>
                  </div>
               </div>
               <small>Last 24 Hours</small>
            </div>
         </div>
         <!-- end insights -->

      <div class="Gym_Equipment">
         <h2>Gym Equipments</h2>
         <table class="equipment-table"> 
             <thead>
              <tr>
                <th>Equipment Name</th>
                <th>Product Number</th>
                <th>Status</th>
              </tr>
             </thead>
              <tbody>
                 <tr>
                   <td>Rowing Machine</td>
                   <td>4563</td>
                   <td><span class="status active">ACTIVE</span></td>
                 </tr>
                 <tr>
                  <td>Treadmill</td>
                  <td>4563</td>
                  <td><span class="status warning">OUT OF ORDER</span></td>
                 </tr>
                 <tr>
                  <td>Leg Press</td>
                  <td>4563</td>
                  <td><span class="status active">ACTIVE</span></td>
                 </tr>
                 <tr>
                  <td>Exercise Bike</td>
                  <td>4563</td>
                  <td><span class="status active">ACTIVE</span></td>
                 </tr>
              </tbody>
         </table>
         <a href="#" class="show-all">Show All</a>
      </div>

      </main>
      <!--end main -->

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
               <div class="profile-photo">
                 -->
               </div>
            </div>
        </div>
<!-- Recent Updates section -->
<div class="recent_updates">
   <h2>Recent Updates</h2>
   <div class="updates">
      <?php include 'backend/fetch_updates.php'; ?>
   </div>
 </div>
 
 <!-- Membership Status (formerly Sales Analytics) section -->
 <div class="sales-analytics">
   <h2>Membership Status</h2>
   
   <div class="item">
     <div class="icon">
       <span class="material-symbols-sharp">person</span>
     </div>
     <div class="right_text">
       <div class="info">
         <h3>Sam Renewed subscription</h3>
         <small class="text-muted">Last 24 Hours</small>
       </div>
     
     </div>
   </div>
   
   <div class="item">
     <div class="icon">
       <span class="material-symbols-sharp">person</span>
     </div>
     <div class="right_text">
       <div class="info">
         <h3>Savio subscription expired</h3>
         <small class="text-muted">Last 24 Hours</small>
       </div>
      
     </div>
   </div>
   
   <div class="item add_product">
     <div>
       <span class="material-symbols-sharp">add</span>
       <h3>Add Membership</h3>
     </div>
   </div>
 </div>

   <script src="script.js"></script>
</body>
</html>