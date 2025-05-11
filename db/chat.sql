-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 01:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chat`
--

-- --------------------------------------------------------

--
-- Table structure for table `follows`
--

CREATE TABLE `follows` (
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `follows`
--

INSERT INTO `follows` (`follower_id`, `following_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(2, 1),
(2, 3),
(3, 2),
(5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_read`) VALUES
(1, 1, 2, 'oyooo', '2025-05-02 15:23:58', 0),
(2, 1, 2, 'kamakawa', '2025-05-02 15:24:09', 0),
(3, 2, 1, 'unyama', '2025-05-02 15:25:06', 0),
(4, 2, 1, 'unyama mambo yanasemaje mdau', '2025-05-02 15:26:40', 0),
(5, 2, 1, 'unyama mambo yanasemaje mdau', '2025-05-02 15:27:22', 0),
(6, 1, 2, 'daaa unyama blood', '2025-05-02 15:28:46', 0),
(7, 1, 2, 'ibilisi alinipanda sa nikafanya nini', '2025-05-02 15:42:42', 0),
(8, 1, 2, 'daaa unyama blood', '2025-05-02 15:42:58', 0),
(9, 1, 2, 'daaa unyama blood', '2025-05-02 15:43:08', 0),
(10, 2, 1, 'wee sikua', '2025-05-02 15:44:26', 0),
(11, 2, 3, 'asalaam aleykum', '2025-05-03 18:41:33', 0),
(12, 2, 3, 'oyoooo', '2025-05-03 19:18:57', 0),
(13, 3, 2, 'ahaa kwema', '2025-05-03 19:20:34', 0),
(14, 4, 1, 'oyaaa', '2025-05-03 19:22:11', 0),
(15, 1, 4, 'uko fleshi', '2025-05-03 19:23:07', 0),
(16, 1, 3, 'ouyaaa', '2025-05-04 10:41:39', 0),
(17, 1, 2, 'oyoo', '2025-05-04 11:13:04', 0),
(18, 5, 3, 'hahahhah', '2025-05-07 15:43:04', 0),
(19, 5, 3, 'kwema', '2025-05-07 15:43:14', 0),
(20, 5, 1, 'hahah kweam', '2025-05-07 15:43:34', 0),
(21, 5, 1, 'daca', '2025-05-07 15:43:37', 0),
(22, 5, 1, 'baba bora', '2025-05-07 15:43:43', 0),
(23, 3, 5, 'haha hujamvo mwanangu', '2025-05-07 15:44:31', 0),
(24, 1, 2, 'sss', '2025-05-07 16:05:57', 0),
(25, 1, 5, 'powa mwanangu', '2025-05-07 16:07:37', 0),
(26, 3, 1, 'kwema', '2025-05-07 16:09:57', 1),
(27, 1, 3, 'nhhhhhhhhh', '2025-05-08 17:32:10', 0),
(28, 1, 3, 'nhhhhhhhhh', '2025-05-08 17:41:55', 0),
(29, 1, 3, 'heeey', '2025-05-08 18:22:14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `typing_status`
--

CREATE TABLE `typing_status` (
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `is_typing` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `profile_picture` varchar(255) DEFAULT 'default.jpg',
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `state` enum('free','busy','booked') DEFAULT 'free',
  `status` enum('online','offline','busy','away') DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_pic`, `profile_picture`, `bio`, `location`, `contact_info`, `state`, `status`) VALUES
(1, 'abdulhabibalkithri', 'abdulhabibalkithri@gmail.com', '$2y$10$fff2Rt0gmmIToFxfX0Y.sugEJOuKqVk.PMxrP1bheVvNCp2mSwGqq', 'default.png', 'profile_1.png', 'oyoooo', NULL, NULL, 'busy', 'offline'),
(2, 'said', 'said@gmail.com', '$2y$10$cWoCxhwzUgUUXuDZ/3BXje2XFu5pLaxZEZC3uXPH.zxqYHjtfQGhC', 'default.png', 'profile_2.png', 'kama kawa', NULL, NULL, 'booked', 'offline'),
(3, 'baba', 'baba@gmail.com', '$2y$10$PG2B4/VQSKUQdydC52m91OrFtI3pDRuOgOueKWI6goQXKIlYTdtvm', 'default.png', 'raw', 'mimi ndo baba', NULL, NULL, 'free', 'offline'),
(4, 'sanga', 'sanga@gmail.com', '$2y$10$KNcBvz5X4KXSaskmkS3FR.MTNn/KT8Rfo87ZFWEGClXZ4nZ8dStP.', 'default.png', 'ahmed_couture_square_logo_512x512.png', 'hello mimi ndo mwamba', NULL, NULL, 'busy', 'offline'),
(5, 'John', 'kehengujohn0@gmail.com', '$2y$10$MY5.kc6mZKXlUUDCXw6QoOrQGTOxCtHeQiN4imqhZe.5HJ8pCpqfi', 'default.png', 'default.jpg', NULL, NULL, NULL, 'free', 'offline');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `typing_status`
--
ALTER TABLE `typing_status`
  ADD PRIMARY KEY (`sender_id`,`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `follows_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
