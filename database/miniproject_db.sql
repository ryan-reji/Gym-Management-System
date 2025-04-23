-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 08:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `miniproject_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `phone_number`, `password_hash`) VALUES
(5023128, 'Miguel', '7887788798', '12345678');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `product_number` varchar(50) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `status` enum('active','maintenance','out-of-order') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `name`, `product_number`, `purchase_date`, `status`, `description`, `created_at`) VALUES
(1, 'Treadmill', '1', '2005-03-23', 'active', 'GG', '2025-03-29 15:19:31'),
(2, 'Treadmill', '3', '2025-01-01', 'active', 'lsnvreip;fjewip;', '2025-03-31 06:52:28');

-- --------------------------------------------------------

--
-- Table structure for table `gym`
--

CREATE TABLE `gym` (
  `gym_id` int(11) NOT NULL,
  `gym_name` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `current_occupancy` int(11) DEFAULT NULL,
  `max_capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gym_attendance`
--

CREATE TABLE `gym_attendance` (
  `id` int(11) NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gym_attendance`
--

INSERT INTO `gym_attendance` (`id`, `check_in_time`, `check_out_time`, `user_id`, `date`) VALUES
(1, '2025-03-29 18:30:48', '2025-03-29 18:32:04', 37, '2025-03-29'),
(2, '2025-03-29 20:36:49', '2025-03-29 20:39:46', 37, '2025-03-29'),
(8, '2025-03-31 13:09:49', '2025-03-31 13:10:01', 37, '2025-03-31'),
(9, '2025-03-31 16:42:40', '2025-03-31 16:42:50', 37, '2025-03-31'),
(10, '2025-03-31 16:50:03', '2025-03-31 16:50:12', 37, '2025-03-31'),
(11, '2025-03-31 16:52:28', '2025-03-31 16:52:35', 37, '2025-03-31'),
(12, '2025-04-03 10:27:38', '2025-04-03 10:28:16', 43, '2025-04-03'),
(13, '2025-04-04 20:38:01', '2025-04-04 20:38:17', 43, '2025-04-04'),
(15, '2025-04-16 09:49:30', NULL, 50, '2025-04-16');

-- --------------------------------------------------------

--
-- Table structure for table `last_read`
--

