-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 30, 2022 at 01:52 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `waddup`
--

-- --------------------------------------------------------

--
-- Table structure for table `active_logins`
--

CREATE TABLE `active_logins` (
  `token` char(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_logged_in` datetime NOT NULL DEFAULT current_timestamp(),
  `date_logged_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `active_logins`
--

INSERT INTO `active_logins` (`token`, `user_id`, `date_logged_in`, `date_logged_out`) VALUES
('0d9fc1ba3c59e5e97ad7ab075bda78d0885a6b77966d3f1b24c38af706e6447e', 2, '2022-01-30 20:27:37', NULL),
('8fbe2a48163a78d51ce5e91624154df01312b66f63c468014ce063686fb9954f', 1, '2022-01-30 20:27:19', '2022-01-30 20:27:22'),
('b097fe9c7f0ba4e7910a5c43c08ca8a81e0b0b7fe9c091d393951d5e2cd11471', 1, '2022-01-30 20:52:02', NULL),
('c3db894bc735eb1a5ac17088e0314f6892f7572af75fa3d2699661ec918526f3', 1, '2022-01-30 20:26:29', '2022-01-30 20:27:12'),
('cd535ac49cfe3b40b338d37470a173a31732779822a9d18e6a7245c76fea683d', 2, '2022-01-30 20:51:41', '2022-01-30 20:51:56');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `body`, `date_created`) VALUES
(1, NULL, 4, 'guest comment\r\n', '2022-01-30 20:51:33'),
(2, 2, 4, 'comment by jdoe', '2022-01-30 20:51:49'),
(3, 1, 4, 'test', '2022-01-30 20:52:17');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `archive` tinyint(1) NOT NULL DEFAULT 0,
  `expired` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `description`, `body`, `archive`, `expired`, `date_created`) VALUES
(1, 2, 'post 1', 'Post 1', 1, 1, '2022-01-29 20:26:44'),
(2, 1, 'archived post', 'Archived post', 1, 1, '2022-01-14 20:26:54'),
(3, 1, 'post 3', 'post  3', 0, 0, '2022-01-30 20:27:03'),
(4, 2, 'yes', 'John Doe', 0, 0, '2022-01-30 20:27:43');

-- --------------------------------------------------------

--
-- Table structure for table `remembered_logins`
--

CREATE TABLE `remembered_logins` (
  `token` char(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiry_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(70) NOT NULL,
  `username` varchar(79) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(79) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_reset` char(64) DEFAULT NULL,
  `password_reset_expiry` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `photo`, `email`, `password`, `password_reset`, `password_reset_expiry`, `date_created`) VALUES
(1, 'Test User', 'testuser', 'avatar.png', 'testuser@test.com', '$2y$10$s7y2scd5XuamqxrJdRXrX.NyEaBkN6OEEeY0OhFh3sp2lFNc/Nu/q', NULL, NULL, '2022-01-30 20:26:29'),
(2, 'John Doe', 'jdoe', 'avatar.png', 'jdoe@test.com', '$2y$10$I8lN5mXilFMphJwIQj8CkOKdWtfHRIBPqlOpZPp3WmGXPCgsPTDXS', NULL, NULL, '2022-01-30 20:27:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `active_logins`
--
ALTER TABLE `active_logins`
  ADD PRIMARY KEY (`token`),
  ADD KEY `active_logins_users_fk` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_users_fk` (`user_id`),
  ADD KEY `comments_posts_fk` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_users_fk` (`user_id`);

--
-- Indexes for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD PRIMARY KEY (`token`),
  ADD KEY `remember_tokens_users_fk` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uqix_username` (`username`),
  ADD UNIQUE KEY `uqix_email` (`email`),
  ADD UNIQUE KEY `uqix_password_reset` (`password_reset`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `active_logins`
--
ALTER TABLE `active_logins`
  ADD CONSTRAINT `active_logins_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_posts_fk` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remembered_logins`
--
ALTER TABLE `remembered_logins`
  ADD CONSTRAINT `remember_tokens_users_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `delete_unarchived_posts` ON SCHEDULE EVERY 5 MINUTE STARTS '2022-01-30 18:55:33' ON COMPLETION PRESERVE ENABLE COMMENT 'delete day old posts' DO update posts set expired = 1 where (date_created < date_sub(curdate(), interval 1 day) and archive = 0)$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
