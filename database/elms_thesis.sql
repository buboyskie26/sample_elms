-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2023 at 08:12 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elms`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `assignment_id` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `dateCreation` datetime NOT NULL DEFAULT current_timestamp(),
  `file_upload` varchar(150) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `creationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_name`, `creationDate`) VALUES
(1, 'BSCS-501', '2023-01-17 15:53:11'),
(2, 'BSCS-701', '2023-01-17 17:10:36'),
(15, 'Sample', '2023-02-24 16:09:55');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `dean` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`, `dean`) VALUES
(1, 'Collge of Industrial Technology', 'Gascon Junemar'),
(3, 'College of Physist', 'Carlo Romulo'),
(4, 'College of Education', 'Majong Cojuanco');

-- --------------------------------------------------------

--
-- Table structure for table `group_chat`
--

CREATE TABLE `group_chat` (
  `group_chat_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `description` varchar(300) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `group_chat`
--

INSERT INTO `group_chat` (`group_chat_id`, `teacher_course_id`, `teacher_id`, `description`, `created_at`) VALUES
(9, 6, 1, 'Group Chat', '2023-03-03 19:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `group_chat_member`
--

CREATE TABLE `group_chat_member` (
  `group_chat_member_id` int(11) NOT NULL,
  `group_chat_id` int(11) NOT NULL,
  `user_username` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `group_chat_member`
--

INSERT INTO `group_chat_member` (`group_chat_member_id`, `group_chat_id`, `user_username`, `created_at`) VALUES
(3, 9, '102', '2023-03-03 19:18:58'),
(4, 9, '101', '2023-03-03 19:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `group_message`
--

CREATE TABLE `group_message` (
  `group_message_id` int(11) NOT NULL,
  `group_chat_id` int(11) NOT NULL,
  `user_username` int(11) NOT NULL,
  `body` varchar(350) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `group_message`
--

INSERT INTO `group_message` (`group_message_id`, `group_chat_id`, `user_username`, `body`, `created_at`) VALUES
(1, 9, 101, 'hello gc from hyper', '2023-03-04 09:31:49'),
(15, 9, 200, 'Hello Gc from Lexter(Teacher)', '2023-03-04 11:10:54'),
(16, 9, 101, 'Crazy is me', '2023-03-04 11:46:14'),
(17, 9, 101, 'Ase', '2023-03-04 11:47:10');

-- --------------------------------------------------------

--
-- Table structure for table `handout_viewed`
--

CREATE TABLE `handout_viewed` (
  `handout_viewed_id` int(11) NOT NULL,
  `subject_period_assignment_handout_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `handout_viewed`
--

INSERT INTO `handout_viewed` (`handout_viewed_id`, `subject_period_assignment_handout_id`, `teacher_course_id`, `student_id`, `date_creation`, `count`) VALUES
(1, 5, 6, 2, '2023-02-21 19:04:16', 16),
(4, 7, 6, 2, '2023-02-22 19:07:17', 4),
(5, 7, 6, 3, '2023-02-22 19:24:47', 1),
(6, 5, 6, 3, '2023-02-22 19:27:25', 1),
(7, 9, 6, 2, '2023-02-23 10:26:53', 2),
(8, 9, 6, 3, '2023-02-23 14:59:18', 1),
(9, 8, 6, 3, '2023-02-23 14:59:25', 1),
(10, 8, 6, 2, '2023-02-24 09:41:56', 2),
(11, 10, 0, 5, '2023-03-02 17:07:17', 2);

-- --------------------------------------------------------

--
-- Table structure for table `message_teacher`
--

CREATE TABLE `message_teacher` (
  `message_teacher_id` int(11) NOT NULL,
  `to_username` varchar(100) NOT NULL,
  `from_username` varchar(100) NOT NULL,
  `body` text NOT NULL,
  `message_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `opened` varchar(3) NOT NULL DEFAULT 'no',
  `viewed` varchar(3) NOT NULL DEFAULT 'no',
  `deleted` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `message_teacher`
--

INSERT INTO `message_teacher` (`message_teacher_id`, `to_username`, `from_username`, `body`, `message_creation`, `opened`, `viewed`, `deleted`) VALUES
(12, '200', '101', 'From Hyper to Lex', '2023-03-03 11:39:39', 'no', 'no', 'no'),
(13, '101', '200', 'From Lex to Hyper', '2023-03-03 11:44:05', 'no', 'no', 'no'),
(14, '200', '102', 'Hello Lex From Justine', '2023-03-03 12:38:03', 'no', 'no', 'no'),
(15, '102', '200', 'Hi Justine From Lex', '2023-03-03 12:55:32', 'no', 'no', 'no'),
(16, '101', '200', 'Hi sir', '2023-03-03 15:22:41', 'no', 'no', 'no'),
(17, '200', '102', 'Hi', '2023-03-03 15:23:02', 'no', 'no', 'no'),
(18, '200', '101', 'Xx', '2023-03-03 15:40:29', 'no', 'no', 'no'),
(36, '200', '101', 'Try', '2023-03-03 16:05:27', 'no', 'no', 'no'),
(37, '200', '101', 'tr', '2023-03-03 16:05:50', 'no', 'no', 'no'),
(38, '200', '101', 't', '2023-03-03 16:06:22', 'no', 'no', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `question_type`
--

CREATE TABLE `question_type` (
  `question_type_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question_type`
--

INSERT INTO `question_type` (`question_type_id`, `type`) VALUES
(1, 'Multiple Choices'),
(2, 'True or False');

-- --------------------------------------------------------

--
-- Table structure for table `school_year`
--

CREATE TABLE `school_year` (
  `school_year_id` int(11) NOT NULL,
  `school_year_term` varchar(10) NOT NULL,
  `period` varchar(15) NOT NULL,
  `statuses` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `school_year`
--

INSERT INTO `school_year` (`school_year_id`, `school_year_term`, `period`, `statuses`) VALUES
(1, '2021-2022', '1st Semester', 'InActive'),
(2, '2021-2022', '2nd Semester', 'InActive'),
(3, '2022-2023', '1st Semester', 'Active'),
(5, '2022-2023', '2nd Semester', 'InActive');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `profilePic` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `status` varchar(15) NOT NULL,
  `dateCreation` datetime NOT NULL DEFAULT current_timestamp(),
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `firstname`, `lastname`, `username`, `profilePic`, `password`, `status`, `dateCreation`, `course_id`) VALUES
(2, 'Hyper', 'Sirios', '101', 'assets/images/profilePictures/default-female.png', '123', '', '2023-01-17 17:18:05', 1),
(3, 'Justine', 'Sirios', '102', 'assets/images/profilePictures/default.png', '123', '', '2023-01-19 10:51:34', 1),
(4, 'Mark', 'Zuckerberg', '103', 'assets/images/profilePictures/default-male.png', '123', '', '2023-01-19 10:59:44', 1),
(5, 'Michael', 'Jordan', '104', 'assets/images/profilePictures/default-female.png', '123', '', '2023-01-19 10:59:52', 1),
(6, 'Jabari', 'Parker', '105', 'assets/images/profilePictures/default-male.png', '123', '', '2023-02-24 12:16:15', 1),
(7, 'Mary', 'Ann', '106', 'assets/images/profilePictures/default-female.png', '123', '', '2023-02-24 12:17:40', 1),
(9, 'Jose', 'Burgos', '108', 'assets/images/profilePictures/default-female.png', '123', '', '2023-02-24 12:19:24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_period_assignment`
--

CREATE TABLE `student_period_assignment` (
  `student_period_assignment_id` int(11) NOT NULL,
  `assignment_file` varchar(300) NOT NULL,
  `file_name` varchar(300) NOT NULL,
  `description` text NOT NULL,
  `subject_period_assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `passed_date` datetime NOT NULL DEFAULT current_timestamp(),
  `grade` int(11) NOT NULL,
  `is_final` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_period_assignment`
--

INSERT INTO `student_period_assignment` (`student_period_assignment_id`, `assignment_file`, `file_name`, `description`, `subject_period_assignment_id`, `student_id`, `passed_date`, `grade`, `is_final`) VALUES
(82, '', 'QWE', '<p>QWEQWE</p>', 46, 2, '2023-02-23 11:14:03', 49, 'yes'),
(83, '', 'Sample Answer', '<p>Sample Answer Desc<br></p>', 36, 2, '2023-02-23 11:16:36', 0, 'no'),
(84, '', 'Sample Answer 2', '<p>Sample Answer 2 Desc<br></p>', 36, 2, '2023-02-23 11:17:15', 99, 'yes'),
(85, '', 'Template Answer', '<p><b>Template Answer</b><br></p>', 50, 2, '2023-02-27 17:15:56', 49, 'yes'),
(86, '', 'Answer for tp 1', '<p>Answer for tp 1 Desc<br></p>', 51, 2, '2023-03-02 07:39:18', 0, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `student_period_assignment_file`
--

CREATE TABLE `student_period_assignment_file` (
  `student_period_assignment_file_id` int(11) NOT NULL,
  `assignment_file_path` varchar(300) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `student_period_assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_period_assignment_file`
--

INSERT INTO `student_period_assignment_file` (`student_period_assignment_file_id`, `assignment_file_path`, `student_id`, `date_creation`, `student_period_assignment_id`) VALUES
(32, 'assets/images/student_assignments_answers/328632483_3476385889315016_3947217404876821484_n.jpg', 2, '2023-02-23 11:14:03', 82),
(33, 'assets/images/student_assignments_answers/plates.jpg', 2, '2023-02-23 11:16:36', 83),
(34, 'assets/images/student_assignments_answers/328632483_3476385889315016_3947217404876821484_n.jpg', 2, '2023-02-23 11:17:15', 84),
(35, 'assets/images/student_assignments_answers/plates.jpg', 2, '2023-02-27 17:15:56', 85),
(36, 'assets/images/student_assignments_answers/324470653_1156158695100351_4817315951187080530_n.jpg', 2, '2023-03-02 07:39:18', 86),
(37, 'assets/images/student_assignments_answers/325273727_705280694540885_662119871940060535_n.jpg', 2, '2023-03-02 07:39:18', 86),
(38, 'assets/images/student_assignments_answers/323691680_1564932584026563_3827488229617188446_n.jpg', 2, '2023-03-02 07:39:18', 86);

-- --------------------------------------------------------

--
-- Table structure for table `student_period_assignment_multi_question_answer`
--

CREATE TABLE `student_period_assignment_multi_question_answer` (
  `student_period_assignment_multi_question_answer_id` int(11) NOT NULL,
  `subject_period_assignment_quiz_question_answer_id` int(11) NOT NULL,
  `my_answer` varchar(150) NOT NULL,
  `question_answer` varchar(150) NOT NULL,
  `time_submit` datetime NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) NOT NULL,
  `subject_period_assignment_id` int(11) NOT NULL,
  `subject_period_assignment_quiz_question_id` int(11) NOT NULL,
  `student_period_assignment_quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_period_assignment_multi_question_answer`
--

INSERT INTO `student_period_assignment_multi_question_answer` (`student_period_assignment_multi_question_answer_id`, `subject_period_assignment_quiz_question_answer_id`, `my_answer`, `question_answer`, `time_submit`, `student_id`, `subject_period_assignment_id`, `subject_period_assignment_quiz_question_id`, `student_period_assignment_quiz_id`) VALUES
(57, 16, 'A', 'A', '2023-02-23 12:07:00', 2, 48, 16, 29),
(59, 18, 'A', 'A', '2023-02-23 12:09:56', 2, 49, 18, 30),
(80, 11, 'B', 'A', '2023-02-24 09:16:03', 2, 40, 11, 28),
(81, 20, 'A', 'A', '2023-03-02 07:45:36', 2, 52, 20, 31),
(82, 16, 'A', 'A', '2023-03-02 15:58:30', 3, 48, 16, 32);

-- --------------------------------------------------------

--
-- Table structure for table `student_period_assignment_quiz`
--

CREATE TABLE `student_period_assignment_quiz` (
  `student_period_assignment_quiz_id` int(11) NOT NULL,
  `subject_period_assignment_quiz_class_id` int(11) NOT NULL,
  `student_quiz_time` varchar(100) NOT NULL,
  `total_score` int(11) NOT NULL,
  `time_taken` datetime NOT NULL DEFAULT current_timestamp(),
  `time_finish` datetime DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `take_quiz_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_period_assignment_quiz`
--

INSERT INTO `student_period_assignment_quiz` (`student_period_assignment_quiz_id`, `subject_period_assignment_quiz_class_id`, `student_quiz_time`, `total_score`, `time_taken`, `time_finish`, `student_id`, `take_quiz_count`) VALUES
(28, 4, '49988', 2, '2023-02-20 11:34:41', '2023-02-24 09:16:44', 2, 3),
(29, 19, '593', 4, '2023-02-23 12:06:57', '2023-02-23 12:07:05', 2, 1),
(30, 18, '595', 4, '2023-02-23 12:09:04', '2023-02-23 12:09:58', 2, 1),
(31, 20, '583', 2, '2023-03-02 07:45:10', '2023-03-02 07:45:47', 2, 1),
(32, 19, '592', 2, '2023-03-02 15:58:23', '2023-03-02 15:58:33', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_period_assignment_quiz_question_answer`
--

CREATE TABLE `student_period_assignment_quiz_question_answer` (
  `student_period_assignment_quiz_question_answer_id` int(11) NOT NULL,
  `subject_period_assignment_quiz_question_id` int(11) NOT NULL,
  `my_answer` varchar(150) NOT NULL,
  `question_answer` varchar(150) NOT NULL,
  `time_submit` datetime NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) NOT NULL,
  `subject_period_assignment_id` int(11) NOT NULL,
  `student_period_assignment_quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_period_assignment_quiz_question_answer`
--

INSERT INTO `student_period_assignment_quiz_question_answer` (`student_period_assignment_quiz_question_answer_id`, `subject_period_assignment_quiz_question_id`, `my_answer`, `question_answer`, `time_submit`, `student_id`, `subject_period_assignment_id`, `student_period_assignment_quiz_id`) VALUES
(56, 17, 'True', 'True', '2023-02-23 12:07:01', 2, 48, 29),
(58, 19, 'True', 'True', '2023-02-23 12:09:58', 2, 49, 30),
(78, 10, 'True', 'True', '2023-02-24 09:16:02', 2, 40, 28),
(79, 21, 'False', 'True', '2023-03-02 07:45:40', 2, 52, 31),
(80, 17, 'False', 'True', '2023-03-02 15:58:32', 3, 48, 32);

-- --------------------------------------------------------

--
-- Table structure for table `student_period_quiz`
--

CREATE TABLE `student_period_quiz` (
  `student_period_quiz_id` int(11) NOT NULL,
  `subject_period_quiz_class_id` int(11) NOT NULL,
  `student_quiz_time` varchar(100) NOT NULL,
  `total_score` int(11) NOT NULL,
  `time_taken` datetime NOT NULL DEFAULT current_timestamp(),
  `time_finish` datetime DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `take_quiz_count` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_period_quiz`
--

INSERT INTO `student_period_quiz` (`student_period_quiz_id`, `subject_period_quiz_class_id`, `student_quiz_time`, `total_score`, `time_taken`, `time_finish`, `student_id`, `take_quiz_count`) VALUES
(18, 6, '0', 0, '2023-02-10 19:11:28', '2023-02-20 12:22:44', 2, 2),
(32, 8, '0', 0, '2023-02-14 11:30:12', NULL, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_period_quiz_multi_question_answer`
--

CREATE TABLE `student_period_quiz_multi_question_answer` (
  `student_period_quiz_multi_question_answer_id` int(11) NOT NULL,
  `subject_period_quiz_question_answer_id` int(11) NOT NULL,
  `my_answer` varchar(150) NOT NULL,
  `question_answer` varchar(150) NOT NULL,
  `time_submit` datetime NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) NOT NULL,
  `subject_period_quiz_id` int(11) NOT NULL,
  `subject_period_quiz_question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `student_period_quiz_question_answer`
--

CREATE TABLE `student_period_quiz_question_answer` (
  `student_period_quiz_question_answer_id` int(11) NOT NULL,
  `subject_period_quiz_question_id` int(11) NOT NULL,
  `my_answer` varchar(150) NOT NULL,
  `question_answer` varchar(150) NOT NULL,
  `time_submit` datetime DEFAULT current_timestamp(),
  `student_id` int(11) NOT NULL,
  `subject_period_quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(100) NOT NULL,
  `subject_title` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `unit` int(11) NOT NULL,
  `semester` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_code`, `subject_title`, `category`, `description`, `unit`, `semester`) VALUES
(5, 'AppDev101', 'Application Development', '', 'Application Development Description', 3, '1st'),
(6, 'COMPROG1', 'Computer Programming 1', '', 'Computer Programming 1 Description', 3, '1st'),
(7, 'Sample Code', 'Sample Title', '', 'Sample Desc', 15, '1st');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period`
--

CREATE TABLE `subject_period` (
  `subject_period_id` int(11) NOT NULL,
  `term` varchar(10) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `thumbnail` varchar(150) NOT NULL,
  `section_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period`
--

INSERT INTO `subject_period` (`subject_period_id`, `term`, `title`, `description`, `subject_id`, `teacher_course_id`, `thumbnail`, `section_num`) VALUES
(2, 'Prelim', 'Requirements Analysis and Modeling (Prelim)', '  <p>\n                        LO1:    Determine the concepts of how technologies emerge in the real world;\n                        <br>\n                        LO2:	Analyze the potential application of various emerging technologies in a wide variety of settings; and\n                        <br>\n                    </p>', 5, 6, 'assets/images/profilePictures/thumb_1.jpg', 1),
(5, 'Midterm', 'Design Principles (Midterm)', '  <p>\n                        LO1:    Determine the concepts of how technologies emerge in the real world;\n                        <br>\n                        LO2:	Analyze the potential application of various emerging technologies in a wide variety of settings; and\n                        <br>\n                    </p>', 5, 6, 'assets/images/profilePictures/thumb_2.jpg', 2),
(13, 'Prelim', 'Basic Programmingee (Prelim)', 'LO1: compare types of programming languages;\r\nLO2: describe the programming cycle;\r\nLO3: construct algorithms, pseudocodes, and flowcharts; and\r\nLO4: differentiate procedural programming from object-oriented programming.', 6, 7, 'assets/images/profilePictures/thumb_1.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_assignment`
--

CREATE TABLE `subject_period_assignment` (
  `subject_period_assignment_id` int(11) NOT NULL,
  `type_name` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `dateCreation` datetime NOT NULL DEFAULT current_timestamp(),
  `assignment_upload` varchar(150) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `subject_period_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `viewed` varchar(3) NOT NULL,
  `due_date` datetime DEFAULT NULL,
  `max_submission` int(11) NOT NULL DEFAULT 1,
  `max_score` int(11) NOT NULL,
  `allow_late_submission` varchar(3) NOT NULL DEFAULT 'no',
  `ass_type` varchar(8) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `set_quiz` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_assignment`
--

INSERT INTO `subject_period_assignment` (`subject_period_assignment_id`, `type_name`, `description`, `dateCreation`, `assignment_upload`, `teacher_course_id`, `subject_period_id`, `subject_id`, `viewed`, `due_date`, `max_submission`, `max_score`, `allow_late_submission`, `ass_type`, `teacher_id`, `set_quiz`) VALUES
(46, 'Task Performance', '<p>Golden rule. Do it Consistently.<br></p>', '2023-02-22 19:16:04', 'assets/images/student_assignments/328632483_3476385889315016_3947217404876821484_n.jpg', 6, 5, 5, 'no', '2023-02-22 00:00:00', 2, 50, 'no', 'Dropbox', 1, 'no'),
(47, 'Activity 1', '<p><b>Answer Please</b><br></p>', '2023-02-23 10:35:00', 'assets/images/student_assignments/328632483_3476385889315016_3947217404876821484_n.jpg', 6, 5, 5, 'no', '2023-02-25 00:00:00', 2, 50, 'no', 'Dropbox', 1, 'no'),
(48, 'Sample Quiz One', 'Sample Quiz One Desc', '2023-02-23 11:56:24', '', 6, 5, 5, '', '2023-03-25 15:10:00', 1, 4, 'no', 'Quiz', 1, 'yes'),
(49, 'Sample Quiz Two', 'Sample Quiz Two Desc', '2023-02-23 11:57:47', '', 6, 5, 5, '', '2023-02-26 15:10:00', 2, 4, 'no', 'Quiz', 1, 'yes'),
(50, 'Activity 1', '<p>Kindly answer this.</p>', '2023-02-27 17:11:26', 'assets/images/student_assignments/325041886_1600900260333937_9063984766035328628_n.jpg', 6, 2, 5, 'no', '2023-02-28 00:00:00', 2, 50, 'no', 'Dropbox', 1, 'no'),
(51, 'Task Performance 1', '<p>Follow the instructions</p>', '2023-03-02 07:38:22', 'assets/images/student_assignments/325041886_1600900260333937_9063984766035328628_n.jpg', 6, 2, 5, 'no', '2023-03-03 00:00:00', 2, 50, 'no', 'Dropbox', 1, 'no'),
(52, 'Quiz 1', 'Quiz 1 Dsc', '2023-03-02 07:42:35', '', 6, 2, 5, '', '2023-03-15 14:15:00', 1, 4, 'no', 'Quiz', 1, 'yes'),
(53, 'Samp Quiz', 'Samp Quiz Desc', '2023-03-02 07:50:28', '', 6, 2, 5, '', '2023-03-14 15:10:00', 1, 0, 'no', 'Quiz', 1, 'no'),
(54, 'Sample Assignment', '<p>Sample Assignment Desc<br></p>', '2023-03-02 17:01:07', '', 0, 13, 6, 'no', '2023-03-30 00:00:00', 2, 50, 'no', 'Dropbox', 1, 'no'),
(55, 'Sample2', '<p>Sample2 Desc<br></p>', '2023-03-02 17:02:48', '', 0, 13, 6, 'no', '2023-03-23 00:00:00', 2, 100, 'no', 'Dropbox', 1, 'no');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_assignment_handout`
--

CREATE TABLE `subject_period_assignment_handout` (
  `subject_period_assignment_handout_id` int(11) NOT NULL,
  `subject_period_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `handout_name` varchar(150) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_assignment_handout`
--

INSERT INTO `subject_period_assignment_handout` (`subject_period_assignment_handout_id`, `subject_period_id`, `teacher_course_id`, `subject_id`, `handout_name`, `date_creation`) VALUES
(5, 2, 6, 5, 'Handout 1', '2023-02-21 15:14:43'),
(7, 2, 6, 5, 'Second Handout', '2023-02-22 19:07:09'),
(8, 5, 6, 5, 'Handbook Essentials 1', '2023-02-23 10:24:31'),
(9, 5, 6, 5, 'Handbook Essentials 2', '2023-02-23 10:26:35'),
(10, 13, 7, 6, 'Happiest Man', '2023-03-02 17:01:22'),
(11, 13, 7, 6, 'Pants Essentials', '2023-03-02 17:03:19');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_assignment_handout_file`
--

CREATE TABLE `subject_period_assignment_handout_file` (
  `subject_period_assignment_handout_file_id` int(11) NOT NULL,
  `subject_period_assignment_handout_id` int(11) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `handout_file_location` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_assignment_handout_file`
--

INSERT INTO `subject_period_assignment_handout_file` (`subject_period_assignment_handout_file_id`, `subject_period_assignment_handout_id`, `date_creation`, `handout_file_location`) VALUES
(1, 5, '2023-02-21 15:14:43', 'assets/images/handouts/STI PANTS PDF.pdf'),
(4, 7, '2023-02-22 19:07:09', 'assets/images/handouts/5373_File_STI PANTS PDF.pdf'),
(5, 7, '2023-02-22 19:07:09', 'assets/images/handouts/DETERGE_Thesis_0.pdf'),
(6, 8, '2023-02-23 10:24:31', 'assets/images/handouts/5373_File_STI PANTS PDF.pdf'),
(7, 9, '2023-02-23 10:26:35', 'assets/images/handouts/TrafficSimulation_Revised.pdf'),
(8, 10, '2023-03-02 17:01:22', 'assets/images/handouts/Happiest Man on Earth by Eddie Jaku.docx'),
(9, 11, '2023-03-02 17:03:19', 'assets/images/handouts/5373_File_STI PANTS PDF.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_assignment_quiz_class`
--

CREATE TABLE `subject_period_assignment_quiz_class` (
  `subject_period_assignment_quiz_class_id` int(11) NOT NULL,
  `subject_period_assignment_id` int(11) NOT NULL,
  `subject_period_id` int(11) NOT NULL,
  `quiz_time` int(11) NOT NULL,
  `max_score` int(11) NOT NULL,
  `max_attempt` int(11) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `due_date` datetime DEFAULT NULL,
  `show_correct_answer` varchar(3) NOT NULL,
  `allow_late_submission` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_assignment_quiz_class`
--

INSERT INTO `subject_period_assignment_quiz_class` (`subject_period_assignment_quiz_class_id`, `subject_period_assignment_id`, `subject_period_id`, `quiz_time`, `max_score`, `max_attempt`, `date_creation`, `due_date`, `show_correct_answer`, `allow_late_submission`) VALUES
(4, 40, 2, 50000, 0, 0, '2023-02-16 16:33:00', NULL, 'yes', ''),
(18, 49, 5, 600, 0, 0, '2023-02-23 12:00:29', NULL, '', ''),
(19, 48, 5, 600, 0, 0, '2023-02-23 12:00:35', NULL, '', ''),
(20, 52, 2, 600, 0, 0, '2023-03-02 07:44:54', NULL, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_assignment_quiz_question`
--

CREATE TABLE `subject_period_assignment_quiz_question` (
  `subject_period_assignment_quiz_question_id` int(11) NOT NULL,
  `subject_period_assignment_id` int(11) NOT NULL,
  `question_type_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_answer` varchar(150) NOT NULL,
  `points` int(11) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_assignment_quiz_question`
--

INSERT INTO `subject_period_assignment_quiz_question` (`subject_period_assignment_quiz_question_id`, `subject_period_assignment_id`, `question_type_id`, `question_text`, `question_answer`, `points`, `date_creation`) VALUES
(10, 40, 2, '<p>One TF</p>', 'True', 2, '2023-02-17 16:24:45'),
(11, 40, 1, '<p>Two Multiple</p>', 'A', 3, '2023-02-17 16:25:12'),
(16, 48, 1, '<p>Sample Quiz One Question<br></p>', 'A', 2, '2023-02-23 11:56:44'),
(17, 48, 2, '<p>Sample Quiz Two Question<br></p>', 'True', 2, '2023-02-23 11:56:58'),
(18, 49, 1, '<p>Sample Quiz Two Question<br></p>', 'A', 2, '2023-02-23 11:58:09'),
(19, 49, 2, '<p><span style=\"background-color: rgb(51, 51, 51);\">Sample Quiz One Question</span><br></p>', 'True', 2, '2023-02-23 11:58:52'),
(20, 52, 1, '<p>Sample Quiz One <br></p>', 'A', 2, '2023-03-02 07:43:04'),
(21, 52, 2, '<p>True or False Question Two</p>', 'True', 2, '2023-03-02 07:44:30');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_assignment_quiz_question_answer`
--

CREATE TABLE `subject_period_assignment_quiz_question_answer` (
  `subject_period_assignment_quiz_question_answer_id` int(11) NOT NULL,
  `answer_text` varchar(150) NOT NULL,
  `choices` varchar(2) NOT NULL,
  `subject_period_assignment_quiz_question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_assignment_quiz_question_answer`
--

INSERT INTO `subject_period_assignment_quiz_question_answer` (`subject_period_assignment_quiz_question_answer_id`, `answer_text`, `choices`, `subject_period_assignment_quiz_question_id`) VALUES
(17, 'AA', 'A', 11),
(18, 'BB', 'B', 11),
(19, 'CC', 'C', 11),
(20, 'DD', 'D', 11),
(21, 'ab', 'A', 12),
(22, 'baca', 'B', 12),
(23, 'ca', 'C', 12),
(24, 'da', 'D', 12),
(25, 'AA', 'A', 16),
(26, 'BB', 'B', 16),
(27, 'CC', 'C', 16),
(28, 'DD', 'D', 16),
(29, 'AA', 'A', 18),
(30, 'BB', 'B', 18),
(31, 'CC', 'C', 18),
(32, 'DD', 'D', 18),
(33, 'Question 1 on Multiple', 'A', 20),
(34, 'Question 2 on Multiple', 'B', 20),
(35, 'Question 3 on Multiple', 'C', 20),
(36, 'Question 4 on Multiple', 'D', 20);

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_quiz`
--

CREATE TABLE `subject_period_quiz` (
  `subject_period_quiz_id` int(11) NOT NULL,
  `quiz_title` varchar(50) NOT NULL,
  `quiz_description` text NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `due_date` datetime NOT NULL,
  `subject_period_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_quiz`
--

INSERT INTO `subject_period_quiz` (`subject_period_quiz_id`, `quiz_title`, `quiz_description`, `date_creation`, `due_date`, `subject_period_id`, `teacher_id`) VALUES
(3, 'Quiz One', 'Quiz One Desc', '2023-02-03 20:34:19', '2023-02-17 11:55:00', 2, 1),
(4, 'Quiz Two', 'Quiz Two Desc', '2023-02-04 12:15:12', '2023-02-17 11:55:00', 2, 1),
(6, 'Quiz Three', 'Quiz Three Desc', '2023-02-10 08:42:39', '2023-02-17 11:55:00', 2, 1),
(8, 'Smart is best', 'Smart is best Desc', '2023-02-13 19:22:53', '2023-02-17 11:55:00', 2, 1),
(33, 'Abacada', 'Abacada Desc', '2023-02-23 11:53:23', '2023-02-25 15:10:00', 5, 1),
(34, 'Pamela One', 'Pamela One Desc', '2023-02-23 11:53:42', '2023-02-26 15:15:00', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_quiz_class`
--

CREATE TABLE `subject_period_quiz_class` (
  `subject_period_quiz_class_id` int(11) NOT NULL,
  `subject_period_quiz_id` int(11) NOT NULL,
  `subject_period_id` int(11) NOT NULL,
  `quiz_time` int(11) NOT NULL,
  `max_score` int(11) NOT NULL,
  `max_attempt` int(11) NOT NULL DEFAULT 1,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `due_date` datetime DEFAULT NULL,
  `show_correct_answer` varchar(3) NOT NULL,
  `allow_late_submission` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_quiz_class`
--

INSERT INTO `subject_period_quiz_class` (`subject_period_quiz_class_id`, `subject_period_quiz_id`, `subject_period_id`, `quiz_time`, `max_score`, `max_attempt`, `date_creation`, `due_date`, `show_correct_answer`, `allow_late_submission`) VALUES
(6, 6, 2, 15000, 6, 5, '2023-02-10 19:11:05', '2023-02-10 19:11:05', 'yes', 'yes'),
(8, 8, 2, 7, 4, 2, '2023-02-13 19:23:45', '2023-02-14 19:23:45', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_quiz_question`
--

CREATE TABLE `subject_period_quiz_question` (
  `subject_period_quiz_question_id` int(11) NOT NULL,
  `subject_period_quiz_id` int(11) NOT NULL,
  `question_type_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_answer` varchar(150) NOT NULL,
  `points` int(11) NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_quiz_question`
--

INSERT INTO `subject_period_quiz_question` (`subject_period_quiz_question_id`, `subject_period_quiz_id`, `question_type_id`, `question_text`, `question_answer`, `points`, `date_creation`) VALUES
(28, 6, 1, '<p>Multiple</p>', 'D', 0, '2023-02-10 19:09:46'),
(29, 6, 2, '<p>True or False</p>', 'False', 0, '2023-02-10 19:10:13'),
(31, 6, 2, '<p>This is False</p>', 'False', 0, '2023-02-12 12:42:57'),
(32, 8, 1, '<p>Hard Work</p>', 'A', 0, '2023-02-13 19:23:19'),
(33, 8, 2, '<p>Practice</p>', 'True', 0, '2023-02-13 19:23:31'),
(34, 33, 1, '<p>Sample Question</p>', 'A', 0, '2023-02-23 11:54:20'),
(35, 33, 2, '<p>Sample Question 2<br></p>', 'True', 0, '2023-02-23 11:54:31');

-- --------------------------------------------------------

--
-- Table structure for table `subject_period_quiz_question_answer`
--

CREATE TABLE `subject_period_quiz_question_answer` (
  `subject_period_quiz_question_answer_id` int(11) NOT NULL,
  `answer_text` varchar(150) NOT NULL,
  `choices` varchar(3) NOT NULL,
  `subject_period_quiz_question_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `subject_period_quiz_question_answer`
--

INSERT INTO `subject_period_quiz_question_answer` (`subject_period_quiz_question_answer_id`, `answer_text`, `choices`, `subject_period_quiz_question_id`) VALUES
(41, 'Alpha', 'A', 28),
(42, 'Beta', 'B', 28),
(43, 'Care', 'C', 28),
(44, 'Duo', 'D', 28),
(45, 'Study', 'A', 32),
(46, 'Always', 'B', 32),
(47, 'Be', 'C', 32),
(48, 'Harder', 'D', 32),
(49, 'AI', 'A', 34),
(50, 'BI', 'B', 34),
(51, 'CI', 'C', 34),
(52, 'DI', 'D', 34);

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  `profilePic` varchar(250) NOT NULL,
  `teacher_status` varchar(10) NOT NULL,
  `teacher_stat` varchar(10) NOT NULL,
  `dateCreation` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`teacher_id`, `username`, `password`, `firstname`, `lastname`, `department_id`, `profilePic`, `teacher_status`, `teacher_stat`, `dateCreation`) VALUES
(1, '200', '123456', 'Lexter', 'Santos', 1, 'assets/images/profilePictures/default.png', 'active', '', NULL),
(2, '201', '123456', 'Kian', 'Regado', 1, 'assets/images/profilePictures/default-male.png', 'active', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_course`
--

CREATE TABLE `teacher_course` (
  `teacher_course_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `thumbnail` varchar(150) NOT NULL,
  `school_year` varchar(10) NOT NULL,
  `school_year_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher_course`
--

INSERT INTO `teacher_course` (`teacher_course_id`, `course_id`, `teacher_id`, `subject_id`, `thumbnail`, `school_year`, `school_year_id`) VALUES
(6, 1, 1, 5, 'assets/images/profilePictures/default.png', '2022-2023', 3),
(7, 2, 1, 6, 'assets/images/profilePictures/default-male.png', '2022-2023', 3),
(8, 1, 2, 6, 'assets/images/profilePictures/default-female.png', '2022-2023', 3),
(9, 2, 2, 6, 'assets/images/profilePictures/default.png', '2022-2023', 3),
(16, 1, 1, 5, 'assets/images/teacher_course_thumbnail/th6 base.jpg', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_course_student`
--

CREATE TABLE `teacher_course_student` (
  `teacher_course_student_id` int(11) NOT NULL,
  `teacher_course_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `deleted` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teacher_course_student`
--

INSERT INTO `teacher_course_student` (`teacher_course_student_id`, `teacher_course_id`, `student_id`, `teacher_id`, `deleted`) VALUES
(1, 6, 3, 1, ''),
(6, 6, 2, 1, ''),
(7, 7, 4, 1, ''),
(8, 7, 5, 1, ''),
(10, 8, 2, 2, ''),
(11, 9, 2, 2, ''),
(12, 7, 9, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstName` varchar(100) NOT NULL,
  `lastName` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstName`, `lastName`, `username`, `password`) VALUES
(1, 'admin', 'admin', 'admin', '123456');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `group_chat`
--
ALTER TABLE `group_chat`
  ADD PRIMARY KEY (`group_chat_id`);

--
-- Indexes for table `group_chat_member`
--
ALTER TABLE `group_chat_member`
  ADD PRIMARY KEY (`group_chat_member_id`),
  ADD KEY `group_chat_id_member` (`group_chat_id`);

--
-- Indexes for table `group_message`
--
ALTER TABLE `group_message`
  ADD PRIMARY KEY (`group_message_id`),
  ADD KEY `group_chat_id` (`group_chat_id`);

--
-- Indexes for table `handout_viewed`
--
ALTER TABLE `handout_viewed`
  ADD PRIMARY KEY (`handout_viewed_id`);

--
-- Indexes for table `message_teacher`
--
ALTER TABLE `message_teacher`
  ADD PRIMARY KEY (`message_teacher_id`);

--
-- Indexes for table `question_type`
--
ALTER TABLE `question_type`
  ADD PRIMARY KEY (`question_type_id`);

--
-- Indexes for table `school_year`
--
ALTER TABLE `school_year`
  ADD PRIMARY KEY (`school_year_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_period_assignment`
--
ALTER TABLE `student_period_assignment`
  ADD PRIMARY KEY (`student_period_assignment_id`);

--
-- Indexes for table `student_period_assignment_file`
--
ALTER TABLE `student_period_assignment_file`
  ADD PRIMARY KEY (`student_period_assignment_file_id`);

--
-- Indexes for table `student_period_assignment_multi_question_answer`
--
ALTER TABLE `student_period_assignment_multi_question_answer`
  ADD PRIMARY KEY (`student_period_assignment_multi_question_answer_id`);

--
-- Indexes for table `student_period_assignment_quiz`
--
ALTER TABLE `student_period_assignment_quiz`
  ADD PRIMARY KEY (`student_period_assignment_quiz_id`);

--
-- Indexes for table `student_period_assignment_quiz_question_answer`
--
ALTER TABLE `student_period_assignment_quiz_question_answer`
  ADD PRIMARY KEY (`student_period_assignment_quiz_question_answer_id`);

--
-- Indexes for table `student_period_quiz`
--
ALTER TABLE `student_period_quiz`
  ADD PRIMARY KEY (`student_period_quiz_id`);

--
-- Indexes for table `student_period_quiz_multi_question_answer`
--
ALTER TABLE `student_period_quiz_multi_question_answer`
  ADD PRIMARY KEY (`student_period_quiz_multi_question_answer_id`);

--
-- Indexes for table `student_period_quiz_question_answer`
--
ALTER TABLE `student_period_quiz_question_answer`
  ADD PRIMARY KEY (`student_period_quiz_question_answer_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `subject_period`
--
ALTER TABLE `subject_period`
  ADD PRIMARY KEY (`subject_period_id`);

--
-- Indexes for table `subject_period_assignment`
--
ALTER TABLE `subject_period_assignment`
  ADD PRIMARY KEY (`subject_period_assignment_id`);

--
-- Indexes for table `subject_period_assignment_handout`
--
ALTER TABLE `subject_period_assignment_handout`
  ADD PRIMARY KEY (`subject_period_assignment_handout_id`);

--
-- Indexes for table `subject_period_assignment_handout_file`
--
ALTER TABLE `subject_period_assignment_handout_file`
  ADD PRIMARY KEY (`subject_period_assignment_handout_file_id`);

--
-- Indexes for table `subject_period_assignment_quiz_class`
--
ALTER TABLE `subject_period_assignment_quiz_class`
  ADD PRIMARY KEY (`subject_period_assignment_quiz_class_id`);

--
-- Indexes for table `subject_period_assignment_quiz_question`
--
ALTER TABLE `subject_period_assignment_quiz_question`
  ADD PRIMARY KEY (`subject_period_assignment_quiz_question_id`);

--
-- Indexes for table `subject_period_assignment_quiz_question_answer`
--
ALTER TABLE `subject_period_assignment_quiz_question_answer`
  ADD PRIMARY KEY (`subject_period_assignment_quiz_question_answer_id`);

--
-- Indexes for table `subject_period_quiz`
--
ALTER TABLE `subject_period_quiz`
  ADD PRIMARY KEY (`subject_period_quiz_id`);

--
-- Indexes for table `subject_period_quiz_class`
--
ALTER TABLE `subject_period_quiz_class`
  ADD PRIMARY KEY (`subject_period_quiz_class_id`);

--
-- Indexes for table `subject_period_quiz_question`
--
ALTER TABLE `subject_period_quiz_question`
  ADD PRIMARY KEY (`subject_period_quiz_question_id`);

--
-- Indexes for table `subject_period_quiz_question_answer`
--
ALTER TABLE `subject_period_quiz_question_answer`
  ADD PRIMARY KEY (`subject_period_quiz_question_answer_id`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_course`
--
ALTER TABLE `teacher_course`
  ADD PRIMARY KEY (`teacher_course_id`);

--
-- Indexes for table `teacher_course_student`
--
ALTER TABLE `teacher_course_student`
  ADD PRIMARY KEY (`teacher_course_student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignment`
--
ALTER TABLE `assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `group_chat`
--
ALTER TABLE `group_chat`
  MODIFY `group_chat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `group_chat_member`
--
ALTER TABLE `group_chat_member`
  MODIFY `group_chat_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `group_message`
--
ALTER TABLE `group_message`
  MODIFY `group_message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `handout_viewed`
--
ALTER TABLE `handout_viewed`
  MODIFY `handout_viewed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `message_teacher`
--
ALTER TABLE `message_teacher`
  MODIFY `message_teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `question_type`
--
ALTER TABLE `question_type`
  MODIFY `question_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `school_year_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_period_assignment`
--
ALTER TABLE `student_period_assignment`
  MODIFY `student_period_assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `student_period_assignment_file`
--
ALTER TABLE `student_period_assignment_file`
  MODIFY `student_period_assignment_file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `student_period_assignment_multi_question_answer`
--
ALTER TABLE `student_period_assignment_multi_question_answer`
  MODIFY `student_period_assignment_multi_question_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `student_period_assignment_quiz`
--
ALTER TABLE `student_period_assignment_quiz`
  MODIFY `student_period_assignment_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `student_period_assignment_quiz_question_answer`
--
ALTER TABLE `student_period_assignment_quiz_question_answer`
  MODIFY `student_period_assignment_quiz_question_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `student_period_quiz`
--
ALTER TABLE `student_period_quiz`
  MODIFY `student_period_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `student_period_quiz_multi_question_answer`
--
ALTER TABLE `student_period_quiz_multi_question_answer`
  MODIFY `student_period_quiz_multi_question_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `student_period_quiz_question_answer`
--
ALTER TABLE `student_period_quiz_question_answer`
  MODIFY `student_period_quiz_question_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subject_period`
--
ALTER TABLE `subject_period`
  MODIFY `subject_period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `subject_period_assignment`
--
ALTER TABLE `subject_period_assignment`
  MODIFY `subject_period_assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `subject_period_assignment_handout`
--
ALTER TABLE `subject_period_assignment_handout`
  MODIFY `subject_period_assignment_handout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subject_period_assignment_handout_file`
--
ALTER TABLE `subject_period_assignment_handout_file`
  MODIFY `subject_period_assignment_handout_file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subject_period_assignment_quiz_class`
--
ALTER TABLE `subject_period_assignment_quiz_class`
  MODIFY `subject_period_assignment_quiz_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `subject_period_assignment_quiz_question`
--
ALTER TABLE `subject_period_assignment_quiz_question`
  MODIFY `subject_period_assignment_quiz_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `subject_period_assignment_quiz_question_answer`
--
ALTER TABLE `subject_period_assignment_quiz_question_answer`
  MODIFY `subject_period_assignment_quiz_question_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `subject_period_quiz`
--
ALTER TABLE `subject_period_quiz`
  MODIFY `subject_period_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `subject_period_quiz_class`
--
ALTER TABLE `subject_period_quiz_class`
  MODIFY `subject_period_quiz_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subject_period_quiz_question`
--
ALTER TABLE `subject_period_quiz_question`
  MODIFY `subject_period_quiz_question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `subject_period_quiz_question_answer`
--
ALTER TABLE `subject_period_quiz_question_answer`
  MODIFY `subject_period_quiz_question_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `teacher`
--
ALTER TABLE `teacher`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_course`
--
ALTER TABLE `teacher_course`
  MODIFY `teacher_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `teacher_course_student`
--
ALTER TABLE `teacher_course_student`
  MODIFY `teacher_course_student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
