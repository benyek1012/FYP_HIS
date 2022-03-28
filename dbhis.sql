-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2022 at 09:03 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbhis`
--
CREATE DATABASE IF NOT EXISTS `dbhis` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `dbhis`;

-- --------------------------------------------------------

--
-- Table structure for table `patient_admission`
--

CREATE TABLE `patient_admission` (
  `rn` varchar(11) NOT NULL,
  `entry_datetime` datetime NOT NULL,
  `patient_uid` varchar(64) NOT NULL,
  `initial_ward_code` varchar(20) NOT NULL,
  `initial_ward_class` varchar(20) NOT NULL,
  `reference` varchar(200) DEFAULT NULL,
  `medigal_legal_code` tinyint(1) DEFAULT 0,
  `reminder_given` int(11) NOT NULL DEFAULT 0,
  `guarantor_name` varchar(200) DEFAULT NULL,
  `guarantor_nric` varchar(20) DEFAULT NULL,
  `guarantor_phone_number` varchar(100) DEFAULT NULL,
  `guarantor_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patient_information`
--

CREATE TABLE `patient_information` (
  `patient_uid` varchar(64) NOT NULL,
  `first_reg_date` date NOT NULL,
  `nric` varchar(20) DEFAULT NULL,
  `nationality` varchar(20) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `job` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `patient_next_of_kin`
--

CREATE TABLE `patient_next_of_kin` (
  `nok_uid` varchar(64) NOT NULL,
  `patient_uid` varchar(64) NOT NULL,
  `nok_name` varchar(200) DEFAULT NULL,
  `nok_relationship` varchar(20) DEFAULT NULL,
  `nok_phone_number` varchar(100) DEFAULT NULL,
  `nok_email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_uid` varchar(64) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_password` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL,
  `retire` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patient_admission`
--
ALTER TABLE `patient_admission`
  ADD PRIMARY KEY (`rn`),
  ADD KEY `patient_uid` (`patient_uid`);

--
-- Indexes for table `patient_information`
--
ALTER TABLE `patient_information`
  ADD PRIMARY KEY (`patient_uid`),
  ADD UNIQUE KEY `nric` (`nric`);

--
-- Indexes for table `patient_next_of_kin`
--
ALTER TABLE `patient_next_of_kin`
  ADD PRIMARY KEY (`nok_uid`),
  ADD KEY `patient_uid` (`patient_uid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_uid`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `patient_admission`
--
ALTER TABLE `patient_admission`
  ADD CONSTRAINT `patient_admission_ibfk_1` FOREIGN KEY (`patient_uid`) REFERENCES `patient_information` (`patient_uid`);

--
-- Constraints for table `patient_next_of_kin`
--
ALTER TABLE `patient_next_of_kin`
  ADD CONSTRAINT `patient_next_of_kin_ibfk_1` FOREIGN KEY (`patient_uid`) REFERENCES `patient_information` (`patient_uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
