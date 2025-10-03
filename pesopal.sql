-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 03, 2025 at 08:21 PM
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
-- Database: `pesopal`
--

-- --------------------------------------------------------

--
-- Table structure for table `budget_categories`
--

CREATE TABLE `budget_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `allocated_amount` decimal(12,2) NOT NULL,
  `spent_amount` decimal(12,2) DEFAULT 0.00,
  `time_frame` varchar(50) DEFAULT 'Monthly',
  `notes` text DEFAULT NULL,
  `color_class` varchar(50) DEFAULT 'bg-gray-500',
  `icon_class` varchar(100) DEFAULT 'bg-gray-100 text-gray-600 rounded-full p-1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_categories`
--

INSERT INTO `budget_categories` (`id`, `user_id`, `title`, `allocated_amount`, `spent_amount`, `time_frame`, `notes`, `color_class`, `icon_class`, `created_at`, `updated_at`) VALUES
(1, 1, 'Food & Dining', 12000.00, 9500.00, 'Monthly', NULL, 'bg-red-500', 'bg-red-100 text-red-600 rounded-full p-1', '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(5, 2, 'Acer BM500', 30000.00, 0.00, 'Weekly', 'Gagihaha', 'bg-gray-500', 'bg-gray-100 text-gray-600 rounded-full p-1', '2025-10-03 13:21:59', '2025-10-03 13:21:59'),
(6, 2, 'Vape V2', 5000.00, 3050.00, 'Weekly', '25 pieces na vape', 'bg-gray-500', 'bg-gray-100 text-gray-600 rounded-full p-1', '2025-10-03 13:46:07', '2025-10-03 16:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('Utilities','Rent','Insurance','Subscriptions','Services','Medical','Other') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('Unpaid','Paid','Overdue','Upcoming') DEFAULT 'Unpaid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `user_id`, `title`, `category`, `amount`, `due_date`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Electric Bill - September', 'Utilities', 1500.00, '2025-10-15', 'Unpaid', NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(2, 1, 'Internet Bill', 'Utilities', 2500.00, '2025-10-20', 'Unpaid', NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(3, 1, 'Apartment Rent', 'Rent', 15000.00, '2025-10-01', 'Paid', NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `receipt_date` date NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receipts`
--

INSERT INTO `receipts` (`id`, `user_id`, `title`, `vendor`, `category`, `amount`, `receipt_date`, `file_path`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Grocery Shopping', 'SM Supermarket', 'Food', 2200.00, '2025-09-20', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(2, 1, 'Gas Fill-up', 'Shell Station', 'Transport', 1200.00, '2025-09-18', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(3, 1, 'Medical Checkup', 'Metro Hospital', 'Health', 3500.00, '2025-09-15', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(4, 2, 'weryFUCKYOUNIGGA', '', 'Business', 30000.00, '2025-10-16', '2_1759424001_68deae0138060.jpg', 'daniel earl capiel', '2025-10-03 17:17:17', '2025-10-03 17:17:59'),
(5, 2, 'SIPP', '', 'Shopping', 3000.00, '2025-10-03', 'SIPP Prefinal Longquiz.pdf', 'sss', '2025-10-03 17:21:22', '2025-10-03 17:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `savings_goals`
--

CREATE TABLE `savings_goals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `target_amount` decimal(12,2) NOT NULL,
  `saved_amount` decimal(12,2) DEFAULT 0.00,
  `target_date` date NOT NULL,
  `color` varchar(20) DEFAULT '#2563eb',
  `icon_class` varchar(100) DEFAULT 'bg-blue-100 text-blue-600 rounded-full p-1',
  `notes` text DEFAULT NULL,
  `status` enum('active','completed','paused') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savings_goals`
--

INSERT INTO `savings_goals` (`id`, `user_id`, `title`, `target_amount`, `saved_amount`, `target_date`, `color`, `icon_class`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Emergency Fund', 120000.00, 78500.00, '2024-12-31', '#2563eb', 'bg-blue-100 text-blue-600 rounded-full p-1', NULL, 'active', '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(2, 1, 'Vacation Fund', 50000.00, 32000.00, '2024-06-30', '#22c55e', 'bg-green-100 text-green-600 rounded-full p-1', NULL, 'active', '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(3, 1, 'New Laptop', 80000.00, 45000.00, '2024-03-31', '#a855f7', 'bg-purple-100 text-purple-600 rounded-full p-1', NULL, 'active', '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(4, 2, 'House n Lot', 1000000.00, 1000000.00, '2030-07-04', '#2563eb', 'bg-blue-100 text-blue-600 rounded-full p-1', 'madafkaay', 'active', '2025-10-03 13:24:05', '2025-10-03 16:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `description` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `budget_category_id` int(11) DEFAULT NULL,
  `savings_goal_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `description`, `category`, `amount`, `transaction_date`, `budget_category_id`, `savings_goal_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'expense', 'Groceries', 'Food', 2200.00, '2025-09-20', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(2, 1, 'expense', 'Electricity', 'Bills', 1500.00, '2025-09-21', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(3, 1, 'expense', 'Grab ride', 'Transport', 250.00, '2025-09-22', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(4, 1, 'expense', 'Netflix', 'Entertainment', 370.00, '2025-09-23', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(5, 1, 'income', 'Salary', 'Salary', 50000.00, '2025-09-15', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(6, 1, 'expense', 'Pharmacy', 'Health', 890.00, '2025-09-25', NULL, NULL, '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(7, 2, 'income', 'Salary', 'Salary', 30000.00, '2025-10-03', NULL, NULL, '2025-10-03 13:28:32', '2025-10-03 13:28:32'),
(9, 2, 'income', 'EDIT YARN', 'Salary', 30000.00, '2025-10-03', NULL, NULL, '2025-10-03 15:22:23', '2025-10-03 17:06:20'),
(12, 2, 'expense', 'aerg', 'Transport', 232434.00, '2025-10-03', NULL, NULL, '2025-10-03 15:50:20', '2025-10-03 15:50:20'),
(15, 2, 'expense', 'mamamamama', 'Budget/Savings', 1500.00, '2025-10-03', NULL, NULL, '2025-10-03 16:01:39', '2025-10-03 16:01:39'),
(16, 2, 'expense', 'house n lot', 'Budget/Savings', 300000.00, '2025-10-03', NULL, NULL, '2025-10-03 16:03:35', '2025-10-03 16:03:35'),
(17, 2, 'expense', 'wefg', 'Budget/Savings', 10000.00, '2025-10-03', NULL, NULL, '2025-10-03 16:07:01', '2025-10-03 16:07:01'),
(22, 2, 'expense', 'mamasita', 'Budget/Savings', 300.00, '2025-10-03', 6, NULL, '2025-10-03 16:40:48', '2025-10-03 16:40:48'),
(23, 2, 'expense', 'testing', 'Budget/Savings', 50.00, '2025-10-03', 6, NULL, '2025-10-03 16:51:28', '2025-10-03 16:51:28'),
(24, 2, 'expense', 'Booom', 'Budget/Savings', 1000000.00, '2025-10-03', NULL, 4, '2025-10-03 16:52:33', '2025-10-03 16:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `birthday`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Doe', 'john.doe@example.com', '+639171234567', '1990-01-15', '$2y$10$example_hash_here', '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(2, 'Drake', 'Sekito', 'kisetodrake@gmail.com', '09204663525', '2004-07-04', '$2y$10$jS94GwTsIsVLts5r9bSrV.zPLi0DrA3Nwdk2vKBdgZOYlXK58f1aO', '2025-10-03 12:51:44', '2025-10-03 12:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `monthly_budget` decimal(12,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'PHP',
  `date_format` varchar(20) DEFAULT 'YYYY-MM-DD',
  `timezone` varchar(50) DEFAULT 'Asia/Manila',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `monthly_budget`, `currency`, `date_format`, `timezone`, `created_at`, `updated_at`) VALUES
(1, 1, 45000.00, 'PHP', 'YYYY-MM-DD', 'Asia/Manila', '2025-10-03 12:47:00', '2025-10-03 12:47:00'),
(2, 2, 0.00, 'PHP', 'YYYY-MM-DD', 'Asia/Manila', '2025-10-03 12:51:44', '2025-10-03 12:51:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budget_categories`
--
ALTER TABLE `budget_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_budget_categories_user` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_invoices_user_status` (`user_id`,`status`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_receipts_user_date` (`user_id`,`receipt_date`);

--
-- Indexes for table `savings_goals`
--
ALTER TABLE `savings_goals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_savings_goals_user` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_transactions_user_date` (`user_id`,`transaction_date`),
  ADD KEY `idx_transactions_category` (`category`),
  ADD KEY `budget_category_id` (`budget_category_id`),
  ADD KEY `savings_goal_id` (`savings_goal_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budget_categories`
--
ALTER TABLE `budget_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `savings_goals`
--
ALTER TABLE `savings_goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budget_categories`
--
ALTER TABLE `budget_categories`
  ADD CONSTRAINT `budget_categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `savings_goals`
--
ALTER TABLE `savings_goals`
  ADD CONSTRAINT `savings_goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`budget_category_id`) REFERENCES `budget_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`savings_goal_id`) REFERENCES `savings_goals` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
