-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2025 at 11:33 AM
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
(1, 'hay finally', 3),
(2, 'red', 4),
(3, 'seyan burat', 4),
(4, 'Any', 3);

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
(2, 'Binyag', 1),
(3, 'Flower', 3),
(4, 'Tulip', 3),
(5, 'Rose', 3);

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
  `featured_photo` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` text NOT NULL,
  `feature` text NOT NULL,
  `other_photo` text NOT NULL,
  `condition` text NOT NULL,
  `is_featured` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `ecat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`p_id`, `name`, `old_price`, `current_price`, `quantity`, `featured_photo`, `description`, `short_description`, `feature`, `other_photo`, `condition`, `is_featured`, `is_active`, `ecat_id`) VALUES
(1, 'tst', '25', '50', 100, 'product-featured-1.jpg', 'test', 'test', 't', '', 't', 1, 1, 4),
(2, 'tst', '25', '50', 100, 'product-featured-2.jpg', 'test', 'test', 't', '', 't', 1, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `product_color`
--

CREATE TABLE `product_color` (
  `id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_color`
--

INSERT INTO `product_color` (`id`, `color_id`, `p_id`) VALUES
(1, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_photo`
--

CREATE TABLE `product_photo` (
  `pp_id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_photo`
--

INSERT INTO `product_photo` (`pp_id`, `photo`, `p_id`) VALUES
(1, '1.jpg', 1),
(2, '2.jpg', 1),
(3, '3.jpg', 1),
(4, '4.jpg', 1),
(5, '5.jpg', 1),
(6, '6.jpg', 1),
(7, '7.jpg', 1),
(8, '8.jpg', 1),
(9, '9.jpg', 1),
(10, '10.jpg', 1),
(11, '11.jpg', 1),
(12, '12.jpg', 1),
(13, '13.jpg', 1),
(14, '14.jpg', 1),
(15, '15.jpg', 1),
(16, '16.jpg', 1),
(17, '17.jpg', 1),
(18, '18.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`id`, `type_id`, `p_id`) VALUES
(1, 2, 2);

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
(1, 'Ocassions', 1),
(2, 'Money', 1),
(3, 'Bouquet', 1);

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
-- Indexes for table `end_category`
--
ALTER TABLE `end_category`
  ADD PRIMARY KEY (`ecat_id`);

--
-- Indexes for table `mid_category`
--
ALTER TABLE `mid_category`
  ADD PRIMARY KEY (`mcat_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `product_color`
--
ALTER TABLE `product_color`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_photo`
--
ALTER TABLE `product_photo`
  ADD PRIMARY KEY (`pp_id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `end_category`
--
ALTER TABLE `end_category`
  MODIFY `ecat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mid_category`
--
ALTER TABLE `mid_category`
  MODIFY `mcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_color`
--
ALTER TABLE `product_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_photo`
--
ALTER TABLE `product_photo`
  MODIFY `pp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
