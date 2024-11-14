-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2024 at 01:35 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Adminid` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `createdby` int(11) DEFAULT NULL,
  `createddate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedby` int(11) DEFAULT NULL,
  `updateddate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Adminid`, `firstname`, `lastname`, `username`, `password`, `email`, `mobile`, `createdby`, `createddate`, `updatedby`, `updateddate`) VALUES
(1, 'admin', 'sohail', 'admin', 'admin', 'admin@gmail.com', '7218762845', NULL, '2024-11-02 09:41:50', NULL, '2024-11-02 09:41:50');

-- --------------------------------------------------------

--
-- Table structure for table `allowancemaster`
--

CREATE TABLE `allowancemaster` (
  `Alid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `product` varchar(100) NOT NULL,
  `site` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createddate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedby` int(11) DEFAULT NULL,
  `updateddate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allowancemaster`
--

INSERT INTO `allowancemaster` (`Alid`, `userid`, `product`, `site`, `amount`, `date`, `createdby`, `createddate`, `updatedby`, `updateddate`, `status`) VALUES
(1, 1, 'Drier', 'Akola', 1000.00, '2024-11-14', NULL, '2024-11-14 10:28:46', NULL, '2024-11-14 12:05:22', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `Exid` int(11) NOT NULL,
  `expense_header` varchar(100) NOT NULL,
  `site` varchar(100) NOT NULL,
  `product` varchar(100) NOT NULL,
  `expense_amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `createdby` varchar(50) DEFAULT NULL,
  `createddate` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedby` varchar(50) DEFAULT NULL,
  `updateddate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`Exid`, `expense_header`, `site`, `product`, `expense_amount`, `date`, `createdby`, `createddate`, `updatedby`, `updateddate`, `file_path`) VALUES
(1, 'Lunch', 'Akola', 'Drier', 1000.00, '2024-11-14', '1', '2024-11-14 06:11:32', NULL, '2024-11-14 10:41:32', NULL),
(2, 'Lunch', 'Akola', 'Drier', 500.00, '2024-11-14', '1', '2024-11-14 07:36:06', NULL, '2024-11-14 12:06:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `registereduser`
--

CREATE TABLE `registereduser` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `userrole` varchar(20) NOT NULL,
  `createddate` timestamp NOT NULL DEFAULT current_timestamp(),
  `createdby` int(11) DEFAULT NULL,
  `updateddate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updatedby` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registereduser`
--

INSERT INTO `registereduser` (`userid`, `firstname`, `lastname`, `mobile`, `email`, `userrole`, `createddate`, `createdby`, `updateddate`, `updatedby`) VALUES
(1, 'sohail', 'khan', '7218762845', 'khansohail7218@gmail.com', 'Employee', '2024-11-14 10:28:25', 1, '2024-11-14 10:28:25', 1),
(2, 'abc', 'sayyed', '9999999999', 'asd@gmail.com', 'Employee', '2024-11-14 11:03:13', 1, '2024-11-14 11:03:13', 1),
(3, 'sohail', 'sayyed', '5555554444', '', 'Employee', '2024-11-14 11:04:56', 1, '2024-11-14 11:04:56', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Adminid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `allowancemaster`
--
ALTER TABLE `allowancemaster`
  ADD PRIMARY KEY (`Alid`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`Exid`);

--
-- Indexes for table `registereduser`
--
ALTER TABLE `registereduser`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Adminid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `allowancemaster`
--
ALTER TABLE `allowancemaster`
  MODIFY `Alid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `Exid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `registereduser`
--
ALTER TABLE `registereduser`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
