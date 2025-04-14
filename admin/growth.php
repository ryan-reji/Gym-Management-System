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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Growth - GYM SHARK</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional styles for growth page */
        .content-data {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            margin-top: 2rem;
            box-shadow: var(--box-shadow);
        }

        .content-data .head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .content-data .head h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--clr-dark);
        }

        .content-data .head .menu {
            position: relative;
        }

        .content-data .head .menu .icon {
            cursor: pointer;
            font-size: 1.2rem;
        }

        .chart {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
        }

        #chart {
            min-height: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
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
             <a href="index.php" >  
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
             
             <a href="scanner.php" > >
                <span class="material-symbols-sharp">qr_code_scanner</span>
                <h3>Attendance</h3>
             </a>
             <a href="growth.php" class="active">
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

        <!-- Main Content -->
        <main>
            <h1>Growth Analytics</h1>
            

            <!-- Sales Report Section -->
            <div class="content-data">
                <div class="head">
                    <h3>Sales Report</h3>
                    <div class="menu">
                        <span class="material-symbols-sharp">more_vert</span>
                    </div>
                </div>
                <div class="chart">
                    <div id="chart"></div>
                </div>
            </div>
        </main>

        <!-- Right Section -->
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
                    <div class="profile-photo"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const sideMenu = document.querySelector('aside');
    const menuBtn = document.querySelector('#menu_bar');
    const closeBtn = document.querySelector('#close_btn');
    const themeToggler = document.querySelector('.theme-toggler');

    // Function to apply the saved theme
    function applyTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme-variables');
            themeToggler.querySelector('span:nth-child(1)').classList.remove('active');
            themeToggler.querySelector('span:nth-child(2)').classList.add('active');
        } else {
            document.body.classList.remove('dark-theme-variables');
            themeToggler.querySelector('span:nth-child(1)').classList.add('active');
            themeToggler.querySelector('span:nth-child(2)').classList.remove('active');
        }
    }

    // Apply theme on page load
    document.addEventListener('DOMContentLoaded', applyTheme);

    menuBtn.addEventListener('click', () => {
        sideMenu.style.display = "block";
    });

    closeBtn.addEventListener('click', () => {
        sideMenu.style.display = "none";
    });

    themeToggler.addEventListener('click', () => {
        // Toggle dark theme class
        document.body.classList.toggle('dark-theme-variables');
        
        // Toggle active states of theme toggle buttons
        themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
        themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
        
        // Save theme preference to localStorage
        const isDarkTheme = document.body.classList.contains('dark-theme-variables');
        localStorage.setItem('theme', isDarkTheme ? 'dark' : 'light');
    });

    // Fetch growth data from backend
fetch('backend/fetch_growth_data.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error fetching data:", data.error);
            return;
        }

        var options = {
            series: [{
                name: 'Revenue',
                data: data.revenue
            }, {
                name: 'Members',
                data: data.members
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
            },
            colors: ['#7380ec', '#ff7782'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth' },
            labels: data.months, // Use months from backend
            markers: { size: 0 },
            tooltip: { shared: true, intersect: false }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    })
    .catch(error => console.error("Fetch error:", error));

</script>
</body>
</html>