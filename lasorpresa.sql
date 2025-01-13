-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2025 at 03:17 PM
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
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('customer','admin') NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `read_status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`message_id`, `sender_id`, `sender_type`, `message`, `timestamp`, `read_status`) VALUES
(1, 14, 'customer', 'hey', '2025-01-10 11:25:24', 'unread'),
(2, 14, 'customer', 'hello', '2025-01-10 11:25:52', 'unread'),
(3, 14, 'customer', 'test', '2025-01-10 11:31:51', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `color`
--

CREATE TABLE `color` (
  `color_id` int(11) NOT NULL,
  `color_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `color`
--

INSERT INTO `color` (`color_id`, `color_name`) VALUES
(1, 'Red'),
(2, 'Blue'),
(3, 'Yellow'),
(4, 'Pink'),
(5, 'Violet'),
(6, 'White');

-- --------------------------------------------------------

--
-- Table structure for table `container`
--

CREATE TABLE `container` (
  `container_id` int(11) NOT NULL,
  `container_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `container`
--

INSERT INTO `container` (`container_id`, `container_name`, `price`) VALUES
(1, 'Basket', 100.00),
(2, 'Paper Wrap', 50.00),
(3, 'Plastic Wrap', 50.00),
(4, 'Vase', 250.00),
(5, 'huwaw', 10.00);

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
(1, 'Johnwayne', 'user@gmail.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4109', 'Johnwayne', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4341', '$2y$10$CVBEIYx28iz79VGVuq2R8eYoqf.o9OZ7w6ykwKFKV3q37oqts6hmW', '2025-01-07 22:02:47', '2025-01-08 08:17:16', 'active'),
(3, 'test', 'test@gmail.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4314', 'Johnwayne', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4108', '979d472a84804b9f647bc185a877a8b5', '2025-01-07 22:34:55', '2025-01-08 07:37:52', 'active'),
(5, 'test', 'qweqwe@gmail.com', '431431', 'asddsa', 'sdadsa', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$LlJJaEWfq3GbuKZmaTXn0.86IneDNpn74YKw89bCLZNgOXINEyNwO', '2025-01-07 22:43:28', '2025-01-07 14:43:28', 'inactive'),
(6, 'test', 'jw@mail.com', '431431', 'asddsa', 'sdadsa', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$NO37F4xP6sQz30nLobL8NeZ08jfI6HvdxxtvjIPA2G55oyfY389DK', '2025-01-07 22:43:53', '2025-01-07 14:43:53', 'inactive'),
(8, 'test', 'lasorpresa76@mail.com', '431431', 'asddsa', 'sdadsa', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$Tdnqmz4o870Sp2xBBK6Vhe1oogE5lNEneUDpw1ksqtMuQeFIE6XRC', '2025-01-07 22:50:56', '2025-01-07 14:50:56', 'inactive'),
(10, 'Johnwayne', 'jpdeogracias@gmail.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '3313', 'Johnwayne', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '2213', '979d472a84804b9f647bc185a877a8b5', '2025-01-07 23:00:23', '2025-01-08 07:40:52', 'active'),
(12, 'jay', 'jaycantemprate31@gmail.com', '31', 'ggfddf', 'hgftr', '1233', NULL, NULL, NULL, NULL, NULL, '$2y$10$VW2.gZt0DtqBcaGqIrIJW.KHw.ySiAiBaBIO1qux6PNd1HiBW9TEm', '2025-01-07 23:02:28', '2025-01-07 15:02:28', 'inactive'),
(13, 'seyan1', 'seanammiel@gmail.com', '0956193051213', 'Brgy Tapia General Trias Cavite', 'street', '3123', NULL, NULL, NULL, NULL, NULL, '$2y$10$1WFoqvQ/Jp6UwxcfQDKHIOvxFgoah5HVmBlt3j.xqLUEpEuzAO1kW', '2025-01-09 02:59:24', '2025-01-13 04:19:12', 'active'),
(14, 'xofahon', 'xofahon594@pariag.com', '09214991751', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4311', NULL, NULL, NULL, NULL, NULL, '$2y$10$/c10sj9aSwwRD0Ewr89zieFX.SIibYEpdJlBIa1egTnI7ANFEdAnm', '2025-01-09 22:30:52', '2025-01-09 14:31:19', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `customer_messages`
--

CREATE TABLE `customer_messages` (
  `message_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `message_text` text NOT NULL,
  `sent_by` enum('customer','admin') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_order`
--

CREATE TABLE `custom_order` (
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `shipping_address` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_order`
--

INSERT INTO `custom_order` (`order_id`, `customer_name`, `customer_email`, `shipping_address`, `total_price`, `order_date`) VALUES
(3, 'Johnwayne', 'jw@gmail.com', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 125.00, '2025-01-09 15:00:42'),
(4, 'Johnwayne', 'jw@gmail.com', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 669.00, '2025-01-09 15:07:26'),
(5, 'seyan', 'seanammiel@gmail.com', 'pogi street', 375.00, '2025-01-10 15:46:05');

-- --------------------------------------------------------

--
-- Table structure for table `custom_orderitems`
--

CREATE TABLE `custom_orderitems` (
  `orderitem_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `flower_type` varchar(255) NOT NULL,
  `num_flowers` int(11) NOT NULL,
  `container_type` varchar(255) NOT NULL,
  `container_color` varchar(255) NOT NULL,
  `flower_price` decimal(10,2) NOT NULL,
  `container_price` decimal(10,2) NOT NULL,
  `color_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_orderitems`
--

INSERT INTO `custom_orderitems` (`orderitem_id`, `order_id`, `flower_type`, `num_flowers`, `container_type`, `container_color`, `flower_price`, `container_price`, `color_price`, `total_price`) VALUES
(4, 3, 'Tulip', 1, 'Basket', 'Red', 25.00, 100.00, 0.00, 125.00),
(5, 4, 'Tulip', 5, 'Plastic Wrap', 'Yellow', 25.00, 50.00, 0.00, 175.00),
(6, 4, 'Rosas', 3, 'Plastic Wrap', 'Yellow', 123.00, 50.00, 0.00, 419.00),
(7, 4, 'Lilac', 1, 'Plastic Wrap', 'Yellow', 25.00, 50.00, 0.00, 75.00),
(8, 5, 'Tulip', 1, 'Plastic Wrap', 'Blue', 25.00, 50.00, 0.00, 75.00),
(9, 5, 'Tulip', 5, 'Plastic Wrap', 'Blue', 25.00, 50.00, 0.00, 175.00),
(10, 5, 'Lilac', 3, 'Plastic Wrap', 'Blue', 25.00, 50.00, 0.00, 125.00);

-- --------------------------------------------------------

--
-- Table structure for table `custom_payment`
--

CREATE TABLE `custom_payment` (
  `cpayment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('Pending','Paid','Failed') DEFAULT 'Pending',
  `shipping_status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `order_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `verification_id` int(11) NOT NULL,
  `cust_id` int(11) DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_verifications`
--

INSERT INTO `email_verifications` (`verification_id`, `cust_id`, `token`, `created_at`) VALUES
(1, 1, 'c1e6a28e43cc0d271fb04b7f6dcdc9f9', '2025-01-07 22:02:47'),
(2, 3, 'ef7eaf958a51bf6416413f86e5320000', '2025-01-07 22:34:55'),
(3, 5, 'fbdce8c6d1999dceae22ddb843ee03f9', '2025-01-07 22:43:28'),
(4, 6, 'f9099c6525708f6c83449e125e785f28', '2025-01-07 22:43:53'),
(6, 8, 'd907fdb988475d90a8827d5d46a2081b', '2025-01-07 22:50:56'),
(10, 12, '25dae009f3e04b14f1eb214af40128f6', '2025-01-07 23:02:28'),
(11, 13, '8b797d8e2dc0d950ad000a71eb97fdb5', '2025-01-09 02:59:24');

-- --------------------------------------------------------

--
-- Table structure for table `end_category`
--

CREATE TABLE `end_category` (
  `ecat_id` int(11) NOT NULL,
  `ecat_name` varchar(255) NOT NULL,
  `mcat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `end_category`
--

INSERT INTO `end_category` (`ecat_id`, `ecat_name`, `mcat_id`) VALUES
(14, 'Rose', 3),
(15, 'Tulip', 3),
(16, 'Sunflower', 3),
(17, 'Chocolate', 19),
(18, 'Balloon', 19),
(19, 'Stuff Toy', 19),
(23, 'Valentines', 20),
(24, 'All Soul\'s Day', 20),
(25, 'Christmas', 20),
(26, 'Daisy', 3),
(27, 'Birthday', 20),
(28, 'Lily', 3),
(29, 'Sampaguita', 3),
(30, 'Gumamela', 3),
(31, 'Flower Daw', 3),
(32, 'Bulaklak', 3),
(33, 'Lavender', 22);

-- --------------------------------------------------------

--
-- Table structure for table `flowers`
--

CREATE TABLE `flowers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `flowers`
--

INSERT INTO `flowers` (`id`, `name`, `quantity`, `price`, `image`) VALUES
(2, 'Rosas', 100, 123.00, '../uploads/flowerRose Flower.jpeg'),
(3, 'Tulip', 100, 25.00, '../uploads/flowertulip flower.png'),
(4, 'Lilac', 10, 25.00, '../uploads/flowerlilac.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `mid_category`
--

CREATE TABLE `mid_category` (
  `mcat_id` int(11) NOT NULL,
  `mcat_name` varchar(255) NOT NULL,
  `tcat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mid_category`
--

INSERT INTO `mid_category` (`mcat_id`, `mcat_name`, `tcat_id`) VALUES
(3, 'Flower', 3),
(19, 'Addons', 2),
(20, 'Occasion', 1),
(22, 'Money', 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `full_name`, `address`, `city`, `postal_code`, `phone`, `total`, `created_at`) VALUES
(1, 1, 'Johnwayne', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4341', '09214991751', 10000.00, '2025-01-08 12:27:49'),
(2, 1, 'Johnwayne', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4108', '09214991751', 50.00, '2025-01-08 12:28:47'),
(4, 1, '', '', '', '', '', 10000.00, '2025-01-08 12:45:23'),
(5, 1, '', '', '', '', '', 10000.00, '2025-01-08 12:46:03'),
(6, 1, '', '', '', '', '', 10000.00, '2025-01-08 12:46:20'),
(7, 1, '', '', '', '', '', 50.00, '2025-01-08 12:46:42'),
(8, 1, '', '', '', '', '', 20000.00, '2025-01-08 12:49:34'),
(9, 1, '', '', '', '', '', 50.00, '2025-01-08 12:50:19'),
(10, 1, 'Johnwayne', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '3441', '09214991751', 50.00, '2025-01-08 12:53:26'),
(15, 1, 'Johnwayne', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4108', '09214991751', 50.00, '2025-01-08 12:57:50'),
(17, 1, 'Johnwayne', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4341', '09214991751', 50.00, '2025-01-08 13:29:27'),
(18, 1, 'Johnwayne', 'Blk 31 Lt 2 Lhinnete Homea\r\nBiga Tanza Cavite', 'Cavite', '4108', '09214991751', 50.00, '2025-01-08 13:32:13'),
(20, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 8600.00, '2025-01-09 13:36:13'),
(21, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 4300.00, '2025-01-09 14:09:22'),
(22, 14, 'xofahon', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4311', '09214991751', 4300.00, '2025-01-09 14:31:46'),
(23, 14, 'xofahon', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4311', '09214991751', 4000.00, '2025-01-09 14:49:01'),
(24, 14, 'xofahon', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4311', '09214991751', 4300.00, '2025-01-09 14:50:14'),
(25, 14, 'xofahon', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4311', '09214991751', 4300.00, '2025-01-10 04:09:28'),
(26, 14, 'xofahon', 'Blk 31 Lt 2 Lhinnete Homea', 'Cavite', '4311', '09214991751', 4300.00, '2025-01-10 04:25:09'),
(27, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 4300.00, '2025-01-10 05:01:22'),
(28, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 4000.00, '2025-01-10 05:22:55'),
(29, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 4300.00, '2025-01-10 05:23:44'),
(30, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 8300.00, '2025-01-10 05:30:01'),
(31, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 4000.00, '2025-01-10 05:32:54'),
(32, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 8600.00, '2025-01-10 07:28:27'),
(33, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 4300.00, '2025-01-11 02:21:35'),
(36, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 3000.00, '2025-01-11 06:36:32'),
(37, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 6000.00, '2025-01-11 07:20:43'),
(38, 13, 'seyan1', 'pogi', 'street', '3123', '0956193051213', 2004.00, '2025-01-12 05:56:49'),
(50, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 72.00, '2025-01-13 12:48:47'),
(51, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 120.00, '2025-01-13 12:50:43'),
(52, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 12.00, '2025-01-13 12:53:40'),
(53, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 12.00, '2025-01-13 12:53:51'),
(54, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 13.00, '2025-01-13 12:57:05'),
(55, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 4.00, '2025-01-13 12:58:17'),
(56, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 12.00, '2025-01-13 12:58:50'),
(57, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 12.00, '2025-01-13 12:59:01'),
(58, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 4000.00, '2025-01-13 13:00:29'),
(59, 13, 'seyan1', 'Brgy Tapia General Trias Cavite', 'street', '3123', '0956193051213', 2000.00, '2025-01-13 13:01:16');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(40, 38, 37, 1, 4.00),
(41, 38, 31, 1, 2000.00),
(53, 50, 35, 6, 12.00),
(54, 51, 36, 1, 120.00),
(55, 52, 35, 1, 12.00),
(56, 53, 35, 1, 12.00),
(57, 54, 34, 1, 13.00),
(58, 55, 37, 1, 4.00),
(59, 56, 33, 1, 12.00),
(60, 57, 33, 1, 12.00),
(61, 58, 32, 1, 4000.00),
(62, 59, 31, 1, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `cust_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `cust_name` varchar(255) NOT NULL,
  `cust_email` varchar(255) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` enum('gcash','cop') NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `shipping_status` enum('pending','shipped','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `cust_id`, `order_id`, `cust_name`, `cust_email`, `reference_number`, `amount_paid`, `payment_method`, `payment_status`, `shipping_status`, `created_at`, `updated_at`) VALUES
(1, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '2893123123', 4300.00, 'gcash', 'paid', 'pending', '2025-01-09 11:06:44', '2025-01-09 11:06:44'),
(2, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '2893123123', 4300.00, 'gcash', 'paid', 'pending', '2025-01-09 11:06:47', '2025-01-09 11:06:47'),
(3, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '231123', 4300.00, 'gcash', 'paid', 'pending', '2025-01-09 11:08:46', '2025-01-09 11:08:46'),
(4, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '12213', 1212.00, 'gcash', 'paid', 'pending', '2025-01-09 11:12:30', '2025-01-09 11:12:30'),
(5, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '12323', 2133.00, 'gcash', 'paid', 'pending', '2025-01-09 11:17:11', '2025-01-09 11:17:11'),
(6, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 123.00, 'gcash', 'paid', 'pending', '2025-01-09 11:21:35', '2025-01-09 11:21:35'),
(7, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '12312', 2133.00, 'gcash', 'paid', 'pending', '2025-01-09 11:32:01', '2025-01-09 11:32:01'),
(8, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '12312', 2133.00, 'gcash', 'paid', 'pending', '2025-01-09 11:32:15', '2025-01-09 11:32:15'),
(9, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123123', 4324.00, 'gcash', 'paid', 'pending', '2025-01-09 11:36:36', '2025-01-09 11:36:36'),
(10, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '2893123123', 1222.00, 'gcash', 'paid', 'pending', '2025-01-09 11:42:52', '2025-01-09 11:42:52'),
(11, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 4444.00, 'gcash', 'paid', 'pending', '2025-01-09 11:51:38', '2025-01-09 11:51:38'),
(12, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 4444.00, 'gcash', 'paid', 'pending', '2025-01-09 11:51:46', '2025-01-09 11:51:46'),
(13, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '13123', 12323.00, 'gcash', 'paid', 'pending', '2025-01-09 12:02:26', '2025-01-09 12:02:26'),
(14, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 4324.00, 'gcash', 'paid', 'pending', '2025-01-09 12:05:47', '2025-01-09 12:05:47'),
(15, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 5678.00, 'gcash', 'paid', 'pending', '2025-01-09 12:09:50', '2025-01-09 12:09:50'),
(16, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 5678.00, 'gcash', 'paid', 'pending', '2025-01-09 12:12:23', '2025-01-09 12:12:23'),
(17, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 5678.00, 'gcash', 'paid', 'pending', '2025-01-09 12:13:28', '2025-01-09 12:13:28'),
(18, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 432.00, 'gcash', 'paid', 'pending', '2025-01-09 12:15:40', '2025-01-09 12:15:40'),
(19, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 432.00, 'gcash', 'paid', 'pending', '2025-01-09 12:15:45', '2025-01-09 12:15:45'),
(20, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 432.00, 'gcash', 'paid', 'pending', '2025-01-09 12:17:21', '2025-01-09 12:17:21'),
(21, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 432.00, 'gcash', 'paid', 'pending', '2025-01-09 12:18:15', '2025-01-09 12:18:15'),
(22, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 432.00, 'gcash', 'paid', 'pending', '2025-01-09 12:19:57', '2025-01-09 12:19:57'),
(23, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '12', 123.00, 'gcash', 'paid', 'pending', '2025-01-09 12:21:01', '2025-01-09 12:21:01'),
(24, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '654', 756.00, 'gcash', 'paid', 'pending', '2025-01-09 12:21:08', '2025-01-09 12:21:08'),
(25, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '4234', 123.00, 'gcash', 'paid', 'pending', '2025-01-09 12:24:56', '2025-01-09 12:24:56'),
(26, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 123.00, 'gcash', 'paid', 'pending', '2025-01-09 12:27:21', '2025-01-09 12:27:21'),
(27, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123123', 123.00, 'gcash', 'paid', 'pending', '2025-01-09 12:28:50', '2025-01-09 12:28:50'),
(28, 13, NULL, 'seyan1', 'seanammiel@gmail.com', '123', 123.00, 'gcash', 'paid', 'pending', '2025-01-09 13:30:44', '2025-01-09 13:30:44'),
(29, 13, 20, 'seyan1', 'seanammiel@gmail.com', '123', 1123.00, 'gcash', 'failed', 'shipped', '2025-01-09 13:36:13', '2025-01-10 02:52:10'),
(30, 13, 21, 'seyan1', 'seanammiel@gmail.com', '111', 1111.00, 'gcash', 'paid', 'pending', '2025-01-09 14:09:22', '2025-01-09 14:09:22'),
(31, 14, 22, 'xofahon', 'xofahon594@pariag.com', '123', 100.00, 'gcash', 'paid', 'pending', '2025-01-09 14:31:46', '2025-01-09 14:31:46'),
(32, 14, 23, 'xofahon', 'xofahon594@pariag.com', '123', 400000.00, 'gcash', 'paid', 'pending', '2025-01-09 14:49:01', '2025-01-09 14:49:01'),
(33, 14, 24, 'xofahon', 'xofahon594@pariag.com', '123', 4300.00, 'gcash', 'failed', 'delivered', '2025-01-09 14:50:14', '2025-01-10 02:52:34'),
(34, 14, 25, 'xofahon', 'xofahon594@pariag.com', '1123', 4300.00, 'gcash', 'paid', 'pending', '2025-01-10 04:09:28', '2025-01-10 04:09:28'),
(35, 14, 26, 'xofahon', 'xofahon594@pariag.com', '1111', 321.00, 'gcash', 'paid', 'pending', '2025-01-10 04:25:09', '2025-01-10 04:25:09'),
(36, 13, 27, 'seyan1', 'seanammiel@gmail.com', '12313', 333.00, 'gcash', 'paid', 'pending', '2025-01-10 05:01:22', '2025-01-10 05:01:22'),
(37, 13, 28, 'seyan1', 'seanammiel@gmail.com', '143', 432.00, 'gcash', 'paid', 'pending', '2025-01-10 05:22:55', '2025-01-10 05:22:55'),
(38, 13, 29, 'seyan1', 'seanammiel@gmail.com', '123', 123.00, 'gcash', 'paid', 'pending', '2025-01-10 05:23:44', '2025-01-10 05:23:44'),
(39, 13, 30, 'seyan1', 'seanammiel@gmail.com', '4123123', 8300.00, 'gcash', 'paid', 'pending', '2025-01-10 05:30:01', '2025-01-10 05:30:01'),
(40, 13, 31, 'seyan1', 'seanammiel@gmail.com', '3123', 4000.00, 'gcash', 'paid', 'pending', '2025-01-10 05:32:54', '2025-01-10 05:32:54'),
(41, 13, 32, 'seyan1', 'seanammiel@gmail.com', '121312', 4000.00, 'gcash', 'paid', 'pending', '2025-01-10 07:28:27', '2025-01-10 07:28:27'),
(42, 13, 33, 'seyan1', 'seanammiel@gmail.com', '123', 123123.00, 'gcash', 'paid', 'pending', '2025-01-11 02:21:35', '2025-01-11 02:21:35'),
(43, 13, 36, 'seyan1', 'seanammiel@gmail.com', '7667666', 766.00, 'gcash', 'paid', 'pending', '2025-01-11 06:36:32', '2025-01-11 06:36:32'),
(44, 13, 37, 'seyan1', 'seanammiel@gmail.com', '123', 123333.00, 'gcash', 'paid', 'pending', '2025-01-11 07:20:43', '2025-01-11 07:20:43'),
(45, 13, 38, 'seyan1', 'seanammiel@gmail.com', '123', 450.00, 'gcash', 'paid', 'pending', '2025-01-12 05:56:49', '2025-01-12 05:56:49'),
(46, 13, 50, 'seyan1', 'seanammiel@gmail.com', '0', 72.00, '', 'pending', 'pending', '2025-01-13 12:48:47', '2025-01-13 12:48:47'),
(47, 13, 51, 'seyan1', 'seanammiel@gmail.com', '0', 120.00, '', 'pending', 'pending', '2025-01-13 12:50:43', '2025-01-13 12:50:43'),
(48, 13, 52, 'seyan1', 'seanammiel@gmail.com', '213', 123.00, 'gcash', 'paid', 'pending', '2025-01-13 12:53:40', '2025-01-13 12:53:40'),
(49, 13, 53, 'seyan1', 'seanammiel@gmail.com', '0', 12.00, '', 'pending', 'pending', '2025-01-13 12:53:51', '2025-01-13 12:53:51'),
(50, 13, 54, 'seyan1', 'seanammiel@gmail.com', '0', 13.00, '', 'pending', 'pending', '2025-01-13 12:57:05', '2025-01-13 12:57:05'),
(51, 13, 55, 'seyan1', 'seanammiel@gmail.com', '0', 4.00, 'cop', 'pending', 'pending', '2025-01-13 12:58:17', '2025-01-13 12:58:17'),
(52, 13, 56, 'seyan1', 'seanammiel@gmail.com', '0', 12.00, 'cop', 'pending', 'pending', '2025-01-13 12:58:50', '2025-01-13 12:58:50'),
(53, 13, 57, 'seyan1', 'seanammiel@gmail.com', '0', 12.00, 'cop', 'pending', 'pending', '2025-01-13 12:59:01', '2025-01-13 12:59:01'),
(54, 13, 58, 'seyan1', 'seanammiel@gmail.com', '0', 4000.00, 'cop', 'pending', 'pending', '2025-01-13 13:00:29', '2025-01-13 13:00:29'),
(55, 13, 59, 'seyan1', 'seanammiel@gmail.com', '0', 2000.00, 'cop', 'pending', 'pending', '2025-01-13 13:01:16', '2025-01-13 13:01:16');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`p_id`, `name`, `old_price`, `current_price`, `quantity`, `featured_photo`, `description`, `short_description`, `feature`, `other_photo`, `condition`, `is_featured`, `is_active`, `ecat_id`) VALUES
(30, 'Pink Life Size Teddy Bear', '2000', '1500', 100, 'product-featured-30.jpg', 'Pink Life Size Teddy Bear', 'Pink Life Size Teddy Bear', 'Pink Life Size Teddy Bear', '', 'Pink Life Size Teddy Bear', 1, 1, 19),
(31, 'Red Life Size Teddy Bear', '1', '2000', 1499, 'product-featured-31.jpg', 'Red Life Size Teddy Bear', 'Red Life Size Teddy Bear', 'Red Life Size Teddy Bear', '', 'Red Life Size Teddy Bear', 1, 1, 19),
(32, 'Vday 10 Roses Boquet', '4500', '4000', 999, 'product-featured-32.jpg', 'Vday 10 Roses Boquet', 'Vday 10 Roses Boquet', 'Vday 10 Roses Boquet', '', 'Vday 10 Roses Boquet', 1, 1, 23),
(33, 'Tulip1', '1', '12', 1, 'product-featured-33.jpg', '', '', '', '', '', 1, 1, 15),
(34, 'Sunflower', '1', '13', 0, 'product-featured-34.jpg', '', '', '', '', '', 1, 1, 16),
(35, 'Roseee', '1', '12', 0, 'product-featured-35.jpg', '', '', '', '', '', 1, 1, 14),
(36, 'Rose3', '1', '120', 0, 'product-featured-36.jpg', '', '', '', '', '', 1, 1, 14),
(37, 'Sunflower', '1', '4', 0, 'product-featured-37.jpg', '', '', '', '', '', 1, 1, 16);

-- --------------------------------------------------------

--
-- Table structure for table `product_color`
--

CREATE TABLE `product_color` (
  `id` int(11) NOT NULL,
  `color_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_color`
--

INSERT INTO `product_color` (`id`, `color_id`, `p_id`) VALUES
(7, 0, 6),
(8, 0, 6),
(9, 0, 6),
(10, 0, 6),
(11, 0, 6),
(12, 0, 6),
(13, 0, 6),
(14, 0, 6),
(22, 1, 15),
(34, 1, 32),
(35, 1, 33),
(36, 1, 34),
(37, 2, 35),
(38, 1, 36),
(39, 2, 37),
(43, 1, 31),
(46, 4, 30);

-- --------------------------------------------------------

--
-- Table structure for table `product_photo`
--

CREATE TABLE `product_photo` (
  `pp_id` int(11) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_photo`
--

INSERT INTO `product_photo` (`pp_id`, `photo`, `p_id`) VALUES
(34, '34.jpg', 30),
(35, '35.jpg', 31),
(36, '36.jpg', 32);

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`id`, `type_id`, `p_id`) VALUES
(1, 2, 2),
(2, 5, 3),
(3, 6, 4),
(4, 5, 5),
(5, 6, 6),
(6, 6, 7),
(7, 5, 8),
(8, 5, 9),
(9, 9, 10),
(10, 9, 11),
(11, 11, 13),
(12, 9, 15),
(13, 9, 19),
(14, 11, 21),
(15, 9, 22),
(16, 10, 23),
(17, 10, 24),
(18, 10, 25),
(19, 10, 26),
(20, 10, 27),
(21, 10, 28),
(22, 10, 29),
(23, 11, 30),
(24, 11, 31),
(25, 11, 32),
(26, 10, 33),
(27, 10, 34),
(28, 9, 35),
(29, 9, 36),
(30, 10, 37);

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `rt_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `comment` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_language`
--

CREATE TABLE `tbl_language` (
  `lang_id` int(11) NOT NULL,
  `lang_name` varchar(255) NOT NULL,
  `lang_value` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_language`
--

INSERT INTO `tbl_language` (`lang_id`, `lang_name`, `lang_value`) VALUES
(1, 'Currency', '$'),
(2, 'Search Product', 'Search Product'),
(3, 'Search', 'Search'),
(4, 'Submit', 'Submit'),
(5, 'Update', 'Update'),
(6, 'Read More', 'Read More'),
(7, 'Serial', 'Serial'),
(8, 'Photo', 'Photo'),
(9, 'Login', 'Login'),
(10, 'Customer Login', 'Customer Login'),
(11, 'Click here to login', 'Click here to login'),
(12, 'Back to Login Page', 'Back to Login Page'),
(13, 'Logged in as', 'Logged in as'),
(14, 'Logout', 'Logout'),
(15, 'Register', 'Register'),
(16, 'Customer Registration', 'Customer Registration'),
(17, 'Registration Successful', 'Registration Successful'),
(18, 'Cart', 'Cart'),
(19, 'View Cart', 'View Cart'),
(20, 'Update Cart', 'Update Cart'),
(21, 'Back to Cart', 'Back to Cart'),
(22, 'Checkout', 'Checkout'),
(23, 'Proceed to Checkout', 'Proceed to Checkout'),
(24, 'Orders', 'Orders'),
(25, 'Order History', 'Order History'),
(26, 'Order Details', 'Order Details'),
(27, 'Payment Date and Time', 'Payment Date and Time'),
(28, 'Transaction ID', 'Transaction ID'),
(29, 'Paid Amount', 'Paid Amount'),
(30, 'Payment Status', 'Payment Status'),
(31, 'Payment Method', 'Payment Method'),
(32, 'Payment ID', 'Payment ID'),
(33, 'Payment Section', 'Payment Section'),
(34, 'Select Payment Method', 'Select Payment Method'),
(35, 'Select a Method', 'Select a Method'),
(36, 'PayPal', 'PayPal'),
(37, 'Stripe', 'Stripe'),
(38, 'Bank Deposit', 'Bank Deposit'),
(39, 'Card Number', 'Card Number'),
(40, 'CVV', 'CVV'),
(41, 'Month', 'Month'),
(42, 'Year', 'Year'),
(43, 'Send to this Details', 'Send to this Details'),
(44, 'Transaction Information', 'Transaction Information'),
(45, 'Include transaction id and other information correctly', 'Include transaction id and other information correctly'),
(46, 'Pay Now', 'Pay Now'),
(47, 'Product Name', 'Product Name'),
(48, 'Product Details', 'Product Details'),
(49, 'Categories', 'Categories'),
(50, 'Category:', 'Category:'),
(51, 'All Products Under', 'All Products Under'),
(52, 'Select Size', 'Select Size'),
(53, 'Select Color', 'Select Color'),
(54, 'Product Price', 'Product Price'),
(55, 'Quantity', 'Quantity'),
(56, 'Out of Stock', 'Out of Stock'),
(57, 'Share This', 'Share This'),
(58, 'Share This Product', 'Share This Product'),
(59, 'Product Description', 'Product Description'),
(60, 'Features', 'Features'),
(61, 'Conditions', 'Conditions'),
(62, 'Return Policy', 'Return Policy'),
(63, 'Reviews', 'Reviews'),
(64, 'Review', 'Review'),
(65, 'Give a Review', 'Give a Review'),
(66, 'Write your comment (Optional)', 'Write your comment (Optional)'),
(67, 'Submit Review', 'Submit Review'),
(68, 'You already have given a rating!', 'You already have given a rating!'),
(69, 'You must have to login to give a review', 'You must have to login to give a review'),
(70, 'No description found', 'No description found'),
(71, 'No feature found', 'No feature found'),
(72, 'No condition found', 'No condition found'),
(73, 'No return policy found', 'No return policy found'),
(74, 'Review not found', 'Review not found'),
(75, 'Customer Name', 'Customer Name'),
(76, 'Comment', 'Comment'),
(77, 'Comments', 'Comments'),
(78, 'Rating', 'Rating'),
(79, 'Previous', 'Previous'),
(80, 'Next', 'Next'),
(81, 'Sub Total', 'Sub Total'),
(82, 'Total', 'Total'),
(83, 'Action', 'Action'),
(84, 'Shipping Cost', 'Shipping Cost'),
(85, 'Continue Shopping', 'Continue Shopping'),
(86, 'Update Billing Address', 'Update Billing Address'),
(87, 'Update Shipping Address', 'Update Shipping Address'),
(88, 'Update Billing and Shipping Info', 'Update Billing and Shipping Info'),
(89, 'Dashboard', 'Dashboard'),
(90, 'Welcome to the Dashboard', 'Welcome to the Dashboard'),
(91, 'Back to Dashboard', 'Back to Dashboard'),
(92, 'Subscribe', 'Subscribe'),
(93, 'Subscribe To Our Newsletter', 'Subscribe To Our Newsletter'),
(94, 'Email Address', 'Email Address'),
(95, 'Enter Your Email Address', 'Enter Your Email Address'),
(96, 'Password', 'Password'),
(97, 'Forget Password', 'Forget Password'),
(98, 'Retype Password', 'Retype Password'),
(99, 'Update Password', 'Update Password'),
(100, 'New Password', 'New Password'),
(101, 'Retype New Password', 'Retype New Password'),
(102, 'Full Name', 'Full Name'),
(103, 'Company Name', 'Company Name'),
(104, 'Phone Number', 'Phone Number'),
(105, 'Address', 'Address'),
(106, 'Country', 'Country'),
(107, 'City', 'City'),
(108, 'State', 'State'),
(109, 'Zip Code', 'Zip Code'),
(110, 'About Us', 'About Us'),
(111, 'Featured Posts', 'Featured Posts'),
(112, 'Popular Posts', 'Popular Posts'),
(113, 'Recent Posts', 'Recent Posts'),
(114, 'Contact Information', 'Contact Information'),
(115, 'Contact Form', 'Contact Form'),
(116, 'Our Office', 'Our Office'),
(117, 'Update Profile', 'Update Profile'),
(118, 'Send Message', 'Send Message'),
(119, 'Message', 'Message'),
(120, 'Find Us On Map', 'Find Us On Map'),
(121, 'Congratulation! Payment is successful.', 'Congratulation! Payment is successful.'),
(122, 'Billing and Shipping Information is updated successfully.', 'Billing and Shipping Information is updated successfully.'),
(123, 'Customer Name can not be empty.', 'Customer Name can not be empty.'),
(124, 'Phone Number can not be empty.', 'Phone Number can not be empty.'),
(125, 'Address can not be empty.', 'Address can not be empty.'),
(126, 'You must have to select a country.', 'You must have to select a country.'),
(127, 'City can not be empty.', 'City can not be empty.'),
(128, 'State can not be empty.', 'State can not be empty.'),
(129, 'Zip Code can not be empty.', 'Zip Code can not be empty.'),
(130, 'Profile Information is updated successfully.', 'Profile Information is updated successfully.'),
(131, 'Email Address can not be empty', 'Email Address can not be empty'),
(132, 'Email and/or Password can not be empty.', 'Email and/or Password can not be empty.'),
(133, 'Email Address does not match.', 'Email Address does not match.'),
(134, 'Email address must be valid.', 'Email address must be valid.'),
(135, 'You email address is not found in our system.', 'You email address is not found in our system.'),
(136, 'Please check your email and confirm your subscription.', 'Please check your email and confirm your subscription.'),
(137, 'Your email is verified successfully. You can now login to our website.', 'Your email is verified successfully. You can now login to our website.'),
(138, 'Password can not be empty.', 'Password can not be empty.'),
(139, 'Passwords do not match.', 'Passwords do not match.'),
(140, 'Please enter new and retype passwords.', 'Please enter new and retype passwords.'),
(141, 'Password is updated successfully.', 'Password is updated successfully.'),
(142, 'To reset your password, please click on the link below.', 'To reset your password, please click on the link below.'),
(143, 'PASSWORD RESET REQUEST - YOUR WEBSITE.COM', 'PASSWORD RESET REQUEST - YOUR WEBSITE.COM'),
(144, 'The password reset email time (24 hours) has expired. Please again try to reset your password.', 'The password reset email time (24 hours) has expired. Please again try to reset your password.'),
(145, 'A confirmation link is sent to your email address. You will get the password reset information in there.', 'A confirmation link is sent to your email address. You will get the password reset information in there.'),
(146, 'Password is reset successfully. You can now login.', 'Password is reset successfully. You can now login.'),
(147, 'Email Address Already Exists', 'Email Address Already Exists.'),
(148, 'Sorry! Your account is inactive. Please contact to the administrator.', 'Sorry! Your account is inactive. Please contact to the administrator.'),
(149, 'Change Password', 'Change Password'),
(150, 'Registration Email Confirmation for YOUR WEBSITE', 'Registration Email Confirmation for YOUR WEBSITE.'),
(151, 'Thank you for your registration! Your account has been created. To active your account click on the link below:', 'Thank you for your registration! Your account has been created. To active your account click on the link below:'),
(152, 'Your registration is completed. Please check your email address to follow the process to confirm your registration.', 'Your registration is completed. Please check your email address to follow the process to confirm your registration.'),
(153, 'No Product Found', 'No Product Found'),
(154, 'Add to Cart', 'Add to Cart'),
(155, 'Related Products', 'Related Products'),
(156, 'See all related products from below', 'See all the related products from below'),
(157, 'Size', 'Size'),
(158, 'Color', 'Color'),
(159, 'Price', 'Price'),
(160, 'Please login as customer to checkout', 'Please login as customer to checkout'),
(161, 'Billing Address', 'Billing Address'),
(162, 'Shipping Address', 'Shipping Address'),
(163, 'Rating is Submitted Successfully!', 'Rating is Submitted Successfully!');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `favicon` varchar(255) NOT NULL,
  `footer_about` text NOT NULL,
  `footer_copyright` text NOT NULL,
  `contact_address` text NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(255) NOT NULL,
  `contact_fax` varchar(255) NOT NULL,
  `contact_map_iframe` text NOT NULL,
  `receive_email` varchar(255) NOT NULL,
  `receive_email_subject` varchar(255) NOT NULL,
  `receive_email_thank_you_message` text NOT NULL,
  `forget_password_message` text NOT NULL,
  `total_recent_post_footer` int(10) NOT NULL,
  `total_popular_post_footer` int(10) NOT NULL,
  `total_recent_post_sidebar` int(11) NOT NULL,
  `total_popular_post_sidebar` int(11) NOT NULL,
  `total_featured_product_home` int(11) NOT NULL,
  `total_latest_product_home` int(11) NOT NULL,
  `total_popular_product_home` int(11) NOT NULL,
  `meta_title_home` text NOT NULL,
  `meta_keyword_home` text NOT NULL,
  `meta_description_home` text NOT NULL,
  `banner_login` varchar(255) NOT NULL,
  `banner_registration` varchar(255) NOT NULL,
  `banner_forget_password` varchar(255) NOT NULL,
  `banner_reset_password` varchar(255) NOT NULL,
  `banner_search` varchar(255) NOT NULL,
  `banner_cart` varchar(255) NOT NULL,
  `banner_checkout` varchar(255) NOT NULL,
  `banner_product_category` varchar(255) NOT NULL,
  `banner_blog` varchar(255) NOT NULL,
  `cta_title` varchar(255) NOT NULL,
  `cta_content` text NOT NULL,
  `cta_read_more_text` varchar(255) NOT NULL,
  `cta_read_more_url` varchar(255) NOT NULL,
  `cta_photo` varchar(255) NOT NULL,
  `featured_product_title` varchar(255) NOT NULL,
  `featured_product_subtitle` varchar(255) NOT NULL,
  `latest_product_title` varchar(255) NOT NULL,
  `latest_product_subtitle` varchar(255) NOT NULL,
  `popular_product_title` varchar(255) NOT NULL,
  `popular_product_subtitle` varchar(255) NOT NULL,
  `testimonial_title` varchar(255) NOT NULL,
  `testimonial_subtitle` varchar(255) NOT NULL,
  `testimonial_photo` varchar(255) NOT NULL,
  `blog_title` varchar(255) NOT NULL,
  `blog_subtitle` varchar(255) NOT NULL,
  `newsletter_text` text NOT NULL,
  `paypal_email` varchar(255) NOT NULL,
  `stripe_public_key` varchar(255) NOT NULL,
  `stripe_secret_key` varchar(255) NOT NULL,
  `bank_detail` text NOT NULL,
  `before_head` text NOT NULL,
  `after_body` text NOT NULL,
  `before_body` text NOT NULL,
  `home_service_on_off` int(11) NOT NULL,
  `home_welcome_on_off` int(11) NOT NULL,
  `home_featured_product_on_off` int(11) NOT NULL,
  `home_latest_product_on_off` int(11) NOT NULL,
  `home_popular_product_on_off` int(11) NOT NULL,
  `home_testimonial_on_off` int(11) NOT NULL,
  `home_blog_on_off` int(11) NOT NULL,
  `newsletter_on_off` int(11) NOT NULL,
  `ads_above_welcome_on_off` int(1) NOT NULL,
  `ads_above_featured_product_on_off` int(1) NOT NULL,
  `ads_above_latest_product_on_off` int(1) NOT NULL,
  `ads_above_popular_product_on_off` int(1) NOT NULL,
  `ads_above_testimonial_on_off` int(1) NOT NULL,
  `ads_category_sidebar_on_off` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `logo`, `favicon`, `footer_about`, `footer_copyright`, `contact_address`, `contact_email`, `contact_phone`, `contact_fax`, `contact_map_iframe`, `receive_email`, `receive_email_subject`, `receive_email_thank_you_message`, `forget_password_message`, `total_recent_post_footer`, `total_popular_post_footer`, `total_recent_post_sidebar`, `total_popular_post_sidebar`, `total_featured_product_home`, `total_latest_product_home`, `total_popular_product_home`, `meta_title_home`, `meta_keyword_home`, `meta_description_home`, `banner_login`, `banner_registration`, `banner_forget_password`, `banner_reset_password`, `banner_search`, `banner_cart`, `banner_checkout`, `banner_product_category`, `banner_blog`, `cta_title`, `cta_content`, `cta_read_more_text`, `cta_read_more_url`, `cta_photo`, `featured_product_title`, `featured_product_subtitle`, `latest_product_title`, `latest_product_subtitle`, `popular_product_title`, `popular_product_subtitle`, `testimonial_title`, `testimonial_subtitle`, `testimonial_photo`, `blog_title`, `blog_subtitle`, `newsletter_text`, `paypal_email`, `stripe_public_key`, `stripe_secret_key`, `bank_detail`, `before_head`, `after_body`, `before_body`, `home_service_on_off`, `home_welcome_on_off`, `home_featured_product_on_off`, `home_latest_product_on_off`, `home_popular_product_on_off`, `home_testimonial_on_off`, `home_blog_on_off`, `newsletter_on_off`, `ads_above_welcome_on_off`, `ads_above_featured_product_on_off`, `ads_above_latest_product_on_off`, `ads_above_popular_product_on_off`, `ads_above_testimonial_on_off`, `ads_category_sidebar_on_off`) VALUES
(1, 'logo.png', 'favicon.png', '<p>Lorem ipsum dolor sit amet, omnis signiferumque in mei, mei ex enim concludaturque. Senserit salutandi euripidis no per, modus maiestatis scribentur est an.Â Ea suas pertinax has.</p>\r\n', 'Copyright Â© 2022 - Ecommerce Website PHP - Developed By Hammad Hassan', '93 Simpson Avenue\r\nHarrisburg, PA', 'support@ecommercephp.com', '+001 10 101 0010', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3094.020958405712!2d-84.39261378514685!3d39.151504939531584!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8841acfb8da30203%3A0x193175e741781f21!2s4293%20Simpson%20Ave%2C%20Cincinnati%2C%20OH%2045227%2C%20USA!5e0!3m2!1sen!2snp!4v1647796779407!5m2!1sen!2snp\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', 'support@ecommercephp.com', 'Visitor Email Message from Ecommerce Site PHP', 'Thank you for sending email. We will contact you shortly.', 'A confirmation link is sent to your email address. You will get the password reset information in there.', 4, 4, 5, 5, 5, 6, 8, 'Ecommerce PHP', 'online fashion store, garments shop, online garments', 'ecommerce php project with mysql database', 'banner_login.jpg', 'banner_registration.jpg', 'banner_forget_password.jpg', 'banner_reset_password.jpg', 'banner_search.jpg', 'banner_cart.jpg', 'banner_checkout.jpg', 'banner_product_category.jpg', 'banner_blog.jpg', 'Welcome To Our Ecommerce Website', 'Lorem ipsum dolor sit amet, an labores explicari qui, eu nostrum copiosae argumentum has. Latine propriae quo no, unum ridens expetenda id sit, \r\nat usu eius eligendi singulis. Sea ocurreret principes ne. At nonumy aperiri pri, nam quodsi copiosae intellegebat et, ex deserunt euripidis usu. ', 'Read More', '#', 'cta.jpg', 'Featured Products', 'Our list on Top Featured Products', 'Latest Products', 'Our list of recently added products', 'Popular Products', 'Popular products based on customer\'s choice', 'Testimonials', 'See what our clients tell about us', 'testimonial.jpg', 'Latest Blog', 'See all our latest articles and news from below', 'Sign-up to our newsletter for latest promotions and discounts.', 'admin@ecom.com', 'pk_test_0SwMWadgu8DwmEcPdUPRsZ7b', 'sk_test_TFcsLJ7xxUtpALbDo1L5c1PN', 'Bank Name: WestView Bank\r\nAccount Number: CA100270589600\r\nBranch Name: CA Branch\r\nCountry: USA', '', '<div id=\"fb-root\"></div>\r\n<script>(function(d, s, id) {\r\n  var js, fjs = d.getElementsByTagName(s)[0];\r\n  if (d.getElementById(id)) return;\r\n  js = d.createElement(s); js.id = id;\r\n  js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=323620764400430\";\r\n  fjs.parentNode.insertBefore(js, fjs);\r\n}(document, \'script\', \'facebook-jssdk\'));</script>', '<!--Start of Tawk.to Script-->\r\n<script type=\"text/javascript\">\r\nvar Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n(function(){\r\nvar s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\ns1.async=true;\r\ns1.src=\'https://embed.tawk.to/5ae370d7227d3d7edc24cb96/default\';\r\ns1.charset=\'UTF-8\';\r\ns1.setAttribute(\'crossorigin\',\'*\');\r\ns0.parentNode.insertBefore(s1,s0);\r\n})();\r\n</script>\r\n<!--End of Tawk.to Script-->', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `top_category`
--

CREATE TABLE `top_category` (
  `tcat_id` int(11) NOT NULL,
  `tcat_name` varchar(255) NOT NULL,
  `show_on_menu` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `top_category`
--

INSERT INTO `top_category` (`tcat_id`, `tcat_name`, `show_on_menu`) VALUES
(1, 'Occasions', 1),
(2, 'Addons', 1),
(3, 'Bouquet', 0);

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`type_id`, `type_name`) VALUES
(9, 'Small'),
(10, 'Medium'),
(11, 'Large');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `container`
--
ALTER TABLE `container`
  ADD PRIMARY KEY (`container_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cust_id`),
  ADD UNIQUE KEY `cust_email` (`cust_email`);

--
-- Indexes for table `customer_messages`
--
ALTER TABLE `customer_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `custom_order`
--
ALTER TABLE `custom_order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `custom_orderitems`
--
ALTER TABLE `custom_orderitems`
  ADD PRIMARY KEY (`orderitem_id`),
  ADD KEY `custom_orderitems_ibfk_1` (`order_id`);

--
-- Indexes for table `custom_payment`
--
ALTER TABLE `custom_payment`
  ADD PRIMARY KEY (`cpayment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `cust_id` (`cust_id`);

--
-- Indexes for table `end_category`
--
ALTER TABLE `end_category`
  ADD PRIMARY KEY (`ecat_id`);

--
-- Indexes for table `flowers`
--
ALTER TABLE `flowers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mid_category`
--
ALTER TABLE `mid_category`
  ADD PRIMARY KEY (`mcat_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `cust_id` (`cust_id`);

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
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rt_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_customer_id` (`customer_id`);

--
-- Indexes for table `tbl_language`
--
ALTER TABLE `tbl_language`
  ADD PRIMARY KEY (`lang_id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
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
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `color`
--
ALTER TABLE `color`
  MODIFY `color_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cust_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customer_messages`
--
ALTER TABLE `customer_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_order`
--
ALTER TABLE `custom_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `custom_orderitems`
--
ALTER TABLE `custom_orderitems`
  MODIFY `orderitem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `custom_payment`
--
ALTER TABLE `custom_payment`
  MODIFY `cpayment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `end_category`
--
ALTER TABLE `end_category`
  MODIFY `ecat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `flowers`
--
ALTER TABLE `flowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mid_category`
--
ALTER TABLE `mid_category`
  MODIFY `mcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `product_color`
--
ALTER TABLE `product_color`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_photo`
--
ALTER TABLE `product_photo`
  MODIFY `pp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `rt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_language`
--
ALTER TABLE `tbl_language`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `top_category`
--
ALTER TABLE `top_category`
  MODIFY `tcat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_messages`
--
ALTER TABLE `customer_messages`
  ADD CONSTRAINT `customer_messages_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_payment`
--
ALTER TABLE `custom_payment`
  ADD CONSTRAINT `custom_payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `custom_order` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD CONSTRAINT `email_verifications_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`cust_id`) REFERENCES `customer` (`cust_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`cust_id`),
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`p_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
