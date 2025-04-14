<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['admin_id'])) {
    echo "Not logged in";
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$query = "SELECT name FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_name);
$stmt->fetch();
$stmt->close();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch total members whose plans have not expired
// Fetch total active members (from plan_bookings)
$getMembersQuery = "SELECT COUNT(DISTINCT user_id) AS active_members 
                    FROM plan_bookings 
                    WHERE end_date >= CURDATE()";

$result = $conn->query($getMembersQuery);
$todaysMembersCount = ($result) ? $result->fetch_assoc()['active_members'] : 0;

// Fetch currently checked-in users (from gym_attendance)
$getUsersInGymQuery = "SELECT COUNT(DISTINCT user_id) AS in_gym 
                       FROM gym_attendance 
                       WHERE check_out_time IS NULL 
                       AND DATE(date) = CURDATE()";

$result = $conn->query($getUsersInGymQuery);
$usersInGym = ($result) ? $result->fetch_assoc()['in_gym'] : 0;

// Calculate gym occupancy percentage
$maxCapacity = 100;
$membersGymPercentage = min(round(($usersInGym / $maxCapacity) * 100, 2), 100);

// Return JSON response
echo json_encode([
    'active_members' => (int) $todaysMembersCount,
    'checked_in_members' => (int) $usersInGym,
    'occupancy_percentage' => ($membersGymPercentage >= 100) ? "Maxed Out" : $membersGymPercentage
]);

// Fetch total equipment count
$totalEquipment = 0;
$getTotalEquipmentQuery = "SELECT COUNT(*) AS total_equipment FROM equipment";
$result = $conn->query($getTotalEquipmentQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $totalEquipment = $row['total_equipment'];
} else {
    die("Error fetching total equipment: " . $conn->error);
}

// Fetch active equipment count
$activeEquipment = 0;
$getActiveEquipmentQuery = "SELECT COUNT(*) AS active_equipment 
                            FROM equipment 
                            WHERE status = 'active'";
$result = $conn->query($getActiveEquipmentQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $activeEquipment = $row['active_equipment'];
} else {
    die("Error fetching active equipment: " . $conn->error);
}

// Calculate equipment usage percentage
$equipmentUsagePercentage = ($totalEquipment > 0) ? round(($activeEquipment / $totalEquipment) * 100, 2) : 0;

// Fetch total trainers count
$totalTrainers = 0;
$getTotalTrainersQuery = "SELECT COUNT(*) AS total_trainers FROM trainers";
$result = $conn->query($getTotalTrainersQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $totalTrainers = $row['total_trainers'];
} else {
    die("Error fetching total trainers: " . $conn->error);
}

// Fetch trainers currently checked in
$currentTrainersCount = 0;
$getTrainersQuery = "SELECT COUNT(DISTINCT trainer_id) AS current_trainers 
                     FROM trainer_attendance 
                     WHERE check_out_time IS NULL";
$result = $conn->query($getTrainersQuery);
if ($result) {
    $row = $result->fetch_assoc();
    $currentTrainersCount = $row['current_trainers'];
} else {
    die("Error fetching trainers count: " . $conn->error);
}

// Calculate trainer presence percentage
$trainerGymPercentage = ($totalTrainers > 0) ? round(($currentTrainersCount / $totalTrainers) * 100, 2) : 0;

// Fetch equipment data
$sql = "SELECT * FROM equipment ORDER BY purchase_date DESC LIMIT 4";
$result = $conn->query($sql);


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
           <a href="index.php" class="active">  
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
           
           <a href="scanner.php" >
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

      <main>
           <h1>Admin Dashboard</h1>

           <div class="date">
             <input type="date" >
           </div>

           <div class="insights">
    <!-- Total Members -->
    <div class="members">
        <span class="material-symbols-sharp">group</span>
        <div class="middle">
            <div class="left">
                <h3>Total Members</h3>
                <h1><?php echo $todaysMembersCount; ?></h1>
            </div>
            <div class="progress">
    <svg width="80" height="80">
        <circle id="members-circle-bg" r="30" cy="40" cx="40" stroke-width="5" fill="transparent" stroke="#ddd"></circle>
        <circle id="members-circle" r="30" cy="40" cx="40" stroke-width="5" fill="transparent" stroke="#4a90e2" stroke-dasharray="188.4" stroke-dashoffset="188.4"></circle>
    </svg>
    <div class="number">
        <p id="members-circle-percentage"><?php echo $membersGymPercentage; ?>%</p>
    </div>
</div>
        </div>
        <small>Occupancy Percentage %</small>
    </div>

    <!-- Equipment in Use -->
    <!-- Equipment in Use -->
<div class="equipment"> <!-- Note lowercase class name here -->
    <span class="material-symbols-sharp">fitness_center</span>
    <div class="middle">
        <div class="left">
            <h3>Equipment In Use</h3>
            <h1><?php echo $totalEquipment; ?></h1>
        </div>
        <div class="progress">
            <svg width="80" height="80">
                <circle id="equipment-circle-bg" r="30" cy="40" cx="40" stroke-width="5" fill="transparent" stroke="#ddd"></circle>
                <circle id="equipment-circle" r="30" cy="40" cx="40" stroke-width="5" fill="transparent" stroke="#4a90e2" stroke-dasharray="188.4" stroke-dashoffset="188.4"></circle>
            </svg>
            <div class="number"><p><?php echo $equipmentUsagePercentage; ?>%</p></div>
        </div>
    </div>
    <small>Active Equipment %</small>
</div>

<!-- Number of Trainers -->
<div class="income">
    <span class="material-symbols-sharp">group</span>
    <div class="middle">
        <div class="left">
            <h3>No Of Trainers</h3>
            <h1><?php echo $totalTrainers; ?></h1>
        </div>
        <div class="progress">
            <svg width="80" height="80">
                <circle id="trainers-circle-bg" r="30" cy="40" cx="40" stroke-width="5" fill="transparent" stroke="#ddd"></circle>
                <circle id="trainers-circle" r="30" cy="40" cx="40" stroke-width="5" fill="transparent" stroke="#4a90e2" stroke-dasharray="188.4" stroke-dashoffset="188.4"></circle>
            </svg>
            <div class="number"><p><?php echo $trainerGymPercentage; ?>%</p></div>
        </div>
    </div>
    <small>Trainer active %</small>
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
              <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $statusClass = strtolower($row['status']) === 'active' ? 'active' : 
                                            (strtolower($row['status']) === 'maintenance' ? 'warning' : 'danger');
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['product_number']); ?></td>
                                    <td><span class="status <?php echo $statusClass; ?>"><?php echo strtoupper($row['status']); ?></span></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='3'>No equipment found</td></tr>";
                        }
                        ?>
              </tbody>
         </table>
         <a href="equipmentpage.php" class="show-all">Show All</a>
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
               <p><b><?php echo htmlspecialchars($admin_name); ?></b></p>
                   <p>Admin</p>
                   <small class="text-muted"></small>
               </div>
               <div class="profile-photo">
                
               </div>
            </div>
        </div>
