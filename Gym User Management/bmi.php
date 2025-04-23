<?php

require_once('utils.php');
require_once('config.php'); // Ensure database connection is available

session_start();
if (!isset($_SESSION['id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['id'];

// Fetch user and plan details
$member_data = get_member_data($user_id, $conn);
$plan_data = get_plan_details($user_id, $conn);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymShark - BMI Scale</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* BMI Specific Styles */
        .bmi-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .bmi-box {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }
        
        .bmi-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .bmi-box label {
            display: block;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
        }
        
        .bmi-box input[type="range"] {
            width: 100%;
            -webkit-appearance: none;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            margin-bottom: 0.75rem;
        }
        
        .bmi-box input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            background: var(--primary-color);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .bmi-box input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }
        
        .bmi-box input[type="number"] {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .bmi-box input[type="number"]:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .bmi-box span {
            display: block;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .gender-toggle {
            display: flex;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .gender-toggle button {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .gender-toggle button.active {
            background: var(--primary-color);
            color: white;
        }
        
        .bmi-box h3 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
        }
        
        .bmi-box h2 {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin: 0.5rem 0;
            transition: color 0.3s ease;
        }
        
        .bmi-box p {
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }
        
        .bmi-info-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
        }
        
        .bmi-info {
            text-align: center;
        }
        
        .bmi-info h4 {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .bmi-info p {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .bmi-info small {
            color: var(--text-tertiary);
            font-size: 0.75rem;
        }
        
        .right-section {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .results-box {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }
        
        .results-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .results-box h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .results-box h3::before {
            content: '\f14a';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .results-box p {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        
        .tips-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }
        
        .tip-box {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }
        
        .tip-box:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }
        
        .tip-box h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .tip-box h4::before {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .tip-box:first-child h4::before {
            content: '\f2e7';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        
        .tip-box:last-child h4::before {
            content: '\f44b';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        
        .tip-box ul {
            padding-left: 1.5rem;
            color: var(--text-secondary);
        }
        
        .tip-box li {
            margin-bottom: 0.5rem;
        }
        
        .bmi-scale {
            width: 100%;
            height: 10px;
            background: linear-gradient(to right, blue, green, orange, red);
            border-radius: 5px;
            position: relative;
            margin: 2rem 0;
        }
        
        .bmi-indicator {
            position: absolute;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            top: -3px;
            transform: translateX(-50%);
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
            transition: left 0.3s ease;
        }
        
        .bmi-scale-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 0.5rem;
        }
        
        .bmi-scale-labels span {
            font-size: 0.75rem;
            color: var(--text-tertiary);
        }

        .header {
            margin-bottom: 2rem;
        }

        @media screen and (max-width: 992px) {
            .bmi-container {
                grid-template-columns: 1fr;
            }
            
            .tips-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Mobile Navigation Toggle -->
        <div class="mobile-nav-toggle">
            <i class="fas fa-bars"></i>
        </div>

<!-- Sidebar Navigation -->
<nav class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-dumbbell logo-icon"></i>
                <h2>GymShark</h2>
                <div class="close-sidebar">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            
            <div class="user-profile">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
        <h3><?php echo htmlspecialchars($_SESSION["display_name"] ?? $_SESSION["username"]); ?></h3>
        <p>Member</p> <!-- Always Member -->
    </div>
            </div>

            <ul class="nav-links">
                <li>
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li>
                    <a href="../user/trainer/">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="members.php">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                </li>
                
                <li>
                    <a href="#">
                        <i class="fas fa-tasks"></i>
                        <span>Workout Plans</span>
                    </a>
                </li>
                <li class="active">
                    <a href="bmi.php">
                        <i class="fas fa-weight"></i>
                        <span>BMI Scale</span>
                    </a>
                </li>
                <li>
                    <a href="live_chat/Live_chat.php">
                        <i class="fas fa-weight"></i>
                        <span>Live Chat</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="../Login/logout.php" class="logout-button">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <div class="greeting">
                    <h1>BMI Scale</h1>
                    <p>Calculate and track your Body Mass Index</p>
                </div>
                <div class="header-actions">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="date-display">
                        <i class="fas fa-calendar"></i>
                        <span id="current-date">April 5, 2025</span>
                    </div>
                </div>
            </div>

            <div class="bmi-container">
                <div class="bmi-box">
                    <div class="gender-toggle">
                        <button id="maleBtn" class="active"><i class="fas fa-mars"></i> Male</button>
                        <button id="femaleBtn"><i class="fas fa-venus"></i> Female</button>
                    </div>
                    
                    <label><i class="fas fa-ruler-vertical"></i> Height</label>
                    <input type="range" id="height" min="100" max="220" value="180">
                    <span id="heightValue">180 cm / 5'11"</span>
                    
                    <label><i class="fas fa-weight"></i> Weight</label>
                    <input type="range" id="weight" min="30" max="150" value="70.5" step="0.5">
                    <span id="weightValue">70.5 kg / 155.43 lbs</span>
                    
                    <label><i class="fas fa-birthday-cake"></i> Age</label>
                    <input type="number" id="age" value="25">
                    
                    <h3><i class="fas fa-calculator"></i> Your BMI Result</h3>
                    <h2 id="bmiResult">21.76</h2>
                    <p id="bmiCategory">Normal weight</p>
                    
                    <div class="bmi-scale">
                        <div class="bmi-indicator" id="bmiIndicator" style="left: 40%;"></div>
                    </div>
                    <div class="bmi-scale-labels">
                        <span>Underweight</span>
                        <span>Normal</span>
                        <span>Overweight</span>
                        <span>Obese</span>
                    </div>
                    
                    <p>Healthy BMI range: 18.5 - 24.9</p>
                    
                    <div class="bmi-info-box">
                        <div class="bmi-info">
                            <h4>BMR</h4>
                            <p id="bmr">1710</p>
                            <small>calories/day</small>
                        </div>
                        <div class="bmi-info">
                            <h4>Daily Calories</h4>
                            <p id="calories">2651</p>
                            <small>calories</small>
                        </div>
                        <div class="bmi-info">
                            <h4>Water Intake</h4>
                            <p id="water">2.5L</p>
                            <small>liters</small>
                        </div>
                    </div>
                </div>
                
                <div class="right-section">
                    <div class="results-box">
                        <h3>What Your Results Mean</h3>
                        <p id="resultText">Great job! Maintain a balanced diet and stay active to keep your body weight within the healthy range. Your BMI indicates you're at a reduced risk for many health issues related to weight.</p>
                    </div>
                    
                    <div class="tips-container">
                        <div class="tip-box">
                            <h4>Diet Tips</h4>
                            <ul id="dietTips">
                                <li>Maintain a balanced diet</li>
                                <li>Stay hydrated throughout the day</li>
                                <li>Control portion sizes</li>
                                <li>Limit processed foods and sugars</li>
                            </ul>
                        </div>
                        <div class="tip-box">
                            <h4>Exercise Tips</h4>
                            <ul id="exerciseTips">
                                <li>Balanced workouts combining cardio and strength</li>
                                <li>Aim for 150 minutes of moderate activity weekly</li>
                                <li>Include flexibility and balance training</li>
                                <li>Find activities you enjoy for consistency</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle mobile sidebar
        document.querySelector('.mobile-nav-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.add('active');
        });
        
        document.querySelector('.close-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
        });

        // Current date display
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);

        const heightInput = document.getElementById("height");
        const weightInput = document.getElementById("weight");
        const ageInput = document.getElementById("age");
        const maleBtn = document.getElementById("maleBtn");
        const femaleBtn = document.getElementById("femaleBtn");
        const bmiResult = document.getElementById("bmiResult");
        const bmiCategory = document.getElementById("bmiCategory");
        const resultText = document.getElementById("resultText");
        const dietTips = document.getElementById("dietTips");
        const exerciseTips = document.getElementById("exerciseTips");
        const heightValue = document.getElementById("heightValue");
        const weightValue = document.getElementById("weightValue");
        const bmiIndicator = document.getElementById("bmiIndicator");

        let gender = "male";

        const cmToFeetInches = (cm) => {
            let totalInches = cm * 0.393701;
            let feet = Math.floor(totalInches / 12);
            let inches = Math.round(totalInches % 12);
            return `${feet}'${inches}"`;
        };

        const kgToLbs = (kg) => (kg * 2.20462).toFixed(2);

        maleBtn.addEventListener("click", () => {
            gender = "male";
            maleBtn.classList.add("active");
            femaleBtn.classList.remove("active");
            calculate();
        });

        femaleBtn.addEventListener("click", () => {
            gender = "female";
            femaleBtn.classList.add("active");
            maleBtn.classList.remove("active");
            calculate();
        });

        const calculate = () => {
            let height = parseFloat(heightInput.value);
            let weight = parseFloat(weightInput.value);
            let age = parseFloat(ageInput.value);

            heightValue.textContent = `${height} cm / ${cmToFeetInches(height)}`;
            weightValue.textContent = `${weight} kg / ${kgToLbs(weight)} lbs`;

            let heightInMeters = height / 100;
            let bmi = (weight / (heightInMeters * heightInMeters)).toFixed(2);
            bmiResult.textContent = bmi;

            // Position the indicator on the BMI scale
            let position = 0;
            if (bmi < 18.5) {
                position = (bmi / 18.5) * 25; // 0-25% of the scale
            } else if (bmi >= 18.5 && bmi <= 24.9) {
                position = 25 + ((bmi - 18.5) / (24.9 - 18.5)) * 25; // 25-50% of the scale
            } else if (bmi >= 25 && bmi <= 29.9) {
                position = 50 + ((bmi - 25) / (29.9 - 25)) * 25; // 50-75% of the scale
            } else {
                position = 75 + Math.min((bmi - 30) / 10, 1) * 25; // 75-100% of the scale, max at BMI 40
            }
            bmiIndicator.style.left = `${position}%`;

            let bmr = gender === "male"
                ? (10 * weight) + (6.25 * height) - (5 * age) + 5
                : (10 * weight) + (6.25 * height) - (5 * age) - 161;

            document.getElementById("bmr").textContent = Math.round(bmr);
            document.getElementById("calories").textContent = Math.round(bmr * 1.55);
            document.getElementById("water").textContent = (weight * 0.035).toFixed(1) + "L";

            if (bmi < 18.5) {
                bmiResult.style.color = "#4287f5"; // Blue
                bmiCategory.textContent = "Underweight";
                resultText.textContent = "You are underweight. Consider increasing your calorie intake with nutritious food and consulting with a healthcare provider for personalized guidance.";
                dietTips.innerHTML = "<li>Increase protein and healthy fats in your diet</li><li>Eat more frequent, nutrient-dense meals</li><li>Add healthy snacks between meals</li><li>Consider protein smoothies for easy calories</li>";
                exerciseTips.innerHTML = "<li>Focus on strength training to build muscle</li><li>Moderate cardio to maintain cardiovascular health</li><li>Ensure adequate rest between workouts</li><li>Consider working with a trainer for a personalized plan</li>";
            } else if (bmi >= 18.5 && bmi <= 24.9) {
                bmiResult.style.color = "#42f54b"; // Green
                bmiCategory.textContent = "Normal weight";
                resultText.textContent = "Great job! Maintain a balanced diet and stay active to keep your body weight within the healthy range. Your BMI indicates you're at a reduced risk for many health issues related to weight.";
                dietTips.innerHTML = "<li>Maintain a balanced diet with variety</li><li>Focus on whole, unprocessed foods</li><li>Stay hydrated throughout the day</li><li>Practice mindful eating habits</li>";
                exerciseTips.innerHTML = "<li>Mix cardio and strength training for balance</li><li>Aim for 150 minutes of moderate activity weekly</li><li>Include flexibility and balance exercises</li><li>Stay consistent with your routine</li>";
            } else if (bmi >= 25 && bmi <= 29.9) {
                bmiResult.style.color = "#f5a742"; // Orange
                bmiCategory.textContent = "Overweight";
                resultText.textContent = "You are in the overweight category. Focus on a healthy diet and regular exercise to gradually move toward a healthier weight. Small, sustainable changes can make a big difference.";
                dietTips.innerHTML = "<li>Reduce processed foods and added sugars</li><li>Control portion sizes at meals</li><li>Increase fiber intake with fruits and vegetables</li><li>Monitor calorie intake with a food journal</li>";
                exerciseTips.innerHTML = "<li>Increase cardio workouts for calorie burning</li><li>Include strength training to build metabolism-boosting muscle</li><li>Try HIIT workouts for efficiency</li><li>Find activities you enjoy for better adherence</li>";
            } else {
                bmiResult.style.color = "#f54242"; // Red
                bmiCategory.textContent = "Obese";
                resultText.textContent = "Your BMI falls in the obese category, which may increase health risks. Consider consulting with healthcare professionals for a structured fitness and nutrition plan tailored to your needs.";
                dietTips.innerHTML = "<li>Work with a dietitian for a personalized plan</li><li>Focus on whole foods and avoid processed items</li><li>Track your food intake to identify patterns</li><li>Stay well-hydrated and limit sugary drinks</li>";
                exerciseTips.innerHTML = "<li>Start with low-impact activities like walking or swimming</li><li>Gradually increase intensity as fitness improves</li><li>Include strength training for better body composition</li><li>Consider a personal trainer for proper form and technique</li>";
            }
        };

        heightInput.addEventListener("input", calculate);
        weightInput.addEventListener("input", calculate);
        ageInput.addEventListener("input", calculate);

        calculate();
    </script>
</body>
</html>