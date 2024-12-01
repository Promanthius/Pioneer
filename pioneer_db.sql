-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 03:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pioneer_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `weight_per_unit` decimal(10,2) NOT NULL,
  `category` enum('Vegetable','Canned Food','Grain','Legume','Meat','Dairy','Fruit','Snack','Beverage') NOT NULL,
  `cost_per_unit` decimal(10,2) NOT NULL,
  `expiration_date` date NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `name`, `quantity`, `weight_per_unit`, `category`, `cost_per_unit`, `expiration_date`, `date_registered`) VALUES
(1, 'Corn', 30, 0.10, 'Vegetable', 25.99, '2024-01-10', '2024-12-01 12:05:07'),
(2, 'Corned Beef', 50, 0.80, 'Canned Food', 48.75, '2035-11-30', '2024-12-01 12:05:07'),
(3, 'Rice', 100, 0.50, 'Grain', 40.00, '2025-05-15', '2024-12-01 12:05:07'),
(4, 'Black Beans', 60, 0.20, 'Legume', 30.00, '2024-03-20', '2024-12-01 12:05:07'),
(5, 'Chicken Breast', 20, 0.50, 'Meat', 150.00, '2024-07-15', '2024-12-01 12:05:07'),
(6, 'Cheddar Cheese', 15, 0.20, 'Dairy', 80.00, '2024-09-10', '2024-12-01 12:05:07'),
(7, 'Apples', 50, 0.10, 'Fruit', 3.00, '2024-12-01', '2024-12-01 12:05:07'),
(8, 'Potato Chips', 25, 0.05, 'Snack', 2.50, '2025-01-30', '2024-12-01 12:05:07'),
(9, 'Bottled Water', 100, 1.00, 'Beverage', 1.00, '2025-06-01', '2024-12-01 12:05:07');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `storage_unit_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_units`
--

CREATE TABLE `storage_units` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storage_units`
--

INSERT INTO `storage_units` (`id`, `name`, `location`, `user_id`, `created_at`) VALUES
(1, 'Home 1', '', 1, '2024-12-01 11:54:13'),
(2, 'Home 2', '', 1, '2024-12-01 11:54:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `first_name`, `last_name`) VALUES
(1, 'Promanthius', '$2y$10$YfE5GaH.XgTFBQ9QGsvSMO.ZkZqsGV1imPE6eabt5fEbQlSVUCgdi', '2024-12-01 10:24:31', 'Abdel-Khaliq', 'Abdulla');

-- --------------------------------------------------------

--
-- Table structure for table `water_storage`
--

CREATE TABLE `water_storage` (
  `id` int(11) NOT NULL,
  `size` decimal(10,2) NOT NULL,
  `date_set` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `water_usage`
--

CREATE TABLE `water_usage` (
  `id` int(11) NOT NULL,
  `usage` decimal(10,2) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `storage_unit_id` (`storage_unit_id`);

--
-- Indexes for table `storage_units`
--
ALTER TABLE `storage_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `water_storage`
--
ALTER TABLE `water_storage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `water_usage`
--
ALTER TABLE `water_usage`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storage_units`
--
ALTER TABLE `storage_units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `water_storage`
--
ALTER TABLE `water_storage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `water_usage`
--
ALTER TABLE `water_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`storage_unit_id`) REFERENCES `storage_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `storage_units`
--
ALTER TABLE `storage_units`
  ADD CONSTRAINT `storage_units_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
