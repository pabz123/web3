-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql113.infinityfree.com
-- Generation Time: Oct 23, 2025 at 12:41 PM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40185804_uniconnect_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('superadmin','moderator') DEFAULT 'moderator',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `role`, `createdAt`, `updatedAt`) VALUES
(1, 'admin1', '$2y$10$E8Sx...', 'admin@example.com', 'superadmin', '2025-10-16 19:04:46', '2025-10-16 19:04:46'),
(3, 'admin2', 'admin123', 'admin@gmail.com', 'moderator', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `status` enum('Applied','Reviewed','Interview','Hired','Rejected') DEFAULT 'Applied',
  `coverLetter` text DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `studentId` int(11) DEFAULT NULL,
  `jobId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employers`
--

CREATE TABLE `employers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `company_description` text DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `company_size` varchar(50) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employers`
--

INSERT INTO `employers` (`id`, `user_id`, `company_name`, `company_logo`, `company_description`, `company_website`, `company_address`, `company_size`, `industry`, `createdAt`, `updatedAt`) VALUES
(1, 8, 'Tech Innovations Ltd', NULL, 'A leading Ugandan software firm offering web, mobile, and enterprise solutions.', 'https://www.techinnovations.co.ug', 'Kampala, Uganda', NULL, 'Information Technology', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(2, 9, 'Bright Marketing Agency', NULL, 'We help brands in East Africa grow through creative campaigns and digital strategies.', 'https://www.brightmarketing.co.ug', 'Kampala, Uganda', NULL, 'Marketing & Communications', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(3, 10, 'DataSense Analytics', NULL, 'Data-driven insights for smarter business decisions in Uganda.', 'https://www.datasense.co.ug', 'Mukono, Uganda', NULL, 'Data & Analytics', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(4, 11, 'SupportPlus Uganda', NULL, 'Customer experience experts providing 24/7 BPO and call center solutions.', 'https://www.supportplus.co.ug', 'Jinja, Uganda', NULL, 'Customer Service', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(5, 12, 'HR Connect Uganda', NULL, 'Connecting Ugandan employers with top local and international talent.', 'https://www.hrconnect.co.ug', 'Kampala, Uganda', NULL, 'Human Resources', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(6, 13, 'CreativeWorks Studio', NULL, 'A design and branding studio helping startups tell their visual stories.', 'https://www.creativeworks.co.ug', 'Kampala, Uganda', NULL, 'Creative & Design', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(7, 14, 'Aid4All NGO', NULL, 'A humanitarian organization empowering rural communities through education and healthcare.', 'https://www.aid4all.org', 'Gulu, Uganda', NULL, 'NGO & Development', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(8, 15, 'FinanceCare Ltd', NULL, 'An accounting and consulting firm providing financial services across Uganda.', 'https://www.financecare.co.ug', 'Mbarara, Uganda', NULL, 'Finance & Accounting', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(9, 16, 'MarketReach Uganda', NULL, 'We empower businesses through strategic marketing, distribution, and customer engagement.', 'https://www.marketreach.co.ug', 'Mbale, Uganda', NULL, 'Sales & Marketing', '2025-10-22 02:45:36', '2025-10-23 05:57:03'),
(10, 17, 'CyberGuard Tech', NULL, 'Leading provider of cybersecurity and IT infrastructure protection solutions.', 'https://www.cyberguard.co.ug', 'Kampala, Uganda', NULL, 'Information Security', '2025-10-22 02:45:36', '2025-10-23 05:57:03');

-- --------------------------------------------------------

--
-- Table structure for table `employers_old`
--

CREATE TABLE `employers_old` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employers_old`
--

INSERT INTO `employers_old` (`id`, `company_name`, `email`, `password`, `description`, `industry`, `website_url`, `contact_person`, `location`, `createdAt`, `updatedAt`, `logo`) VALUES
(1, 'Tech Innovations Ltd', 'hr@techinnovations.co.ug', 'b6bc7b58510319a151d168ba3d5aecb3ac0a9708d06dd930f37fbc89b6cdc697', 'A leading Ugandan software firm offering web, mobile, and enterprise solutions.', 'Information Technology', 'https://www.techinnovations.co.ug', 'Grace Namutebi', 'Kampala, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/techinnovations.png'),
(2, 'Bright Marketing Agency', 'jobs@brightmarketing.co.ug', 'd9cf25623dc300ad184692ad6c653c6fb4854349eed9c8d2a71c7f3aef1f8182', 'We help brands in East Africa grow through creative campaigns and digital strategies.', 'Marketing & Communications', 'https://www.brightmarketing.co.ug', 'Ronald Kintu', 'Kampala, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/brightmarketing.png'),
(3, 'DataSense Analytics', 'careers@datasense.co.ug', '3efe6c09db81a05e7e85900b4354a2bf6341a1f9f2ed3773120f66d47f4943d6', 'Data-driven insights for smarter business decisions in Uganda.', 'Data & Analytics', 'https://www.datasense.co.ug', 'Pauline Atwine', 'Mukono, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/datasense.png'),
(4, 'SupportPlus Uganda', 'info@supportplus.co.ug', '61510d6c8de503138f3a6d491ae44f53f0c44d41a43cc2986b491fc88dbcd5f1', 'Customer experience experts providing 24/7 BPO and call center solutions.', 'Customer Service', 'https://www.supportplus.co.ug', 'Brian Mugisha', 'Jinja, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/supportplus.png'),
(5, 'HR Connect Uganda', 'recruit@hrconnect.co.ug', 'd287cbc008cd85a8ba15d6bba6520af435aba3e3cd85cbd0fc4a81911a444f4b', 'Connecting Ugandan employers with top local and international talent.', 'Human Resources', 'https://www.hrconnect.co.ug', 'Rita Nansubuga', 'Kampala, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/hrconnect.png'),
(6, 'CreativeWorks Studio', 'jobs@creativeworks.co.ug', '93194cab2b5067f906d5a9f1934b49b28a58c2c588f8c1b27267482e69a21707', 'A design and branding studio helping startups tell their visual stories.', 'Creative & Design', 'https://www.creativeworks.co.ug', 'Derrick Lubega', 'Kampala, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/creativeworks.png'),
(7, 'Aid4All NGO', 'careers@aid4all.org', 'f0567e78bd04f2e9835ad13e428261f95e0c679d0f5c6de72ab070328438d5e2', 'A humanitarian organization empowering rural communities through education and healthcare.', 'NGO & Development', 'https://www.aid4all.org', 'Sarah Kyaligonza', 'Gulu, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/aid4all.png'),
(8, 'FinanceCare Ltd', 'apply@financecare.co.ug', 'eedf2ec87d603c7d5ce728a717d34f249f8e03457bd3f2f787c06e804c0872c2', 'An accounting and consulting firm providing financial services across Uganda.', 'Finance & Accounting', 'https://www.financecare.co.ug', 'Isaac Tumwine', 'Mbarara, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/financecare.png'),
(9, 'MarketReach Uganda', 'sales@marketreach.co.ug', '3b6b865cb1a8983484a6fdb37836c7a5f0ac2d30c925364b8c9f84df5195a236', 'We empower businesses through strategic marketing, distribution, and customer engagement.', 'Sales & Marketing', 'https://www.marketreach.co.ug', 'Lydia Nabbosa', 'Mbale, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/marketreach.png'),
(10, 'CyberGuard Tech', 'security@cyberguard.co.ug', 'd6b8de3cdcad3265e87c74c57073d91d07f35402aaa9fa69b80294a581a4b225', 'Leading provider of cybersecurity and IT infrastructure protection solutions.', 'Information Security', 'https://www.cyberguard.co.ug', 'Peter Ocen', 'Kampala, Uganda', '2025-10-22 02:45:36', '2025-10-22 02:45:36', 'logos/cyberguard.png');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `type` enum('Internship','Full-time','Part-time','Contract') NOT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `application_deadline` datetime DEFAULT NULL,
  `application_method` enum('Direct','External') DEFAULT 'Direct',
  `external_link` varchar(255) DEFAULT NULL,
  `status` enum('Open','Closed') DEFAULT 'Open',
  `employer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `description`, `location`, `type`, `createdAt`, `updatedAt`, `industry`, `responsibilities`, `requirements`, `application_deadline`, `application_method`, `external_link`, `status`, `employer_id`) VALUES
(1, 'Software Engineer', 'Develop scalable web applications and APIs for financial services.', 'Kampala, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Information Technology', 'Write clean, efficient code; collaborate with cross-functional teams.', 'Bachelor\'s degree in Computer Science; 2+ years in PHP/JS frameworks.', '2025-11-30 00:00:00', 'Direct', NULL, 'Open', 1),
(2, 'Marketing Intern', 'Assist in social media campaigns, branding, and customer engagement.', 'Kampala, Uganda', 'Internship', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Marketing & Communications', 'Manage social media pages; create digital posters.', 'Diploma or Degree in Marketing, active social media skills.', '2025-11-15 00:00:00', 'Direct', NULL, 'Open', 2),
(3, 'Data Analyst', 'Analyze company datasets to generate insights for decision-making.', 'Mukono, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Data & Analytics', 'Data cleaning, visualization, and reporting.', 'Proficiency in SQL and Excel; knowledge of Power BI or Tableau.', '2025-12-10 00:00:00', 'External', 'https://www.companyug.com/jobs/data-analyst', 'Open', 3),
(4, 'Customer Support Officer', 'Provide first-line support to customers and resolve issues efficiently.', 'Jinja, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Customer Service', 'Handle calls, chat, and email support.', 'Good communication skills and a customer-oriented attitude.', '2025-11-20 00:00:00', 'Direct', NULL, 'Open', 4),
(5, 'HR Assistant', 'Support HR functions including recruitment, onboarding, and payroll.', 'Kampala, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Human Resources', 'Assist in interviews and maintain employee records.', 'Bachelor\'s in HRM or Business Administration.', '2025-11-30 00:00:00', 'Direct', NULL, 'Open', 5),
(6, 'Graphic Designer', 'Design digital and print materials for campaigns.', 'Kampala, Uganda', 'Part-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Creative & Design', 'Create posters, infographics, and web banners.', 'Proficient in Adobe Photoshop or Canva.', '2025-12-01 00:00:00', 'Direct', NULL, 'Open', 6),
(7, 'Project Coordinator', 'Coordinate field and administrative project activities for the NGO.', 'Gulu, Uganda', 'Contract', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'NGO & Development', 'Supervise field staff, report writing, and stakeholder engagement.', 'Bachelor\'s in Project Management or related field.', '2025-11-25 00:00:00', 'Direct', NULL, 'Open', 7),
(8, 'Finance Officer', 'Manage budgets, transactions, and financial reports.', 'Mbarara, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Finance & Accounting', 'Prepare monthly financial statements.', 'ACCA Level 2 or equivalent experience.', '2025-12-05 00:00:00', 'Direct', NULL, 'Open', 8),
(9, 'Field Sales Representative', 'Promote and sell company products across districts.', 'Mbale, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Sales & Marketing', 'Meet monthly sales targets; market research.', 'Minimum Diploma in Business or Marketing.', '2025-11-18 00:00:00', 'Direct', NULL, 'Open', 9),
(10, 'Cybersecurity Analyst', 'Protect company systems and data from security breaches.', 'Kampala, Uganda', 'Full-time', '2025-10-22 02:46:56', '2025-10-22 02:46:56', 'Information Security', 'Monitor threats, perform risk assessments.', 'Degree in IT or Cybersecurity, knowledge of firewalls.', '2025-12-15 00:00:00', 'External', 'https://jobs.techug.com/cybersecurity-analyst', 'Open', 10);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `graduation_year` int(11) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `portfolio_links` text DEFAULT NULL,
  `job_preferences` text DEFAULT NULL,
  `profile_visibility` tinyint(1) DEFAULT 1,
  `cv_file` varchar(255) DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `profilePic` varchar(255) DEFAULT NULL,
  `theme` enum('light','dark') DEFAULT 'dark'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `full_name`, `email`, `password`, `profile_image`, `contact`, `location`, `university`, `course`, `graduation_year`, `gpa`, `skills`, `experience`, `portfolio_links`, `job_preferences`, `profile_visibility`, `cv_file`, `createdAt`, `updatedAt`, `profilePic`, `theme`) VALUES
(8, 'precious pabz', 'precious pabz', 'mulungipabire11@gmail.com', '$2y$10$7.7R9hlJ1hGGemhZq9RSEOkEC6BP4wVktVSsftnB/SezB7YbbNBr6', '/uploads/profile/default-avatar.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-10-22 02:52:11', '2025-10-22 02:52:11', NULL, 'dark'),
(9, 'Wasswa Emmanuel', 'Wasswa Emmanuel', 'emmawas03@gmail.com', '$2y$10$ytb/ZuYR18ScCI09XoKOouWjuO6WQxI8SPIibzL7Ziv5iPg1SAY8u', '/uploads/profile/default-avatar.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-10-22 10:38:33', '2025-10-22 10:38:33', NULL, 'dark'),
(10, 'marthhaa', NULL, 'martha@gmail.com', '$2y$10$veUvfiw3p1tS4MXeNy5GTOmdP0ug15rEoKCTfIFg7h.3/EtfPzuJC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-10-23 09:26:27', '2025-10-23 09:26:27', NULL, 'dark');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fullName` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `education` varchar(50) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `profilePic` varchar(255) DEFAULT NULL,
  `cvFile` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `email`, `fullName`, `phone`, `education`, `skills`, `profilePic`, `cvFile`, `created_at`) VALUES
(1, 'emmawas03@gmail.com', 'WASSWA EMMANUEL', '0742476269', 'Undergraduate', 'js', '/uploads/profile/profile-7-1761233114.jpg', '5a271133a5f811a01e082e3d87c7f0c8', '2025-10-14 09:57:21'),
(2, 'mulungipabire11@gmail.com', 'Mulungi Precious Pabire', '0774081112', 'Undergraduate', 'coding', '/uploads/profile/profile-6-1761221602.jpg', '/uploads/cv/cv-6-1761221602.pdf', '2025-10-23 12:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_type` enum('profile','cv','logo') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','employer') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_type` varchar(255) DEFAULT NULL,
  `industry` varchar(255) DEFAULT NULL,
  `company_description` text DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `theme` enum('light','dark') DEFAULT 'dark',
  `remember_token` varchar(255) DEFAULT NULL,
  `remember_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `phone`, `password`, `role`, `company_name`, `company_type`, `industry`, `company_description`, `createdAt`, `updatedAt`, `profile_image`, `theme`, `remember_token`, `remember_expires_at`) VALUES
(1, '2024bcs225', '2024bcs225', 'ewasswa03@gnail', NULL, '$2y$10$G14HDKa.w1mUTsMnX9KgQe1QAPlPoHC6O8PAxACy.6cvDECeR8FrW', 'student', NULL, NULL, NULL, NULL, '2025-10-15 22:25:56', '2025-10-15 22:25:56', '/uploads/profile/default-avatar.png', 'dark', NULL, NULL),
(2, '', '', 'ssemuli02@gmail.com', NULL, '$2y$10$6PuVv0xWvBTKahxXWo4bq.m04FA7pjtZ0R0oKWjutd2i5WYVy1CZq', 'student', NULL, NULL, NULL, NULL, '2025-10-16 16:58:52', '2025-10-16 16:58:52', '/uploads/profile/default-avatar.png', 'dark', NULL, NULL),
(3, '', '', 'arafat@gmail.com', NULL, '$2y$10$AiVVlqDgh/5NeC1pNESZa.Ogi9S7GpZOztP5cWSi9Zh7kS7GehHrW', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 00:50:13', '2025-10-22 00:50:13', '/uploads/profile/default-avatar.png', 'dark', NULL, NULL),
(4, '', '', 'ainembabazidaphineainembabazi002@gmail.com', NULL, '$2y$10$hXQ0ZD6At9TVWChZBjd0hO4eEHSa5S4jwZ0Wt2itPTQAa7G/z/iCm', 'student', NULL, NULL, NULL, NULL, '2025-10-22 02:29:57', '2025-10-22 02:29:57', '/uploads/profile/default-avatar.png', 'dark', NULL, NULL),
(5, '', '', 'ainembabazidaphineainembabazi001@gmail.com', NULL, '$2y$10$/WiYJgIts10qkiFK7XwpEe9eEASRTxjISeHqIs21r3PNciyFOyNSu', 'student', NULL, NULL, NULL, NULL, '2025-10-22 02:31:22', '2025-10-22 02:31:22', '/uploads/profile/default-avatar.png', 'dark', NULL, NULL),
(6, '', 'Mulungi Precious Pabire', 'mulungipabire11@gmail.com', '0774081112', '$2y$10$7.7R9hlJ1hGGemhZq9RSEOkEC6BP4wVktVSsftnB/SezB7YbbNBr6', 'student', NULL, NULL, NULL, NULL, '2025-10-22 02:52:11', '2025-10-23 05:13:22', '/uploads/profile/profile-6-1761221602.jpg', 'dark', NULL, NULL),
(7, '', 'WASSWA EMMANUEL', 'emmawas03@gmail.com', '0742476269', '$2y$10$ytb/ZuYR18ScCI09XoKOouWjuO6WQxI8SPIibzL7Ziv5iPg1SAY8u', 'student', NULL, NULL, NULL, NULL, '2025-10-22 10:38:33', '2025-10-23 08:25:14', '/uploads/profile/profile-7-1761233114.jpg', 'dark', NULL, NULL),
(8, '', 'Grace Namutebi', 'hr@techinnovations.co.ug', NULL, 'b6bc7b58510319a151d168ba3d5aecb3ac0a9708d06dd930f37fbc89b6cdc697', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(9, '', 'Ronald Kintu', 'jobs@brightmarketing.co.ug', NULL, 'd9cf25623dc300ad184692ad6c653c6fb4854349eed9c8d2a71c7f3aef1f8182', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(10, '', 'Pauline Atwine', 'careers@datasense.co.ug', NULL, '3efe6c09db81a05e7e85900b4354a2bf6341a1f9f2ed3773120f66d47f4943d6', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(11, '', 'Brian Mugisha', 'info@supportplus.co.ug', NULL, '61510d6c8de503138f3a6d491ae44f53f0c44d41a43cc2986b491fc88dbcd5f1', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(12, '', 'Rita Nansubuga', 'recruit@hrconnect.co.ug', NULL, 'd287cbc008cd85a8ba15d6bba6520af435aba3e3cd85cbd0fc4a81911a444f4b', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(13, '', 'Derrick Lubega', 'jobs@creativeworks.co.ug', NULL, '93194cab2b5067f906d5a9f1934b49b28a58c2c588f8c1b27267482e69a21707', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(14, '', 'Sarah Kyaligonza', 'careers@aid4all.org', NULL, 'f0567e78bd04f2e9835ad13e428261f95e0c679d0f5c6de72ab070328438d5e2', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(15, '', 'Isaac Tumwine', 'apply@financecare.co.ug', NULL, 'eedf2ec87d603c7d5ce728a717d34f249f8e03457bd3f2f787c06e804c0872c2', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(16, '', 'Lydia Nabbosa', 'sales@marketreach.co.ug', NULL, '3b6b865cb1a8983484a6fdb37836c7a5f0ac2d30c925364b8c9f84df5195a236', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(17, '', 'Peter Ocen', 'security@cyberguard.co.ug', NULL, 'd6b8de3cdcad3265e87c74c57073d91d07f35402aaa9fa69b80294a581a4b225', 'employer', NULL, NULL, NULL, NULL, '2025-10-22 02:45:36', '2025-10-22 02:45:36', NULL, 'dark', NULL, NULL),
(23, '', NULL, 'martha@gmail.com', NULL, '$2y$10$veUvfiw3p1tS4MXeNy5GTOmdP0ug15rEoKCTfIFg7h.3/EtfPzuJC', 'student', NULL, NULL, NULL, NULL, '2025-10-23 09:26:27', '2025-10-23 09:26:27', NULL, 'dark', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `username_3` (`username`),
  ADD UNIQUE KEY `username_4` (`username`),
  ADD UNIQUE KEY `username_5` (`username`),
  ADD UNIQUE KEY `username_6` (`username`),
  ADD UNIQUE KEY `username_7` (`username`),
  ADD UNIQUE KEY `username_8` (`username`),
  ADD UNIQUE KEY `username_9` (`username`),
  ADD UNIQUE KEY `username_10` (`username`),
  ADD UNIQUE KEY `username_11` (`username`),
  ADD UNIQUE KEY `username_12` (`username`),
  ADD UNIQUE KEY `username_13` (`username`),
  ADD UNIQUE KEY `username_14` (`username`),
  ADD UNIQUE KEY `username_15` (`username`),
  ADD UNIQUE KEY `username_16` (`username`),
  ADD UNIQUE KEY `username_17` (`username`),
  ADD UNIQUE KEY `username_18` (`username`),
  ADD UNIQUE KEY `username_19` (`username`),
  ADD UNIQUE KEY `username_20` (`username`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `studentId` (`studentId`),
  ADD KEY `jobId` (`jobId`);

--
-- Indexes for table `employers`
--
ALTER TABLE `employers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `employers_old`
--
ALTER TABLE `employers_old`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`),
  ADD UNIQUE KEY `email_4` (`email`),
  ADD UNIQUE KEY `email_5` (`email`),
  ADD UNIQUE KEY `email_6` (`email`),
  ADD UNIQUE KEY `email_7` (`email`),
  ADD UNIQUE KEY `email_8` (`email`),
  ADD UNIQUE KEY `email_9` (`email`),
  ADD UNIQUE KEY `email_10` (`email`),
  ADD UNIQUE KEY `email_11` (`email`),
  ADD UNIQUE KEY `email_12` (`email`),
  ADD UNIQUE KEY `email_13` (`email`),
  ADD UNIQUE KEY `email_14` (`email`),
  ADD UNIQUE KEY `email_15` (`email`),
  ADD UNIQUE KEY `email_16` (`email`),
  ADD UNIQUE KEY `email_17` (`email`),
  ADD UNIQUE KEY `email_18` (`email`),
  ADD UNIQUE KEY `email_19` (`email`),
  ADD UNIQUE KEY `email_20` (`email`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`),
  ADD KEY `idx_employer_id` (`employer_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`),
  ADD UNIQUE KEY `email_4` (`email`),
  ADD UNIQUE KEY `email_5` (`email`),
  ADD UNIQUE KEY `email_6` (`email`),
  ADD UNIQUE KEY `email_7` (`email`),
  ADD UNIQUE KEY `email_8` (`email`),
  ADD UNIQUE KEY `email_9` (`email`),
  ADD UNIQUE KEY `email_10` (`email`),
  ADD UNIQUE KEY `email_11` (`email`),
  ADD UNIQUE KEY `email_12` (`email`),
  ADD UNIQUE KEY `email_13` (`email`),
  ADD UNIQUE KEY `email_14` (`email`),
  ADD UNIQUE KEY `email_15` (`email`),
  ADD UNIQUE KEY `email_16` (`email`),
  ADD UNIQUE KEY `email_17` (`email`),
  ADD UNIQUE KEY `email_18` (`email`),
  ADD UNIQUE KEY `email_19` (`email`),
  ADD UNIQUE KEY `email_20` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD UNIQUE KEY `email_3` (`email`),
  ADD UNIQUE KEY `email_4` (`email`),
  ADD UNIQUE KEY `email_5` (`email`),
  ADD UNIQUE KEY `email_6` (`email`),
  ADD UNIQUE KEY `email_7` (`email`),
  ADD UNIQUE KEY `email_8` (`email`),
  ADD UNIQUE KEY `email_9` (`email`),
  ADD UNIQUE KEY `email_10` (`email`),
  ADD UNIQUE KEY `email_11` (`email`),
  ADD UNIQUE KEY `email_12` (`email`),
  ADD UNIQUE KEY `email_13` (`email`),
  ADD UNIQUE KEY `email_14` (`email`),
  ADD UNIQUE KEY `email_15` (`email`),
  ADD UNIQUE KEY `email_16` (`email`),
  ADD UNIQUE KEY `email_17` (`email`),
  ADD UNIQUE KEY `email_18` (`email`),
  ADD UNIQUE KEY `email_19` (`email`),
  ADD UNIQUE KEY `email_20` (`email`),
  ADD UNIQUE KEY `email_21` (`email`),
  ADD UNIQUE KEY `email_22` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_theme` (`theme`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employers`
--
ALTER TABLE `employers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employers_old`
--
ALTER TABLE `employers_old`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`studentId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_10` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_11` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_12` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_13` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_14` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_15` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_16` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_17` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_18` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_19` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_20` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_21` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_22` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_23` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_24` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_25` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_26` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_27` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_28` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_29` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`studentId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_30` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_31` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_32` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_33` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_34` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_35` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_36` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_37` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_38` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_39` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_4` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_40` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_41` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_42` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_43` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_44` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_5` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_6` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_7` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_8` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_9` FOREIGN KEY (`studentId`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employers`
--
ALTER TABLE `employers`
  ADD CONSTRAINT `employers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_jobs_employer` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_10` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_11` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_12` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_13` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_14` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_15` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_16` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_17` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_18` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_19` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_3` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_4` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_5` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_6` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_7` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_8` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_9` FOREIGN KEY (`employer_id`) REFERENCES `employers_old` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
