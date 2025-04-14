<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Get trainers with filtering options
$specialization = isset($_GET['specialization']) ? $_GET['specialization'] : '';
$experience = isset($_GET['experience']) ? $_GET['experience'] : '';

$query = "SELECT * FROM trainers WHERE status = 'active'";
if (!empty($specialization)) {
    $query .= " AND specialization = '" . mysqli_real_escape_string($conn, $specialization) . "'";
}
if (!empty($experience)) {
    $query .= " AND experience >= " . intval($experience);
}

// Execute query
$result = mysqli_query($conn, $query);

// Check for errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>

<div class="container mt-5">
    <h2>Find Your Personal Trainer</h2>
    
    <!-- Filter Form -->
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="specialization" class="form-control">
                    <option value="">All Specializations</option>
                    <option value="Weight Loss">Weight Loss</option>
                    <option value="Muscle Building">Muscle Building</option>
                    <option value="Yoga">Yoga</option>
                    <!-- Add more options -->
                </select>
            </div>
            <div class="col-md-4">
                <select name="experience" class="form-control">
                    <option value="">Any Experience</option>
                    <option value="1">1+ Year</option>
                    <option value="3">3+ Years</option>
                    <option value="5">5+ Years</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    
    <!-- Trainers List -->
    <div class="row">
        <?php if (mysqli_num_rows($result) > 0) { ?>
            <?php while ($trainer = mysqli_fetch_assoc($result)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($trainer['FirstName']) . ' ' . htmlspecialchars($trainer['LastName']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($trainer['specialization']); ?></h6>
                            <p class="card-text">Experience: <?php echo htmlspecialchars($trainer['experience']); ?> years</p>
                            <p class="card-text">Rate: ₹<?php echo htmlspecialchars($trainer['hourly_rate']); ?> per hour</p>
                            <a href="trainer-details.php?id=<?php echo $trainer['trainer_id']; ?>" class="btn btn-primary">View Profile</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="text-center">No trainers found matching your criteria.</p>
        <?php } ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
