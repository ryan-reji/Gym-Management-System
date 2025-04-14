<?php
// BMI Scale page
require_once('utils.php');

$current_page = 'bmi';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management - BMI Scale</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php render_sidebar($current_page); ?>

    <div class="main-content">
        <div class="tab-container">
            <h2>BMI Scale</h2>
            <div class="bmi-container">
                <div class="bmi-box">
                    <div class="gender-toggle">
                        <button id="maleBtn" class="active">♂ Male</button>
                        <button id="femaleBtn">♀ Female</button>
                    </div>
                    <label>Height</label>
                    <input type="range" id="height" min="100" max="220" value="180">
                    <span id="heightValue">180 cm / 5'11"</span>
                    <label>Weight</label>
                    <input type="range" id="weight" min="30" max="150" value="70.5" step="0.5">
                    <span id="weightValue">70.5 kg / 155.43 lbs</span>
                    <label>Age</label>
                    <input type="number" id="age" value="25">
                    <h3>Your BMI Result</h3>
                    <h2 id="bmiResult">21.76</h2>
                    <p id="bmiCategory">Normal weight</p>
                    <p>Healthy BMI range: 18.5 - 24.9</p>
                    <div class="bmi-info-box">
                        <div class="bmi-info">
                            <h4>BMR</h4>
                            <p id="bmr">1710</p>
                            <small>calories/day</small>
                        </div>
                        <div class="bmi-info">
                            <h4>Daily Caloric Needs</h4>
                            <p id="calories">2651</p>
                            <small>calories</small>
                        </div>
                        <div class="bmi-info">
                            <h4>Daily Water Intake</h4>
                            <p id="water">2.5L</p>
                            <small>liters</small>
                        </div>
                    </div>
                </div>
                <div class="right-section">
                    <div class="results-box">
                        <h3>What Your Results Mean</h3>
                        <p id="resultText">Great job! Maintain a balanced diet and stay active.</p>
                    </div>
                    <div class="tips-container">
                        <div class="tip-box">
                            <h4>Diet Tips</h4>
                            <ul id="dietTips">
                                <li>Maintain a balanced diet</li>
                                <li>Stay active</li>
                            </ul>
                        </div>
                        <div class="tip-box">
                            <h4>Exercise Tips</h4>
                            <ul id="exerciseTips">
                                <li>Balanced workouts</li>
                                <li>Regular physical activity</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

            let bmr = gender === "male"
                ? (10 * weight) + (6.25 * height) - (5 * age) + 5
                : (10 * weight) + (6.25 * height) - (5 * age) - 161;

            document.getElementById("bmr").textContent = Math.round(bmr);
            document.getElementById("calories").textContent = Math.round(bmr * 1.55);
            document.getElementById("water").textContent = (weight * 0.035).toFixed(1) + "L";

            if (bmi < 18.5) {
                bmiResult.style.color = "blue";
                bmiCategory.textContent = "Underweight";
                resultText.textContent = "You are underweight. Consider increasing your calorie intake with nutritious food.";
                dietTips.innerHTML = "<li>Increase protein and healthy fats</li><li>Eat more frequent meals</li>";
                exerciseTips.innerHTML = "<li>Strength training workouts</li><li>Moderate cardio</li>";
            } else if (bmi >= 18.5 && bmi <= 24.9) {
                bmiResult.style.color = "green";
                bmiCategory.textContent = "Normal weight";
                resultText.textContent = "Great job! Maintain a balanced diet and stay active.";
                dietTips.innerHTML = "<li>Maintain a balanced diet</li><li>Stay active</li>";
                exerciseTips.innerHTML = "<li>Balanced workouts</li><li>Regular physical activity</li>";
            } else if (bmi >= 25 && bmi <= 29.9) {
                bmiResult.style.color = "orange";
                bmiCategory.textContent = "Overweight";
                resultText.textContent = "You are overweight. Focus on a healthy diet and regular exercise.";
                dietTips.innerHTML = "<li>Reduce processed foods</li><li>Control portion sizes</li>";
                exerciseTips.innerHTML = "<li>Increase cardio workouts</li><li>Strength training</li>";
            } else {
                bmiResult.style.color = "red";
                bmiCategory.textContent = "Obese";
                resultText.textContent = "Your BMI is high. Consider a structured fitness plan and diet adjustment.";
                dietTips.innerHTML = "<li>Avoid sugary foods</li><li>Drink plenty of water</li>";
                exerciseTips.innerHTML = "<li>High-intensity cardio</li><li>Weight training</li>";
            }
        };

        heightInput.addEventListener("input", calculate);
        weightInput.addEventListener("input", calculate);
        ageInput.addEventListener("input", calculate);

        calculate();
    </script>
</body>
</html>