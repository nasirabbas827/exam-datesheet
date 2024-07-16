-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2024 at 12:14 PM
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
  `course_id` int(11) NOT NULL,
  `course_code` varchar(255) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `faculty_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_code`, `course_name`, `faculty_id`) VALUES
(15, 'CS201', 'CS201', 1),
(16, 'CS301', 'CS301', 2),
(17, 'CS304', 'CS304', 3),
(18, 'CS401', 'CS401', 2);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `enroll_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `course_id`, `student_id`, `enroll_date`) VALUES
(25, 15, 'BC230400535', '2024-07-16 10:10:04'),
(26, 15, 'BC220400424', '2024-07-16 10:10:26'),
(27, 15, 'BC210402444', '2024-07-16 10:10:26'),
(28, 16, 'BC210209424', '2024-07-16 10:11:11'),
(29, 16, 'BC220201625', '2024-07-16 10:11:11'),
(30, 16, 'BC220400551', '2024-07-16 10:11:11'),
(31, 17, 'BC210402120', '2024-07-16 10:11:45'),
(32, 17, 'BC210402328', '2024-07-16 10:11:45'),
(33, 17, 'BC210402444', '2024-07-16 10:11:45'),
(34, 18, 'BC210402328', '2024-07-16 10:12:25'),
(35, 18, 'BC210402444', '2024-07-16 10:12:25'),
(36, 18, 'BC210402488', '2024-07-16 10:12:25'),
(37, 18, 'BC210402561', '2024-07-16 10:12:25'),
(38, 18, 'BC210402613', '2024-07-16 10:12:25'),
(39, 18, 'BC210402688', '2024-07-16 10:12:25');

-- --------------------------------------------------------

--
-- Table structure for table `examinationhalls`
--

CREATE TABLE `examinationhalls` (
  `hall_id` int(11) NOT NULL,
  `hall_name` varchar(255) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examinationhalls`
--

INSERT INTO `examinationhalls` (`hall_id`, `hall_name`, `capacity`) VALUES
(1, 'Jampur Campus Haal', 50),
(2, 'DgK Hall', 20);

-- --------------------------------------------------------

--
-- Table structure for table `examschedule`
--

CREATE TABLE `examschedule` (
  `id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `courses` text NOT NULL,
  `superintendent` varchar(255) NOT NULL,
  `hall_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `examschedule`
--

INSERT INTO `examschedule` (`id`, `day_number`, `courses`, `superintendent`, `hall_name`) VALUES
(84, 1, 'CS301', 'Dr. John Smith', 'Jampur Campus Haal'),
(85, 2, 'CS201, CS304, CS401', 'Dr. Jane Doe', 'Jampur Campus Haal'),
(86, 3, 'CS301', 'Dr. John Smith', 'Jampur Campus Haal'),
(87, 4, 'CS301', 'Dr. John Smith', 'Jampur Campus Haal');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `name`) VALUES
(1, 'Dr. John Smith'),
(2, 'Dr. Jane Doe'),
(3, 'Prof. Alan Turing'),
(4, 'Prof. Grace Hopper'),
(5, 'Dr. Ada Lovelace');

-- --------------------------------------------------------

--
-- Table structure for table `superintendents`
--

CREATE TABLE `superintendents` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `superintendents`
--

INSERT INTO `superintendents` (`id`, `faculty_id`, `email`, `password`) VALUES
(1, 1, 'sup@gmail.com', '123'),
(2, 4, 'sup1@gmail.com', '123'),
(3, 2, 'sup2@gmail.com', '123');

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
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `unique_enrollment` (`course_id`,`student_id`);

--
-- Indexes for table `examinationhalls`
--
ALTER TABLE `examinationhalls`
  ADD PRIMARY KEY (`hall_id`);

--
-- Indexes for table `examschedule`
--
ALTER TABLE `examschedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `superintendents`
--
ALTER TABLE `superintendents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

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
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `examinationhalls`
--
ALTER TABLE `examinationhalls`
  MODIFY `hall_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `examschedule`
--
ALTER TABLE `examschedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `superintendents`
--
ALTER TABLE `superintendents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `superintendents`
--
ALTER TABLE `superintendents`
  ADD CONSTRAINT `superintendents_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
