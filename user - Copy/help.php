<?php
// Default to 'help' tab
$current_page = 'help';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management - Help</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .main-content { padding: 20px; }

        /* Help Tab Styles */
        .tab-container {
            background-color: #1a1a1a; /* Dark background */
            padding: 30px;
            border-radius: 10px;
            width: 100%;
            max-width: 100%; /* Ensure it takes full width */
            box-sizing: border-box;
            margin: 0;
        }
        .tab-container h2 {
            font-size: 32px;
            color: #b74b4b; /* Orange heading */
            margin-bottom: 20px;
            text-align: left;
        }
        .tab-container h3 {
            font-size: 28px;
            color: #b74b4b; /* Orange subheading */
            margin-bottom: 15px;
            margin-top: 30px;
        }
        .tab-container p, .tab-container span, .tab-container li {
            color: white; /* White text */
            font-size: 16px;
            line-height: 1.6;
        }
        .help-section {
            background-color: #333;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .help-section h4 {
            color: #b74b4b;
            font-size: 22px;
            margin-bottom: 10px;
        }
        .help-list {
            list-style-type: disc;
            margin-left: 25px;
            margin-bottom: 15px;
        }
        .help-list li {
            margin-bottom: 10px;
        }
        .contact-info {
            background-color: #333;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
        .faq-item {
            background-color: #333;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .faq-question {
            font-weight: bold;
            font-size: 18px;
            color: #b74b4b;
            margin-bottom: 10px;
            cursor: pointer;
        }
        .faq-answer {
            padding-left: 15px;
            border-left: 2px solid #b74b4b;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo">Gym Shark</div>
    <ul>
        <li><a href="index.php?tab=home">Home</a></li>
        <li><a href="trainer">Bookings</a></li>
        <li><a href="index.php?tab=bmi">BMI Scale</a></li>
        <li><a href="index.php?tab=members">Member Info</a></li>
        <li class="active"><a href="help.php">Help</a></li>
    </ul>
    <div class="logout-container">
        <a href="../Login/logout.php" class="logout-button">Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="tab-container">
        <h2>Help & Support</h2>
        
        <div class="help-section">
            <h3>Getting Started</h3>
            <p>Welcome to the Gym Shark management system. Here's how to get the most out of your membership:</p>
            <ul class="help-list">
                <li><strong>Home Page:</strong> View your recent activities, upcoming classes, and attendance history at a glance.</li>
                <li><strong>Bookings:</strong> Schedule training sessions with our fitness professionals.</li>
                <li><strong>BMI Scale:</strong> Calculate your Body Mass Index and get personalized health recommendations.</li>
                <li><strong>Member Info:</strong> View and manage your membership details and plan information.</li>
            </ul>
        </div>
        
        <h3>Frequently Asked Questions</h3>
        
        <div class="faq-item">
            <div class="faq-question">How do I book a training session?</div>
            <div class="faq-answer">
                <p>To book a training session:</p>
                <ol class="help-list">
                    <li>Navigate to the "Bookings" tab in the sidebar</li>
                    <li>Select your preferred date and time</li>
                    <li>Choose the session duration and workout type</li>
                    <li>Click "Book Session" to confirm your booking</li>
                </ol>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">How do I renew my membership plan?</div>
            <div class="faq-answer">
                <p>To renew your membership plan:</p>
                <ol class="help-list">
                    <li>Go to the "Member Info" tab</li>
                    <li>Scroll down to the "Plan Details" section</li>
                    <li>Click the "RENEW PLAN" button at the bottom of the page</li>
                    <li>Follow the payment instructions to complete your renewal</li>
                </ol>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">What does my BMI result mean?</div>
            <div class="faq-answer">
                <p>Your BMI (Body Mass Index) provides a general indication of your weight category:</p>
                <ul class="help-list">
                    <li><strong>Below 18.5:</strong> Underweight - You may need to gain some weight</li>
                    <li><strong>18.5 to 24.9:</strong> Normal weight - Maintain your current healthy lifestyle</li>
                    <li><strong>25 to 29.9:</strong> Overweight - Consider making lifestyle adjustments</li>
                    <li><strong>30 and above:</strong> Obese - Medical advice recommended</li>
                </ul>
                <p>Remember, BMI is just one measure of health and doesn't account for factors like muscle mass, body composition, or individual health conditions.</p>
            </div>
        </div>
        
        <div class="faq-item">
            <div class="faq-question">How can I track my gym attendance?</div>
            <div class="faq-answer">
                <p>Your gym attendance is automatically tracked whenever you check in at the gym. You can view your attendance history on the Home page, where you'll find an attendance heat map displaying your visits for the current month.</p>
            </div>
        </div>
        
        <h3>Contact Support</h3>
        
        <div class="contact-info">
            <p>If you need further assistance, please contact our support team:</p>
            <ul class="help-list">
                <li><strong>Email:</strong> support@gymshark.com</li>
                <li><strong>Phone:</strong> (555) 123-4567</li>
                <li><strong>Hours:</strong> Monday-Friday, 8:00 AM - 8:00 PM</li>
                <li><strong>In Person:</strong> Visit the front desk during gym operating hours</li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Simple toggle for FAQ items
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            if (answer.style.display === 'none') {
                answer.style.display = 'block';
            } else {
                answer.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>