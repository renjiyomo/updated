-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2024 at 07:15 AM
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
-- Database: `fast_csd`
--

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `sched_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `yr_and_block` varchar(55) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `day_of_week` varchar(20) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `from_month` varchar(20) NOT NULL,
  `to_month` varchar(20) NOT NULL,
  `year` int(11) NOT NULL,
  `room_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`sched_id`, `faculty_id`, `course_id`, `yr_and_block`, `start_time`, `end_time`, `day_of_week`, `subject`, `from_month`, `to_month`, `year`, `room_id`) VALUES
(1, 3, 1, '2A', '09:00:00', '10:00:00', 'Monday', 'System Analysis, Design and Development', 'January', 'May', 2024, 4),
(2, 3, 1, '2B', '10:00:00', '11:00:00', 'Thursday', 'System Analysis, Design and Development', 'January', 'May', 2024, 4),
(3, 3, 1, '2A', '09:00:00', '12:00:00', 'Wednesday', 'System Analysis, Design and Development', 'January', 'May', 2024, 9),
(4, 6, 1, '2A', '13:00:00', '14:30:00', 'Thursday', 'Organization Development', 'January', 'May', 2024, 2),
(5, 6, 1, '2B', '14:30:00', '16:00:00', 'Sunday', 'Organization Development', 'January', 'May', 2024, 2),
(9, 3, 2, '2A', '13:00:00', '14:30:00', 'Thursday', 'System Analysis, Design and Development', 'January', 'May', 2024, 2),
(10, 3, 2, '2B', '14:30:00', '16:00:00', 'Tuesday', 'System Analysis, Design and Development', 'January', 'May', 2024, 2),
(11, 3, 2, '3B', '07:30:00', '09:00:00', 'Thursday', 'System Analysis, Design and Development', 'January', 'May', 2024, 4),
(12, 3, 1, '2B', '13:00:00', '15:00:00', 'Wednesday', 'System Analysis, Design and Development', 'January', 'May', 2024, 9),
(13, 3, 4, '2A', '02:36:00', '05:36:00', 'Wednesday', 'System Analysis, Design and Development', 'January', 'May', 2024, 5),
(14, 0, 3, '2A', '09:10:00', '11:10:00', 'Sunday', 'Financial Management', 'January', 'May', 2024, 10),
(15, 3, 4, '3B', '10:10:00', '02:10:00', 'Friday', 'Ethics', 'February', 'June', 2025, 6),
(26, 3, 1, '4A', '14:44:00', '16:44:00', 'Thursday', 'Organization Development', 'January', 'July', 2024, 6),
(27, 3, 3, '4C', '17:02:00', '20:02:00', 'Friday', 'Mag Mahal', 'January', 'December', 2025, 5),
(28, 3, 4, '4C', '12:16:23', '14:16:23', 'Thursday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(29, 3, 4, '4C', '12:16:23', '14:16:23', 'Saturday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(30, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(31, 3, 4, '4C', '12:16:23', '14:16:23', 'Saturday', 'System Analysis, Design and Development', 'January', 'February', 2026, 7),
(32, 3, 4, '4C', '12:16:23', '14:16:23', 'Thursday', 'System Analysis, Design and Development', 'January', 'February', 2025, 4),
(33, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(34, 3, 4, '4C', '12:16:23', '14:16:23', 'Thursday', 'System Analysis, Design and Development', 'January', 'February', 2024, 3),
(35, 3, 4, '4C', '12:16:23', '14:16:23', 'Wednesday', 'System Analysis, Design and Development', 'January', 'February', 2027, 3),
(36, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(37, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(38, 3, 4, '4C', '12:16:23', '14:16:23', 'Thursday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(39, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(40, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3),
(41, 3, 4, '4C', '12:16:23', '14:16:23', 'Sunday', 'System Analysis, Design and Development', 'January', 'February', 2025, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`sched_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `sched_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
