<?php include('db_connect.php'); ?>
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
        /* Filter Section Styles */
        .filter-section {
            margin: 2rem 0;
            font-family: 'Poppins', sans-serif;
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
        }
        

        .filter-buttons {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin-top: 0.8rem;
        }

        .filter-btn 
        {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-1);
            border: none;
            cursor: pointer;
            font-weight: 500;
            background: var(--clr-light);
            color: var(--clr-dark);
            transition: all 300ms ease;
        }

        .filter-btn.active {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        /* Equipment Grid Styles */
        .equipment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .equipment-card {
            background: var(--clr-white);
            padding: var(--card-padding);
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            transition: all 300ms ease;
        }

        .equipment-card:hover {
            box-shadow: none;
        }

        .equipment-card .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .equipment-card .title-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        /* Equipment icon styling based on status */
        .equipment-card[data-status="active"] .equipment-icon {
            background: var(--clr-success);
        }
        
        .equipment-card[data-status="maintenance"] .equipment-icon {
            background: var(--clr-warning);
        }
        
        .equipment-card[data-status="out-of-order"] .equipment-icon {
            background: var(--clr-danger);
        }

        .equipment-card .equipment-icon {
            padding: 0.8rem;
            border-radius: 50%;
            color: var(--clr-white);
        }

        .equipment-card .title {
            font-size: 1rem;
            margin: 0;
            color: var(--clr-dark);
        }

        .equipment-card .actions {
            display: flex;
            gap: 0.3rem;
        }

        .equipment-card .details {
            display: grid;
            gap: 0.7rem;
        }

        .equipment-card .product-number,
        .equipment-card .purchase-date {
            color: var(--clr-dark-variant);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .equipment-card .material-symbols-sharp {
            color: var(--clr-primary);
            font-size: 1.2rem;
        }

        .equipment-card .description {
            color: var(--clr-dark);
            font-size: 0.9rem;
            line-height: 1.4;
            margin-top: 0.3rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.8rem;
            border-radius: var(--border-radius-1);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-badge .material-symbols-sharp {
            font-size: 1rem !important;
        }

        .equipment-card .btn-edit,
        .equipment-card .btn-delete {
            background: var(--clr-light);
            border: none;
            cursor: pointer;
            color: var(--clr-dark);
            padding: 0.4rem;
            border-radius: var(--border-radius-1);
            transition: all 300ms ease;
        }

        .equipment-card .btn-edit:hover {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        .equipment-card .btn-delete:hover {
            background: var(--clr-danger);
            color: var(--clr-white);
        }

        /* Status badge styling */
        .status-active {
            background: var(--clr-success);
            color: var(--clr-white);
        }

        .status-active .material-symbols-sharp {
            color: var(--clr-white) !important;
        }

        .status-maintenance {
            background: var(--clr-warning);
            color: var(--clr-dark); 
            font-weight: 600;
        }

        .status-maintenance .material-symbols-sharp {
            color: var(--clr-dark) !important;
        }

        .status-out-of-order {
            background: var(--clr-danger);
            color: var(--clr-white);
        }

        .status-out-of-order .material-symbols-sharp {
            color: var(--clr-white) !important;
        }

        /* Dark mode specific styles */
        html[data-theme="dark"] .status-maintenance {
            background: #e6a700; /* Brighter yellow for dark mode */
            color: #000;
            font-weight: 600;
        }

        html[data-theme="dark"] .status-maintenance .material-symbols-sharp {
            color: #000 !important;
        }

        /* Hide cards when filtered out */
        .equipment-card.hidden {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .equipment-grid {
                grid-template-columns: 1fr;
                padding: 0 1rem;
            }
        }
        
        /* Modal Styles */
        .edit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1000;
            font-family: 'Poppins', sans-serif;
        }

        .edit-modal.active {
            display: block;
        }

        .edit-modal-backdrop {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .edit-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--clr-white);
            width: 90%;
            max-width: 600px;
            border-radius: var(--card-border-radius);
            box-shadow: var(--box-shadow);
            max-height: 90vh;
            overflow-y: auto;
        }

        .edit-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--clr-light);
        }

        .edit-modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--clr-primary);
        }

        .close-modal {
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--clr-dark-variant);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 300ms ease;
        }

        .close-modal:hover {
            color: var(--clr-danger);
        }

        .edit-modal-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--clr-dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--clr-light);
            border-radius: var(--border-radius-1);
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 300ms ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--clr-primary);
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
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 300ms ease;
        }

        .btn-primary {
            background: var(--clr-primary);
            color: var(--clr-white);
        }

        .btn-primary:hover {
            background: var(--clr-primary-variant);
        }

        .btn-secondary {
            background: var(--clr-light);
            color: var(--clr-dark);
        }

        .btn-secondary:hover {
            background: var(--clr-dark-variant);
            color: var(--clr-white);
        }

        /* Dark mode adjustments */
        html[data-theme="dark"] .edit-modal-content {
            background: var(--clr-background);
        }

        html[data-theme="dark"] .edit-modal-header {
            border-bottom-color: var(--clr-dark-variant);
        }
        .status-maintenance {
            background: #ffa500; /* Orange color for maintenance */
            color: var(--clr-white);
        }

        .status-maintenance .material-symbols-sharp {
            color: var(--clr-white) !important;
        }

        /* Dark mode adjustment for maintenance status */
        html[data-theme="dark"] .status-maintenance {
            background: #ffa500;
            color: var(--clr-white);
        }

        html[data-theme="dark"] .status-maintenance .material-symbols-sharp {
            color: var(--clr-white) !important;
        }
    </style>
