-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2025 at 09:54 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `lasorpresa`
--

-- --------------------------------------------------------

--
-- Table structure for table `color`
--

CREATE TABLE `color` (
  `color_id` int(11) NOT NULL,
  `color_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `color`
--

INSERT INTO `color` (`color_id`, `color_name`) VALUES
(0, 'red'),
(0, 'blue');

-- --------------------------------------------------------

--
-- Table structure for table `end_category`
--

CREATE TABLE `end_category` (
  `ecat_id` int(11) NOT NULL,
  `ecat_name` varchar(255) NOT NULL,
  `mcat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `end_category`
--

INSERT INTO `end_category` (`ecat_id`, `ecat_name`, `mcat_id`) VALUES
(0, 'hay finally', 3),
(0, 'red', 4);

-- --------------------------------------------------------

--
-- Table structure for table `mid_category`
--

CREATE TABLE `mid_category` (
  `mcat_id` int(11) NOT NULL,
  `mcat_name` varchar(255) NOT NULL,
  `tcat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mid_category`
--

INSERT INTO `mid_category` (`mcat_id`, `mcat_name`, `tcat_id`) VALUES
(1, 'blue', 3),
(2, 'test', 1),
(3, 'blue11', 3),
(4, 'Red11', 2),
(5, 'Red1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `p_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `old_price` varchar(10) NOT NULL,
  `current_price` varchar(10) NOT NULL,
  `quantity` int(10) NOT NULL,
  `product_photo` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` text NOT NULL,
  `other_photo` text NOT NULL,
  `condition` text NOT NULL,
  `total_view` int(11) NOT NULL,
  `is_featured` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `ecat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `top_category`
--

CREATE TABLE `top_category` (
  `tcat_id` int(11) NOT NULL,
  `tcat_name` varchar(255) NOT NULL,
  `show_on_menu` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `top_category`
--

INSERT INTO `top_category` (`tcat_id`, `tcat_name`, `show_on_menu`) VALUES
(1, '0', 1),
(2, 'red', 1),
(3, 'Blue', 0);

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`type_id`, `type_name`) VALUES
(1, '222'),
(2, 'tulip'),
(3, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `contact`, `password`, `user_type`) VALUES
(1, 'seyan asdas', 'asdasd@dasd.asd', 'seyan', '12312398123', '$2y$10$DH.E7L5uCFNLXTC7DDS1NOBxJQIbBcYmd.wbi3waj3LAzKjAX./aW', 'admin'),
(2, 'johnwayne aquino', 'jw@gmail.com', 'jw', '09214991751', '$2y$10$AW9ZQOGuBRPBMN.8Z8piAOYlwxoN7ZYEom1OjMeGZvyou6pI5pW0W', 'admin'),
(3, 'jw Johnwayne', 'jpdeogracias@gmail.com', 'kbkr', '123123', '$2y$10$2fzRKOpndF1FpYUPyTr2H.QijN19/vvvpbFrar/Vytd3qGD0u04ZW', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mid_category`
--
ALTER TABLE `mid_category`
  ADD PRIMARY KEY (`mcat_id`);

--
-- Indexes for table `top_category`
--
ALTER TABLE `top_category`
  ADD PRIMARY KEY (`tcat_id`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mid_category`
--
ALTER TABLE `mid_category`
  MODIFY `mcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `top_category`
--
ALTER TABLE `top_category`
  MODIFY `tcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
