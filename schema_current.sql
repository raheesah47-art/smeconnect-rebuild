-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 10, 2026 at 03:25 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smeconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `session_id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(5, 'noaja8euqjaljdq70rj1aatrv0', NULL, 4, 4, '2026-08-15 09:45:01'),
(6, 'noaja8euqjaljdq70rj1aatrv0', NULL, 1, 5, '2026-08-15 09:54:10'),
(12, '45g1aau3065jeorq03o1qe2kb0', NULL, 26, 1, '2026-08-30 04:59:20'),
(13, '6ueksmu8tmcn7utrgqbt8m3690', NULL, 26, 1, '2026-08-30 05:32:27'),
(18, 'se1gt7avitjsf92fvvh137alrf', NULL, 26, 1, '2026-08-31 04:44:06'),
(26, '0c1sinprracjcl481mf1v665o8', NULL, 27, 1, '2026-09-09 14:31:25');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_fees`
--

CREATE TABLE `delivery_fees` (
  `id` int(11) NOT NULL,
  `district` varchar(50) NOT NULL,
  `fee` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_fees`
--

INSERT INTO `delivery_fees` (`id`, `district`, `fee`) VALUES
(1, 'Port Louis', 0.00),
(2, 'Plaines Wilhems', 50.00),
(3, 'Moka', 75.00),
(4, 'Flacq', 100.00),
(5, 'Grand Port', 100.00),
(6, 'Black River', 120.00),
(7, 'Pamplemousses', 80.00),
(8, 'Riviere du Rempart', 90.00),
(9, 'Savanne', 130.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `buyer_name` varchar(100) DEFAULT NULL,
  `buyer_phone` varchar(30) DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(30) DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `session_id`, `user_id`, `district`, `buyer_name`, `buyer_phone`, `delivery_address`, `total`, `created_at`, `payment_method`) VALUES
(1, 'noaja8euqjaljdq70rj1aatrv0', NULL, 'Port Louis', NULL, NULL, NULL, 4320.00, '2026-08-15 06:54:06', 'unpaid'),
(2, 'vgsnk4eqka6um02h01i9pca5h9', NULL, 'Port Louis', NULL, NULL, NULL, 380.00, '2026-08-30 09:59:11', 'paypal'),
(3, '0c1sinprracjcl481mf1v665o8', NULL, 'Plaines Wilhems', 'Rahee', '555', NULL, 4299.00, '2026-08-30 12:07:00', 'paypal'),
(4, '0c1sinprracjcl481mf1v665o8', NULL, 'Pamplemousses', 'Rahees', '555', NULL, 380.00, '2026-08-30 12:12:07', 'paypal'),
(5, '0c1sinprracjcl481mf1v665o8', NULL, 'Port Louis', 'Rahee', '555', NULL, 380.00, '2026-08-30 12:13:18', 'paypal'),
(6, '0c1sinprracjcl481mf1v665o8', NULL, 'Port Louis', 'r', '5', NULL, 380.00, '2026-08-30 12:36:03', 'paypal'),
(7, '0c1sinprracjcl481mf1v665o8', NULL, 'Plaines Wilhems', 'Sarah', '59102084', NULL, 1599.00, '2026-09-09 03:27:35', 'paypal'),
(8, '0c1sinprracjcl481mf1v665o8', NULL, 'Plaines Wilhems', 'Sarah', '59102084', NULL, 350.00, '2026-09-09 09:12:04', 'paypal');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES
(1, 1, 1, 'Hand-dyed sarong wrap', 890.00, 2),
(2, 1, 2, 'Organic pineapple, 3-pack', 210.00, 3),
(3, 1, 3, 'Woven vetiver table basket', 650.00, 1),
(4, 1, 4, 'Coconut & vetiver body oil', 420.00, 3),
(5, 2, 26, 'Woven Vetiver Placemat', 380.00, 1),
(6, 3, 24, 'Local Honey, 500ml Jar', 280.00, 3),
(7, 3, 21, 'Bamboo Storage Basket', 520.00, 4),
(8, 3, 18, 'Ravenala Leaf Placemats (set of 4)', 480.00, 1),
(9, 3, 15, 'Solar Phone Charger', 899.00, 1),
(10, 4, 26, 'Woven Vetiver Placemat', 380.00, 1),
(11, 5, 26, 'Woven Vetiver Placemat', 380.00, 1),
(12, 6, 26, 'Woven Vetiver Placemat', 380.00, 1),
(13, 7, 11, 'Vetiver Soap Bar Set', 180.00, 1),
(14, 7, 15, 'Solar Phone Charger', 899.00, 1),
(15, 7, 21, 'Bamboo Storage Basket', 520.00, 1),
(16, 8, 27, 'Handmade Soy Candle', 350.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_log`
--

CREATE TABLE `order_status_log` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_log`
--

INSERT INTO `order_status_log` (`id`, `order_id`, `status`, `created_at`) VALUES
(1, 1, 'Placed', '2026-08-15 06:54:06'),
(2, 2, 'Placed', '2026-08-30 09:59:11'),
(3, 2, 'Confirmed', '2026-08-30 10:17:22'),
(4, 2, 'Out for delivery', '2026-08-30 10:17:29'),
(5, 2, 'Delivered', '2026-08-30 10:17:33'),
(6, 3, 'Placed', '2026-08-30 12:07:00'),
(7, 4, 'Placed', '2026-08-30 12:12:07'),
(8, 5, 'Placed', '2026-08-30 12:13:18'),
(9, 6, 'Placed', '2026-08-30 12:36:03'),
(10, 5, 'Confirmed', '2026-09-07 11:58:57'),
(11, 7, 'Placed', '2026-09-09 03:27:35'),
(12, 6, 'Confirmed', '2026-09-09 04:09:46'),
(13, 8, 'Placed', '2026-09-09 09:12:04'),
(14, 8, 'Cancelled', '2026-09-09 09:21:23');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `seller_name` varchar(100) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `image_url_2` varchar(255) DEFAULT NULL,
  `image_url_3` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `trust_score` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `seller_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `seller_name`, `district`, `category`, `image_url`, `image_url_2`, `image_url_3`, `price`, `original_price`, `stock_quantity`, `trust_score`, `created_at`, `seller_id`) VALUES
