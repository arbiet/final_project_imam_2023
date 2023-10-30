-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2023 at 02:38 PM
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
-- Database: `login_system_php`
--

-- --------------------------------------------------------

--
-- Table structure for table `logactivity`
--

CREATE TABLE `logactivity` (
  `LogID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `ActivityDescription` text DEFAULT NULL,
  `ActivityTimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logactivity`
--

INSERT INTO `logactivity` (`LogID`, `UserID`, `ActivityDescription`, `ActivityTimestamp`) VALUES
(1, 0, 'User logged in', '2023-10-29 12:35:16'),
(2, 0, 'User logged out', '2023-10-29 12:38:21'),
(3, 0, 'User logged in', '2023-10-29 12:38:44'),
(4, 0, 'User logged out', '2023-10-29 12:47:05'),
(5, 0, 'User logged in', '2023-10-29 12:47:14'),
(6, 0, 'User logged out', '2023-10-29 12:48:24'),
(7, 0, 'User logged in', '2023-10-29 12:48:36'),
(8, 0, 'User logged out', '2023-10-29 13:04:46'),
(9, 0, 'User logged in', '2023-10-29 13:04:55'),
(10, 0, 'Changed profile picture from 653e58a6edc33.png to 653e5a409b4fb.jpeg', '2023-10-29 13:12:32'),
(11, 0, 'User updated profile. Changes: Full Name, Email, Username, Date of Birth, Gender, Address, Phone Number', '2023-10-29 13:17:59'),
(12, 0, 'User updated profile. Changes: ', '2023-10-29 13:18:51'),
(13, 0, 'User updated profile. Changes: Address', '2023-10-29 13:19:00'),
(14, 0, 'User updated profile. Changes: Address', '2023-10-29 13:19:10'),
(15, 0, 'User logged out', '2023-10-29 13:26:42'),
(16, 137648118, 'User logged in', '2023-10-29 13:36:58');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `RoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`RoleID`, `RoleName`) VALUES
(1, 'Admin'),
(3, 'Student'),
(2, 'Teacher');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `RoleID` int(11) DEFAULT NULL,
  `AccountCreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `LastLogin` datetime DEFAULT NULL,
  `AccountStatus` varchar(20) DEFAULT NULL,
  `ProfilePictureURL` varchar(255) DEFAULT NULL,
  `ActivationStatus` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Password`, `Email`, `FullName`, `DateOfBirth`, `Gender`, `Address`, `PhoneNumber`, `RoleID`, `AccountCreationDate`, `LastLogin`, `AccountStatus`, `ProfilePictureURL`, `ActivationStatus`) VALUES
(0, 'ikimukti', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', '19103020046@unpkediri.ac.id', 'Firmansyah Mukti Wijaya', '2023-10-12', 'Male', 'Nglaban 1111', '081216318022', 3, '2023-10-29 13:19:10', '2023-10-29 20:04:55', NULL, '653e5a409b4fb.jpeg', NULL),
(137648118, 'admin', 'ef797c8118f02dfb649607dd5d3f8c7623048c9c063d532cc95c5ed7a898a64f', 'admin@ikimukti.com', 'Administrator', NULL, NULL, NULL, NULL, 1, '2023-10-29 13:36:58', '2023-10-29 20:36:58', NULL, 'default.png', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logactivity`
--
ALTER TABLE `logactivity`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`RoleID`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `users_ibfk_1` (`RoleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logactivity`
--
ALTER TABLE `logactivity`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `logactivity`
--
ALTER TABLE `logactivity`
  ADD CONSTRAINT `logactivity_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `role` (`RoleID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
