-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2024 at 05:09 AM
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
-- Database: `finch fashion`
--

-- --------------------------------------------------------

--
-- Table structure for table `highlighted_products`
--

CREATE TABLE `highlighted_products` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `highlighted_products`
--

INSERT INTO `highlighted_products` (`id`, `product_id`, `featured`) VALUES
(7, 27, 1),
(8, 31, 1),
(9, 26, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','cancelled','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone_number` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_phone` varchar(20) NOT NULL DEFAULT '',
  `full_name` varchar(255) NOT NULL
) ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`, `phone_number`, `address`, `transaction_id`, `payment_method`, `payment_phone`, `full_name`) VALUES
(7, 2, 124.00, 'completed', '2024-09-28 12:53:33', '01845901833', 'savar,dhaka', 'bghkuy1245', 'bkash_nagad', '0123354650', 'the tech captain');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 20, 1, 132.00),
(2, 1, 20, 1, 132.00),
(3, 2, 20, 1, 132.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `created_at`, `image`) VALUES
(17, 'the tech captain', 'the tech captain', 124.00, 120, '2024-09-20 10:49:06', '5_vJgp79x.jpg'),
(18, 'jeans', 'jeans', 199.00, 10, '2024-09-20 10:49:26', 'Ripped-Blue-Jeans-Pant-54-Slim-Fit.jpg'),
(20, 'Linen Long Sleeve Shirt', 'Linen Long Sleeve Shirt', 132.00, 10, '2024-09-20 13:07:40', 'images.jpeg'),
(24, 'Shirt', 'Shirt', 120.00, 10, '2024-09-28 14:22:07', 'images (1).jpeg'),
(25, 'T-Shirt', 'T-Shirt', 110.00, 10, '2024-09-28 14:22:48', 'download.jpeg'),
(26, 'Short Sleeve Casual Shirts', 'Short Sleeve Casual Shirts', 111.00, 10, '2024-09-28 14:24:05', 'download (1).jpeg'),
(27, 'Premium Casual Shirt', 'Premium Casual Shirt', 89.00, 10, '2024-09-28 14:24:58', 'download (2).jpeg'),
(28, 'Adventure Light Pants Men', 'Adventure Light Pants Men', 50.00, 10, '2024-09-28 14:25:41', 'download (3).jpeg'),
(30, 'Field Pants', 'Field Pants', 49.00, 10, '2024-09-28 14:26:17', 'download (4).jpeg'),
(31, 'Lightweight Work Pants', 'Lightweight Work Pants', 100.00, 11, '2024-09-28 14:26:53', 't1_werkpants_mens_olive_flat_lay_4825e693-f588-4813-bff0-1d4c46ce82ce.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `phone_no` varchar(15) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `phone_no`, `address`, `created_at`) VALUES
(1, 'ridoy', 'ridoy@gmail.com', '$2y$10$.GRikI3nacNfCpjaxK7pz.ySyO07aYx3uSDbAMxlUHOFy/1ZKKeAm', 'user', '1234567890', '123 Main St', '2024-09-19 04:53:12'),
(2, 'kayes', 'kayes@gmail.com', '$2y$10$B5vSoLBe0ffpRN.hi/cJYOjgueXKjV0Ry5ebi5Lb6NUj2QpCJWDMW', 'admin', '0987654321', '456 Elm St', '2024-09-19 08:41:06'),
(3, 'customer', 'customer@gmail.com', '$2y$10$dSIaZDsJ3zrMUPymN8ffr.XJUaALkE2mNl1v4GAz02BVDrzeVlVuO', 'user', '018459018811', 'savar,dhaka', '2024-09-20 13:12:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `highlighted_products`
--
ALTER TABLE `highlighted_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `highlighted_products`
--
ALTER TABLE `highlighted_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `highlighted_products`
--
ALTER TABLE `highlighted_products`
  ADD CONSTRAINT `highlighted_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
