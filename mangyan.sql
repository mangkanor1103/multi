-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 09:22 AM
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
-- Database: `mangyan`
--

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(11) NOT NULL,
  `tagalog` varchar(255) DEFAULT NULL,
  `mangyan` varchar(255) DEFAULT NULL,
  `english` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `translations`
--

INSERT INTO `translations` (`id`, `tagalog`, `mangyan`, `english`, `created_at`, `updated_at`) VALUES
(1, 'aso', 'ayam', 'dog', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(2, 'pusa', 'kuting', 'cat', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(3, 'bahay', 'balay', 'house', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(4, 'tubig', 'danum', 'water', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(5, 'araw', 'adlaw', 'sun', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(6, 'pagkain', 'pagkaon', 'food', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(7, 'maganda', 'maanyag', 'beautiful', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(8, 'salamat', 'salamat', 'thank you', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(9, 'kumusta', 'kumusta', 'how are you', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(10, 'mahal', 'palangga', 'love', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(11, 'magandang umaga', 'maayong aga', 'good morning', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(12, 'paalam', 'paalam', 'goodbye', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(13, 'kaibigan', 'abyan', 'friend', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(14, 'tao', 'tawo', 'person', '2025-04-20 06:39:51', '2025-04-20 06:39:51'),
(15, 'pamilya', 'pamilya', 'family', '2025-04-20 06:39:51', '2025-04-20 06:39:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
