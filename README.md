# Gym Management System

## Overview
The Gym Management System is a web-based application to streamline all major gym operations.

## Features

- **Member Registration and Login**: Members can create an account, log in, and manage their profile.
- **Membership Management**: Members can subscribe to different membership plans and renew them.
- **Trainer Attendance**: The system tracks trainers' attendance via QR codes.
- **Member Attendance**: Members' attendance is recorded when they check in and check out.
- **Payment Integration**: Payment for plans is integrated using Razorpay.
- **Database Setup**: A `.sql` file is included to set up the database locally.
- **Notifications**: Sound notifications for new messages in the group chat.
- **Group Chat**: Real-time group chat feature for trainers and members with message timestamps.
- **Admin Panel**: Admins can manually add users and manage memberships.

## Setup Instructions

### Prerequisites
- Install [XAMPP](https://www.apachefriends.org/index.html) to run the local server and MySQL.
- Make sure Apache and MySQL are running in XAMPP.

### Setting Up the Database
1. Download the `.sql` file from the repository (located in the `database/` folder).
2. Open XAMPP and click on **Admin** next to **MySQL** to open phpMyAdmin.
3. In phpMyAdmin, create a new database. (You can name it something like `gym_management`).
4. Select the newly created database and click on the **Import** tab.
5. Choose the `.sql` file from the `database/` folder and click **Go** to import the database.
6. The tables and data will now be available in your local MySQL database.

### Setting up Razorpay Gateway
1. Sign up on Razorpay's Websiter and navigate to dashboard.
2. Generate API Keys and use test keys for Razorpay Payment gateway.
3. You will need to add keys in following files: plan_section/submitpayment.php, Gym User Management/razorpay_checkout.php, user/trainer/payment.php.
4. Add keys in the designated areas. For example: 
    // Razorpay API Key (Replace with your actual key)
    $razorpay_api_key = 'razorpay_test_key';

### Configuration
- Update your PHP configuration to connect to the local database. In your project, find the configuration file where the database connection is defined (e.g., `config.php` or `db.php`).
- Set the database host to `localhost`, username to `root`, and leave the password blank (default settings in XAMPP):
  ```php
  $host = 'localhost';
  $username = 'root';
  $password = ''; // default is empty in XAMPP
  $dbname = 'gym_management'; // name of your database
  ```

### Running the Project
- Open the project folder in your local XAMPP directory (usually `htdocs/`).
- Access the project by opening a browser and going to `http://localhost/your_project_folder_name`.

## 👥 Contributors

- [@ryanreji](https://github.com/ryanreji) 
- [@nathanjohncy](https://github.com/nathan12agent)
- [@miguellopes](https://github.com/user11111111s) 


