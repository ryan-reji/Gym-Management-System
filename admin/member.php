<?php
include('db_connect.php');

// Get filter parameters
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$membership = isset($_GET['membership']) ? $_GET['membership'] : '';

// Build the query
$query = "SELECT 
    u.id AS ID,
    CONCAT(u.FirstName, ' ', u.LastName) AS Name,
    p.plan_type AS Membership,
    pb.start_date AS join_date,
    pb.end_date AS expiry_date,
    pb.total_cost AS amount,
    pb.status AS payment_status,
    CASE 
        WHEN pb.end_date >= CURDATE() THEN 'Active'
        ELSE 'Expired'
    END AS membership_status
FROM users u
LEFT JOIN plan_bookings pb ON u.id = pb.user_id
LEFT JOIN plans p ON pb.plan_id = p.PlanId
WHERE 1=1";

if ($month) {
    $query .= " AND MONTH(pb.start_date) = '$month'";
}
if ($year) {
    $query .= " AND YEAR(pb.start_date) = '$year'";
}
if ($status) {
    $query .= " AND pb.status = '$status'";
}
if ($membership) {
    $query .= " AND p.plan_type = '$membership'";
}

$result = $conn->query($query);
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
        .filters-container {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 0.5rem;
            color: var(--clr-dark);
        }

        .filter-group select {
            padding: 0.5rem;
            border: 1px solid var(--clr-info-light);
            border-radius: var(--border-radius-1);
            background: transparent;
        }

        .actions-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-1);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        .btn-secondary {
            background: var(--clr-success);
            color: var(--clr-white);
        }

        .table-container {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--clr-info-light);
        }

        th {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        tr:hover {
            background: var(--clr-light);
        }

        .status {
            padding: 0.5rem;
            border-radius: var(--border-radius-1);
            font-weight: 500;
        }

        .status-completed { background: var(--clr-success-light); color: var(--clr-success); }
        .status-pending { background: var(--clr-danger-light); color: var(--clr-danger); }
        .status-partial { background: var(--clr-warning-light); color: var(--clr-warning); }
        .status-active { background: var(--clr-success-light); color: var(--clr-success); }
        .status-expired { background: var(--clr-danger-light); color: var(--clr-danger); }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Sidebar (Same as before) -->
        
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
             <a href="index.php">  
                <span class="material-symbols-sharp">grid_view </span>
                <h3>Dashboard</h3>
             </a>
             <a href="member.php" class="active">   
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
  
        <!-- end aside -->
  
        <main>
            <h1>Members Management</h1>

            <!-- Filters -->
            <div class="filters-container">
                <form id="filterForm" method="GET">
                    <div class="filters-grid">
                        <div class="filter-group">
                            <label for="month">Month</label>
                            <select name="month" id="month">
                                <option value="">All Months</option>
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    $selected = ($month == $i) ? 'selected' : '';
                                    echo "<option value='$i' $selected>" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="year">Year</label>
                            <select name="year" id="year">
                                <option value="">All Years</option>
                                <?php
                                $currentYear = date('Y');
                                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                    $selected = ($year == $i) ? 'selected' : '';
                                    echo "<option value='$i' $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="status">Payment Status</label>
                            <select name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="completed" <?php echo ($status == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="membership">Membership Type</label>
                            <select name="membership" id="membership">
                                <option value="">All Types</option>
                                <option value="basic" <?php echo ($membership == 'basic') ? 'selected' : ''; ?>>Basic</option>
                                <option value="standard" <?php echo ($membership == 'standard') ? 'selected' : ''; ?>>Standard</option>
                                <option value="premium" <?php echo ($membership == 'premium') ? 'selected' : ''; ?>>Premium</option>
                            </select>
                        </div>
                    </div>

                    <div class="actions-container">
                        <button type="submit" class="btn btn-primary">
                            <span class="material-symbols-sharp">filter_alt</span>
                            Apply Filters
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="exportToCSV()">
                            <span class="material-symbols-sharp">download</span>
                            Export CSV
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="printTable()">
                            <span class="material-symbols-sharp">print</span>
                            Print
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-container">
                <table id="membersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Membership</th>
                            <th>Join Date</th>
                            <th>Expiry Date</th>
                            <th>Amount (₹)</th>
                           
                            <th>Payment Status</th>
                            <th>Membership Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['ID']}</td>";
                                echo "<td>{$row['Name']}</td>";
                                echo "<td>" . ucfirst($row['Membership']) . "</td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['join_date'])) . "</td>";
                                echo "<td>" . date('d-m-Y', strtotime($row['expiry_date'])) . "</td>";
                                echo "<td>{$row['amount']}</td>";
                                
                                echo "<td><span class='status status-" . strtolower($row['payment_status']) . "'>{$row['payment_status']}</span></td>";
                                echo "<td><span class='status status-" . strtolower($row['membership_status']) . "'>{$row['membership_status']}</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' style='text-align: center;'>No members found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>

        <!-- Right Section (Same as before) -->
    
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
                   <p><b>miguel</b></p>
                   <p>Admin</p>
                   <small class="text-muted"></small>
               </div>
               <div class="profile-photo">
                
               </div>
            </div>
        </div>
        <script src="script.js"></script>

    <script>
        function exportToCSV() {
            let csv = [];
            const rows = document.querySelectorAll("table tr");
            
            for (const row of rows) {
                const cols = row.querySelectorAll("td, th");
                const rowData = Array.from(cols).map(col => {
                    // Get text content without status styling
                    let content = col.textContent.trim();
                    // Remove quotes to avoid CSV issues
                    content = content.replace(/"/g, '""');
                    // Wrap in quotes
                    return `"${content}"`;
                });
                csv.push(rowData.join(","));
            }

            const csvContent = "data:text/csv;charset=utf-8," + csv.join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `members_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function printTable() {
            const printContent = document.querySelector('.table-container').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = `
                <div style="padding: 20px;">
                    <h1 style="text-align: center;">GymShark Members Report</h1>
                    <p style="text-align: center;">Generated on: ${new Date().toLocaleDateString()}</p>
                    ${printContent}
                </div>
            `;

            window.print();
            document.body.innerHTML = originalContent;
            location.reload(); // Reload to restore event listeners
        }

        // Auto-submit form when filters change
        document.querySelectorAll('.filter-group select').forEach(select => {
            select.addEventListener('change', () => {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
</body>
</html>