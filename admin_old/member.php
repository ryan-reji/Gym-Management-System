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
    <title>Members - GymShark</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Additional styles for members page */
        .search-container {
            margin: 2rem 0;
            width: 100%;
        }

        .search-container input {
            width: 100%;
            padding: 1rem;
            border-radius: var(--border-radius-1);
            background: var(--clr-white);
            box-shadow: var(--box-shadow);
        }

        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .member-card {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .member-card:hover {
            box-shadow: none;
        }

        .member-card .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .member-card .profile-icon {
            background: var(--clr-primary);
            padding: 0.8rem;
            border-radius: 50%;
            color: var(--clr-white);
        }

        .member-info {
            display: grid;
            gap: 0.5rem;
        }

        .member-info p {
            color: var(--clr-dark-variant);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .member-info .icon {
            font-size: 1.2rem;
            color: var(--clr-primary);
        }

        @media screen and (max-width: 768px) {
            .members-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }
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
                <a href="index.php">
                    <span class="material-symbols-sharp">grid_view</span>
                    <h3>Dashboard</h3>
                </a>
                <a href="member.php" class="active">
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
                <a href="equipmentpage.php">
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

        <!-- Main Content -->
        <main>
            <h1>Members</h1>
            

            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="searchMembers" placeholder="Search members..." onkeyup="searchMembers()">
            </div>

            <!-- Members Grid -->
            <div class="members-grid" id="membersGrid">
                <!-- Member cards will be dynamically populated here -->
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
                    <div class="profile-photo">
                        <!-- Profile photo here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
     document.addEventListener("DOMContentLoaded", function () {
    fetchMembers(); // Fetch members on page load
});

// Function to fetch members from backend
function fetchMembers() {
    fetch("backend/fetch_members.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("SQL Error:", data.error);
                return;
            }
            if (data.message) {
                console.warn("No members found");
                document.getElementById('membersGrid').innerHTML = "<p>No members found</p>";
                return;
            }

            window.members = data; // Store in global variable
            populateMembersGrid(data);
        })
        .catch(error => console.error("Error fetching members:", error));
}

// Function to create member cards
function createMemberCard(member) {
    return `
        <div class="member-card">
            <div class="header">
                <span class="material-symbols-sharp profile-icon">person</span>
                <h3>${member.name}</h3>
            </div>
            <div class="member-info">
                <p><span class="material-symbols-sharp icon">calendar_today</span> Joined: ${member.joined ? new Date(member.joined).toLocaleDateString() : 'N/A'}</p>
                <p><span class="material-symbols-sharp icon">phone</span> ${member.phone ? member.phone : 'N/A'}</p>
            </div>
        </div>
    `;
}

// Function to populate members grid
function populateMembersGrid(membersToShow) {
    const grid = document.getElementById('membersGrid');
    if (membersToShow.length === 0) {
        grid.innerHTML = "<p>No members found</p>";
        return;
    }
    grid.innerHTML = membersToShow.map(createMemberCard).join('');
}
function searchMembers() {
    const searchInput = document.getElementById('searchMembers');
    const filter = searchInput.value.toLowerCase();
    
    // Make sure we have the members data
    if (!window.members) {
        console.warn("Members data not loaded yet");
        return;
    }
    
    // Filter the members based on search input
    const filteredMembers = window.members.filter(member => {
        // Search in name and phone
        return (
            member.name.toLowerCase().includes(filter) ||
            (member.phone && member.phone.toLowerCase().includes(filter))
        );
    });
    
    // Update the display with filtered members
    populateMembersGrid(filteredMembers);
}

    </script>
</body>
</html>