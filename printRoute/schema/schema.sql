-- This script will create the database and all necessary tables for the printRoute project.

-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `printroute_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `printroute_db`;

--
-- Table structure for table `users`
-- Stores info for customers, shop owners, and admins
--
CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'shop_owner', 'admin') NOT NULL DEFAULT 'user',
  `wallet_balance` DECIMAL(10, 2) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

--
-- Table structure for table `shops`
-- Stores details for each Xerox shop
--
CREATE TABLE `shops` (
  `shop_id` INT AUTO_INCREMENT PRIMARY KEY,
  `owner_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `latitude` DECIMAL(10, 8) NOT NULL,
  `longitude` DECIMAL(11, 8) NOT NULL,
  `status` ENUM('Idle', 'Busy', 'Closed') NOT NULL DEFAULT 'Closed',
  `price_list_json` TEXT COMMENT 'Store prices as a JSON string',
  `is_verified` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`owner_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB;

--
-- Table structure for table `orders`
-- Tracks all print orders
--
CREATE TABLE `orders` (
  `order_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `shop_id` INT NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `options_json` TEXT NOT NULL COMMENT 'e.g., {"pages": 50, "color": "b&w", "binding": "spiral"}',
  `status` ENUM('Pending', 'Accepted', 'Printing', 'Ready', 'Completed', 'Rejected') NOT NULL DEFAULT 'Pending',
  `deposit_amount` DECIMAL(10, 2) NOT NULL,
  `final_amount` DECIMAL(10, 2) DEFAULT NULL,
  `verification_code` VARCHAR(10) DEFAULT NULL COMMENT 'For OTP/QR code',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`),
  FOREIGN KEY (`shop_id`) REFERENCES `shops`(`shop_id`)
) ENGINE=InnoDB;

--
-- Table structure for table `transactions`
-- Logs all payments
--
CREATE TABLE `transactions` (
  `txn_id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT NULL,
  `gateway_txn_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID from the payment gateway',
  `platform_fee` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('Pending', 'Success', 'Failed') NOT NULL DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`)
) ENGINE=InnoDB;