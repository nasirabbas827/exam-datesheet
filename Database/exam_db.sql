-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 09:34 AM
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
(20, 'CS201', 'CS201', 7),
(21, 'CS301', 'CS301', 7),
(22, 'CS401', 'CS401', 10),
(24, 'CS609', 'CS609', 12);

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
(70, 20, 'BC220400424', '2024-09-12 09:10:22'),
(71, 20, 'BC230400535', '2024-09-12 09:10:22'),
(72, 20, 'BC210402444', '2024-09-12 09:10:22'),
(73, 21, 'BC210209424', '2024-09-12 09:10:36'),
(74, 21, 'BC220201625', '2024-09-12 09:10:36'),
(79, 22, 'BC210402328', '2024-09-12 09:11:10'),
(80, 22, 'BC210402444', '2024-09-12 09:11:10'),
(81, 22, 'BC210402488', '2024-09-12 09:11:10'),
(82, 22, 'BC210402561', '2024-09-12 09:11:10'),
(83, 22, 'BC210402613', '2024-09-12 09:11:10'),
(84, 22, 'BC210402688', '2024-09-12 09:11:10');

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
(4, 'Jampur Hall', 50),
(5, 'DgK Hall', 500);

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
(412, 1, '20', 'CS304 Teacher', 'Jampur Hall'),
(413, 2, '21', 'CS609 Teacher', 'Jampur Hall'),
(414, 3, '22', 'CS304 Teacher', 'Jampur Hall'),
(415, 4, '24', 'CS608 Teacher', 'Jampur Hall');

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
(7, 'CS201 and CS301 Teacher'),
(9, 'CS304 Teacher'),
(10, 'CS401 Teacher'),
(11, 'CS608 Teacher'),
(12, 'CS609 Teacher');

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
(5, 7, 'ex@gmail.com', '123'),
(6, 9, 'ex1@gmail.com', '123'),
(7, 10, 'ex2@gmail.com', '123'),
(8, 11, 'cs608@gmail.com', '123'),
(9, 12, 'cs609@gmail.com', '123');

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
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `examinationhalls`
--
ALTER TABLE `examinationhalls`
  MODIFY `hall_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `examschedule`
--
ALTER TABLE `examschedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=416;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `superintendents`
--
ALTER TABLE `superintendents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
