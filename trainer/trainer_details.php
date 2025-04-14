<?php
session_start();
include "db_config.php";

// Check if trainer is logged in
if (!isset($_SESSION['trainer_id'])) {
    header("Location: login.php");
    exit();
}

$trainer_id = $_SESSION['trainer_id'];

// Fetch trainer details from the database
$stmt = $conn->prepare("SELECT FirstName, LastName, number, experience, hourly_rate, bio, profile_image FROM trainers WHERE trainer_id = ?");
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
$trainer = $result->fetch_assoc();

if (!$trainer) {
    die("Trainer not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Include Header -->
    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <div class="ml-64 p-6">
        <h2 class="text-3xl font-semibold mb-6">Trainer Details</h2>

        <div class="bg-white p-6 shadow-md rounded-lg max-w-xl">
            <div class="flex items-center mb-6">
                <?php
                $profilePic = !empty($trainer['profile_pic']) ? $trainer['profile_pic'] : 'default_profile.png';
                ?>
                <img src="<?= $profilePic; ?>" alt="Profile Picture" class="w-24 h-24 rounded-full border border-gray-300">
                
                <div class="ml-4">
                    <h3 class="text-xl font-bold"><?= htmlspecialchars($trainer['FirstName'] . ' ' . $trainer['LastName']); ?></h3>
                    <p class="text-gray-600">
                        <?= htmlspecialchars($trainer['bio']); ?>
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <p><strong>Mobile:</strong> <?= htmlspecialchars($trainer['number']); ?></p>
                <p><strong>Experience:</strong> <?= htmlspecialchars($trainer['experience']); ?></p>
                <p><strong>Hourly Rate:₹</strong> <?= htmlspecialchars($trainer['hourly_rate']); ?></p>
            </div>

            <!-- Change Password Button -->
            <div class="mt-6">
                <a href="change_password.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Change Password</a>
            </div>
        </div>
    </div>
</body>
</html>
