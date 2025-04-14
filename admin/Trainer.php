<?php
// Database connection
include('db_connect.php');

// Get filter parameters
$experience = $_GET['experience'] ?? '';
$specialization = $_GET['specialization'] ?? '';
$status = $_GET['status'] ?? '';

// Fetch trainers data
$sql = "SELECT trainer_id, CONCAT(FirstName, ' ', LastName) AS name, number, specialization, experience, join_date, status FROM trainers WHERE 1=1";

// Apply filters dynamically
if (!empty($experience)) {
    switch ($experience) {
        case '1':
            $sql .= " AND experience < 1";
            break;
        case '2':
            $sql .= " AND experience BETWEEN 1 AND 3";
            break;
        case '3':
            $sql .= " AND experience BETWEEN 3 AND 5";
            break;
        case '4':
            $sql .= " AND experience > 5";
            break;
    }
}

if (!empty($specialization)) {
    $sql .= " AND specialization LIKE '%" . $conn->real_escape_string($specialization) . "%'";
}

if (!empty($status)) {
    $sql .= " AND status = '" . $conn->real_escape_string(ucfirst($status)) . "'";
}

// Execute query
$result = $conn->query($sql);

// Check for query errors
if (!$result) {
    die("Query failed: " . $conn->error);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trainers - GYM SHARK</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
  <link rel="stylesheet" href="styles.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,500;0,600;0,700;0,800;1,600&display=swap');

/* variables */
:root {
  --clr-primary: #7380ec;
  --clr-danger: #ff7782;
  --clr-success: #41f1b6;
  --clr-white: #fff;
  --clr-info-dark: #7d8da1;
  --clr-info-light: #dce1eb;
  --clr-dark: #363949;
  --clr-warning: #ff4edc;
  --clr-light: rgba(132, 139, 200, 0.18);
  --clr-primary-variant: #111e88;
  --clr-dark-variant: #677483;
  --clr-color-background: #f6f6f9;

  --card-border-radius: 2rem;
  --border-radius-1: 0.4rem;
  --border-radius-2: 0.8rem;
  --border-radius-3: 1.2rem;

  --card-padding: 1.8rem;
  --padding-1: 1.2rem;
  --box-shadow: 0 2rem 3rem var(--clr-light);
}

/* dark theme */
.dark-theme-variables {
  --clr-color-background: #181a1e;
  --clr-white: #202528;
  --clr-light: rgba(0, 0, 0, 0.4);
  --clr-dark: #edeffd;
  --clr-dark-variant: #677483;
  --box-shadow: 0 2rem 3rem var(--clr-light);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  border: 0;
  text-decoration: none;
  list-style: none;
  appearance: none;
}

body {
  width: 100vw;
  height: 100vh;
  font-size: 0.7rem;
  user-select: none;
  background: var(--clr-color-background);
  font-family: 'Poppins', sans-serif;
}

.container {
  display: grid;
  width: 96%;
  gap: 1.8rem;
  grid-template-columns: 14rem auto 16rem;
  margin: 0 auto;
}

a {
  color: var(--clr-dark);
}

h1, h2, h3, h4, h5, p, b, span, small, a, th {
  color: var(--clr-dark);
}

h1 {
  font-weight: 800;
  font-size: 2.8rem;
}

h2 {
  font-size: 1.4rem;
}

h3 {
  font-size: 0.87rem;
}

h4 {
  font-size: 0.8rem;
}

h5 {
  font-size: 0.77rem;
}

small {
  font-size: 0.75rem;
}

.profile-photo img {
  width: 2.8rem;
  height: 2.8rem;
  overflow: hidden;
  border-radius: 50%;
}

.text-muted {
  color: var(--clr-info-dark);
}

b {
  color: var(--clr-dark);
}

.primary {
  color: var(--clr-primary);
}

.success {
  color: var(--clr-success);
}

.danger {
  color: var(--clr-danger);
}

.warning {
  color: var(--clr-warning);
}

/* aside */
aside {
  height: 100vh;
  border-spacing: border-box;
  position: sticky;
  top: 0;
  align-self: start;
  border-right: 1px;
}

aside .top {
  background: var(--clr-white);
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 1.4rem;
}

aside .logo {
  display: flex;
  gap: 1rem;
}

aside .logo img {
  width: 2rem;
  height: 2rem;
}

aside .top div.close span {
  display: none;
}

/* Sidebar */
aside .sidebar {
  background: var(--clr-white);
  display: flex;
  flex-direction: column;
  height: 86vh;
  position: relative;
  top: 1rem;
}

aside h3 {
  font-weight: 500;
}

aside .sidebar a {
  display: flex;
  color: var(--clr-info-dark);
  margin-left: 2rem;
  gap: 1rem;
  align-items: center;
  height: 3.3rem;
  transition: all 0.1s ease;
}

aside .sidebar a span {
  font-size: 1.6rem;
  transition: all 0.3s ease-in-out;
}

aside .sidebar a:last-child {
  position: absolute;
  bottom: 1rem;
  width: 100%;
}

aside .sidebar a.active {
  background-color: var(--clr-light);
  color: var(--clr-primary);
  margin-left: 0;
  border-left: 5px solid var(--clr-primary);
  margin-left: calc(1rem - 3px);
}

aside .sidebar a:hover span {
  margin-left: 1rem;
}

aside .sidebar a span.msg_count {
  background-color: var(--clr-danger);
  color: var(--clr-white);
  padding: 2px 5px;
  font-size: 11px;
  border-radius: var(--border-radius-1);
}

.sidebar svg {
  flex-shrink: 0;
  fill: var(--text-clr);
}

/* Main */
main {
  margin-top: 1.4rem;
  width: auto;
}

main input {
  background-color: transparent;
  border: 0;
  outline: 0;
  color: var(--clr-dark);
}

main .date {
  display: inline-block;
  background: var(--clr-white);
  border-radius: var(--border-radius-1);
  margin-top: 1rem;
  padding: 0.5rem 1.6rem;
}

/* New styles for trainers page */
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

.status-active { background: var(--clr-success-light); color: var(--clr-success); }
.status-inactive { background: var(--clr-danger-light); color: var(--clr-danger); }
.status-seniority-high { background: var(--clr-success-light); color: var(--clr-success); }
.status-seniority-medium { background: var(--clr-primary-light); color: var(--clr-primary); }
.status-seniority-low { background: var(--clr-warning-light); color: var(--clr-warning); }

/* Right Section Styles */
.right {
  margin-top: 1.4rem;
}

.right h2 {
  color: var(--clr-dark);
}

.right .top {
  display: flex;
  justify-content: start;
  gap: 2rem;
}

.right .top button {
  display: none;
}

.right .theme-toggler {
  background: var(--clr-white);
  display: flex;
  justify-content: space-between;
  height: 1.6rem;
  width: 4.2rem;
  cursor: pointer;
  border-radius: var(--border-radius-1);
}

.right .theme-toggler span {
  font-size: 1.2rem;
  width: 50%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.right .theme-toggler span.active {
  background: var(--clr-primary);
  color: #fff;
}

.right .top .profile {
  display: flex;
  gap: 2rem;
  text-align: right;
}

/* Media Queries */
@media screen and (max-width: 1200px) {
  .container {
    width: 94%;
    grid-template-columns: 7rem auto 14rem;
  }

  aside .sidebar h3 {
    display: none;
  }

  aside .sidebar a {
    width: 5.6rem;
  }

  aside .sidebar a:last-child {
    position: relative;
    margin-top: 1.8rem;
  }
}

@media screen and (max-width: 768px) {
  .container {
    width: 100%;
    grid-template-columns: 1fr;
  }

  aside {
    position: fixed;
    width: 18rem;
    z-index: 3;
    height: 100vh;
    background-color: var(--clr-white);
    display: none;
    left: -110px;
    animation: menuLeft 0.3s ease forwards;
  }

  @keyframes menuLeft {
    to {
      left: 0;
    }
  }

  aside .logo h2 {
    display: inline;
  }

  aside .sidebar h3 {
    display: inline;
  }

  aside .sidebar a {
    width: 100%;
    height: 3.4rem;
  }

  aside .top div.close span {
    display: inline;
    position: absolute;
    right: 0;
    margin-right: 30px;
    font-size: 35px;
    cursor: pointer;
  }

  .right .top {
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 0.8rem;
    background: var(--clr-white);
    height: 4.6rem;
    width: 100%;
    z-index: 2;
    box-shadow: 0 1rem 1rem var(--clr-light);
    margin: 0;
  }

  .right .profile {
    position: absolute;
    left: 70%;
  }

  .right .top .theme-toggler {
    width: 4.4rem;
    position: absolute;
    left: 50%;
  }

  .right .profile .info {
    display: none;
  }

  .right .top button {
    display: inline-block;
    background: transparent;
    cursor: pointer;
    color: var(--clr-dark);
    position: absolute;
    left: 1rem;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .actions-container {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
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
          <h2>GYM</h2><h2><span class="danger">SHARK</span> </h2>
        </div>
        <div class="close" id="close_btn">
          <span class="material-symbols-sharp">close</span>
        </div>
      </div>
      <!-- end top -->
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
        <a href="add_member.php">
          <span class="material-symbols-sharp">person_add</span>
          <h3>Add Member</h3>
        </a>
        <a href="addtrainer.php">
          <span class="material-symbols-sharp">person_add</span>
          <h3>Add Trainer</h3>
        </a>
        <a href="scanner.php">
          <span class="material-symbols-sharp">qr_code_scanner</span>
          <h3>Attendance</h3>
        </a>
        <a href="growth.php">
          <span class="material-symbols-sharp">insights</span>
          <h3>Growth</h3>
        </a>
        <a href="Trainer.php" class="active">
          <span class="material-symbols-sharp">Person</span>
          <h3>Trainers</h3>
          <span class="msg_count">14</span>
        </a>
        <a href="equipmentpage.php">
          <span class="material-symbols-sharp">receipt_long</span>
          <h3>Equipments</h3>
        </a>
        <a href="settings.html">
          <span class="material-symbols-sharp">settings</span>
          <h3>Settings</h3>
        </a>
        <a href="addeq.php">
          <span class="material-symbols-sharp">add</span>
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
      <h1>Trainers Management</h1>

      <!-- Filters -->
      <div class="filters-container">
    <form id="filterForm" method="GET">
        <div class="filters-grid">
            <div class="filter-group">
                <label for="experience">Experience</label>
                <select name="experience" id="experience">
                    <option value="" <?= $experience == '' ? 'selected' : '' ?>>All Experience</option>
                    <option value="1" <?= $experience == '1' ? 'selected' : '' ?>>Less than 1 year</option>
                    <option value="2" <?= $experience == '2' ? 'selected' : '' ?>>1-3 years</option>
                    <option value="3" <?= $experience == '3' ? 'selected' : '' ?>>3-5 years</option>
                    <option value="4" <?= $experience == '4' ? 'selected' : '' ?>>5+ years</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="specialization">Specialization</label>
                <select name="specialization" id="specialization">
                    <option value="" <?= $specialization == '' ? 'selected' : '' ?>>All Specializations</option>
                    <option value="weight" <?= $specialization == 'weight' ? 'selected' : '' ?>>Weight Training</option>
                    <option value="cardio" <?= $specialization == 'cardio' ? 'selected' : '' ?>>Cardio</option>
                    <option value="yoga" <?= $specialization == 'yoga' ? 'selected' : '' ?>>Yoga</option>
                    <option value="crossfit" <?= $specialization == 'crossfit' ? 'selected' : '' ?>>CrossFit</option>
                    <option value="nutrition" <?= $specialization == 'nutrition' ? 'selected' : '' ?>>Nutrition</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="" <?= $status == '' ? 'selected' : '' ?>>All Statuses</option>
                    <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $status == 'inactive' ? 'selected' : '' ?>>Inactive</option>
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
    <table id="trainersTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Specialization</th>
                <th>Experience</th>
                <th>Join Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['trainer_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['specialization']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['experience']) . " years</td>";
                    echo "<td>" . htmlspecialchars(date("d-m-Y", strtotime($row['join_date']))) . "</td>";
                    echo "<td><span class='status " . ($row['status'] == 'active' ? 'status-active' : 'status-inactive') . "'>" . htmlspecialchars($row['status']) . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No trainers found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="script.js"></script>
<script>
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

</script>
</body>
</html>

<?php
$conn->close();
?>