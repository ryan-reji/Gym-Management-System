<?php include('db_connect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Equipment - GYM SHARK</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@48,400,0,0" />
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
  --clr-warnig: #ff4edc;
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
  color: var(--clr-warnig);
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

main .insights {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.6rem;
}

main .insights > div {
  background-color: var(--clr-white);
  padding: var(--card-padding);
  border-radius: var(--card-border-radius);
  margin-top: 1rem;
  box-shadow: var(--box-shadow);
  transition: all 0.3s ease;
}

main .insights > div:hover {
  box-shadow: none;
}

main .insights > div span {
  background: coral;
  padding: 0.5rem;
  border-radius: 50%;
  color: var(--clr-white);
  font-size: 2rem;
}

main .insights > div.expenses span {
  background: var(--clr-danger);
}

main .insights > div.income span {
  background: var(--clr-success);
}

main .insights > div .middle {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

main .insights > div .middle h1 {
  font-size: 1.6rem;
}

/* Add Equipment Form Styles */
.add-equipment-form {
  margin-top: 2rem;
}

.form-card {
  background: var(--clr-white);
  padding: var(--card-padding);
  border-radius: var(--card-border-radius);
  box-shadow: var(--box-shadow);
  transition: all 0.3s ease;
}

.form-card:hover {
  box-shadow: none;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  color: var(--clr-dark);
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid var(--clr-info-light);
  border-radius: var(--border-radius-1);
  background: transparent;
  color: var(--clr-dark);
  font-family: 'Poppins', sans-serif;
}

.form-group textarea {
  resize: vertical;
}

.form-buttons {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.8rem 1.5rem;
  border-radius: var(--border-radius-1);
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-primary {
  background: var(--clr-primary);
  color: var(--clr-white);
}

.btn-primary:hover {
  background: var(--clr-primary-variant);
}

.btn-secondary {
  background: var(--clr-white);
  color: var(--clr-dark);
  border: 1px solid var(--clr-info-light);
}

.btn-secondary:hover {
  background: var(--clr-light);
}

/* Right Side */
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

  main .insights {
    grid-template-columns: repeat(1, 1fr);
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

  .add-equipment-form {
    padding: 0 1rem;
  }
  
  .form-buttons {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    width: 100%;
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
}




  </style>
</head>
<body>
  <div class="container">
    <!-- Keeping the same sidebar from your original code -->
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
             <a href="addeq.php" class="active">
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
      <h1>Add New Equipment</h1>
      <?php
      // Process form submission
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $name = $_POST['equipment-name'];
          $product_number = $_POST['product-number'];
          $purchase_date = $_POST['purchase-date'];
          $status = $_POST['equipment-status'];
          $description = $_POST['equipment-description'];
          
          $sql = "INSERT INTO equipment (name, product_number, purchase_date, status, description)
                  VALUES ('$name', '$product_number', '$purchase_date', '$status', '$description')";
          
          if ($conn->query($sql) === TRUE) {
              echo "<div class='success-message'>Equipment added successfully!</div>";
          } else {
              echo "<div class='error-message'>Error: " . $conn->error . "</div>";
          }
      }
      ?>

      <div class="add-equipment-form">
        <div class="form-card">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
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
            <p><b>miguel</b></p>
            <p>Admin</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="script.js"></script>
</body>
</html>