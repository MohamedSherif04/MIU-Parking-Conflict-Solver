-- MIU Parking Conflict Solver Schema (Updated)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- 1. Table: users
-- Data Validation: 
--  - University ID must be Unique
--  - Phone Number must be Unique and contain only digits (MySQL 8.0.16+)
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `university_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('Admin','Student') NOT NULL DEFAULT 'Student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `university_id` (`university_id`),
  UNIQUE KEY `phone_number` (`phone_number`),
  CONSTRAINT `chk_phone_digits` CHECK (`phone_number` REGEXP '^[0-9]+$')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 2. Table: vehicles
-- Data Validation: License Plate is Primary Key (No duplicates allowed)
--

CREATE TABLE `vehicles` (
  `license_plate` varchar(20) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `model` varchar(50) NOT NULL,
  `color` varchar(30) NOT NULL,
  `status` enum('Active','Blacklisted') NOT NULL DEFAULT 'Active',
  PRIMARY KEY (`license_plate`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `fk_vehicle_owner` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 3. Table: reports
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `reporter_id` int(11) NOT NULL,
  `blocked_plate` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Acknowledged','Resolved','Escalated') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`report_id`),
  KEY `reporter_id` (`reporter_id`),
  KEY `blocked_plate` (`blocked_plate`),
  CONSTRAINT `fk_report_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_report_vehicle` FOREIGN KEY (`blocked_plate`) REFERENCES `vehicles` (`license_plate`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 4. Table: notifications
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 5. Table: audit_log
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `actor_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `fk_audit_actor` FOREIGN KEY (`actor_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 6. Table: menu_items (Solves "Dynamic Menu" & "Self Reference")
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `url` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `role_access` enum('All','Student','Admin') DEFAULT 'All',
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `fk_menu_parent` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Seeding Menu Data
--
INSERT INTO `menu_items` (`label`, `url`, `role_access`, `order_index`, `parent_id`) VALUES
('Home', '/', 'All', 1, NULL),
('Login', '/auth/login', 'All', 2, NULL),
('Register', '/auth/register', 'All', 3, NULL),
('Dashboard', '/dashboard/index', 'Student', 1, NULL),
('Admin Panel', '/dashboard/admin', 'Admin', 1, NULL),
('My Profile', '/profile', 'Student', 2, 4), -- Example of Self Reference: Child of Dashboard (ID 4)
('Logout', '/auth/logout', 'Student', 99, NULL),
('Logout', '/auth/logout', 'Admin', 99, NULL);

COMMIT;