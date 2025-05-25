-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 07:13 AM
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
-- Database: `clonr_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` text NOT NULL,
  `timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `timestamp`) VALUES
(1, 13, 'Updated order #3 status to completed', '2025-05-16 19:55:59'),
(2, 13, 'Updated order #4 status to pending with notes: shabu', '2025-05-16 21:02:45'),
(3, 13, 'Updated order #4 status to completed with notes: Your order will ship soon', '2025-05-16 21:20:43'),
(4, 13, 'Updated order #6 status to cancelled with notes: Unknown user', '2025-05-16 21:36:08'),
(5, 13, 'Updated order #7 status to cancelled', '2025-05-16 21:36:20'),
(6, 13, 'Updated order #8 status to completed with notes: Your payment has receive you may now wait till you...', '2025-05-16 21:59:46'),
(7, 13, 'Updated order #10 status to completed', '2025-05-17 03:02:37'),
(8, 13, 'Changed order #29 status to pending', '2025-05-18 18:27:43'),
(9, 13, 'Changed order #25 status to completed', '2025-05-18 18:28:02'),
(10, 13, 'Changed order #25 status to completed', '2025-05-18 18:28:10'),
(11, 13, 'Changed order #24 status to completed', '2025-05-18 18:28:28'),
(12, 13, 'Changed order #23 status to completed', '2025-05-18 18:28:46'),
(13, 13, 'Changed order #22 status to cancelled', '2025-05-18 18:28:59'),
(14, 13, 'Changed order #5 status to completed', '2025-05-18 18:30:40'),
(15, 13, 'Changed order #22 status to pending', '2025-05-18 18:30:47');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `pid` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `size` varchar(20) NOT NULL,
  `quantity` int(100) NOT NULL,
  `price` float(10,2) NOT NULL,
  `image` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `pid`, `name`, `size`, `quantity`, `price`, `image`) VALUES
(2, 1, 1, 'CIPHER SPLICED SHORTS - KHAKI/CREAM', 'Small', 1, 1100.00, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg'),
(5, 4, 1, 'CIPHER SPLICED SHORTS - KHAKI/CREAM', 'Small', 5, 1100.00, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg'),
(6, 4, 1, 'CIPHER SPLICED SHORTS - KHAKI/CREAM', 'Large', 5, 1100.00, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg'),
(7, 1, 1, 'CIPHER SPLICED SHORTS - KHAKI/CREAM', 'Small', 1, 1100.00, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg'),
(8, 1, 1, 'CIPHER SPLICED SHORTS - KHAKI/CREAM', 'Small', 1, 1100.00, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg'),
(13, 11, 2, 'CIPHER STREAK CREWNECK - BROWN CREAM', 'Small', 1, 2300.00, 'https://dbtkco.com/cdn/shop/files/CIPHERSTREAKCREWNECK3.jpg?v=1728695291'),
(14, 11, 4, 'HYPER GARAGE METAL KEYCHAIN', 'Small', 1, 350.00, 'https://dbtkco.com/cdn/shop/files/HYPERGARAGEMETALKEYCHAIN.jpg?v=1732589925'),
(59, 16, 0, 'RACING PANTS', 'Small', 1, 5995.00, 'https://dbtkco.com/cdn/shop/files/RACING_PANTS.jpg?v=1741421203');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('admin','user') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `is_important` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `parent_id`, `user_id`, `sender_id`, `sender_type`, `subject`, `message`, `is_read`, `is_archived`, `is_important`, `created_at`) VALUES
(16, NULL, 16, 13, 'admin', 'Your Order', 'shabu mo idol', 0, 0, 0, '2025-05-17 14:44:48'),
(17, NULL, 13, 11, 'user', 'Your Order', 'tay gatas namin ', 1, 0, 0, '2025-05-17 14:45:34'),
(18, NULL, 11, 13, 'admin', 'Your Order', 'tang ina mo', 0, 0, 0, '2025-05-17 14:46:34');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(100) NOT NULL,
  `user_id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `method` varchar(50) NOT NULL,
  `address` varchar(500) NOT NULL,
  `total_products` varchar(1000) NOT NULL,
  `total_price` float(10,2) NOT NULL,
  `placed_on` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `name`, `number`, `email`, `method`, `address`, `total_products`, `total_price`, `placed_on`, `payment_status`) VALUES
