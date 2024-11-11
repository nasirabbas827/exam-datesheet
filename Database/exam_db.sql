-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2024 at 02:56 PM
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
-- Database: `exam_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`) VALUES
(2, 'CS201'),
(3, 'CS301'),
(4, 'CS304'),
(5, 'CS401');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `course_id`, `student_id`) VALUES
(8, 2, 'BC220400424'),
(9, 2, 'BC210402328'),
(11, 3, 'BC210209424'),
(12, 3, 'BC220201625'),
(13, 3, 'MC230200032'),
(14, 3, 'MC230400321'),
(15, 3, 'MC220400551'),
(16, 4, 'BC210402120'),
(17, 4, 'BC210402167'),
(18, 4, 'BC210402328'),
(19, 4, 'BC210402444'),
(20, 5, 'BC210402328'),
(21, 5, 'BC210402444'),
(22, 5, 'BC210402488'),
(23, 5, 'BC210402561'),
(24, 5, 'BC210402613'),
(25, 5, 'BC210402688');

-- --------------------------------------------------------

--
-- Table structure for table `exam_halls`
--

CREATE TABLE `exam_halls` (
  `id` int(11) NOT NULL,
  `building` varchar(100) NOT NULL,
  `floor` int(11) NOT NULL,
  `hall_number` varchar(50) NOT NULL,
  `seating_capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_halls`
--

INSERT INTO `exam_halls` (`id`, `building`, `floor`, `hall_number`, `seating_capacity`) VALUES
(1, 'Main Building', 1, '101', 5),
(2, 'Science Block', 2, '202', 100),
(3, 'Library Annex', 3, '301', 30),
(4, 'Engineering Hall', 1, '104', 70),
(5, 'Arts Center', 2, '203', 40);

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedule`
--

CREATE TABLE `exam_schedule` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `slot` varchar(50) NOT NULL,
  `time_range` varchar(50) NOT NULL,
  `superintendent_id` int(11) DEFAULT NULL,
  `hall_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_schedule`
--

INSERT INTO `exam_schedule` (`id`, `course_id`, `day`, `slot`, `time_range`, `superintendent_id`, `hall_number`) VALUES
(9, 2, 1, 'Slot 1', '8:00 - 9:30', 3, '101'),
(10, 3, 1, 'Slot 2', '10:00 - 11:30', 1, '101'),
(11, 4, 4, 'Slot 1', '8:00 - 9:30', 1, '101'),
(12, 5, 7, 'Slot 1', '8:00 - 9:30', 1, '202');

-- --------------------------------------------------------

--
-- Table structure for table `superintendents`
--

CREATE TABLE `superintendents` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superintendents`
--

INSERT INTO `superintendents` (`id`, `name`, `designation`, `department`, `email`, `password`) VALUES
(1, 'Haider CS201', 'Clerk', 'Emergency', 'sup@gmail.com', '$2y$10$6RGawRckEmnX1WulLr9bS.oXj2Wc90oTl.gfxQtWxTx0DC..jzUhS'),
(3, 'Nasir CS401', 'Clerk', 'Emergency', 'sup1@gmail.com', '$2y$10$/dqzsaYLW6mJ2oaWFmdgEOivsL/kYV7.2JKBTvgpFy0kHwmCV2wRW');

-- --------------------------------------------------------

--
-- Table structure for table `superintendent_courses`
--

CREATE TABLE `superintendent_courses` (
  `id` int(11) NOT NULL,
  `superintendent_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superintendent_courses`
--

INSERT INTO `superintendent_courses` (`id`, `superintendent_id`, `course_id`) VALUES
(5, 3, 5),
(6, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('exam_coordinator') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `name`, `email`) VALUES
(3, 'Exam-coordinator', '123', 'exam_coordinator', 'Ex Cor', 'ex@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `exam_halls`
--
ALTER TABLE `exam_halls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `superintendent_id` (`superintendent_id`);

--
-- Indexes for table `superintendents`
--
ALTER TABLE `superintendents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `superintendent_courses`
--
ALTER TABLE `superintendent_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_id` (`course_id`),
  ADD KEY `superintendent_id` (`superintendent_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `exam_halls`
--
ALTER TABLE `exam_halls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `superintendents`
--
ALTER TABLE `superintendents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `superintendent_courses`
--
ALTER TABLE `superintendent_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_schedule`
--
ALTER TABLE `exam_schedule`
  ADD CONSTRAINT `exam_schedule_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `exam_schedule_ibfk_2` FOREIGN KEY (`superintendent_id`) REFERENCES `superintendents` (`id`);

--
-- Constraints for table `superintendent_courses`
--
ALTER TABLE `superintendent_courses`
  ADD CONSTRAINT `superintendent_courses_ibfk_1` FOREIGN KEY (`superintendent_id`) REFERENCES `superintendents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `superintendent_courses_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
