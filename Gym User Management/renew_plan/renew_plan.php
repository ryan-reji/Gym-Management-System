<?php
include '../config.php'; // Uncomment in actual use
session_start(); // Uncomment in actual use
$user_id = $_SESSION['id'];

// Fetch current active plan
// Commented PHP for HTML-only preview
$query = "SELECT pb.*, p.plan_type FROM plan_bookings pb
          JOIN plans p ON pb.plan_id = p.PlanId
          WHERE pb.user_id = $user_id
          ORDER BY pb.end_date DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$current_plan = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Renew Plan</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 20px; }
    .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
    .plans { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 20px; }
    .plan-card { padding: 20px; border: 1px solid #ccc; border-radius: 10px; background: #fafafa; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .plan-card:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
    button { background: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #0056b3; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Renew Your Plan</h2>

    <!-- Current plan -->
    <div class="current-plan">
      <h3>Current Plan</h3>
      <!-- Replace with PHP: -->
      
      <?php if ($current_plan): ?>
        <p><strong>Plan:</strong> <?= $current_plan['plan_type'] ?></p>
        <p><strong>Valid Till:</strong> <?= $current_plan['end_date'] ?></p>
      <?php else: ?>
        <p>No active plan found.</p>
      <?php endif; ?>
      
      
    </div>

    <!-- Available plans -->
<div class="available-plans">
  <h3>Choose a New Plan</h3>
  <div class="plans">
    <?php
    // Fetch all plans
    $plans = mysqli_query($conn, "SELECT * FROM plans");
    while ($plan = mysqli_fetch_assoc($plans)):
      $plan_id = $plan['PlanId'];
      $plan_name = $plan['plan_type'];
      $price = $plan['price'];
    ?>
      <div class="plan-card">
        <h4><?php echo $plan_name; ?></h4>
        <p>₹<?php echo $price; ?> / month</p>

        <!-- Add a dropdown for selecting duration -->
        <label for="duration_<?= $plan_id ?>">Choose Duration:</label>
        <select id="duration_<?= $plan_id ?>" name="duration" required>
          <option value="1">1 Month</option>
          <option value="3">3 Months</option>
          <option value="6">6 Months</option>
          <option value="12">12 Months</option>
        </select>

        <button onclick="renewPlan(<?php echo $plan_id; ?>)">Renew</button>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<script>
  function renewPlan(planId) {
    // Get the selected duration
    const duration = document.getElementById('duration_' + planId).value;

    // Create a form to submit
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "create_renew_order.php";

    // Add hidden inputs
    const planInput = document.createElement("input");
    planInput.type = "hidden";
    planInput.name = "plan_id";
    planInput.value = planId;
    form.appendChild(planInput);

    const durationInput = document.createElement("input");
    durationInput.type = "hidden";
    durationInput.name = "duration";
    durationInput.value = duration;
    form.appendChild(durationInput);

    document.body.appendChild(form);
    form.submit();
  }
</script>

</body>
</html>
