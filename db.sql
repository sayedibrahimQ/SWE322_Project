-- blood_donation_db.sql
-- This script creates the entire database structure for the Blood Donation Management System.

-- Set SQL mode and disable foreign key checks for a clean import
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS=0;

--
-- Database: `blood_donation_db`
--
DROP DATABASE IF EXISTS `blood_donation_db`;
CREATE DATABASE IF NOT EXISTS `blood_donation_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `blood_donation_db`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--
INSERT INTO `admins` (`username`, `email`, `password`) VALUES
('admin', 'admin@bloodbank.com', '$2y$10$I0a.8y.D1d.f5h6j/yK.a.UeG0C9k.eN7Z3b.X8w.I3p.N1o/xK.y');
-- The default password is 'password123'

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--
CREATE TABLE `donors` (
  `donor_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `blood_type` varchar(5) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `reg_date` datetime NOT NULL,
  PRIMARY KEY (`donor_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--
CREATE TABLE `hospitals` (
  `hospital_id` int(11) NOT NULL AUTO_INCREMENT,
  `hospital_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `reg_date` datetime NOT NULL,
  PRIMARY KEY (`hospital_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `blood_drives`
--
CREATE TABLE `blood_drives` (
  `drive_id` int(11) NOT NULL AUTO_INCREMENT,
  `hospital_id` int(11) NOT NULL,
  `drive_name` varchar(255) NOT NULL,
  `location_address` text NOT NULL,
  `start_time` datetime NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`drive_id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--
CREATE TABLE `blood_requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `hospital_id` int(11) NOT NULL,
  `blood_type_needed` varchar(5) NOT NULL,
  `urgency_level` varchar(50) NOT NULL,
  `date_posted` datetime NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  PRIMARY KEY (`request_id`),
  KEY `hospital_id` (`hospital_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `donor_responses`
--
CREATE TABLE `donor_responses` (
  `response_id` int(11) NOT NULL AUTO_INCREMENT,
  `donor_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `response_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`response_id`),
  UNIQUE KEY `donor_request_unique` (`donor_id`,`request_id`),
  KEY `request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--
CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL AUTO_INCREMENT,
  `donor_id` int(11) NOT NULL,
  `drive_id` int(11) NOT NULL,
  `registration_date` datetime NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'registered',
  PRIMARY KEY (`registration_id`),
  UNIQUE KEY `donor_drive_unique` (`donor_id`,`drive_id`),
  KEY `drive_id` (`drive_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Add Foreign Key Constraints
--

ALTER TABLE `blood_drives`
  ADD CONSTRAINT `fk_hospital_drive` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `blood_requests`
  ADD CONSTRAINT `fk_hospital_request` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`hospital_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `donor_responses`
  ADD CONSTRAINT `fk_resp_donor` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_resp_request` FOREIGN KEY (`request_id`) REFERENCES `blood_requests` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `registrations`
  ADD CONSTRAINT `fk_reg_donor` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`donor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reg_drive` FOREIGN KEY (`drive_id`) REFERENCES `blood_drives` (`drive_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;