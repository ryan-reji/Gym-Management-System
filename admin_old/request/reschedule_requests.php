<?php
session_start();
include '../db_config.php'; // Ensure this connects to your DB properly

// Fetch all reschedule requests ordered by latest first
$query = "SELECT rr.trainer_reschedule_id, rr.request_type, rr.created_at AS requested_date, 
                 rr.start_date, rr.end_date, rr.new_start_time, rr.new_end_time, rr.status,
                 CONCAT(t.FirstName, ' ', t.LastName) AS trainer_name
          FROM trainer_reschedules rr
          JOIN trainers t ON rr.trainer_id = t.trainer_id
          ORDER BY rr.created_at DESC";

$result = $conn->query($query);

// Fetch logged-in user name
$logged_in_user = $_SESSION['username'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Requests - GymShark</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="../styles.css">
    <style>
        /* Additional styles for reschedule requests page */
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

        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .request-card {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
        }

        .request-card:hover {
            box-shadow: none;
        }

        .request-card .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .request-card .profile-icon {
            background: var(--clr-primary);
            padding: 0.8rem;
            border-radius: 50%;
            color: var(--clr-white);
        }

        .request-info {
            display: grid;
            gap: 0.5rem;
        }

        .request-info p {
            color: var(--clr-dark-variant);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .request-info .icon {
            font-size: 1.2rem;
            color: var(--clr-primary);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .approve-btn, .reject-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-1);
            border: none;
            cursor: pointer;
            font-weight: 500;
            flex: 1;
        }

        .approve-btn {
            background: var(--clr-success);
            color: white;
        }

        .reject-btn {
            background: var(--clr-danger);
            color: white;
        }

        @media screen and (max-width: 768px) {
            .requests-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }
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
                <a href="reschedule_requests.php" class="active">
                    <span class="material-symbols-sharp">calendar_today</span>
                    <h3>Reschedule Requests</h3>
                </a>
            </div>
        </aside>

        <main>
            <h1>Reschedule Requests</h1>
            
            <div class="search-container">
                <input type="text" id="searchRequests" placeholder="Search requests..." onkeyup="searchRequests()">
            </div>

            <div class="requests-grid" id="requestsGrid">
                <!-- Request cards will be dynamically populated here -->
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchRequests();
        });

        function fetchRequests() {
            fetch("fetch_reschedule_requests.php")
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error("SQL Error:", data.error);
                        return;
                    }
                    if (data.length === 0) {
                        document.getElementById('requestsGrid').innerHTML = "<p>No requests found</p>";
                        return;
                    }
                    window.requests = data;
                    populateRequestsGrid(data);
                })
                .catch(error => console.error("Error fetching requests:", error));
        }

        function createRequestCard(request) {
            return `
                <div class="request-card" id="request-${request.trainer_reschedule_id}">
                    <div class="header">
                        <span class="material-symbols-sharp profile-icon">person</span>
                        <h3>${request.trainer_name}</h3>
                    </div>
                    <div class="request-info">
                        <p><span class="material-symbols-sharp icon">calendar_today</span> Requested: ${new Date(request.requested_date).toLocaleDateString()}</p>
                        <p><span class="material-symbols-sharp icon">event</span> Old Date: ${new Date(request.start_date).toLocaleDateString()} - ${new Date(request.end_date).toLocaleDateString()}</p>
                        <p><span class="material-symbols-sharp icon">schedule</span> New Time: ${request.new_start_time} - ${request.new_end_time}</p>
                        <p><span class="material-symbols-sharp icon">notes</span> Reason: ${request.request_type || "N/A"}</p>
                        <p><span class="material-symbols-sharp icon">info</span> <b>Status:</b> <span id="status-${request.trainer_reschedule_id}">${request.status}</span></p>
                    </div>
                    ${request.status === 'pending' ? `
                        <div class="action-buttons">
                            <button class="approve-btn" onclick="updateRequestStatus(${request.trainer_reschedule_id}, 'approved')">Approve</button>
                            <button class="reject-btn" onclick="updateRequestStatus(${request.trainer_reschedule_id}, 'rejected')">Reject</button>
                        </div>
                    ` : ''}
                </div>
            `;
        }

        function populateRequestsGrid(requestsToShow) {
            const grid = document.getElementById('requestsGrid');
            if (requestsToShow.length === 0) {
                grid.innerHTML = "<p>No requests found</p>";
                return;
            }
            grid.innerHTML = requestsToShow.map(createRequestCard).join('');
        }

        function updateRequestStatus(rescheduleId, status) {
            fetch("update_reschedule_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `trainer_reschedule_id=${rescheduleId}&status=${status}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`status-${rescheduleId}`).innerText = status;
                    const requestCard = document.getElementById(`request-${rescheduleId}`);
                    const actionButtons = requestCard.querySelector(".action-buttons");
                    if (actionButtons) {
                        actionButtons.remove();
                    }
                } else {
                    alert("Error updating request: " + data.error);
                }
            })
            .catch(error => console.error("Error:", error));
        }

        function searchRequests() {
            const searchInput = document.getElementById('searchRequests').value.toLowerCase();
            if (!window.requests) return;
            
            const filteredRequests = window.requests.filter(request => 
                request.trainer_name.toLowerCase().includes(searchInput) ||
                (request.request_type && request.request_type.toLowerCase().includes(searchInput))
            );
            
            populateRequestsGrid(filteredRequests);
        }
    </script>
</body>
</html>