</head>
<body>
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
           <a href="equipmentpage.php" class="active">  
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
      <h1>Equipment List</h1>
      
      <div class="filter-section">
        <h3>Filter by Status</h3>
        <div class="filter-buttons">
          <button class="filter-btn active" data-status="all">All</button>
          <button class="filter-btn" data-status="active">Active</button>
          <button class="filter-btn" data-status="maintenance">Under Maintenance</button>
          <button class="filter-btn" data-status="out-of-order">Out of Order</button>
        </div>
      </div>
      <div class="equipment-grid">
            <?php
            $sql = "SELECT * FROM equipment ORDER BY purchase_date DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $status = strtolower($row['status']);
                    $statusClass = 'status-' . $status;
                    
                    // Define appropriate equipment icon based on name/type
                    $equipmentIcon = "fitness_center"; // Default icon
                    $name = strtolower($row['name']);
                    
                    $equipmentIcon = "fitness_center";
                    ?>
                    <div class="equipment-card" data-status="<?php echo $status; ?>">
                        <div class="header">
                            <div class="title-section">
                                <span class="material-symbols-sharp equipment-icon"><?php echo $equipmentIcon; ?></span>
                                <h3 class="title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            </div>
                            <div class="actions">
    <button type="button" class="btn-edit" data-id="<?php echo $row['id']; ?>">
        <span class="material-symbols-sharp">edit</span>
    </button>
    <button type="button" class="btn-delete" data-id="<?php echo $row['id']; ?>">
        <span class="material-symbols-sharp">delete</span>
    </button>
