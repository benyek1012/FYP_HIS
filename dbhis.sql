-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2022 at 03:15 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `bill_uid` varchar(64) NOT NULL,
  `rn` varchar(11) NOT NULL,
  `status_code` varchar(20) NOT NULL,
  `status_description` varchar(100) NOT NULL,
  `class` varchar(20) NOT NULL,
  `daily_ward_cost` decimal(10,2) NOT NULL,
  `department_code` varchar(20) DEFAULT NULL,
  `department_name` varchar(50) DEFAULT NULL,
  `is_free` tinyint(1) NOT NULL DEFAULT 0,
  `collection_center_code` varchar(20) DEFAULT NULL,
  `nurse_responsilbe` varchar(20) DEFAULT NULL,
  `bill_generation_datetime` datetime DEFAULT NULL,
  `generation_responsible_uid` varchar(64) DEFAULT NULL,
  `bill_generation_billable_sum_rm` decimal(10,2) DEFAULT NULL,
  `bill_generation_final_fee_rm` decimal(10,2) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `bill_print_responsible_uid` varchar(64) DEFAULT NULL,
  `bill_print_datetime` datetime DEFAULT NULL,
  `bill_print_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bill_content_receipt`
--

CREATE TABLE `bill_content_receipt` (
  `bill_content_receipt_uid` varchar(64) NOT NULL,
  `bill_uid` varchar(64) NOT NULL,
  `receipt_uid` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_department`
--

CREATE TABLE `lookup_department` (
  `department_uid` varchar(64) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_general`
--

CREATE TABLE `lookup_general` (
  `lookup_general_uid` varchar(64) NOT NULL,
  `code` varchar(20) NOT NULL,
  `category` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `long_description` varchar(100) NOT NULL,
  `recommend` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_status`
--

CREATE TABLE `lookup_status` (
  `status_uid` varchar(64) NOT NULL,
  `status_code` varchar(20) NOT NULL,
  `status_description` varchar(100) NOT NULL,
  `class_1a_ward_cost` decimal(10,2) NOT NULL,
  `class_1b_ward_cost` decimal(10,2) NOT NULL,
  `class_1c_ward_cost` decimal(10,2) NOT NULL,
  `class_2_ward_cost` decimal(10,2) NOT NULL,
  `class_3_ward_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_treatment`
--

CREATE TABLE `lookup_treatment` (
  `treatment_uid` varchar(64) NOT NULL,
  `treatment_code` varchar(20) NOT NULL,
  `treatment_name` varchar(50) NOT NULL,
  `class_1_cost_per_unit` decimal(10,2) NOT NULL,
  `class_2_cost_per_unit` decimal(10,2) NOT NULL,
  `class_3_cost_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_ward`
--

CREATE TABLE `lookup_ward` (
  `ward_uid` varchar(64) NOT NULL,
  `ward_code` varchar(20) NOT NULL,
  `ward_name` varchar(50) NOT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `min_age` int(11) DEFAULT NULL,
  `max_age` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
-- Table structure for table `receipt`
--

CREATE TABLE `receipt` (
  `receipt_uid` varchar(64) NOT NULL,
  `rn` varchar(64) NOT NULL,
  `receipt_type` varchar(20) NOT NULL,
  `receipt_content_sum` decimal(10,2) NOT NULL,
  `receipt_content_bill_id` varchar(20) DEFAULT NULL,
  `receipt_content_description` varchar(100) DEFAULT NULL,
  `receipt_content_date_paid` date NOT NULL,
  `receipt_content_payer_name` varchar(200) NOT NULL,
  `receipt_content_payment_method` varchar(20) NOT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `cheque_number` varchar(20) DEFAULT NULL,
  `receipt_responsible` varchar(64) NOT NULL,
  `receipt_serial_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_details`
--

CREATE TABLE `treatment_details` (
  `treatment_details_uid` varchar(64) NOT NULL,
  `bill_uid` varchar(64) NOT NULL,
  `treatment_code` varchar(20) NOT NULL,
  `treatment_name` varchar(50) NOT NULL,
  `item_per_unit_cost_rm` decimal(10,2) NOT NULL,
  `item_count` int(11) NOT NULL,
  `item_total_unit_cost_rm` decimal(10,2) NOT NULL
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

-- --------------------------------------------------------

--
-- Table structure for table `ward`
--

CREATE TABLE `ward` (
  `ward_uid` varchar(64) NOT NULL,
  `bill_uid` varchar(64) NOT NULL,
  `ward_code` varchar(20) NOT NULL,
  `ward_name` varchar(50) NOT NULL,
  `ward_start_datetime` datetime NOT NULL,
  `ward_end_datetime` datetime NOT NULL,
  `ward_number_of_days` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`bill_uid`),
  ADD UNIQUE KEY `rn` (`rn`),
  ADD UNIQUE KEY `bill_print_id` (`bill_print_id`);

--
-- Indexes for table `bill_content_receipt`
--
ALTER TABLE `bill_content_receipt`
  ADD PRIMARY KEY (`bill_content_receipt_uid`),
  ADD KEY `bill_uid` (`bill_uid`),
  ADD KEY `receipt_uid` (`receipt_uid`);

--
-- Indexes for table `lookup_department`
--
ALTER TABLE `lookup_department`
  ADD PRIMARY KEY (`department_uid`),
  ADD UNIQUE KEY `department_code` (`department_code`);

--
-- Indexes for table `lookup_general`
--
ALTER TABLE `lookup_general`
  ADD PRIMARY KEY (`lookup_general_uid`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `lookup_status`
--
ALTER TABLE `lookup_status`
  ADD PRIMARY KEY (`status_uid`),
  ADD UNIQUE KEY `status_code` (`status_code`);

--
-- Indexes for table `lookup_treatment`
--
ALTER TABLE `lookup_treatment`
  ADD PRIMARY KEY (`treatment_uid`),
  ADD UNIQUE KEY `treatment_code` (`treatment_code`);

--
-- Indexes for table `lookup_ward`
--
ALTER TABLE `lookup_ward`
  ADD PRIMARY KEY (`ward_uid`),
  ADD UNIQUE KEY `ward_code` (`ward_code`);

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
-- Indexes for table `receipt`
--
ALTER TABLE `receipt`
  ADD PRIMARY KEY (`receipt_uid`),
  ADD KEY `rn` (`rn`);

--
-- Indexes for table `treatment_details`
--
ALTER TABLE `treatment_details`
  ADD PRIMARY KEY (`treatment_details_uid`),
  ADD KEY `bill_uid` (`bill_uid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_uid`);

--
-- Indexes for table `ward`
--
ALTER TABLE `ward`
  ADD PRIMARY KEY (`ward_uid`),
  ADD KEY `bill_uid` (`bill_uid`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`rn`) REFERENCES `patient_admission` (`rn`);

--
-- Constraints for table `bill_content_receipt`
--
ALTER TABLE `bill_content_receipt`
  ADD CONSTRAINT `bill_content_receipt_ibfk_1` FOREIGN KEY (`bill_uid`) REFERENCES `bill` (`bill_uid`),
  ADD CONSTRAINT `bill_content_receipt_ibfk_2` FOREIGN KEY (`receipt_uid`) REFERENCES `receipt` (`receipt_uid`);

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

--
-- Constraints for table `receipt`
--
ALTER TABLE `receipt`
  ADD CONSTRAINT `receipt_ibfk_1` FOREIGN KEY (`rn`) REFERENCES `patient_admission` (`rn`);

--
-- Constraints for table `treatment_details`
--
ALTER TABLE `treatment_details`
  ADD CONSTRAINT `treatment_details_ibfk_1` FOREIGN KEY (`bill_uid`) REFERENCES `bill` (`bill_uid`);

--
-- Constraints for table `ward`
--
ALTER TABLE `ward`
  ADD CONSTRAINT `ward_ibfk_1` FOREIGN KEY (`bill_uid`) REFERENCES `bill` (`bill_uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
