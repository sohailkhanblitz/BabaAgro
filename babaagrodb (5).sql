-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 07:24 AM
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
-- Database: `babaagrodb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_master`
--

CREATE TABLE `admin_master` (
  `ad_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_master`
--

INSERT INTO `admin_master` (`ad_id`, `first_name`, `last_name`, `mobile`, `email`, `password`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES
(1, 'Sayeed', 'khan', '8888502776', 'sayeed@gmail.com', 'palan', '2024-11-17 11:03:11', 'admin', '2024-11-17 11:03:11', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `allowance_master`
--

CREATE TABLE `allowance_master` (
  `al_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sp_id` int(11) NOT NULL,
  `al_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL,
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allowance_master`
--

INSERT INTO `allowance_master` (`al_id`, `user_id`, `sp_id`, `al_amount`, `status`, `date`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES
(1, 1, 1, 10000.00, 'Active', '2024-11-16', '2024-11-16 11:43:46', 'admin', '2024-11-16 11:43:46', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `expense_master`
--

CREATE TABLE `expense_master` (
  `ex_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sp_id` int(11) NOT NULL,
  `ex_header` varchar(255) NOT NULL,
  `ex_amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL,
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_master`
--

INSERT INTO `expense_master` (`ex_id`, `user_id`, `sp_id`, `ex_header`, `ex_amount`, `date`, `file_path`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES
(1, 1, 1, 'Lunch', 100.00, '2024-11-16', NULL, '2024-11-16 11:42:17', '1', '2024-11-16 11:42:17', '1');

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `site_id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `site_description` varchar(500) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL,
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`site_id`, `site_name`, `site_description`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES
(1, 'Akola', 'Akola', '2024-11-18 11:52:02', '1', '2024-11-18 11:52:02', '1');

-- --------------------------------------------------------

--
-- Table structure for table `site_product`
--

CREATE TABLE `site_product` (
  `sp_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_product`
--

INSERT INTO `site_product` (`sp_id`, `site_id`, `product_name`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES
(1, 1, 'Drier', '2024-11-18 11:52:58', '1', '2024-11-18 11:52:58', '1');

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `user_role` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(100) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `first_name`, `last_name`, `mobile`, `password`, `email`, `user_role`, `created_date`, `created_by`, `updated_date`, `updated_by`) VALUES
(1, 'sohail', 'khan', '7218762845', 'sohail', '', 'Employee', '2024-11-17 11:36:47', '1', '2024-11-17 11:36:47', '1'),
(2, 'inkeshaf', 'khan', '1234567890', 'khan', 'ink@gmail.com', 'Employee', '2024-11-17 11:37:17', '1', '2024-11-17 11:37:17', '1'),
(3, 'bilal', 'shaikh', '9130357782', 'bilal', '', 'Employee', '2024-11-17 11:50:34', '1', '2024-11-17 11:50:34', '1'),
(4, 'sayyed', 'rahil', '8010874698', 'rahil', '', 'Employee', '2024-11-17 13:50:35', '1', '2024-11-17 13:50:35', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_master`
--
ALTER TABLE `admin_master`
  ADD PRIMARY KEY (`ad_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `allowance_master`
--
ALTER TABLE `allowance_master`
  ADD PRIMARY KEY (`al_id`);

--
-- Indexes for table `expense_master`
--
ALTER TABLE `expense_master`
  ADD PRIMARY KEY (`ex_id`);

--
-- Indexes for table `sites`
--
ALTER TABLE `sites`
  ADD PRIMARY KEY (`site_id`);

--
-- Indexes for table `site_product`
--
ALTER TABLE `site_product`
  ADD PRIMARY KEY (`sp_id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `mobile` (`mobile`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_master`
--
ALTER TABLE `admin_master`
  MODIFY `ad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `allowance_master`
--
ALTER TABLE `allowance_master`
  MODIFY `al_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expense_master`
--
ALTER TABLE `expense_master`
  MODIFY `ex_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sites`
--
ALTER TABLE `sites`
  MODIFY `site_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `site_product`
--
ALTER TABLE `site_product`
  MODIFY `sp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
