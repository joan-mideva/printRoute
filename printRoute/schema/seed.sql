-- database/seed.sql
-- This script adds 3 predefined shop owners and their shops.
-- The password for all three users is 'password123'.

-- Use the correct database
USE `printroute_db`;

-- 1. Create the shop owners in the `users` table
INSERT INTO `users` (`name`, `email`, `password`, `role`, `wallet_balance`) VALUES
('Sonal Shah', 'sonal@printroute.com', '$2y$10$E.qslENhGr.t/Cg9l055JO0unlAT.BF9tX/ECu204MFlmhF5O3dI2', 'shop_owner', 0.00),
('Jay Patel', 'jay@printroute.com', '$2y$10$E.qslENhGr.t/Cg9l055JO0unlAT.BF9tX/ECu204MFlmhF5O3dI2', 'shop_owner', 0.00),
('Krishna Dave', 'krishna@printroute.com', '$2y$10$E.qslENhGr.t/Cg9l055JO0unlAT.BF9tX/ECu204MFlmhF5O3dI2', 'shop_owner', 0.00);

-- 2. Create the corresponding shops in the `shops` table
-- Note: We assume the user_ids will be 1, 2, and 3 if this is run on a clean database.
-- Adjust the `owner_id` if you have existing users.
INSERT INTO `shops` (`owner_id`, `name`, `address`, `latitude`, `longitude`, `status`, `is_verified`) VALUES
(1, 'Sonal Xerox', '12, Ground Floor, Maninagar, Ahmedabad', 23.0031, 72.5959, 'Idle', 1),
(2, 'Janta Digital Studio', '45, Anand Complex, Rambagh, Ahmedabad', 23.0028, 72.5999, 'Busy', 1),
(3, 'Krishna Print Hub', 'Shop 8, Jawahar Chowk, Maninagar East, Ahmedabad', 23.0084, 72.5960, 'Closed', 1);