</div>
                        </div>
                        
                        <div class="details">
                            <p class="product-number">
                                <span class="material-symbols-sharp">inventory_2</span>
                                Product #: <?php echo htmlspecialchars($row['product_number']); ?>
                            </p>
                            <p class="purchase-date">
                                <span class="material-symbols-sharp">calendar_today</span>
                                <?php echo date('M d, Y', strtotime($row['purchase_date'])); ?>
                            </p>
                            <p class="status">
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <span class="material-symbols-sharp">
                                        <?php 
                                            echo $status === 'active' ? 'check_circle' : 
                                                ($status === 'maintenance' ? 'construction' : 'error');
                                        ?>
                                    </span>
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </p>
                            <?php if(!empty($row['description'])): ?>
                            <p class="description">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No equipment found</p>";
            }
            ?>
        </div>

        <!-- Edit Modal -->
        <div class="edit-modal" id="editEquipmentModal">
          <div class="edit-modal-backdrop"></div>
          <div class="edit-modal-content">
            <div class="edit-modal-header">
              <h2>Edit Equipment</h2>
              <button class="close-modal" id="closeEditModal">
                <span class="material-symbols-sharp">close</span>
              </button>
            </div>
            <div class="edit-modal-body">
              <form id="editEquipmentForm" method="POST" action="update_equipment.php">
                <input type="hidden" id="edit-equipment-id" name="id">
                
                <div class="form-group">
                  <label for="edit-equipment-name">Equipment Name</label>
                  <input type="text" id="edit-equipment-name" name="equipment-name" required>
                </div>

                <div class="form-group">
                  <label for="edit-product-number">Product Number</label>
                  <input type="text" id="edit-product-number" name="product-number" required>
                </div>

                <div class="form-group">
                  <label for="edit-purchase-date">Purchase Date</label>
                  <input type="date" id="edit-purchase-date" name="purchase-date" required>
                </div>

                <div class="form-group">
                  <label for="edit-equipment-status">Status</label>
                  <select id="edit-equipment-status" name="equipment-status" required>
                    <option value="active">Active</option>
                    <option value="maintenance">Under Maintenance</option>
                    <option value="out-of-order">Out of Order</option>
                  </select>
                </div>

                <div class="form-group">
                  <label for="edit-equipment-description">Description</label>
                  <textarea id="edit-equipment-description" name="equipment-description" rows="4"></textarea>
                </div>

                <div class="form-buttons">
                  <button type="submit" class="btn-primary">Save Changes</button>
                  <button type="button" class="btn-secondary cancel-edit">Cancel</button>
                </div>
              </form>
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
            <p><b>miguel</b></p>
            <p>Admin</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
   // Wait for DOM to be fully loaded before attaching event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const equipmentCards = document.querySelectorAll('.equipment-card');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            button.classList.add('active');

            const status = button.getAttribute('data-status');

            equipmentCards.forEach(card => {
                if (status === 'all' || card.getAttribute('data-status') === status) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });

    // Theme toggler - Enhanced to handle status badge visibility
    const themeToggler = document.querySelector('.theme-toggler');
    const html = document.querySelector('html');
    
    themeToggler.addEventListener('click', function() {
        document.body.classList.toggle('dark-theme-variables');
        
        themeToggler.querySelector('span:nth-child(1)').classList.toggle('active');
        themeToggler.querySelector('span:nth-child(2)').classList.toggle('active');
        
        // Toggle data-theme attribute for specific dark mode styles
        if (html.getAttribute('data-theme') === 'dark') {
            html.removeAttribute('data-theme');
        } else {
            html.setAttribute('data-theme', 'dark');
        }
    });
    
    // Edit Modal Functionality
    const modal = document.getElementById('editEquipmentModal');
    const closeBtn = document.getElementById('closeEditModal');
    const cancelBtn = document.querySelector('.cancel-edit');
    const editForm = document.getElementById('editEquipmentForm');
    
    // Collection of all edit buttons
    const editButtons = document.querySelectorAll('.btn-edit');
    
    // Add event listeners to each edit button - FIX APPLIED HERE
    editButtons.forEach(button => {
        // Replace the inline onclick with a proper event listener
        button.removeAttribute('onclick');
        
        button.addEventListener('click', function(e) {
            // Prevent default behavior - THIS IS THE CRITICAL FIX
            e.preventDefault();
            e.stopPropagation();
            
            const equipmentId = this.getAttribute('data-id');
            fetchEquipmentDetails(equipmentId);
        });
    });
    
    // Handle delete buttons with improved prevention
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        // Remove the inline onclick
        button.removeAttribute('onclick');
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (confirm('Are you sure you want to delete this equipment?')) {
                const equipmentId = this.getAttribute('data-id');
                // Redirect to delete script
                window.location.href = `delete_equipment.php?id=${equipmentId}`;
            }
        });
    });
    
    // Function to fetch equipment details using AJAX
    function fetchEquipmentDetails(id) {
        fetch(`get_equipment.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            populateForm(data);
            openModal();
        })
        .catch(error => {
            console.error('Error fetching equipment details:', error);
            alert('Failed to fetch equipment details. Please try again.');
        });
    }
    
    // Function to populate the form
    function populateForm(equipment) {
        document.getElementById('edit-equipment-id').value = equipment.id;
        document.getElementById('edit-equipment-name').value = equipment.name;
        document.getElementById('edit-product-number').value = equipment.product_number;
        document.getElementById('edit-purchase-date').value = equipment.purchase_date;
        document.getElementById('edit-equipment-status').value = equipment.status.toLowerCase();
        document.getElementById('edit-equipment-description').value = equipment.description || '';
    }
    
    // Function to open modal
    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
    
    // Function to close modal
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }
    
    // Close modal events
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    
    // Close when clicking on backdrop
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('edit-modal-backdrop')) {
            closeModal();
        }
    });
    
    // Handle form submission
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create FormData object
        const formData = new FormData(this);
        
        // Send AJAX request
        fetch('update_equipment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the page to show updated data
                window.location.reload();
            } else {
                alert('Error updating equipment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the equipment.');
        });
    });
});
  </script>
  <script src="script.js"></script>
</body>
</html>