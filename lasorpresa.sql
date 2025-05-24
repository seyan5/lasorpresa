-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 24, 2025 at 02:58 PM
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
-- Database: `lasorpresa`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cust_id` int(11) NOT NULL,
  `cust_name` varchar(255) NOT NULL,
  `cust_email` varchar(255) NOT NULL,
  `cust_phone` varchar(15) NOT NULL,
  `cust_address` text NOT NULL,
  `cust_city` varchar(255) NOT NULL,
  `cust_zip` varchar(10) NOT NULL,
  `cust_s_name` varchar(255) DEFAULT NULL,
  `cust_s_phone` varchar(15) DEFAULT NULL,
  `cust_s_address` text DEFAULT NULL,
  `cust_s_city` varchar(255) DEFAULT NULL,
  `cust_s_zip` varchar(10) DEFAULT NULL,
  `cust_password` varchar(255) NOT NULL,
  `cust_datetime` datetime DEFAULT current_timestamp(),
  `cust_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cust_status` enum('active','inactive','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cust_id`, `cust_name`, `cust_email`, `cust_phone`, `cust_address`, `cust_city`, `cust_zip`, `cust_s_name`, `cust_s_phone`, `cust_s_address`, `cust_s_city`, `cust_s_zip`, `cust_password`, `cust_datetime`, `cust_timestamp`, `cust_status`) VALUES
(1, 'Johnwayne', 'user@gmail.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4109', 'Johnwayne', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4341', '$2y$10$Y3eCBn.PneAQn5FOD40v9OQx14R759ezLvf55bB7FMRORFZqWFcbm', '2025-01-07 22:02:47', '2025-01-27 17:42:35', 'active'),
(3, 'Walk-in Customer', 'test@gmail.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4314', 'Johnwayne', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4108', '979d472a84804b9f647bc185a877a8b5', '2025-01-07 22:34:55', '2025-02-01 17:09:22', 'active'),
(5, 'test', 'qweqwe@gmail.com', '431431', 'asddsa', 'sdadsa', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$LlJJaEWfq3GbuKZmaTXn0.86IneDNpn74YKw89bCLZNgOXINEyNwO', '2025-01-07 22:43:28', '2025-01-07 14:43:28', 'inactive'),
(6, 'test', 'jw@mail.com', '431431', 'asddsa', 'sdadsa', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$NO37F4xP6sQz30nLobL8NeZ08jfI6HvdxxtvjIPA2G55oyfY389DK', '2025-01-07 22:43:53', '2025-01-07 14:43:53', 'inactive'),
(8, 'test', 'lasorpresa76@mail.com', '431431', 'asddsa', 'sdadsa', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$Tdnqmz4o870Sp2xBBK6Vhe1oogE5lNEneUDpw1ksqtMuQeFIE6XRC', '2025-01-07 22:50:56', '2025-01-07 14:50:56', 'inactive'),
(10, 'Johnwayne', 'jpdeogracias@gmail.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '3313', 'Johnwayne', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '2213', '979d472a84804b9f647bc185a877a8b5', '2025-01-07 23:00:23', '2025-01-08 07:40:52', 'active'),
(12, 'jay', 'jaycantemprate31@gmail.com', '31', 'ggfddf', 'hgftr', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$VW2.gZt0DtqBcaGqIrIJW.KHw.ySiAiBaBIO1qux6PNd1HiBW9TEm', '2025-01-07 23:02:28', '2025-01-07 15:02:28', 'inactive'),
(13, 'seyan1', 'seanammiel@gmail.com', '0956193051213', 'Brgy Tapia General Trias Cavite', 'street', '3123', NULL, NULL, NULL, NULL, NULL, '$2y$10$1WFoqvQ/Jp6UwxcfQDKHIOvxFgoah5HVmBlt3j.xqLUEpEuzAO1kW', '2025-01-09 02:59:24', '2025-01-26 09:16:59', 'active'),
(15, 'nayes', 'seanammiel6@gmail.com', '0956193051213', 'pogi', 'street', '412', NULL, NULL, NULL, NULL, NULL, '$2y$10$cDKe.kOz/lOgArraceM6vuzZ9wPjbbSM48DOIb7ddjlhFQI52pbSC', '2025-01-24 00:15:06', '2025-01-23 16:15:06', 'inactive'),
(16, '123', 'seanammiel1@gmail.com', '0956193051213', 'pogi', 'cavite', '123', NULL, NULL, NULL, NULL, NULL, '$2y$10$z4saTxkHVgiKC9RbBS3yNeU65t8ai7Op1feJjnhwaYu10.u9T573G', '2025-02-02 00:26:50', '2025-02-01 16:26:50', 'inactive'),
(17, 'asd', 'asdas@fasd.com', '0956193051213', '123', 'street', '123', NULL, NULL, NULL, NULL, NULL, '$2y$10$JH9LHEw18guPGR2ayiTPH.FElpJONg3SIliGdeaDWpZeMhEQW42V.', '2025-02-02 23:48:33', '2025-02-02 15:48:33', 'inactive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`),
  ADD UNIQUE KEY `cust_email` (`cust_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
