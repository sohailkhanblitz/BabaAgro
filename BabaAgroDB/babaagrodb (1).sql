-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2024 at 06:42 AM
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
(1, 'admin', 'sohail', 'admin', 'admin', 'admin@gmail.com', '7218762845', NULL, '2024-11-02 09:41:50', NULL, '2024-11-02 09:41:50'),
(2, 'tabish', 'ahmed', 'tahmed', 'admin', '', '', NULL, '2024-11-02 10:09:38', NULL, '2024-11-02 10:09:38');

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
(1, 1, 'boiler', 'amravati', 10000.00, '2024-11-02', NULL, '2024-11-02 09:44:49', NULL, '2024-11-05 12:20:28', 'inactive'),
(2, 2, 'drier', 'nagpur', 10000.00, '2024-11-02', NULL, '2024-11-02 09:49:24', NULL, '2024-11-05 12:39:49', 'inactive'),
(5, 2, 'mobile', 'Mumbai', 10000.00, '2024-10-29', NULL, '2024-11-02 10:35:23', NULL, '2024-11-05 11:54:45', 'inactive'),
(6, 1, 'boiler', 'pune', 40000.00, '2024-11-04', NULL, '2024-11-04 05:58:47', NULL, '2024-11-05 12:19:11', 'inactive'),
(7, 1, 'drier', 'amravati', 50000.00, '2024-11-01', '1', '2024-11-04 06:45:23', 1, '2024-11-05 07:41:08', 'inactive'),
(8, 2, 'charger', 'bangalore', 10000.00, '2024-11-05', NULL, '2024-11-05 08:02:41', NULL, '2024-11-05 12:39:55', 'active'),
(9, 1, 'usb', 'akola', 5000.00, '2024-11-05', NULL, '2024-11-05 08:03:33', NULL, '2024-11-05 12:22:12', 'active'),
(11, 18, 'earphone', 'akola', 60000.00, '2024-11-05', NULL, '2024-11-05 10:14:59', NULL, '2024-11-05 11:43:17', 'done'),
(12, 18, 'earphone', 'akola', 60000.00, '2024-11-05', NULL, '2024-11-05 10:15:06', NULL, '2024-11-05 11:43:22', 'done');

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
(1, 'bike', 'nagpur', 'drier', 100.00, '2024-10-29', NULL, '2024-11-04 06:09:44', NULL, '2024-11-04 10:39:44', NULL),
(2, 'chai', 'nagpur', 'drier', 200.00, '2024-10-29', NULL, '2024-11-04 06:13:57', NULL, '2024-11-04 10:43:57', NULL),
(3, 'travelling ', 'nagpur', 'drier', 66.00, '2024-10-29', NULL, '2024-11-04 06:20:57', NULL, '2024-11-04 10:50:57', NULL),
(4, 'chai', 'nagpur', 'drier', 99.00, '2024-10-30', NULL, '2024-11-04 06:39:26', NULL, '2024-11-04 11:09:26', NULL),
(5, 'chai', 'nagpur', 'drier', 66.00, '2024-10-30', NULL, '2024-11-04 06:49:03', NULL, '2024-11-04 11:19:03', NULL),
(6, 'travelling ', 'nagpur', 'drier', 1000.00, '2024-11-05', NULL, '2024-11-04 06:50:47', NULL, '2024-11-04 11:20:47', NULL),
(7, 'ss', 'nagpur', 'drier', 1000.00, '2024-11-05', NULL, '2024-11-04 07:08:18', NULL, '2024-11-04 11:38:18', NULL),
(8, 'chai', 'nagpur', 'drier', 1000.00, '2024-11-05', NULL, '2024-11-04 07:11:45', NULL, '2024-11-04 11:41:45', 'uploads/6728b2f9b0afc3.19874452.jpg'),
(9, 'cutinchai', 'nagpur', 'drier', 100.00, '2024-11-05', NULL, '2024-11-04 07:12:59', NULL, '2024-11-04 11:42:59', 'uploads/6728b343858601.77987014.jpg'),
(10, 'Lunch', 'nagpur', 'drier', 2000.00, '2024-11-05', NULL, '2024-11-04 07:17:41', NULL, '2024-11-04 11:47:41', 'uploads/6728b45d37b076.87673909.jpg'),
(11, 'Lunch', 'nagpur', 'drier', 1000.00, '2024-11-05', NULL, '2024-11-04 07:19:19', NULL, '2024-11-04 11:49:19', 'uploads/6728b4bf57ae62.20491663.jpg'),
(12, 'dinner', 'nagpur', 'drier', 500.00, '2024-11-06', NULL, '2024-11-04 07:21:43', NULL, '2024-11-04 11:51:43', 'uploads/6728b54f904e44.42364704.jpg'),
(13, 'coffee', 'nagpur', 'drier', 200.00, '2024-10-30', NULL, '2024-11-04 07:27:21', NULL, '2024-11-04 11:57:21', NULL),
(14, 'panikam', 'nagpur', 'drier', 100.00, '2024-11-06', NULL, '2024-11-04 07:37:46', NULL, '2024-11-04 12:07:46', 'uploads/6728b9120e3100.86561793.jpg'),
(15, 'PULAO', 'nagpur', 'drier', 800.00, '2024-10-30', NULL, '2024-11-04 07:38:08', NULL, '2024-11-04 12:08:08', 'uploads/6728b928a15b80.78870233.jpg'),
(16, 'BIRYANI', 'nagpur', 'drier', 800.00, '2024-10-29', NULL, '2024-11-04 07:41:45', NULL, '2024-11-04 12:11:45', 'uploads/6728ba01292412.16412519.jpg'),
(17, 'MASALA', 'nagpur', 'drier', 770.00, '2024-10-29', '2', '2024-11-04 07:49:27', NULL, '2024-11-04 12:19:27', 'uploads/6728bbcf5fcfd4.85148608.jpg'),
(18, 'chicken masala', 'nagpur', 'drier', 900.00, '2024-10-30', '2', '2024-11-04 07:49:48', NULL, '2024-11-04 12:19:48', 'uploads/6728bbe406d967.25860493.jpg'),
(19, 'lunch', 'Mumbai', 'mobile', 700.00, '2024-10-29', '2', '2024-11-04 07:56:22', NULL, '2024-11-04 12:26:22', 'uploads/6728bd6e730771.84543232.jpg'),
(20, 'chai', 'pune', 'boiler', 80.00, '2024-10-29', '1', '2024-11-04 10:32:01', NULL, '2024-11-04 15:02:01', 'uploads/6728e1e9b70e95.18223612.jpg'),
(21, 'ss', 'amravati', 'boiler', 44.00, '2024-10-29', '1', '2024-11-04 10:48:11', NULL, '2024-11-04 15:18:11', 'uploads/6728e5b3ba8c36.71254067.jpg'),
(22, 'chai', 'amravati', 'boiler', 44.00, '2024-10-31', '1', '2024-11-04 11:51:36', NULL, '2024-11-04 16:21:36', 'uploads/6728f49046d587.87265494.jpg'),
(23, 'ddd', 'amravati', 'boiler', 123.00, '2024-10-29', '1', '2024-11-04 11:51:51', NULL, '2024-11-04 16:21:51', 'uploads/6728f49f614387.61126802.jpg'),
(24, 'pulao', 'amravati', 'boiler', 111.00, '2024-11-03', '1', '2024-11-04 11:56:19', NULL, '2024-11-04 16:26:19', 'uploads/6728f5ab286fa9.07912167.jpg'),
(25, 'tahari', 'amravati', 'boiler', 100.00, '2024-10-28', '1', '2024-11-04 11:56:45', NULL, '2024-11-04 16:26:45', 'uploads/6728f5c50d0a31.13259864.jpg'),
(26, 'dinner', 'amravati', 'boiler', 10000.00, '2024-10-28', '1', '2024-11-04 11:57:14', NULL, '2024-11-04 16:27:14', 'uploads/6728f5e2cf4485.05182707.jpg'),
(27, 'chai', 'amravati', 'boiler', 876.00, '2024-11-05', '1', '2024-11-05 03:16:34', NULL, '2024-11-05 07:46:34', 'uploads/6729cd5a387959.43976267.jpg'),
(28, 'sutta', 'akola', 'usb', 100.00, '2024-11-06', '1', '2024-11-05 06:03:57', NULL, '2024-11-05 10:33:57', 'uploads/6729f495963d87.50271030.jpg'),
(29, 'suttabar', 'nagpur', 'drier', 1000.00, '2024-11-05', '2', '2024-11-05 08:07:26', NULL, '2024-11-05 12:37:26', 'uploads/672a1186d59df2.42256902.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `registereduser`
--

CREATE TABLE `registereduser` (
  `userid` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
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
(1, 'sohail', 'khan', '7218762845', 'khansohail7218@gmail.com', 'contractor', '2024-11-02 15:32:16', NULL, '2024-11-02 15:32:16', NULL),
(2, 'bilal', 'shaikh', '9130357782', 'bilal@gmail.com', 'employee', '2024-11-02 15:33:09', NULL, '2024-11-02 15:33:09', NULL),
(11, 'tab', 'ahmed', '808789665', 'tahmed@providianglobal.com', 'employee', '2024-11-05 08:06:32', 1, '2024-11-05 08:06:32', 1),
(12, 'sufi', 'shaikh', '88888888', 'sufi@gmail.com', 'employee', '2024-11-05 08:12:30', 1, '2024-11-05 08:12:30', 1),
(13, 'asif', 'khan', '10101010', 'asif@gmail.com', 'contractor', '2024-11-05 10:03:39', 1, '2024-11-05 10:03:39', 1),
(15, 'asif', 'khan', '10101010', 'asifss@gmail.com', 'contractor', '2024-11-05 10:05:36', 1, '2024-11-05 10:05:36', 1),
(18, 'sss', 'kkk', '8888888', 'ssskkk@gmail.com', 'contractor', '2024-11-05 10:11:16', 1, '2024-11-05 10:11:16', 1),
(19, 'sohail', 'khan', '223456677778', 'xyz@gmail.com', 'contract', '2024-11-05 12:44:40', 1, '2024-11-05 12:44:40', 1);

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
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `Alid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `Exid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `registereduser`
--
ALTER TABLE `registereduser`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
