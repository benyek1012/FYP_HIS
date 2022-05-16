-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2022 at 06:18 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

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
CREATE DATABASE IF NOT EXISTS `dbhis` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dbhis`;

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE IF NOT EXISTS `bill` (
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
  `nurse_responsible` varchar(20) DEFAULT NULL,
  `bill_generation_datetime` datetime DEFAULT NULL,
  `generation_responsible_uid` varchar(64) DEFAULT NULL,
  `bill_generation_billable_sum_rm` decimal(10,2) DEFAULT NULL,
  `bill_generation_final_fee_rm` decimal(10,2) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `bill_print_responsible_uid` varchar(64) DEFAULT NULL,
  `bill_print_datetime` datetime DEFAULT NULL,
  `bill_print_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`bill_uid`),
  KEY `rn` (`rn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `bill_content_receipt`
--

CREATE TABLE IF NOT EXISTS `bill_content_receipt` (
  `bill_content_receipt_uid` varchar(64) NOT NULL,
  `bill_uid` varchar(64) NOT NULL,
  `receipt_uid` varchar(64) NOT NULL,
  PRIMARY KEY (`bill_content_receipt_uid`),
  KEY `bill_uid` (`bill_uid`),
  KEY `receipt_uid` (`receipt_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `lookup_department`
--

CREATE TABLE IF NOT EXISTS `lookup_department` (
  `department_uid` varchar(64) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(50) NOT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`department_uid`),
  UNIQUE KEY `department_code` (`department_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_department`
--

INSERT INTO `lookup_department` (`department_uid`, `department_code`, `department_name`, `phone_number`, `address1`, `address2`, `address3`) VALUES
('1', '17', 'JABATAN BURUH', '', 'TINGKAT 13, BANGUNAN SULTAN ISKANDAR, JALAN SIMPANG TIGA, 93532, KUCHING', '', ''),
('2', '18', 'JABATAN GALIAN', NULL, 'TINGKAT 8, BANGUNAN SULTAN ISKANDAR, JALAN SIMPANG TIGA, 93656, KUCHING', NULL, NULL),
('3', '19', 'JABATAN HASIL DALAM NEGERI MALAYSIA', NULL, 'ARAS 18, WISMA HASIL, NO.1 JLN PADUNGAN, 93100, KUCHING', NULL, NULL),
('4', '20', 'JABATAN IMIGRESEN, NEGERI SARAWAK', NULL, 'TINGKAT 2, BANGUNAN SULTAN ISKANDAR, JALAN SIMPANG TIGA, 95530, KUCHING', NULL, NULL),
('5', '21', 'JABATAN KASTAM & EKSAIS DIRAJA MALAYSIA', '082-333133', 'JALAN GEDONG, TANAH PUTEH, 93596, KUCHING,', NULL, NULL),
('6', '22', 'JABATAN KERJA RAYA', '082-244041', 'TINGKAT 18, WISMA SABERKAS, JALAN TUN HAJI OPENG, 93582, KUCHING', NULL, NULL),
('7', '23', 'JABATAN KILANG DAN JENTERA', '082-242257', 'TINGKAT 12, BANGUNAN SULTAN ISKANDAR, JALAN SIMPANG TIGA, 93300, KUCHING', NULL, NULL),
('8', '24', 'JABATAN KIMIA', '082-333267', 'PETI SURAT 1363, JALAN SEKAMA, 93728, KUCHING', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lookup_general`
--

CREATE TABLE IF NOT EXISTS `lookup_general` (
  `lookup_general_uid` varchar(64) NOT NULL,
  `code` varchar(20) NOT NULL,
  `category` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `long_description` varchar(100) NOT NULL,
  `recommend` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lookup_general_uid`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_general`
--

INSERT INTO `lookup_general` (`lookup_general_uid`, `code`, `category`, `name`, `long_description`, `recommend`) VALUES
('2EJH7eoy70VtSW28HdWmqPR8GuBJ-0AN', 'Father', 'Relationship', 'Father', 'Father', 1),
('4uqnt80lzCEfbqVtw15DJB44UI6QL8iF', 'Mother', 'Relationship', 'Mother', 'Mother', 1),
('aaFXZAE9L9368Ha8kUCr4WtSXNPjsQGw', 'Indonesia', 'Nationality', 'Indonesia', 'Indonesia', 1),
('aRYQoZ0PUgME-uCF_cdxjF6dYp8eSMfk', 'Son', 'Relationship', 'Son', 'Son', 1),
('DCMaNq6WPc6L5tP-kYnPUFhZPJ_l-EKC', 'China', 'Nationality', 'China', 'China', 1),
('Dh9GP_xVMCs1IS_6CKineglPl56QYVUv', 'Kadazandusun', 'Race', 'Kadazandusun', 'Kadazandusun', 1),
('dysj6KiH3X4jsDxS3N3b3GlkNV9yJOPx', 'Chinese', 'Race', 'Chinese', 'Chinese', 1),
('DyUydTXK7F2zYXrFjfANS06IlGhRnbQ3', 'Indian', 'Race', 'Indian', 'Indian', 1),
('EnThnPoarmFJQ6LyKfBr0vTT5ChY4Vvi', 'Singapore', 'Nationality', 'Singapore', 'Singapore', 1),
('jmUi7zek4A-0EKslcAAUhJUVcplGa8wz', 'Husband/Spouse', 'Relationship', 'Husband/Spouse', 'Husband/Spouse', 1),
('Jor2cgaU0etz67yALq3odFYCl2n5Uc_E', 'Female', 'Sex', 'Female', 'Female', 1),
('NH2m9rCS3gW1kml1GFtJQBd6W7KsDjMr', 'Iban', 'Race', 'Iban', 'Iban', 1),
('q18lvwYjLv8qE-FT9yYgQikmaekPGyoZ', 'Male', 'Sex', 'Male', 'Male', 1),
('SHxs6f-CqlgvJOACnEJ6jWpE6wl9aFJY', 'Thailand', 'Nationality', 'Thailand', 'Thailand', 1),
('smIfbSJJI1b970rWS0it1kCqDaRMsSSg', 'Malaysia', 'Nationality', 'Malaysia', 'Malaysia', 1),
('tpMLuKI7rr4jn0FVyCWA2c62DyOKsrYe', 'Brother', 'Relationship', 'Brother', 'Brother', 1),
('u5lcXSAV0bYqVclKtQZao9xRRTykmA_O', 'Sister', 'Relationship', 'Sister', 'Sister', 1),
('UAaI0eNAqAEk6VwJaMqXGeYHF9rsqFWY', 'Malay', 'Race', 'Malay', 'Malay', 1),
('WQSPmw1lcpysbN3sR_IpQsvvEQ8WFzBf', 'Daughter', 'Relationship', 'Daughter', 'Daughter', 1);

-- --------------------------------------------------------

--
-- Table structure for table `lookup_status`
--

CREATE TABLE IF NOT EXISTS `lookup_status` (
  `status_uid` varchar(64) NOT NULL,
  `status_code` varchar(20) NOT NULL,
  `status_description` varchar(100) NOT NULL,
  `class_1a_ward_cost` decimal(10,2) NOT NULL,
  `class_1b_ward_cost` decimal(10,2) NOT NULL,
  `class_1c_ward_cost` decimal(10,2) NOT NULL,
  `class_2_ward_cost` decimal(10,2) NOT NULL,
  `class_3_ward_cost` decimal(10,2) NOT NULL,
  PRIMARY KEY (`status_uid`),
  UNIQUE KEY `status_code` (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_status`
--

INSERT INTO `lookup_status` (`status_uid`, `status_code`, `status_description`, `class_1a_ward_cost`, `class_1b_ward_cost`, `class_1c_ward_cost`, `class_2_ward_cost`, `class_3_ward_cost`) VALUES
('1', 'BD2', 'BD 11-15 KALI-KELAS 2- 1 TAHUN', '80.00', '60.00', '40.00', '0.00', '0.00'),
('10', 'FP', 'AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('11', 'FPF', 'KELUARGA AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('12', 'FXN', 'EX-AHLI MAJLIS NEGERI', '0.00', '0.00', '0.00', '0.00', '0.00'),
('13', 'FXNF', 'KELUARGA EX-AHLI MAJLIS NEGERI', '0.00', '0.00', '0.00', '0.00', '0.00'),
('14', 'FXP', 'EX-AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('15', 'FXPF', 'KELUARGA EX-AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('16', 'GB2', 'SEMI-GOVT (2)', '80.00', '60.00', '30.00', '0.00', '0.00'),
('17', 'GB3', 'SEMI-GOVT (3)', '80.00', '60.00', '30.00', '20.00', '0.00'),
('18', 'GBA', 'SEMI-GOVT (1A)', '0.00', '0.00', '0.00', '0.00', '0.00'),
('19', 'GBB', 'SEMI-GOVT (1B)', '80.00', '0.00', '0.00', '0.00', '0.00'),
('2', 'BD3', 'BD 16-20 KALI-KELAS 2- 2 TAHUN', '80.00', '60.00', '40.00', '0.00', '0.00'),
('20', 'GBC', 'SEMI-GOVT (1C)', '80.00', '60.00', '0.00', '0.00', '0.00'),
('21', 'GBF2', 'KELUARGA SEMI-GOVT (2)', '80.00', '60.00', '30.00', '0.00', '0.00'),
('22', 'GBF3', 'KELUARGA SEMI-GOVT (3)', '80.00', '60.00', '30.00', '20.00', '0.00'),
('23', 'GBFA', 'KELUARGA SEMI-GOVT (1A)', '0.00', '0.00', '0.00', '0.00', '0.00'),
('24', 'GBFB', 'KELUARGA SEMI-GOVT (1B)', '80.00', '0.00', '0.00', '0.00', '0.00'),
('25', 'GBFC', 'KELUARGA SEMI-GOVT (1C)', '80.00', '60.00', '0.00', '0.00', '0.00'),
('26', 'GC2', 'CANCER PATIENT (GOVT) 2', '80.00', '60.00', '30.00', '0.00', '0.00'),
('27', 'GC3', 'CANCER PATIENT (GOVT) 3', '80.00', '60.00', '30.00', '20.00', '0.00'),
('28', 'GCA', 'CANCER PATIENT (GOVT) 1A', '0.00', '0.00', '0.00', '0.00', '0.00'),
('29', 'GCB', 'CANCER PATIENT (GOVT) 1B', '80.00', '0.00', '0.00', '0.00', '0.00'),
('3', 'BD4', 'BD 21-30 KALI-KELAS 2- 3 TAHUN', '80.00', '60.00', '40.00', '0.00', '0.00'),
('30', 'GCC', 'CANCER PATIENT (GOVT) 1C', '80.00', '60.00', '0.00', '0.00', '0.00'),
('31', 'GCF2', 'CANCER PATIENT (GSF2)', '80.00', '60.00', '40.00', '0.00', '0.00'),
('32', 'GCF3', 'CANCER PATIENT (GSF3)', '80.00', '60.00', '40.00', '20.00', '0.00'),
('33', 'GCFA', 'CANCER PATIENT (GSFA)', '0.00', '0.00', '0.00', '0.00', '0.00'),
('34', 'GCFB', 'CANCER PATIENT (GSFB)', '80.00', '0.00', '0.00', '0.00', '0.00'),
('35', 'GCFC', 'CANCER PATIENT (GSFC)', '80.00', '0.00', '0.00', '0.00', '0.00'),
('36', 'GI2', 'INF. DISEASE (GOVT) 2', '80.00', '60.00', '30.00', '0.00', '0.00'),
('37', 'GI3', 'INF. DISEASE (GOVT) 3', '80.00', '60.00', '30.00', '20.00', '0.00'),
('38', 'GIA', 'INF. DISEASE (GOVT) 1A', '0.00', '60.00', '0.00', '0.00', '0.00'),
('39', 'GIB', 'INF. DISEASE (GOVT) 1B', '80.00', '0.00', '0.00', '0.00', '0.00'),
('4', 'BD5', 'BD 31-40 KALI-KELAS 1- 4 TAHUN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('40', 'GIC', 'INF. DISEASE (GOVT) 1C', '80.00', '60.00', '0.00', '0.00', '0.00'),
('41', 'GM2', 'MEDICAL (2)', '80.00', '60.00', '30.00', '0.00', '0.00'),
('42', 'GM3', 'MEDICAL (3)', '80.00', '60.00', '30.00', '20.00', '0.00'),
('43', 'GMA', 'MEDICAL (1A)', '0.00', '0.00', '0.00', '0.00', '0.00'),
('44', 'GMB', 'MEDICAL (1B)', '120.00', '90.00', '60.00', '40.00', '0.00'),
('45', 'GMC', 'MEDICAL (1C)', '80.00', '60.00', '0.00', '0.00', '0.00'),
('5', 'BD6', 'BD 41-50 KALI-KELAS 1- 6 TAHUN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('6', 'BD7', 'BD >50 KALI-KELAS 1- 10 TAHUN', '0.00', '0.00', '0.00', '0.00', '0.00'),
('7', 'BSPS', 'TERCEDERA DALAM BERTUGAS', '0.00', '0.00', '0.00', '0.00', '0.00'),
('8', 'FN', 'AHLI UNDANGAN NEGERI', '0.00', '0.00', '0.00', '0.00', '0.00'),
('9', 'FNF', 'KELUARGA AHLI UNDANGAN NEG.', '0.00', '0.00', '0.00', '0.00', '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `lookup_treatment`
--

CREATE TABLE IF NOT EXISTS `lookup_treatment` (
  `treatment_uid` varchar(64) NOT NULL,
  `treatment_code` varchar(20) NOT NULL,
  `treatment_name` varchar(50) NOT NULL,
  `class_1_cost_per_unit` decimal(10,2) NOT NULL,
  `class_2_cost_per_unit` decimal(10,2) NOT NULL,
  `class_3_cost_per_unit` decimal(10,2) NOT NULL,
  PRIMARY KEY (`treatment_uid`),
  UNIQUE KEY `treatment_code` (`treatment_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_treatment`
--

INSERT INTO `lookup_treatment` (`treatment_uid`, `treatment_code`, `treatment_name`, `class_1_cost_per_unit`, `class_2_cost_per_unit`, `class_3_cost_per_unit`) VALUES
('1', '2AC', 'PEMERIKSAAN RADIOLOGI AC', '150.00', '60.00', '25.00'),
('10', '2G', 'PEMERIKSAAN RADIOLOGI G', '150.00', '40.00', '17.00'),
('11', '2H', 'PEMERIKSAAN RADIOLOGI H', '100.00', '40.00', '17.00'),
('12', '2I', 'PEMERIKSAAN RADIOLOGI I', '150.00', '40.00', '12.00'),
('13', '2J', 'PEMERIKSAAN RADIOLOGI J', '375.00', '100.00', '37.00'),
('14', '2K', 'PEMERIKSAAN RADIOLOGI K', '150.00', '40.00', '12.00'),
('15', '2L', 'PEMERIKSAAN RADIOLOGI L', '15.00', '5.00', '37.00'),
('16', '2M', 'PEMERIKSAAN RADIOLOGI M', '105.00', '30.00', '12.00'),
('17', '2N', 'PEMERIKSAAN RADIOLOGI N', '150.00', '40.00', '1.00'),
('18', '2O', 'PEMERIKSAAN RADIOLOGI O', '75.00', '20.00', '10.00'),
('19', '2O266', 'ENTERAL NUT. FORMULA', '70.00', '70.00', '70.00'),
('2', '2AD', 'PEMERIKSAAN RADIOLOGI AD', '300.00', '120.00', '50.00'),
('20', '2P', 'RADIOLOGI P(CT PERUT/SPINE/HIP)', '675.00', '200.00', '75.00'),
('21', '2PO', 'RADIO.P(CT PERUT/SPINE/LIVER)', '450.00', '450.00', '450.00'),
('22', '2Q', 'RADIOLOGI Q(CT KEPALA)', '350.00', '150.00', '60.00'),
('23', '2QO', 'RADIOLOGI Q(CT THORAX)', '416.00', '416.00', '570.00'),
('24', '2R', 'RADIOLOGI R(CT SCAN/MRI)', '600.00', '240.00', '120.00'),
('25', '2RO', 'RADIOLOGI R(MRI SPINE)', '833.00', '833.00', '833.00'),
('26', '2S', 'PEMERIKSAAN RADIOLOGI S', '300.00', '120.00', '50.00'),
('27', '2T', 'PEMERIKSAAN RADIOLOGI T', '100.00', '40.00', '17.00'),
('28', '2U', 'PEMERIKSAAN RADIOLOGI U', '120.00', '48.00', '20.00'),
('29', '2V', 'PEMERIKSAAN RADIOLOGI V', '100.00', '40.00', '17.00'),
('3', '2AE', 'PEMERIKSAAN RADIOLOGI AE', '100.00', '40.00', '17.00'),
('30', '2W', 'PEMERIKSAAN RADIOLOGI W', '150.00', '60.00', '25.00'),
('31', '2X', 'PEMERIKSAAN RADIOLOGI X', '75.00', '30.00', '12.00'),
('32', '2XO1', 'ABDOMEN (BERDIRI/BARING)', '80.00', '80.00', '80.00'),
('33', '2XO10', 'BRONCHOGRAM', '140.00', '140.00', '140.00'),
('34', '2XO11', 'KALKANIUM LATERAL-AXIAL', '40.00', '40.00', '40.00'),
('35', '2XO12', 'TULANG CERVICAL(ANTERO.+LAT+2)', '60.00', '60.00', '60.00'),
('36', '2XO13', 'TULANG CERVICAL(PRO. LATERAL)', '30.00', '30.00', '30.00'),
('37', '2XO14', 'TULANG CERVICAL(ANTERO+LAT.)', '40.00', '40.00', '40.00'),
('38', '2XO15', 'X-RAY DADA-2 PROJEKSI)', '50.00', '50.00', '50.00'),
('39', '2XO16', 'X-RAY DADA-1 PROJEKSI', '40.00', '40.00', '40.00'),
('4', '2B', 'PEMERIKSAAN RADIOLOGI B', '90.00', '25.00', '12.00'),
('40', '2XO17', 'KLAVIKAL(ANTEROPOSTERIOR+APICA', '40.00', '40.00', '40.00'),
('41', '2XO18', 'SISTOGRAM', '120.00', '120.00', '120.00'),
('42', '2XO19', 'SENDI SIKU(ASTERO + LATERAL', '40.00', '40.00', '40.00'),
('43', '2XO2', 'ABDOMEN UTK MENGESAN KEHAMILAN', '35.00', '35.00', '35.00'),
('44', '2XO20', 'TULANG MUKA(PRO.OCC.MENTEL)', '40.00', '40.00', '40.00'),
('45', '2XO21', 'TULANG PAHA(ANTERO + LATERAL)', '40.00', '40.00', '40.00'),
('5', '2C', 'PEMERIKSAAN RADIOLOGI C', '90.00', '25.00', '12.00'),
('6', '2CO', 'CT BRAIN/HEAD', '915.00', '915.00', '915.00'),
('7', '2D', 'PEMERIKSAAN RADIOLOGI D', '75.00', '20.00', '10.00'),
('8', '2E', 'PEMERIKSAAN RADIOLOGI E', '25.00', '6.00', '2.00'),
('9', '2F', 'PEMERIKSAAN RADIOLOGI F', '90.00', '25.00', '12.00');

-- --------------------------------------------------------

--
-- Table structure for table `lookup_ward`
--

CREATE TABLE IF NOT EXISTS `lookup_ward` (
  `ward_uid` varchar(64) NOT NULL,
  `ward_code` varchar(20) NOT NULL,
  `ward_name` varchar(50) NOT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `min_age` int(11) DEFAULT NULL,
  `max_age` int(11) DEFAULT NULL,
  PRIMARY KEY (`ward_uid`),
  UNIQUE KEY `ward_code` (`ward_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_ward`
--

INSERT INTO `lookup_ward` (`ward_uid`, `ward_code`, `ward_name`, `sex`, `min_age`, `max_age`) VALUES
('1', '01', 'FIRST CLASS WARD', 'C', 12, 150),
('10', '14', 'LABOUR WARD', 'P', 12, 150),
('11', '15', 'MATERNITY1', 'P', 12, 45),
('12', '16', 'GYNAE WARD', 'P', 12, 150),
('13', '17', 'EYE WARD', 'C', 0, 150),
('14', '18', 'A&E UNIT', 'C', 0, 150),
('15', '19', 'CTW', 'C', 0, 150),
('16', '1U', 'UROLOGY WARD', NULL, 0, 100),
('17', '1V', 'VASCULAR WARD', NULL, 0, 100),
('18', '20', 'FEMALE RTU', 'P', 12, 100),
('19', '21', 'MALE RTU', 'L', 12, 100),
('2', '02', 'FEMALE 2ND CLASS', 'P', 12, 150),
('20', '22', 'RADIOACTIVE WAD', NULL, 12, 150),
('21', '23', 'VIP', 'C', 12, 150),
('22', '24', 'MALE 2ND CLASS', 'L', 12, 150),
('23', '25', 'EMT WARD', 'C', 12, 150),
('24', '26', 'BURN UNIT', 'C', 0, 150),
('25', '27', 'AMBULATORY (RTU)', NULL, 0, 150),
('26', '28', 'CICU', 'C', 0, 90),
('27', '29', 'BABY MAT 1', 'C', 0, 1),
('28', '2A', 'PAED ONCOLOGY', NULL, 0, 100),
('29', '2B', 'PAED MEDICAL', NULL, 0, 70),
('3', '03', 'ICU WARD', 'C', 0, 150),
('30', '30', 'PAED ICU', NULL, 0, 70),
('31', '31', 'PAED MDU', 'C', 0, 12),
('32', '32', 'WAD ISC.KHAS', NULL, 0, 60),
('33', '33', 'INF DISEASE WAD', NULL, 12, 100),
('34', '34', 'PCW (RTU)', NULL, 0, 100),
('35', '35', 'MED. ISO SEMEMTARA', NULL, 0, 100),
('36', '36', 'F/ORT. WARD', 'P', 12, 100),
('37', '37', 'NEUROSX WD', NULL, 0, 100),
('38', '38', 'NEURO KDU', NULL, 1, 9),
('39', '3A', 'PAED ISO', NULL, 0, 100),
('4', '04', 'FEMALE MEDICAL', 'P', 12, 150),
('40', '3B', 'CTW', NULL, 12, 100),
('41', '42', 'CSSD', NULL, 0, 0),
('42', '45', 'FARMASI SATELLITE', NULL, 0, 0),
('43', '46', 'FARMASI OPD', NULL, 0, 0),
('44', '47', 'FARMASI WARD SUPPLY', NULL, 0, 0),
('45', '48', 'FARMASI INJECTION', NULL, 0, 0),
('5', '05', 'MALE MEDICAL', 'L', 12, 100),
('6', '09', 'NURSERY WARD', 'C', 0, 0),
('7', '10', 'MALE SURGICAL', 'L', 12, 150),
('8', '11', 'FEMALE SURGICAL', 'P', 12, 150),
('9', '13', 'M/CRT.WARD', 'L', 12, 150);

-- --------------------------------------------------------

--
-- Table structure for table `new_user`
--

CREATE TABLE IF NOT EXISTS `new_user` (
  `user_uid` varchar(64) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_password` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL,
  `retire` tinyint(1) DEFAULT 0,
  `authKey` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `new_user`
--

INSERT INTO `new_user` (`user_uid`, `username`, `user_password`, `role`, `retire`, `authKey`) VALUES
('1', 'cashier1', '12345', 'Cashier', 1, '12345a'),
('2', 'administrator1', '12345', 'Administrator', 1, '12345b'),
('3', 'clerk1', '12345', 'Clerk', 1, '12345c');

-- --------------------------------------------------------

--
-- Table structure for table `patient_admission`
--

CREATE TABLE IF NOT EXISTS `patient_admission` (
  `rn` varchar(11) NOT NULL,
  `entry_datetime` datetime NOT NULL,
  `patient_uid` varchar(64) NOT NULL,
  `initial_ward_code` varchar(20) NOT NULL,
  `initial_ward_class` varchar(20) NOT NULL,
  `reference` varchar(200) DEFAULT NULL,
  `medigal_legal_code` tinyint(1) DEFAULT 0,
  `reminder_given` int(11) NOT NULL,
  `guarantor_name` varchar(200) DEFAULT NULL,
  `guarantor_nric` varchar(20) DEFAULT NULL,
  `guarantor_phone_number` varchar(100) DEFAULT NULL,
  `guarantor_email` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`rn`),
  KEY `patient_uid` (`patient_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patient_information`
--

CREATE TABLE IF NOT EXISTS `patient_information` (
  `patient_uid` varchar(64) NOT NULL,
  `first_reg_date` date NOT NULL,
  `nric` varchar(20) DEFAULT NULL,
  `nationality` varchar(20) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  `race` varchar(20) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `job` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`patient_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `patient_next_of_kin`
--

CREATE TABLE IF NOT EXISTS `patient_next_of_kin` (
  `nok_uid` varchar(64) NOT NULL,
  `patient_uid` varchar(64) NOT NULL,
  `nok_name` varchar(200) DEFAULT NULL,
  `nok_relationship` varchar(20) DEFAULT NULL,
  `nok_phone_number` varchar(100) DEFAULT NULL,
  `nok_email` varchar(100) DEFAULT NULL,
  `nok_address1` varchar(100) DEFAULT NULL,
  `nok_address2` varchar(100) DEFAULT NULL,
  `nok_address3` varchar(100) DEFAULT NULL,
  `nok_datetime_updated` datetime NOT NULL,
  PRIMARY KEY (`nok_uid`),
  KEY `patient_uid` (`patient_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `receipt`
--

CREATE TABLE IF NOT EXISTS `receipt` (
  `receipt_uid` varchar(64) NOT NULL,
  `rn` varchar(64) NOT NULL,
  `receipt_type` varchar(20) NOT NULL,
  `receipt_content_sum` decimal(10,2) NOT NULL,
  `receipt_content_bill_id` varchar(20) DEFAULT NULL,
  `receipt_content_description` varchar(100) DEFAULT NULL,
  `receipt_content_datetime_paid` datetime NOT NULL,
  `receipt_content_payer_name` varchar(200) NOT NULL,
  `receipt_content_payment_method` varchar(20) NOT NULL,
  `card_no` varchar(20) DEFAULT NULL,
  `cheque_number` varchar(20) DEFAULT NULL,
  `receipt_responsible` varchar(64) NOT NULL,
  `receipt_serial_number` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`receipt_uid`),
  KEY `rn` (`rn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_details`
--

CREATE TABLE IF NOT EXISTS `treatment_details` (
  `treatment_details_uid` varchar(64) NOT NULL,
  `bill_uid` varchar(64) NOT NULL,
  `treatment_code` varchar(20) NOT NULL,
  `treatment_name` varchar(50) NOT NULL,
  `item_per_unit_cost_rm` decimal(10,2) NOT NULL,
  `item_count` int(11) NOT NULL,
  `item_total_unit_cost_rm` decimal(10,2) NOT NULL,
  PRIMARY KEY (`treatment_details_uid`),
  KEY `bill_uid` (`bill_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `ward`
--

CREATE TABLE IF NOT EXISTS `ward` (
  `ward_uid` varchar(64) NOT NULL,
  `bill_uid` varchar(64) NOT NULL,
  `ward_code` varchar(20) NOT NULL,
  `ward_name` varchar(50) NOT NULL,
  `ward_start_datetime` datetime NOT NULL,
  `ward_end_datetime` datetime NOT NULL,
  `ward_number_of_days` int(11) NOT NULL,
  PRIMARY KEY (`ward_uid`),
  KEY `bill_uid` (`bill_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