<!-- Recent Updates section -->
<div class="recent_updates">
   <h2>Recent Updates</h2>
   <div class="updates">
     <div class="update">
       <div class="profile-photo">
         <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS_xO4UktfIe6YAE0bSQ1nbm8VJwY7gh5_NjA&s" alt=""/>
       </div>
       <div class="message">
         <p><b>Sam</b> Renewed monthly subscription</p>
         <small class="text-muted">2 Minutes Ago</small>
       </div>
     </div>
     <div class="update">
       <div class="profile-photo">
         <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWi4bk1s8Q2HPdq4fAPfLVKO6I4UrbUGW93w&s" alt=""/>
       </div>
       <div class="message">
         <p><b>Divine</b> Membership expired</p>
         <small class="text-muted">1 Hour Ago</small>
       </div>
     </div>
     <div class="update">
       <div class="profile-photo">
         <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS_xO4UktfIe6YAE0bSQ1nbm8VJwY7gh5_NjA&s" alt=""/>
       </div>
       <div class="message">
         <p><b>Ram</b> New membership</p>
         <small class="text-muted">3 Hours Ago</small>
       </div>
     </div>
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

 <script>
document.addEventListener('DOMContentLoaded', function() {
    
  function setCircleDashoffset(circleElement, percentage) {
    const radius = circleElement.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;
    
    circleElement.style.strokeDasharray = `${circumference}`;
    const offset = circumference - (circumference * percentage / 100);
    
    console.log("Offset for", circleElement.id, ":", offset); // Debugging line
    circleElement.style.strokeDashoffset = `${offset}`;
}

    // Function to update the members display
function updateMembersDisplay(count, percentage) {
    const membersCountElement = document.querySelector('.members .middle .left h1');
    const membersCircle = document.getElementById('members-circle');
    const membersNumberElement = document.querySelector('.members .number p');

    if (membersCountElement) {
        membersCountElement.textContent = count;
    }
    
    if (membersNumberElement) {
        membersNumberElement.textContent = (percentage === "Maxed Out") ? "100%" : `${percentage}%`;
    }

    if (membersCircle) {
        setCircleDashoffset(membersCircle, percentage === "Maxed Out" ? 100 : percentage);
    }
}


    
  
    // Function to update the equipment display
   function updateEquipmentDisplay(activeCount, totalCount) {
    const equipmentCountElement = document.querySelector('.equipment .middle .left h1');
    const equipmentCircle = document.getElementById('equipment-circle');
    const equipmentNumberElement = document.querySelector('.equipment .number p');
    
    if (equipmentCountElement) {
        equipmentCountElement.textContent = activeCount;
        
        // Calculate percentage based on active vs total equipment
        const percentage = totalCount > 0 ? 
            Math.min(Math.round((activeCount / totalCount) * 100), 100) : 0;
        
        if (equipmentNumberElement) {
            equipmentNumberElement.textContent = `${percentage}%`;
        }
        
        if (equipmentCircle) {
            setCircleDashoffset(equipmentCircle, percentage);
        }
    }
}
    
    // Function to update the trainers display
    function updateTrainersDisplay(count) {
        const trainersCountElement = document.querySelector('.income .middle .left h1');
        const trainersCircle = document.getElementById('trainers-circle');
        const trainersNumberElement = document.querySelector('.income .number p');
        
        if (trainersCountElement) {
            trainersCountElement.textContent = count;
            
            // Calculate percentage (assuming 20 is max capacity for trainers)
            const maxTrainerCapacity = 20;
            const percentage = Math.min(Math.round((count / maxTrainerCapacity) * 100), 100);
            
            if (trainersNumberElement) {
              trainersNumberElement.textContent = `${percentage}%`;
            }
            
            if (trainersCircle) {
                setCircleDashoffset(trainersCircle, percentage);
            }
        }
    }
    
    // Initialize with current values from PHP
    // For members
    const membersCount = parseInt(document.querySelector('.members .middle .left h1').textContent) || 0;
    updateMembersDisplay(membersCount);
    
    // For equipment
    const equipmentCount = parseInt(document.querySelector('.Equipment .middle .left h1').textContent) || 0;
    updateEquipmentDisplay(equipmentCount);
    
    // For trainers
    const trainersCount = parseInt(document.querySelector('.income .middle .left h1').textContent) || 0;
    updateTrainersDisplay(trainersCount);

    const activeEquipment = <?php echo $activeEquipment; ?>;
const totalEquipment = <?php echo $totalEquipment; ?>;
updateEquipmentDisplay(activeEquipment, totalEquipment);

    
    // Function to fetch the latest members count periodically
function refreshMembersCount() {
    fetch('get-members-data.php') // Fetches the correct PHP file
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Members Data:", data);
            
            if (data.active_members !== undefined && data.occupancy_percentage !== undefined) {
                updateMembersDisplay(data.active_members, data.occupancy_percentage);
                localStorage.setItem('currentMembersCount', data.active_members);
            } else {
                console.error("API response missing required keys");
            }
        })
        .catch(error => console.error('Error fetching members count:', error));
}