(1, 'Hand-dyed sarong wrap', 'Atelier Coco', 'Grand Baie', 'Fashion', NULL, NULL, NULL, 890.00, 1190.00, 0, 98, '2026-08-15 00:44:05', NULL),
(2, 'Organic pineapple, 3-pack', 'Ferme Bois Chéri', 'Moka', 'Produce', NULL, NULL, NULL, 210.00, 250.00, 0, 95, '2026-08-15 00:44:05', NULL),
(3, 'Woven vetiver table basket', 'Karo Design', 'Curepipe', 'Home & craft', NULL, NULL, NULL, 650.00, 930.00, 0, 91, '2026-08-15 00:44:05', NULL),
(4, 'Coconut & vetiver body oil', 'Île Naturelle', 'Rose Hill', 'Beauty', NULL, NULL, NULL, 420.00, 470.00, 0, 99, '2026-08-15 00:44:05', NULL),
(5, 'Carrot cake', 'Rahee', 'P.wilhems', 'Food', NULL, NULL, NULL, 80.00, NULL, 0, 80, '2026-08-17 22:25:06', 1),
(6, 'Handwoven Raffia Tote Bag', 'Atelier Coco', 'Grand Baie', 'Fashion', '/smeconnect/uploads/products/product_6a9766904bd731.65198528.jpg', NULL, NULL, 850.00, 1100.00, 0, 92, '2026-08-26 15:14:36', NULL),
(7, 'Embroidered Cotton Shirt', 'Karo Design', 'Curepipe', 'Fashion', '/smeconnect/uploads/products/product_6a9ee120ab9b17.13411380.jpg', NULL, NULL, 1200.00, NULL, 0, 88, '2026-08-26 15:14:36', NULL),
(8, 'Sega-print Beach Sarong', 'Ile Textile', 'Flacq', 'Fashion', '/smeconnect/uploads/products/product_6a9ee1273b08e9.10130039.jpg', NULL, NULL, 450.00, 600.00, 0, 79, '2026-08-26 15:14:36', NULL),
(9, 'Leather Sandals, Handmade', 'Cuir Local', 'Rose Hill', 'Fashion', '/smeconnect/uploads/products/product_6a9ee12a648bd6.46270468.jpg', NULL, NULL, 950.00, NULL, 0, 85, '2026-08-26 15:14:36', NULL),
(10, 'Coconut Oil Body Butter', 'Ti Nature', 'Moka', 'Beauty', '/smeconnect/uploads/products/product_6a9ee1cf607575.53339620.jpg', NULL, NULL, 320.00, NULL, 0, 90, '2026-08-26 15:14:36', NULL),
(11, 'Vetiver Soap Bar Set', 'Savon Lakaz', 'Black River', 'Beauty', '/smeconnect/uploads/products/product_6a9ee1d4025f73.77815149.png', NULL, NULL, 180.00, 220.00, 0, 95, '2026-08-26 15:14:36', NULL),
(12, 'Aloe Vera Face Gel', 'Bien-Etre Maurice', 'Quatre Bornes', 'Beauty', '/smeconnect/uploads/products/product_6a9ee2006d0646.02189323.webp', NULL, NULL, 275.00, NULL, 0, 82, '2026-08-26 15:14:36', NULL),
(13, 'Sugar Cane Scrub', 'Sik Beauty', 'Vacoas', 'Beauty', '/smeconnect/uploads/products/product_6a9ee2443547c6.35957022.jpg', NULL, NULL, 150.00, 190.00, 0, 76, '2026-08-26 15:14:36', NULL),
(14, 'Refurbished Bluetooth Speaker', 'TechFix MU', 'Port Louis', 'Electronics', 'https://placehold.co/400x400/2E4A62/FFFFFF?text=Electronics', NULL, NULL, 1450.00, 1900.00, 0, 70, '2026-08-26 15:14:36', NULL),
(15, 'Solar Phone Charger', 'GreenVolt', 'Quatre Bornes', 'Electronics', '/smeconnect/uploads/products/product_6aa0d0e2ec02e0.95694490.jpg', NULL, NULL, 899.00, NULL, 0, 84, '2026-08-26 15:14:36', NULL),
(16, 'Handmade Wooden Phone Stand', 'Bwa Kreatif', 'Curepipe', 'Electronics', 'https://placehold.co/400x400/2E4A62/FFFFFF?text=Electronics', NULL, NULL, 250.00, NULL, 0, 91, '2026-08-26 15:14:36', NULL),
(17, 'USB Desk Fan (locally assembled)', 'CoolBreeze MU', 'Rose Hill', 'Electronics', 'https://placehold.co/400x400/2E4A62/FFFFFF?text=Electronics', NULL, NULL, 620.00, 750.00, 0, 77, '2026-08-26 15:14:36', NULL),
(18, 'Ravenala Leaf Placemats (set of 4)', 'Fer Konfyans Home', 'Moka', 'Home & craft', 'https://placehold.co/400x400/D9A441/FFFFFF?text=Home+%26+Craft', NULL, NULL, 480.00, NULL, 0, 94, '2026-08-26 15:14:36', NULL),
(19, 'Hand-carved Wooden Bowl', 'Bwa Kreatif', 'Curepipe', 'Home & craft', 'https://placehold.co/400x400/D9A441/FFFFFF?text=Home+%26+Craft', NULL, NULL, 650.00, 800.00, 0, 89, '2026-08-26 15:14:36', NULL),
(20, 'Recycled Glass Vase', 'Vitre Rekup', 'Flacq', 'Home & craft', 'https://placehold.co/400x400/D9A441/FFFFFF?text=Home+%26+Craft', NULL, NULL, 390.00, NULL, 0, 81, '2026-08-26 15:14:36', NULL),
(21, 'Bamboo Storage Basket', 'Baské Bambou', 'Black River', 'Home & craft', '/smeconnect/uploads/products/product_6a9ee39acb4812.16036867.jpg', NULL, NULL, 520.00, 600.00, 0, 87, '2026-08-26 15:14:36', NULL),
(22, 'Organic Pineapples (per kg)', 'Ferme Bois Chéri', 'Moka', 'Produce', 'https://placehold.co/400x400/6B9E3F/FFFFFF?text=Produce', NULL, NULL, 65.00, NULL, 0, 96, '2026-08-26 15:14:36', NULL),
(23, 'Fresh Litchis (per kg)', 'Verger Riviere Noire', 'Black River', 'Produce', 'https://placehold.co/400x400/6B9E3F/FFFFFF?text=Produce', NULL, NULL, 120.00, 150.00, 0, 93, '2026-08-26 15:14:36', NULL),
(24, 'Local Honey, 500ml Jar', 'Miel de Chez Nous', 'Savanne', 'Produce', '/smeconnect/uploads/products/product_6a9ee02c3bb043.71400715.jpg', NULL, NULL, 280.00, NULL, 0, 97, '2026-08-26 15:14:36', NULL),
(25, 'Farm Tomatoes (per kg)', 'Ferme Bois Chéri', 'Moka', 'Produce', '/smeconnect/uploads/products/product_6a9ee063c21eb5.10656574.jpg', NULL, NULL, 45.00, 55.00, 0, 90, '2026-08-26 15:14:36', NULL),
(26, 'Woven Vetiver Placemat', 'Test Seller', 'Curepipe', 'Home & craft', '/smeconnect/uploads/products/product_6a93b46b9e2cf8.71261443.jpg', NULL, NULL, 380.00, 450.00, 0, 81, '2026-08-29 22:25:32', 3),
(27, 'Handmade Soy Candle', 'Test Seller', 'Plaines Wilhems', 'Home & Living', '/smeconnect/uploads/products/product_6aa0db79159e13.35481449.jpg', NULL, NULL, 350.00, 400.00, 0, 80, '2026-09-09 04:02:10', 3);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `buyer_name` varchar(100) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('buyer','seller','admin') DEFAULT 'buyer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `profile_image` varchar(255) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`, `profile_image`, `district`, `created_at`) VALUES
(1, 'Rahee', 'test@example.com', '$2y$10$rtYvUDqQyBvS6BFCwHpDUeYkH9iLIvnJAsXs/eZjn8I.Zs0RJdHA.', 'seller', 1, NULL, NULL, '2026-08-15 09:54:35'),
(2, 'Test Buyer', 'test@smeconnect.mu', '$2b$12$2V8/dzg3bDjh7nxIWkN4XugjxVoyt6Ybalp/rrSmdRbCOyEOt/56S', 'buyer', 1, NULL, NULL, '2026-08-28 03:25:31'),
(3, 'Test Seller', 'seller@smeconnect.mu', '$2b$12$2V8/dzg3bDjh7nxIWkN4XugjxVoyt6Ybalp/rrSmdRbCOyEOt/56S', 'seller', 1, '/smeconnect/uploads/products/profile_6a97932226dc24.24483736.jpg', NULL, '2026-08-28 03:25:44'),
(4, 'Admin', 'admin@smeconnect.mu', '$2b$12$FAlydrzfI4e3Q95tMd4UP.8HYfAtezzMVP5vkpvib9PVqu6jLeHGm', 'admin', 1, NULL, NULL, '2026-09-01 23:42:05'),
(5, 'Sarah', 'sarah@gmail.com', '$2y$10$C/dayJQTbjnl2aBUeqwk0O5Mcw1z3xclO.D6zMy6hd4agU52Pn4U2', 'buyer', 1, NULL, NULL, '2026-09-09 02:22:37'),
(6, 'Sarah', 'sarah@gmail', '$2y$10$haak0UoMFHT1k/9BrqZDyuPDjJX4Kd8e4AKAYNsudGrteX9a.NV7S', 'buyer', 0, NULL, NULL, '2026-09-09 02:32:15'),
(7, 'Sarah', 'Sarah', '$2y$10$mZ8pOAjfttWfqaNxpBVrMeeVQFNdRw5QTJjsUHLX7TKSg3itdxS4u', 'buyer', 1, NULL, NULL, '2026-09-09 02:32:50');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `session_id`, `product_id`, `created_at`) VALUES
(1, 1, NULL, 1, '2026-08-17 22:33:39'),
(7, NULL, 'vgsnk4eqka6um02h01i9pca5h9', 26, '2026-08-30 10:20:20'),
(9, NULL, '0c1sinprracjcl481mf1v665o8', 24, '2026-09-06 21:11:04'),
(10, NULL, '0c1sinprracjcl481mf1v665o8', 21, '2026-09-06 21:11:19'),
(11, NULL, '0c1sinprracjcl481mf1v665o8', 15, '2026-09-06 21:11:32'),
(12, NULL, '0c1sinprracjcl481mf1v665o8', 11, '2026-09-09 02:47:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_cart_items_user` (`user_id`);

--
-- Indexes for table `delivery_fees`
--
ALTER TABLE `delivery_fees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `district` (`district`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review_per_order_item` (`product_id`,`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wish` (`user_id`,`product_id`),
  ADD UNIQUE KEY `unique_wishlist_session` (`session_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `delivery_fees`
--
ALTER TABLE `delivery_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_status_log`
--
ALTER TABLE `order_status_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_cart_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD CONSTRAINT `order_status_log_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
