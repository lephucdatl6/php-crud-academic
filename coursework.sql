-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 28, 2024 at 08:46 PM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coursework`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `comment_text` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=193 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `username`, `comment_text`, `created_at`) VALUES
(107, 79, 'user2', 'You know, asking questions is like unwrapping a present - you never know what fascinating insights you\'ll discover inside', '2024-04-26 07:45:25'),
(191, 109, 'user4', 'Hello', '2024-04-28 09:16:41'),
(148, 94, 'user1', 'Asking the right questions can truly change the game! Love this reminder to be intentional with our inquiries', '2024-04-26 18:06:08'),
(149, 94, 'user2', 'Wow, never realized the impact of questioning until now. Definitely going to practice being more \'question wise\' in my daily interactions!', '2024-04-26 18:06:31'),
(150, 94, 'Admin', 'Such a simple concept but so powerful! Great post on the importance of thoughtful questioning.', '2024-04-26 18:06:45'),
(187, 79, 'user1', 'Oh, totally! It\'s like each question opens up a whole new world of possibilities', '2024-04-28 06:36:25'),
(174, 94, 'user3', 'I love to watch TV shows like On Our Own (1994–1995)', '2024-04-27 03:54:45');

-- --------------------------------------------------------

--
-- Table structure for table `dashboard`
--

DROP TABLE IF EXISTS `dashboard`;
CREATE TABLE IF NOT EXISTS `dashboard` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `author` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `user_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dashboard`
--

INSERT INTO `dashboard` (`id`, `title`, `author`, `image`, `description`, `user_id`, `created_at`) VALUES
(109, 'Software Engineering Career Path\r\n', 'user2', 'uploads/Softwar-Engineer-la-gi-2.jpg', 'I need help with SE', 1, '2024-04-27 04:12:14'),
(94, 'Question Wise', 'user1', 'uploads/question-1015308_640.jpg', 'Learn to ask better questions for deeper understanding and connection', 50, '2024-04-26 16:24:11'),
(79, 'Have a question?', 'user2', 'uploads/slido-blog-cover-1600x1066px-3.jpg', 'Got a burning question? Don\'t hold back! Whether it\'s about life\'s mysteries or the latest trends, this is your platform to ask and explore. Let\'s ignite conversations and uncover new perspectives together!', 47, '2024-04-26 08:10:48');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `subject`, `message`, `created_at`) VALUES
(26, 50, 'Thumbs Up for Your Site!', 'Just a quick shoutout to say I love what you\'re doing with your website!', '2024-04-28 12:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(81, 'user5', 'userfive@example.com', '$2y$10$4EKFr7viK2/djy8hCtXjc.Vo0KrJExMgP0HX.F8YUGnBvetFdHhtC', NULL),
(70, 'user3', 'userthree@example.com', '$2y$10$ny7DlPsHJ1fOxsloZqgLrOnpdXzLjH0PhPI6SX4CkpNHWJFFrAUtS', NULL),
(47, 'user2', 'usertwo@example.com', '$2y$10$dcrP6p1XgJu8cjPeoS/Qi.QufTx4AdyGqHeZ0PG1UvYRmpTMUS.gK', NULL),
(50, 'user1', 'userone@example.com', '$2y$10$4GdkqFd7g9/ioL7Gj07WUOQ2ytIf7MhA4d7t2YcI/Mma0QWbqN7bm', NULL),
(1, 'Admin', 'admin@gmail.com', '$2y$10$g4iBJmKA5x91dHKcMCz8e.JluqXVYOT.UdvoqibZT1ophXUwUxGQa', 'admin');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
