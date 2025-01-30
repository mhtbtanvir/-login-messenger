-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 20, 2024 at 08:15 PM
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
-- Database: `messaging-system-main`
--

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `send_from` varchar(255) NOT NULL,
  `send_to` varchar(255) NOT NULL,
  `title` text NOT NULL,
  `message` text NOT NULL,
  `URL` varchar(1000) NOT NULL,
  `send_on` date NOT NULL,
  `seen` tinyint(1) NOT NULL,
  `public` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `send_from`, `send_to`, `title`, `message`, `URL`, `send_on`, `seen`, `public`) VALUES
(1, 'admin', '@tanvir', 'test', '<p>123456</p>', '', '2024-09-20', 0, 0),
(2, 'admin', 'all', 'test-2', '<p>asdsgdjd</p>', '', '2024-09-20', 0, 1),
(3, 'admin', 'admin', 'test-3', '<p>abcd</p>', '', '2024-09-20', 1, 0),
(4, 'admin', '@tanvir454', 'test', '<p>45678</p>', '', '2024-09-20', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `public_seen`
--

CREATE TABLE `public_seen` (
  `Auto_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_seen`
--

INSERT INTO `public_seen` (`Auto_id`, `id`, `user`) VALUES
(1, 2, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_seen`
--
ALTER TABLE `public_seen`
  ADD PRIMARY KEY (`Auto_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `public_seen`
--
ALTER TABLE `public_seen`
  MODIFY `Auto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