// Run on page load
refreshMembersCount();

// Auto-refresh every 30 seconds
setInterval(refreshMembersCount, 30000);


const storedActiveEquipment = localStorage.getItem('activeEquipmentCount');
const storedTotalEquipment = localStorage.getItem('totalEquipmentCount');
if (storedActiveEquipment && storedTotalEquipment) {
    updateEquipmentDisplay(parseInt(storedActiveEquipment), parseInt(storedTotalEquipment));
}
    
    // Function to fetch the latest equipment count periodically
    function refreshEquipmentCount() {
        fetch('equipment-tracking.php?action=get_active_equipment')
            .then(response => response.json())
            .then(data => {
                updateEquipmentDisplay(data.active_equipment);
                // Store for page refreshes
                localStorage.setItem('activeEquipmentCount', data.active_equipment);
            })
            .catch(error => console.error('Error fetching equipment count:', error));
    }
    
    // Function to fetch the latest trainer count periodically
    function refreshTrainersCount() {
        fetch('attendance-tracking.php?action=get_current_trainers')
            .then(response => response.json())
            .then(data => {
                updateTrainersDisplay(data.current_trainers);
                // Store for page refreshes
                localStorage.setItem('currentTrainersCount', data.current_trainers);
            })
            .catch(error => console.error('Error fetching trainer count:', error));
    }
    
    // Check localStorage for stored values
    const storedMembersCount = localStorage.getItem('currentMembersCount');
    if (storedMembersCount) {
        updateMembersDisplay(parseInt(storedMembersCount));
    }
    
    const storedEquipmentCount = localStorage.getItem('activeEquipmentCount');
    if (storedEquipmentCount) {
        updateEquipmentDisplay(parseInt(storedEquipmentCount));
    }
    
    const storedTrainersCount = localStorage.getItem('currentTrainersCount');
    if (storedTrainersCount) {
        updateTrainersDisplay(parseInt(storedTrainersCount));
    }
    
    // Refresh counts every 30 seconds
    setInterval(refreshMembersCount, 30000);
    setInterval(refreshEquipmentCount, 30000);
    setInterval(refreshTrainersCount, 30000);
    
    // Initial fetch if API endpoints are available
    try {
        refreshMembersCount();
        refreshEquipmentCount();
        refreshTrainersCount();
    } catch (error) {
        console.log('Using initial values from PHP');
    }
    
    // Theme toggler functionality
    const themeToggler = document.querySelector('.theme-toggler');
    if (themeToggler) {
        themeToggler.addEventListener('click', () => {
            document.body.classList.toggle('dark-theme-variables');
            
            themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
            themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
            
            // Save theme preference
            const isDarkTheme = document.body.classList.contains('dark-theme-variables');
            localStorage.setItem('darkTheme', isDarkTheme);
        });
        
        // Load saved theme preference
        const savedTheme = localStorage.getItem('darkTheme');
        if (savedTheme === 'true') {
            document.body.classList.add('dark-theme-variables');
            themeToggler.querySelector('span:nth-child(1)').classList.remove('active');
            themeToggler.querySelector('span:nth-child(2)').classList.add('active');
        }
    }
    
    // Mobile menu functionality
    const menuBtn = document.querySelector('#menu_bar');
    const closeBtn = document.querySelector('#close_btn');
    const sidebar = document.querySelector('aside');
    
    if (menuBtn) {
        menuBtn.addEventListener('click', () => {
            sidebar.style.display = 'block';
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            sidebar.style.display = 'none';
        });
    }
    
    // Date picker functionality
    const datePicker = document.querySelector('.date input');
    if (datePicker) {
        const today = new Date().toISOString().substr(0, 10);
        datePicker.value = today;
        
        datePicker.addEventListener('change', (e) => {
            const selectedDate = e.target.value;
            // You can add code here to fetch data for the selected date
            console.log('Date selected:', selectedDate);
        });
    }
    
    // Responsive layout adjustments
    function handleResponsiveLayout() {
        if (window.innerWidth < 768) {
            sidebar.style.display = 'none';
        } else {
            sidebar.style.display = 'block';
        }
    }
    
    window.addEventListener('resize', handleResponsiveLayout);
    handleResponsiveLayout(); // Initial check
    
    // Add Member functionality (for the "Add Membership" button)
    const addMembershipBtn = document.querySelector('.add_product');
    if (addMembershipBtn) {
        addMembershipBtn.addEventListener('click', () => {
            window.location.href = 'add_member.php';
        });
    }
});
</script>

   <script src="script.js"></script>
</body>
</html>