CREATE TABLE `last_read` (
  `user_id` bigint(11) NOT NULL,
  `last_read_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` bigint(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `message`, `created_at`) VALUES
(1, 43, 'Hello', '2025-04-14 16:31:06'),
(2, 43, 'Hello', '2025-04-14 16:31:58'),
(3, 4, 'Hi ryan', '2025-04-14 16:33:32'),
(4, 50, 'hi ryan', '2025-04-15 14:03:01'),
(5, 43, 'hu', '2025-04-15 14:03:11'),
(6, 50, 'hello', '2025-04-16 04:26:45'),
(7, 43, 'hello infdf', '2025-04-16 04:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `online_users`
--

CREATE TABLE `online_users` (
  `user_id` bigint(11) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer') DEFAULT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `PlanId` int(11) NOT NULL,
  `plan_type` enum('basic','standard','premium') DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`PlanId`, `plan_type`, `price`) VALUES
(1, 'basic', 6000.00),
(2, 'standard', 10000.00),
(3, 'premium', 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `plan_bookings`
--

CREATE TABLE `plan_bookings` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `plan_duration` enum('1M','3M','6M','1Y') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plan_bookings`
--

INSERT INTO `plan_bookings` (`id`, `plan_id`, `user_id`, `plan_duration`, `start_date`, `end_date`, `status`, `total_cost`, `razorpay_payment_id`) VALUES
(2, 2, 2, '3M', '2025-03-23', '2025-06-23', 'completed', 30000.00, 'pay_QAJD2yHeGzNOxb'),
(3, 1, 3, '3M', '2025-03-23', '2025-06-23', 'completed', 18000.00, 'pay_QAJYCvDtvCenJb'),
(4, 1, 4, '3M', '2025-03-24', '2025-06-24', 'completed', 18000.00, 'pay_QAa9U0Sz9AND1W'),
(5, 1, 6, '3M', '2025-03-24', '2025-06-24', 'completed', 18000.00, 'pay_QAbHbinjOpBuwW'),
(19, 1, 37, '1M', '2025-03-25', '2025-04-25', 'completed', 18000.00, NULL),
(20, 1, 38, '3M', '2025-03-26', '2025-06-26', 'completed', 18000.00, 'pay_QBRKGFaodhnWnc'),
(24, 1, 42, '3M', '2025-03-26', '2025-06-26', 'completed', 18000.00, 'pay_QBTD8a02IGJubL'),
(25, 2, 43, '3M', '2025-04-02', '2025-07-02', 'completed', 30000.00, 'pay_QEFjLeV5M0ogjV'),
(26, 2, 44, '3M', '2025-04-03', '2025-07-03', 'completed', 30000.00, 'pay_QESrTX12x1bbfS'),
(28, 2, 48, '1M', '2025-04-09', '2025-05-09', 'completed', 10000.00, 'pay_QGqq49XQ2vDkMa'),
(29, 1, 43, NULL, '2025-07-03', '2025-08-03', 'completed', 6000.00, 'pay_QICgytQA7mbbUa'),
(31, 2, 50, '1M', '2025-04-15', '2025-05-15', 'completed', 10000.00, 'pay_QJMJd3gYCCpqgI');

-- --------------------------------------------------------

--
-- Table structure for table `trainers`
--

CREATE TABLE `trainers` (
  `trainer_id` int(11) NOT NULL,
  `trainer_username` varchar(50) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `address` text NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `blood_type` varchar(5) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainers`
--

INSERT INTO `trainers` (`trainer_id`, `trainer_username`, `FirstName`, `LastName`, `specialization`, `experience`, `hourly_rate`, `number`, `email`, `dob`, `address`, `password_hash`, `status`, `blood_type`, `profile_pic`, `join_date`, `created_at`, `updated_at`) VALUES
(1, 'john123', 'John', 'Doe', 'Strength', 5, 300.00, '8989887667', 'johndoe@gmail.com', NULL, '', '$2y$10$MVI7TMfh6yX5TqcSSYs.P.Yit/rNKogo8gZlVhZmNe5A51B1Y8qxe', 'active', NULL, NULL, NULL, '2025-03-24 08:35:55', '2025-03-29 04:51:45'),
(2, 'johncena123', 'John', 'Cena', 'cardio', 5, 350.00, '8676867680', 'ryanrejir@gmail.com', '1997-05-23', ';AUEHd', '$2y$10$X83RALimYChfg1Oc6T4TQO.Pg8LeZkZrKNzZy0bJJWYM01GueyP8y', 'active', 'A+', 'uploads/trainers/1743227034_FMur3.png', '2025-03-29', '2025-03-29 05:43:54', '2025-03-29 05:43:54'),
(3, 'roman123', 'Roman', 'Reigns', 'bodybuilding', 6, 400.00, '8675868890', 'ryanrejir@gmail.com', '1997-01-01', 'pmdodmnowdn', '$2y$10$.6Zg3L9pNGkgRRujqLloNOwtzrUSW34FnYEtK9liNp80fqN6jxrEm', 'active', 'A-', 'uploads/trainers/1743407433_Screenshot 2025-03-31 130130.png', '2025-03-31', '2025-03-31 07:50:33', '2025-03-31 07:50:33'),
(4, 'bruce123', 'Bruce', 'Wayne', 'crossfit', 5, 350.00, '8678798970', 'ryanrejir@gmail.com', '1991-02-12', 'oewdnewjoinewoj', '$2y$10$HRFkbfTzrIcHONt/IIGe5usRspWq3bDHZgVILn9YAiChsYSIJhEM2', 'active', 'A+', 'uploads/trainers/1743407659_Screenshot 2025-03-31 125802.png', '2025-03-31', '2025-03-31 07:54:19', '2025-03-31 07:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_attendance`
--

CREATE TABLE `trainer_attendance` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_attendance`
--

INSERT INTO `trainer_attendance` (`id`, `trainer_id`, `check_in_time`, `check_out_time`, `date`) VALUES
(1, 4, '2025-03-31 16:52:50', '2025-03-31 16:53:24', '2025-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_availability`
--

CREATE TABLE `trainer_availability` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `time_from` time NOT NULL,
  `time_to` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_availability`
--

INSERT INTO `trainer_availability` (`id`, `trainer_id`, `date`, `day_of_week`, `time_from`, `time_to`) VALUES
(1, 1, NULL, 'Monday', '06:00:00', '18:00:00'),
(2, 1, NULL, 'Tuesday', '06:00:00', '18:00:00'),
(3, 1, NULL, 'Wednesday', '06:00:00', '18:00:00'),
(4, 1, NULL, 'Thursday', '06:00:00', '18:00:00'),
(5, 1, NULL, 'Friday', '06:00:00', '18:00:00'),
(6, 1, NULL, 'Saturday', '06:00:00', '18:00:00'),
(7, 2, NULL, 'Monday', '06:00:00', '14:00:00'),
(8, 2, NULL, 'Sunday', '06:00:00', '14:00:00'),
(9, 3, NULL, 'Monday', '06:00:00', '14:00:00'),
(10, 3, NULL, 'Tuesday', '06:00:00', '14:00:00'),
(11, 3, NULL, 'Wednesday', '06:00:00', '14:00:00'),
(12, 3, NULL, 'Thursday', '06:00:00', '14:00:00'),
(13, 4, NULL, 'Monday', '06:00:00', '18:00:00'),
(14, 4, NULL, 'Saturday', '06:00:00', '18:00:00'),
(15, 4, NULL, 'Sunday', '06:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_bookings`
--

CREATE TABLE `trainer_bookings` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `booking_start_date` date NOT NULL,
  `booking_end_date` date NOT NULL,
  `default_session_time` time NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `booking_status` enum('active','cancelled','completed') NOT NULL DEFAULT 'active',
  `razorpay_payment_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_bookings`
--

INSERT INTO `trainer_bookings` (`id`, `user_id`, `trainer_id`, `booking_start_date`, `booking_end_date`, `default_session_time`, `status`, `total_cost`, `payment_status`, `booking_status`, `razorpay_payment_id`) VALUES
(4, 2, 1, '2025-03-23', '2025-04-21', '06:00:00', NULL, 9000.00, '', 'active', 'pay_QAJW1JKPsrIMmP'),
(5, 4, 1, '2025-03-25', '2025-04-23', '13:00:00', NULL, 9000.00, '', 'active', 'pay_QAaB316aj1ZQq3'),
(7, 43, 1, '2025-04-07', '2025-05-06', '07:00:00', NULL, 9000.00, '', 'active', 'pay_QFkr3TpHHpepzE');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_reschedules`
--

CREATE TABLE `trainer_reschedules` (
  `trainer_reschedule_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `request_type` enum('part-time','full-time','leave') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `new_start_time` time DEFAULT NULL,
  `new_end_time` time DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_reschedules`
--

INSERT INTO `trainer_reschedules` (`trainer_reschedule_id`, `trainer_id`, `request_type`, `start_date`, `end_date`, `new_start_time`, `new_end_time`, `status`, `created_at`) VALUES
(2, 1, 'leave', '2025-03-26', '2025-03-26', NULL, NULL, 'approved', '2025-03-23 18:12:20'),
(3, 1, 'leave', '2025-04-01', '2025-04-01', NULL, NULL, 'approved', '2025-03-31 11:51:57'),
(4, 1, 'leave', '2025-04-08', '2025-04-08', NULL, NULL, 'approved', '2025-04-06 11:09:05'),
(5, 1, 'leave', '2025-04-10', '2025-04-10', NULL, NULL, 'approved', '2025-04-06 11:27:02'),
(6, 1, 'leave', '2025-04-18', '2025-04-18', NULL, NULL, 'approved', '2025-04-15 13:54:54');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_sessions`
--

CREATE TABLE `trainer_sessions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `session_duration` int(11) NOT NULL,
  `session_status` enum('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_sessions`
--

INSERT INTO `trainer_sessions` (`id`, `booking_id`, `trainer_id`, `session_date`, `session_time`, `session_duration`, `session_status`, `notes`) VALUES
(26, 4, 1, '2025-03-24', '06:00:00', 0, 'scheduled', NULL),
(27, 4, 1, '2025-03-25', '06:00:00', 0, 'scheduled', NULL),
(28, 4, 1, '2025-03-26', '00:00:00', 0, 'cancelled', NULL),
(29, 4, 1, '2025-03-27', '06:00:00', 0, 'scheduled', NULL),
(30, 4, 1, '2025-03-28', '06:00:00', 0, 'scheduled', NULL),
(31, 4, 1, '2025-03-29', '06:00:00', 0, 'scheduled', NULL),
(32, 4, 1, '2025-03-31', '06:00:00', 0, 'scheduled', NULL),
(33, 4, 1, '2025-04-01', '06:00:00', 0, 'cancelled', NULL),
(34, 4, 1, '2025-04-02', '06:00:00', 0, 'scheduled', NULL),
(35, 4, 1, '2025-04-03', '06:00:00', 0, 'scheduled', NULL),
(36, 4, 1, '2025-04-04', '06:00:00', 0, 'scheduled', NULL),
(37, 4, 1, '2025-04-05', '06:00:00', 0, 'scheduled', NULL),
(38, 4, 1, '2025-04-07', '06:00:00', 0, 'scheduled', NULL),
(39, 4, 1, '2025-04-08', '06:00:00', 0, 'cancelled', NULL),
(40, 4, 1, '2025-04-09', '06:00:00', 0, 'scheduled', NULL),
(41, 4, 1, '2025-04-10', '06:00:00', 0, 'cancelled', NULL),
(42, 4, 1, '2025-04-11', '06:00:00', 0, 'scheduled', NULL),
(43, 4, 1, '2025-04-12', '06:00:00', 0, 'scheduled', NULL),
(44, 4, 1, '2025-04-14', '06:00:00', 0, 'scheduled', NULL),
(45, 4, 1, '2025-04-15', '06:00:00', 0, 'scheduled', NULL),
(46, 4, 1, '2025-04-16', '06:00:00', 0, 'scheduled', NULL),
(47, 4, 1, '2025-04-17', '06:00:00', 0, 'scheduled', NULL),
(48, 4, 1, '2025-04-18', '06:00:00', 0, 'cancelled', NULL),
(49, 4, 1, '2025-04-19', '06:00:00', 0, 'scheduled', NULL),
(50, 4, 1, '2025-04-21', '06:00:00', 0, 'scheduled', NULL),
(51, 5, 1, '2025-03-25', '13:00:00', 0, 'scheduled', NULL),
(52, 5, 1, '2025-03-26', '13:00:00', 0, 'scheduled', NULL),
(53, 5, 1, '2025-03-27', '13:00:00', 0, 'scheduled', NULL),
(54, 5, 1, '2025-03-28', '13:00:00', 0, 'scheduled', NULL),
(55, 5, 1, '2025-03-29', '13:00:00', 0, 'scheduled', NULL),
(56, 5, 1, '2025-03-31', '13:00:00', 0, 'scheduled', NULL),
(57, 5, 1, '2025-04-01', '13:00:00', 0, 'cancelled', NULL),
(58, 5, 1, '2025-04-02', '13:00:00', 0, 'scheduled', NULL),
(59, 5, 1, '2025-04-03', '13:00:00', 0, 'scheduled', NULL),
(60, 5, 1, '2025-04-04', '13:00:00', 0, 'scheduled', NULL),
(61, 5, 1, '2025-04-05', '13:00:00', 0, 'scheduled', NULL),
(62, 5, 1, '2025-04-07', '13:00:00', 0, 'scheduled', NULL),
(63, 5, 1, '2025-04-08', '13:00:00', 0, 'cancelled', NULL),
(64, 5, 1, '2025-04-09', '13:00:00', 0, 'scheduled', NULL),
(65, 5, 1, '2025-04-10', '13:00:00', 0, 'cancelled', NULL),
(66, 5, 1, '2025-04-11', '13:00:00', 0, 'scheduled', NULL),
(67, 5, 1, '2025-04-12', '13:00:00', 0, 'scheduled', NULL),
(68, 5, 1, '2025-04-14', '13:00:00', 0, 'scheduled', NULL),
(69, 5, 1, '2025-04-15', '13:00:00', 0, 'scheduled', NULL),
(70, 5, 1, '2025-04-16', '13:00:00', 0, 'scheduled', NULL),
(71, 5, 1, '2025-04-17', '13:00:00', 0, 'scheduled', NULL),
(72, 5, 1, '2025-04-18', '13:00:00', 0, 'cancelled', NULL),
(73, 5, 1, '2025-04-19', '13:00:00', 0, 'scheduled', NULL),
(74, 5, 1, '2025-04-21', '13:00:00', 0, 'scheduled', NULL),
(75, 5, 1, '2025-04-22', '13:00:00', 0, 'scheduled', NULL),
(76, 5, 1, '2025-04-23', '13:00:00', 0, 'scheduled', NULL),
(103, 7, 1, '2025-04-07', '07:00:00', 0, 'scheduled', NULL),
(104, 7, 1, '2025-04-08', '07:00:00', 0, 'scheduled', NULL),
(105, 7, 1, '2025-04-09', '07:00:00', 0, 'scheduled', NULL),
(106, 7, 1, '2025-04-10', '07:00:00', 0, 'cancelled', NULL),
(107, 7, 1, '2025-04-11', '07:00:00', 0, 'scheduled', NULL),
(108, 7, 1, '2025-04-12', '07:00:00', 0, 'scheduled', NULL),
(109, 7, 1, '2025-04-14', '07:00:00', 0, 'scheduled', NULL),
(110, 7, 1, '2025-04-15', '07:00:00', 0, 'scheduled', NULL),
(111, 7, 1, '2025-04-16', '07:00:00', 0, 'scheduled', NULL),
(112, 7, 1, '2025-04-17', '07:00:00', 0, 'scheduled', NULL),
(113, 7, 1, '2025-04-18', '07:00:00', 0, 'cancelled', NULL),
(114, 7, 1, '2025-04-19', '07:00:00', 0, 'scheduled', NULL),
(115, 7, 1, '2025-04-21', '07:00:00', 0, 'scheduled', NULL),
(116, 7, 1, '2025-04-22', '07:00:00', 0, 'scheduled', NULL),
(117, 7, 1, '2025-04-23', '07:00:00', 0, 'scheduled', NULL),
(118, 7, 1, '2025-04-24', '07:00:00', 0, 'scheduled', NULL),
(119, 7, 1, '2025-04-25', '07:00:00', 0, 'scheduled', NULL),
(120, 7, 1, '2025-04-26', '07:00:00', 0, 'scheduled', NULL),
(121, 7, 1, '2025-04-28', '07:00:00', 0, 'scheduled', NULL),
(122, 7, 1, '2025-04-29', '07:00:00', 0, 'scheduled', NULL),
(123, 7, 1, '2025-04-30', '07:00:00', 0, 'scheduled', NULL),
(124, 7, 1, '2025-05-01', '07:00:00', 0, 'scheduled', NULL),
(125, 7, 1, '2025-05-02', '07:00:00', 0, 'scheduled', NULL),
(126, 7, 1, '2025-05-03', '07:00:00', 0, 'scheduled', NULL),
(127, 7, 1, '2025-05-05', '07:00:00', 0, 'scheduled', NULL),
(128, 7, 1, '2025-05-06', '07:00:00', 0, 'scheduled', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(255) NOT NULL,
  `FirstName` varchar(255) DEFAULT NULL,
  `LastName` varchar(255) DEFAULT NULL,
  `number` varchar(15) DEFAULT NULL,
  `gender` enum('m','f','o') DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `reset_token_hash` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `reg_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT NULL,
  `payment_mode` varchar(20) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `last_active` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `FirstName`, `LastName`, `number`, `gender`, `email`, `dob`, `username`, `display_name`, `password_hash`, `reset_token_hash`, `reset_token_expires_at`, `status`, `reg_no`, `address`, `blood_type`, `duration`, `amount`, `balance`, `payment_mode`, `profile_pic`, `last_active`) VALUES
(2, 'Miguel', 'Lopes', '9878676787', 'm', 'miguel@gmail.com', '2007-02-07', 'miguel123', NULL, '$2y$10$ho1yqngCB.ihOJNXq9eMb.cRIwdq6mZwcrnfPHdU5MKKRyNdEWJ92', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(3, 'Divine', 'Marshal', '7888676655', 'm', 'divine@gmail.com', '2006-02-15', 'divine123', NULL, '$2y$10$oGwKF4Wa3bDdCR7BrRdrYOCsa9t0l5eCf.FBhX0cDoo.f.bo.E61.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(4, 'nathan ', 'johncy', '1234726812', 'm', 'nathan@gmail.com', '2005-04-10', 'nathan_07', NULL, '$2y$10$yHGk37a8hYcrwtcb2b.VhOltDMsUpasSnLaJ.5HVBu/6iqHQsxujy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(6, 'savio', 'fransis', '1298348745', 'm', 'savio@gmail.com', '2005-04-10', 'savio18', NULL, '$2y$10$2fUqjjS2zbDzvkcbnj1JmOOSlkG3SpmfxkDuWPyNXrbCf.Iacup/u', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(7, 'Ved', 'Teredesai', '8777889987', 'm', 'ved@gmail.com', '2006-06-14', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Thane', 'A+', NULL, NULL, NULL, NULL, 'uploads/67e29e1875127.png', '2025-04-14 15:39:28'),
(37, 'Akash', 'Singh', '1234567890', 'm', 'gymsharkuser@gmail.com', '2005-06-14', 'akash123', NULL, NULL, NULL, NULL, NULL, NULL, 'adss', 'A-', 3, NULL, NULL, NULL, 'uploads/67e2f32449b2a.png', '2025-04-14 15:39:28'),
(38, 'Sagar', 'Singh', '8777889990', 'm', 'sagar@gmail.com', '2005-06-14', 'sagar123', NULL, '$2y$10$tl77GOOScA6xzZVmOS5JuujQ6GP26I6b4Vb31oRbrR5Tkn8ZFVP5u', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(42, 'Annie', 'Reji', '8776857689', 'f', 'anabeenareji@gmail.com', '2002-06-10', 'annie123', NULL, '$2y$10$zhaSRlIkYHWdSqWLBf.pH.l8c8XqRYZE4n5PKX/aV8NooVhXAj3RK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(43, 'Ryan', 'Reji', '8787878787', 'm', 'ryanrejir@gmail.com', '2005-03-23', 'ryan_reji', NULL, '$2y$10$DuyKxYCnHwdEilaV2riSUOlEmgMRp8Da/r0bU3G4vS45pMRk.x9x.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(44, 'Divine', 'Marshal', '9898788990', 'm', 'divinemarshal33@gmail.com', '2005-03-23', 'divine@1234', NULL, '$2y$10$EPisExQPsMicXiF/0KKyYOFePDN9Wg4C2vmYoJ1EfskMClR2lytla', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(47, 'sample', 'Fourteen', '7676767670', 'm', 'sample14@gmail.com', '2007-03-23', 'sample14', NULL, '$2y$10$nAbvE5cTQCEJp.Tr17dehuoG3RhJxjEV/xgsnROnmfiDkdv0OqJ/K', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(48, 'Sample', 'Eighteen', '8695869790', 'f', 'sample18@gmail.com', '2003-10-10', 'sample18', NULL, '$2y$10$hCmGhhr7isFjzHBnK88GSOnoTzk8mUWQPNTIIY/08Mw1cG.PdTB4y', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-14 15:39:28'),
(50, 'Miguel', 'Lopes', '9898989970', 'm', 'migueljr3118@gmail.com', '2006-03-23', 'miguel@123', NULL, '$2y$10$k//l3J.6qaZAVtiq1ydXb.OGueJk6jsEeW/2DXapU5/LfRtIAkq2G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-04-15 14:01:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_number` (`product_number`);

--
-- Indexes for table `gym`
--
ALTER TABLE `gym`
  ADD PRIMARY KEY (`gym_id`);

--
-- Indexes for table `gym_attendance`
--
ALTER TABLE `gym_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gym_attendance_user` (`user_id`);

--
-- Indexes for table `last_read`
--
ALTER TABLE `last_read`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `online_users`
--
ALTER TABLE `online_users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`PlanId`);

--
-- Indexes for table `plan_bookings`
--
ALTER TABLE `plan_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trainers`
--
ALTER TABLE `trainers`
  ADD PRIMARY KEY (`trainer_id`);

--
-- Indexes for table `trainer_attendance`
--
ALTER TABLE `trainer_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trainer_attendance` (`trainer_id`);

--
-- Indexes for table `trainer_availability`
--
ALTER TABLE `trainer_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `trainer_reschedules`
--
ALTER TABLE `trainer_reschedules`
  ADD PRIMARY KEY (`trainer_reschedule_id`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `trainer_sessions`
--
ALTER TABLE `trainer_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `fk_trainer_sessions_trainer` (`trainer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5023129;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gym_attendance`
--
ALTER TABLE `gym_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plan_bookings`
--
ALTER TABLE `plan_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `trainers`
--
ALTER TABLE `trainers`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trainer_attendance`
--
ALTER TABLE `trainer_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainer_availability`
--
ALTER TABLE `trainer_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `trainer_reschedules`
--
ALTER TABLE `trainer_reschedules`
  MODIFY `trainer_reschedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `trainer_sessions`
--
ALTER TABLE `trainer_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gym_attendance`
--
ALTER TABLE `gym_attendance`
  ADD CONSTRAINT `fk_gym_attendance_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `last_read`
--
ALTER TABLE `last_read`
  ADD CONSTRAINT `last_read_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_users`
--
ALTER TABLE `online_users`
  ADD CONSTRAINT `online_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `plan_bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_bookings`
--
ALTER TABLE `plan_bookings`
  ADD CONSTRAINT `plan_bookings_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`PlanId`) ON DELETE CASCADE,
  ADD CONSTRAINT `plan_bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_attendance`
--
ALTER TABLE `trainer_attendance`
  ADD CONSTRAINT `fk_trainer_attendance` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_availability`
--
ALTER TABLE `trainer_availability`
  ADD CONSTRAINT `trainer_availability_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_bookings`
--
ALTER TABLE `trainer_bookings`
  ADD CONSTRAINT `trainer_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_bookings_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_reschedules`
--
ALTER TABLE `trainer_reschedules`
  ADD CONSTRAINT `trainer_reschedules_ibfk_1` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE;

--
-- Constraints for table `trainer_sessions`
--
ALTER TABLE `trainer_sessions`
  ADD CONSTRAINT `fk_trainer_sessions_trainer` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`trainer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_sessions_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `trainer_bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