(1, 12, 'Ryan Lozana', 1, 'Zekaido123@gmail.com', 'cash on delivery', '112213 13awcw langka Meycauayan Bulacan Phillipines', ', CIPHER SPLICED SHORTS - KHAKI/CREAM ( 1 )', 1100.00, '0000-00-00 00:00:00', 'Pending'),
(2, 13, 'Zeck', 12345, 'Zekaido123@gmail.com', 'cash on delivery', '1 paz Tugatog Malabon Manila Phillippines', ', CIPHER SPLICED SHORTS - KHAKI/CREAM ( 1 ), CIPHER SPLICED SHORTS - KHAKI/CREAM ( 1 )', 2200.00, '0000-00-00 00:00:00', 'Pending'),
(3, 13, 'Zeck', 2147483647, 'Zekaido123@gmail.com', 'cash on delivery', '112213 13awcw langka Meycauayan Bulacan Phillipines', ', CIPHER TEE 2025 - BLACK AND WHITE ( 1 ), CIPHER SPLICED SHORTS - BLACK/GRAY ( 1 )', 2100.00, '0000-00-00 00:00:00', 'completed'),
(4, 11, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'RACING PANTS (1), CIPHER TEE 2025 - BLACK AND WHITE (1)', 6995.00, '0000-00-00 00:00:00', 'completed'),
(5, 15, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', ', CIPHER TEE 2025 - BLACK AND WHITE ( 1 )', 1000.00, '0000-00-00 00:00:00', 'completed'),
(6, 15, '', 0, '', '', '    ', 'D-SPARK PANELED PANTS - CREAM BEIGE (1)', 2700.00, '0000-00-00 00:00:00', 'cancelled'),
(7, 15, '', 0, '', '', '    ', 'CIPHER STREAK CREWNECK - BROWN CREAM (1)', 2300.00, '0000-00-00 00:00:00', 'cancelled'),
(8, 15, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - BROWN CREAM (1)', 2300.00, '0000-00-00 00:00:00', 'completed'),
(9, 15, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'RACING PANTS (1), SWIFT SHORTS - MULTI TONAL BLACK GRAY (1)', 7095.00, '0000-00-00 00:00:00', 'canceled'),
(10, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE (1), D-SPARK PANELED JACKET - CREAM BEIGE (1), OAKSHADE WIDE PANTS (1), GRAND PRIX ENAMEL PIN (1), CIPHER SPLICED SHORTS - KHAKI/CREAM (1)', 9200.00, '0000-00-00 00:00:00', 'completed'),
(11, 16, 'Ryan Lozana', 91234567, 'ral531715@gmail.com', 'gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'D-SPARK PANELED JACKET - CREAM BEIGE (1)', 3000.00, '0000-00-00 00:00:00', 'shipped'),
(12, 16, 'Ryan Lozana', 912645698, 'ral531715@gmail.com', '', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER TEE 2025 - BLACK AND WHITE (1)', 1000.00, '0000-00-00 00:00:00', 'Pending'),
(13, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'D-SPARK PANELED JACKET - CREAM BEIGE (1)', 3000.00, '0000-00-00 00:00:00', 'Pending'),
(14, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - BROWN CREAM (1)', 2300.00, '0000-00-00 00:00:00', 'Pending'),
(15, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER TEE 2025 - BLACK AND WHITE (1), CIPHER TEE 2025 - BLACK AND WHITE (1)', 2000.00, '0000-00-00 00:00:00', 'Pending'),
(16, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'D-SPARK PANELED JACKET - CREAM BEIGE (1)', 3000.00, '0000-00-00 00:00:00', 'Pending'),
(17, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - BROWN CREAM (1)', 2300.00, '0000-00-00 00:00:00', 'Pending'),
(19, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - BROWN CREAM (1)', 2300.00, '2025-05-17 09:26:07', 'Pending'),
(20, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'D-SPARK PANELED JACKET - CREAM BEIGE (1)', 3000.00, '2025-05-17 15:40:43', 'Pending'),
(21, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - BROWN CREAM (1)', 2300.00, '2025-05-17 15:59:42', 'Pending'),
(22, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER SPLICED SHORTS - WHITE/GREY (4), CIPHER TEE 2025 - BLACK AND WHITE (1)', 5400.00, '2025-05-18 13:30:48', 'pending'),
(23, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER TEE 2025 - BLACK AND NEON GREEN (1)', 1000.00, '2025-05-18 14:07:48', 'completed'),
(24, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER TEE 2025 - BLACK AND WHITE (1), CIPHER STREAK CREWNECK - BROWN CREAM (1), CIPHER TEE 2025 - BLACK AND WHITE (5)', 8300.00, '2025-05-18 17:03:05', 'completed'),
(25, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'MOB V2 TEE - BROWN (1), MOB V2 TEE - BROWN (1), D-SPARK PANELED PANTS - CREAM BEIGE (1), D-SPARK PANELED PANTS - CREAM BEIGE (5)', 19200.00, '2025-05-18 17:32:44', 'processing'),
(29, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'CIPHER STREAK CREWNECK - BROWN CREAM (5), HYPER GARAGE STICKER PACK (5)', 13250.00, '2025-05-18 18:11:29', 'canceled'),
(30, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'D-SPARK PANELED PANTS - CREAM BEIGE (1), OAKSHADE WIDE PANTS (3), DBTK SLANT BODY BAG (3)', 14250.00, '2025-05-18 18:56:22', 'Pending'),
(31, 16, 'Ryan Lozana', 2147483647, 'ral531715@gmail.com', 'Gcash', '146 Langka Modesta st., langka Meycauayan Bulacan Philippines', 'D-SPARK PANELED PANTS - CREAM BEIGE (1)', 2700.00, '2025-05-18 19:09:58', 'shipped'),
(32, 17, 'ace', 2147483647, 'cordivilla.micaace@ue.edu.ph', 'Gcash', '22 Honradez Tugatog Malabon NCR Philippines', 'CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE (1), HYPER GARAGE METAL KEYCHAIN (1), INFINITE CHASE TEE - WHITE (1)', 3600.00, '2025-05-18 22:45:37', 'canceled'),
(33, 16, 'ace', 2147483647, 'cordivilla.micaace@ue.edu.ph', 'OGcash', '22 Honradez Tugatog Malabon NCR Philippines', 'RACING PANTS (1)', 5995.00, '2025-05-19 11:14:38', 'Pending'),
(34, 16, 'ace', 2147483647, 'cordivilla.micaace@ue.edu.ph', 'OGcash', '22 Honradez Tugatog Malabon NCR Philippines', 'D-SPARK PANELED JACKET - CREAM BEIGE (1) (Small)', 3000.00, '2025-05-19 11:20:03', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `notes` text DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_history`
--

INSERT INTO `order_history` (`id`, `order_id`, `status`, `notes`, `updated_by`, `updated_at`) VALUES
(1, 3, 'completed', '', 13, '2025-05-16 19:55:59'),
(2, 4, 'pending', 'shabu', 13, '2025-05-16 21:02:45'),
(3, 4, 'completed', 'Your order will ship soon', 13, '2025-05-16 21:20:43'),
(4, 6, 'cancelled', 'Unknown user', 13, '2025-05-16 21:36:08'),
(5, 7, 'cancelled', '', 13, '2025-05-16 21:36:20'),
(6, 8, 'completed', 'Your payment has receive you may now wait till your order shipped', 13, '2025-05-16 21:59:46'),
(7, 10, 'completed', '', 13, '2025-05-17 03:02:37');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `price`) VALUES
(1, 29, 0, 'CIPHER STREAK CREWNECK - BROWN CREAM', NULL, 5, 2300.00),
(2, 29, 0, 'HYPER GARAGE STICKER PACK', NULL, 5, 350.00),
(3, 30, 0, 'D-SPARK PANELED PANTS - CREAM BEIGE', NULL, 1, 2700.00),
(4, 30, 0, 'OAKSHADE WIDE PANTS', NULL, 3, 2500.00),
(5, 30, 0, 'DBTK SLANT BODY BAG', NULL, 3, 1350.00),
(6, 31, 0, 'D-SPARK PANELED PANTS - CREAM BEIGE', NULL, 1, 2700.00),
(7, 32, 1, 'CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE', NULL, 1, 2300.00),
(8, 32, 0, 'HYPER GARAGE METAL KEYCHAIN', NULL, 1, 350.00),
(9, 32, 0, 'INFINITE CHASE TEE - WHITE', NULL, 1, 950.00),
(10, 33, 0, 'RACING PANTS', 'Small', 1, 5995.00),
(11, 34, 0, 'D-SPARK PANELED JACKET - CREAM BEIGE', 'Small', 1, 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category`, `image`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'GRAND PRIX ENAMEL PIN', NULL, 300.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/GRANDPRIXENAMELPIN.png?v=1733378380', '2025-05-16 21:51:44', '2025-05-17 00:26:52'),
(2, 'HYPER GARAGE STICKER PACK', NULL, 350.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/HYPERGARAGESTICKERPACK.jpg?v=1732590086', '2025-05-16 23:54:52', '2025-05-16 23:54:52'),
(3, 'HYPER GARAGE WOVEN KEYCHAIN', '', 300.00, 'tshirts', '13.jpg', 'https://dbtkco.com/cdn/shop/files/HYPERGARAGEWOVENTAG1.jpg?v=1732589996', '2025-05-16 23:57:29', '2025-05-18 04:44:24'),
(4, 'HYPER GARAGE METAL KEYCHAIN', NULL, 350.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/HYPERGARAGEMETALKEYCHAIN.jpg?v=1732589925', '2025-05-16 23:57:29', '2025-05-16 23:57:29'),
(5, 'DBTK HOLOGRAPHIC STICKER PACK', NULL, 300.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/products/2_2_1.jpg?v=1677252535', '2025-05-17 00:23:29', '2025-05-17 00:23:29'),
(6, 'DBTK EVERMORE SLING BAG - BLACK', NULL, 950.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/DBTKEVERMORESLINGBAG1.jpg?v=1734082111', '2025-05-17 00:24:56', '2025-05-17 00:24:56'),
(7, 'WOODLAND CIPHER FLASK - BLACK/GRAY', NULL, 1100.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/WOODLAND_CIPHER_FLASK_1.jpg?v=1721960065', '2025-05-17 00:24:56', '2025-05-17 00:24:56'),
(8, 'DBTK x MHA ENAMEL PIN', NULL, 300.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/products/enamel.jpg?v=1661509910', '2025-05-17 00:25:29', '2025-05-17 00:25:29'),
(9, 'WOODLAND CIPHER UMBRELLA', NULL, 900.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/WOODLANDCIPHERUMBRELLA1.jpg?v=1721958145', '2025-05-17 00:26:22', '2025-05-17 00:26:22'),
(10, 'DBTK SLANT BODY BAG', NULL, 1350.00, 'Accessories', NULL, 'https://dbtkco.com/cdn/shop/files/BAG1_9bd676b9-3db7-4ea8-adf7-190d5d446be7.jpg?v=1716898728', '2025-05-17 00:26:22', '2025-05-17 00:26:22'),
(11, 'CIPHER STREAK CREWNECK - MID GRAY/ OFF WHITE', NULL, 2300.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHER_STREAK_CREWNECK_1.jpg?v=1728695354', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(12, 'CIPHER STREAK CREWNECK - BROWN CREAM', NULL, 2300.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHERSTREAKCREWNECK3.jpg?v=1728695291', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(13, 'D-SPARK PANELED JACKET - CREAM BEIGE', NULL, 3000.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDJACKET5.jpg?v=1728696742', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(14, 'OAKSHADE WORKWEAR JACKET', NULL, 2800.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/OAKSHADEWORKWEARJACKET1.jpg?v=1737095917', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(15, 'SPARK PANELED JACKET - BLACK', NULL, 3300.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/FULL-ZIP_SPARK_3.jpg?v=1737099889', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(16, 'COMPILATION HOODIE - OFF-WHITE', NULL, 3800.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/Artboard3_c03fdea3-ed94-4eb1-9c79-da90934744c5.jpg?v=1734081482', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(17, 'SPARK PANELED JACKET - BLUEBERRY', NULL, 3300.00, 'Jackets', NULL, 'https://dbtkco.com/cdn/shop/files/FULL-ZIPSPARK1.jpg?v=1737100793', '2025-05-17 04:49:20', '2025-05-17 04:49:20'),
(18, 'CIPHER SPLICED SHORTS - KHAKI/CREAM', NULL, 1100.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS3.jpg?v=1734574023', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(19, 'CIPHER SPLICED SHORTS - WHITE/GREY', NULL, 1100.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS5.jpg?v=1734573884', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(20, 'SWIFT SHORTS - MULTI TONAL BLACK GRAY', NULL, 1100.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/SWIFTSHORTS1.jpg?v=1728698184', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(21, 'CIPHER SPLICED SHORTS - BLACK/GRAY', NULL, 1100.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHERSPLICEDSHORTS1.jpg?v=1734573884', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(22, 'CIPHER FLOCK SHORTS - ACID WASHED BLACK', NULL, 1500.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHER_FLOCK_SHORTS_1.jpg?v=1724923454', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(23, 'CIPHER FLOCK SHORTS - ACID DARK GRAY', NULL, 1500.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHERFLOCKSHORTS2_d69e22f6-75e3-4bc8-99a9-161f48937d9c.jpg?v=1733981325', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(24, 'GRAND PRIX SHORTS - CREAM', NULL, 1800.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/GRANDPRIXSHORTS3.jpg?v=1733377627', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(25, 'CIPHER FLOCK SHORTS - ACID WASHED PINK', NULL, 1500.00, 'Shorts', NULL, 'https://dbtkco.com/cdn/shop/files/CIPHERFLOCKSHORTS2.jpg?v=1724922721', '2025-05-17 05:17:55', '2025-05-17 05:17:55'),
(26, 'CIPHER TEE 2025 - BLACK AND NEON GREEN', NULL, 1000.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/SidePocketBlackShirtFront.jpg?v=1742283606', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(27, 'CIPHER TEE 2025 - BLACK AND WHITE', NULL, 1000.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/Cipher_Black_and_White_Shirt_Front.jpg?v=1742283658', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(28, 'SLANT TEE 2025 - CREAM AND BLACK', NULL, 1100.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/Slant_Cream_Shirt_Front.jpg?v=1742283821', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(29, 'CIPHER TEE 2025 - BLACK AND WHITE', NULL, 1000.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/Slant_Black_White_Shirt_Front.jpg?v=1742283943', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(30, 'MOB V2 TEE - BROWN', NULL, 1500.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/MOBV21.jpg?v=1735981287', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(31, 'DBTK ARC TEE - WHITE', NULL, 1450.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/DBTKARCTEE3.jpg?v=1715245989', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(32, 'COUPE TEE - WHITE', NULL, 1000.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/COUPE3.jpg?v=1732697599', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(33, 'Nationals Fruits Tee - White', NULL, 1600.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/NationalsFruitsTee1.jpg?v=1733130710', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(34, 'INFINITE CHASE TEE - WHITE', NULL, 950.00, 'T-shirts', NULL, 'https://dbtkco.com/cdn/shop/files/INFINITECHASE3.jpg?v=1733898703', '2025-05-17 05:50:44', '2025-05-17 05:50:44'),
(35, 'OAKSHADE WIDE PANTS', NULL, 2500.00, 'Pants', NULL, 'https://dbtkco.com/cdn/shop/files/OAKSHADEWIDEPANTS1.jpg?v=1737096791', '2025-05-17 06:08:46', '2025-05-17 06:08:46'),
(36, 'D-SPARK PANELED PANTS - CREAM BEIGE', NULL, 2700.00, 'Pants', NULL, 'https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDPANTS3.jpg?v=1728696742', '2025-05-17 06:08:46', '2025-05-17 06:08:46'),
(37, 'RACING PANTS', NULL, 5995.00, 'Pants', NULL, 'https://dbtkco.com/cdn/shop/files/RACING_PANTS.jpg?v=1741421203', '2025-05-17 06:08:46', '2025-05-17 06:08:46'),
(38, 'MERGE WIDE PANTS - BLACK', NULL, 2300.00, 'Pants', NULL, 'https://dbtkco.com/cdn/shop/files/MERGE_WIDE_PANTS_1.jpg?v=1737099107', '2025-05-17 06:08:46', '2025-05-17 06:08:46'),
(39, 'MERGE WIDE PANTS - LIGHT GRAY', NULL, 2300.00, 'Pants', NULL, 'https://dbtkco.com/cdn/shop/files/MERGEWIDEPANTS3.jpg?v=1737099509', '2025-05-17 06:08:46', '2025-05-17 06:08:46'),
(40, 'SPARK PANELED WIDE PANTS - BLACK', NULL, 2500.00, 'Pants', NULL, 'https://dbtkco.com/cdn/shop/files/SPARKPANELEDWIDEPANTS3.jpg?v=1737103509', '2025-05-17 06:08:46', '2025-05-17 06:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 11, 'https://dbtkco.com/cdn/shop/files/CIPHERSTREAKCREWNECK2.jpg?v=1728695291', 0, '2025-05-17 04:57:56', '2025-05-17 04:57:56'),
(2, 11, 'https://dbtkco.com/cdn/shop/files/Artboard122.jpg?v=1728695292', 1, '2025-05-17 04:57:56', '2025-05-17 04:57:56'),
(3, 12, 'https://dbtkco.com/cdn/shop/files/CIPHERSTREAKCREWNECK4.jpg?v=1728695292', 0, '2025-05-17 04:57:56', '2025-05-17 04:57:56'),
(4, 12, 'https://dbtkco.com/cdn/shop/files/Artboard119.jpg?v=1728695292', 1, '2025-05-17 04:57:56', '2025-05-17 04:57:56'),
(5, 13, 'https://dbtkco.com/cdn/shop/files/D-SPARKPANELEDJACKET6.jpg?v=1728696743', 0, '2025-05-17 04:57:56', '2025-05-17 04:57:56'),
(6, 13, 'https://dbtkco.com/cdn/shop/files/Artboard91.jpg?v=1728696742', 1, '2025-05-17 04:57:56', '2025-05-17 04:57:56'),
(7, 14, 'https://dbtkco.com/cdn/shop/files/OAKSHADEWORKWEARJACKET2.jpg?v=1737095917', 0, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(8, 14, 'https://dbtkco.com/cdn/shop/files/OAKSHADEPOLO.webp?v=1737095917', 1, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(9, 15, 'https://dbtkco.com/cdn/shop/files/FULL-ZIP_SPARK_4.jpg?v=1737099889', 0, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(10, 15, 'https://dbtkco.com/cdn/shop/files/FULL_ZIP_BLACK.webp?v=1737099889', 1, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(11, 16, 'https://dbtkco.com/cdn/shop/files/Artboard4_313ff247-c6af-4876-b63d-2fb07b4176be.jpg?v=1734081482', 0, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(12, 16, 'https://dbtkco.com/cdn/shop/files/DBT04646-Enhanced-NR.webp?v=1734081674', 1, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(13, 17, 'https://dbtkco.com/cdn/shop/files/FULL-ZIPSPARK2.jpg?v=1737100793', 0, '2025-05-17 04:57:57', '2025-05-17 04:57:57'),
(14, 17, 'https://dbtkco.com/cdn/shop/files/SPARK_PANELED_JACKET_BLACK_3a5f5ec0-0682-4669-9d04-6e33d60290f5.webp?v=1737100793', 1, '2025-05-17 04:57:57', '2025-05-17 04:57:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'user',
  `verification_token` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `image` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_acc` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(10) NOT NULL DEFAULT 'Active',
  `balance` decimal(65,0) NOT NULL,
  `money` decimal(65,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `verification_token`, `is_verified`, `image`, `phone`, `created_acc`, `Status`, `balance`, `money`) VALUES
(6, 'Mi', 'a@a', '0cc175b9c0f1b6a831c399e269772661', 'user', NULL, 0, 'Ace.jpg', '0', '2025-05-16 07:21:33', 'active', 0, 0),
(11, 'Ryan Lozana', 'ral531715@gmail.com', 'e807f1fcf82d132f9bb018ca6738a19f', 'user', NULL, 0, '68272061a3dde.png', '0', '2025-05-16 07:21:33', 'Active', 0, 3005),
(12, 'aaa', 'a@a.c', '12f9cf6998d52dbe773b06f848bb3608', 'user', NULL, 0, 'IMG_7372 (1).jpg', '0', '2025-05-16 07:21:33', 'active', 0, 0),
(13, 'Zeck', 'Zekaido123@gmail.com', '247f8b5944da561cd6c2cda1748fb081', 'admin', NULL, 0, '6826ec2ff24dc.jpg', '0', '2025-05-16 07:21:33', 'Active', 0, 0),
(14, 'Ryan Lozana', 'ral5131715@gmail.com', '25d55ad283aa400af464c76d713c07ad', 'user', NULL, 0, 'Ace.jpg', '09166245138', '2025-05-16 07:21:33', 'Active', 0, 0),
(15, 'Ryan', 'kazutokir@gmail.com', '25f9e794323b453885f5181f1b624d0b', 'user', NULL, 0, '205d97f5-14af-4ff4-b3d8-dadcb3bc217c.jfif', '9606202043', '2025-05-16 07:37:25', 'Active', 0, 9992700),
(16, 'Zekkkkkk', 'Zekaido13@gmail.com', '247f8b5944da561cd6c2cda1748fb081', 'user', NULL, 0, 'Ace.jpg', '0921420397', '2025-05-16 18:52:03', 'Active', 0, 9908455),
(17, 'Aaa', 'micaacec@gmail.com', '55bff7256d568fa0705b09643ebe5284', 'user', NULL, 0, 'line.jpg', '9929046528', '2025-05-18 14:39:37', 'Active', 0, 10000000),
(18, 'eee', 'acy995kim@gmail.com', '3dbe00a167653a1aaee01d93e77e730e', 'user', 'db6dd55f1678e0ca1f76f03e66421a4b0913583e8fa6d283e6bf41cf8b9cc60e', 0, 'line.jpg', '9929046528', '2025-05-18 16:34:41', 'Active', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_history_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
