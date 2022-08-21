-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2022 at 06:09 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.1.6

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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `receipt_bill_procedure` (IN `rn` VARCHAR(11))   BEGIN

   SELECT receipt.rn, receipt.receipt_content_datetime_paid, receipt.receipt_content_sum, receipt.receipt_type, 	receipt.receipt_content_payment_method, receipt.receipt_content_payer_name, receipt.receipt_serial_number, receipt.receipt_content_description, receipt.receipt_responsible FROM receipt WHERE receipt.rn = rn
    UNION
    SELECT bill.rn, bill.bill_generation_datetime, bill.bill_generation_billable_sum_rm, null, null, null, bill.bill_print_id, null, bill.generation_responsible_uid FROM bill WHERE bill.deleted = 0 AND bill.rn = rn AND bill.bill_generation_datetime != '';

    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_records` (IN `pid` VARCHAR(64))   BEGIN

	SELECT receipt.rn, receipt.receipt_content_datetime_paid, receipt.receipt_content_sum, receipt.receipt_type, 	receipt.receipt_content_payment_method, receipt.receipt_content_payer_name, receipt.receipt_serial_number, receipt.receipt_content_description, receipt.receipt_responsible
	FROM receipt
	INNER JOIN patient_admission 
    ON patient_admission.rn = receipt.rn
    INNER JOIN patient_information
    ON patient_information.patient_uid = patient_admission.patient_uid
    WHERE patient_information.patient_uid = pid
    
    UNION
     
    SELECT bill.rn, bill.bill_generation_datetime, bill.bill_generation_billable_sum_rm, null, null, null, bill.bill_print_id, null, bill.generation_responsible_uid 
    FROM bill 
    INNER JOIN patient_admission 
    ON patient_admission.rn = bill.rn
    INNER JOIN patient_information
    ON patient_information.patient_uid = patient_admission.patient_uid
    WHERE patient_information.patient_uid = pid 
    AND bill.deleted = 0 AND bill.bill_generation_datetime != '';

    
 
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `id` int(11) NOT NULL,
  `batch` bigint(20) NOT NULL,
  `file_import` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `nurse_responsible` varchar(20) DEFAULT NULL,
  `bill_generation_datetime` datetime DEFAULT NULL,
  `generation_responsible_uid` varchar(64) DEFAULT NULL,
  `bill_generation_billable_sum_rm` decimal(10,2) DEFAULT NULL,
  `bill_generation_final_fee_rm` decimal(10,2) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `bill_print_responsible_uid` varchar(64) DEFAULT NULL,
  `bill_print_datetime` datetime DEFAULT NULL,
  `bill_print_id` varchar(20) DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cancellation`
--

CREATE TABLE `cancellation` (
  `cancellation_uid` varchar(64) NOT NULL,
  `table` varchar(64) NOT NULL,
  `reason` varchar(100) NOT NULL,
  `replacement_uid` varchar(64) DEFAULT NULL,
  `responsible_uid` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

CREATE TABLE `lookup_general` (
  `lookup_general_uid` varchar(64) NOT NULL,
  `code` varchar(20) NOT NULL,
  `category` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `long_description` varchar(100) NOT NULL,
  `recommend` tinyint(1) NOT NULL DEFAULT 1
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
('Dh9GP_xVMCs1IS_6CKineglPl56QYVUv', 'ME', 'Race', 'Melanau', 'Melanau', 1),
('dysj6KiH3X4jsDxS3N3b3GlkNV9yJOPx', 'CH', 'Race', 'Cina', 'Cina', 1),
('DyUydTXK7F2zYXrFjfANS06IlGhRnbQ3', 'BI', 'Race', 'Bidayuh', 'Bidayuh', 1),
('e0Ky93ubNfC3ekGmN3gj5aXW7iD-FyXu', 'CQ', 'Payment Method', 'cheque', 'cheque', 1),
('EnThnPoarmFJQ6LyKfBr0vTT5ChY4Vvi', 'Singapore', 'Nationality', 'Singapore', 'Singapore', 1),
('jH4xzCyHpJF-Sxp-eUBAx69zZCYwN9ZA', 'CD', 'Payment Method', 'card', 'card', 1),
('jmUi7zek4A-0EKslcAAUhJUVcplGa8wz', 'Husband/Spouse', 'Relationship', 'Husband/Spouse', 'Husband/Spouse', 1),
('Jor2cgaU0etz67yALq3odFYCl2n5Uc_E', 'Female', 'Sex', 'Female', 'Female', 1),
('NgRT9pY7CuuVhBgl2hzZE5o1uGFC3YX0', 'OI', 'Race', 'Others', 'Others (Kenyah, Kelabit, Lunbawang, Kayan, Penan etc)', 1),
('NH2m9rCS3gW1kml1GFtJQBd6W7KsDjMr', 'IB', 'Race', 'Iban', 'Iban', 1),
('q18lvwYjLv8qE-FT9yYgQikmaekPGyoZ', 'Male', 'Sex', 'Male', 'Male', 1),
('SHxs6f-CqlgvJOACnEJ6jWpE6wl9aFJY', 'Thailand', 'Nationality', 'Thailand', 'Thailand', 1),
('smIfbSJJI1b970rWS0it1kCqDaRMsSSg', 'Malaysia', 'Nationality', 'Malaysia', 'Malaysia', 1),
('tpMLuKI7rr4jn0FVyCWA2c62DyOKsrYe', 'Brother', 'Relationship', 'Brother', 'Brother', 1),
('u5lcXSAV0bYqVclKtQZao9xRRTykmA_O', 'Sister', 'Relationship', 'Sister', 'Sister', 1),
('UAaI0eNAqAEk6VwJaMqXGeYHF9rsqFWY', 'MA', 'Race', 'Malay', 'Malay', 1),
('WQSPmw1lcpysbN3sR_IpQsvvEQ8WFzBf', 'Daughter', 'Relationship', 'Daughter', 'Daughter', 1),
('ZvyRuvg4xhTay4CQsFVnDr7y_Us_DblQ', 'CS', 'Payment Method', 'cash', 'cash', 1);

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
  `class_3_ward_cost` decimal(10,2) NOT NULL,
  `class_Daycare_FPP_ward_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_status`
--

INSERT INTO `lookup_status` (`status_uid`, `status_code`, `status_description`, `class_1a_ward_cost`, `class_1b_ward_cost`, `class_1c_ward_cost`, `class_2_ward_cost`, `class_3_ward_cost`, `class_Daycare_FPP_ward_cost`) VALUES
('1', 'BD2', 'BD 11-15 KALI-KELAS 2- 1 TAHUN', '80.00', '60.00', '40.00', '0.00', '0.00', '0.00'),
('10', 'FP', 'AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('11', 'FPF', 'KELUARGA AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('12', 'FXN', 'EX-AHLI MAJLIS NEGERI', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('13', 'FXNF', 'KELUARGA EX-AHLI MAJLIS NEGERI', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('14', 'FXP', 'EX-AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('15', 'FXPF', 'KELUARGA EX-AHLI PARLIMEN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('16', 'GB2', 'SEMI-GOVT (2)', '80.00', '60.00', '30.00', '0.00', '0.00', '0.00'),
('17', 'GB3', 'SEMI-GOVT (3)', '80.00', '60.00', '30.00', '20.00', '0.00', '0.00'),
('18', 'GBA', 'SEMI-GOVT (1A)', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('19', 'GBB', 'SEMI-GOVT (1B)', '80.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('2', 'BD3', 'BD 16-20 KALI-KELAS 2- 2 TAHUN', '80.00', '60.00', '40.00', '0.00', '0.00', '0.00'),
('20', 'GBC', 'SEMI-GOVT (1C)', '80.00', '60.00', '0.00', '0.00', '0.00', '0.00'),
('21', 'GBF2', 'KELUARGA SEMI-GOVT (2)', '80.00', '60.00', '30.00', '0.00', '0.00', '0.00'),
('22', 'GBF3', 'KELUARGA SEMI-GOVT (3)', '80.00', '60.00', '30.00', '20.00', '0.00', '0.00'),
('23', 'GBFA', 'KELUARGA SEMI-GOVT (1A)', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('24', 'GBFB', 'KELUARGA SEMI-GOVT (1B)', '80.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('25', 'GBFC', 'KELUARGA SEMI-GOVT (1C)', '80.00', '60.00', '0.00', '0.00', '0.00', '0.00'),
('26', 'GC2', 'CANCER PATIENT (GOVT) 2', '80.00', '60.00', '30.00', '0.00', '0.00', '0.00'),
('27', 'GC3', 'CANCER PATIENT (GOVT) 3', '80.00', '60.00', '30.00', '20.00', '0.00', '0.00'),
('28', 'GCA', 'CANCER PATIENT (GOVT) 1A', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('29', 'GCB', 'CANCER PATIENT (GOVT) 1B', '80.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('3', 'BD4', 'BD 21-30 KALI-KELAS 2- 3 TAHUN', '80.00', '60.00', '40.00', '0.00', '0.00', '0.00'),
('30', 'GCC', 'CANCER PATIENT (GOVT) 1C', '80.00', '60.00', '0.00', '0.00', '0.00', '0.00'),
('31', 'GCF2', 'CANCER PATIENT (GSF2)', '80.00', '60.00', '40.00', '0.00', '0.00', '0.00'),
('32', 'GCF3', 'CANCER PATIENT (GSF3)', '80.00', '60.00', '40.00', '20.00', '0.00', '0.00'),
('33', 'GCFA', 'CANCER PATIENT (GSFA)', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('34', 'GCFB', 'CANCER PATIENT (GSFB)', '80.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('35', 'GCFC', 'CANCER PATIENT (GSFC)', '80.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('36', 'GI2', 'INF. DISEASE (GOVT) 2', '80.00', '60.00', '30.00', '0.00', '0.00', '0.00'),
('37', 'GI3', 'INF. DISEASE (GOVT) 3', '80.00', '60.00', '30.00', '20.00', '0.00', '0.00'),
('38', 'GIA', 'INF. DISEASE (GOVT) 1A', '0.00', '60.00', '0.00', '0.00', '0.00', '0.00'),
('39', 'GIB', 'INF. DISEASE (GOVT) 1B', '80.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('4', 'BD5', 'BD 31-40 KALI-KELAS 1- 4 TAHUN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('40', 'GIC', 'INF. DISEASE (GOVT) 1C', '80.00', '60.00', '0.00', '0.00', '0.00', '0.00'),
('41', 'GM2', 'MEDICAL (2)', '80.00', '60.00', '30.00', '0.00', '0.00', '0.00'),
('42', 'GM3', 'MEDICAL (3)', '80.00', '60.00', '30.00', '20.00', '0.00', '0.00'),
('43', 'GMA', 'MEDICAL (1A)', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('44', 'GMB', 'MEDICAL (1B)', '120.00', '90.00', '60.00', '40.00', '0.00', '0.00'),
('45', 'GMC', 'MEDICAL (1C)', '80.00', '60.00', '0.00', '0.00', '0.00', '0.00'),
('5', 'BD6', 'BD 41-50 KALI-KELAS 1- 6 TAHUN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('6', 'BD7', 'BD >50 KALI-KELAS 1- 10 TAHUN', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('7', 'BSPS', 'TERCEDERA DALAM BERTUGAS', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('8', 'FN', 'AHLI UNDANGAN NEGERI', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00'),
('9', 'FNF', 'KELUARGA AHLI UNDANGAN NEG.', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00');

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
  `class_3_cost_per_unit` decimal(10,2) NOT NULL,
  `class_Daycare_FPP_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_treatment`
--

INSERT INTO `lookup_treatment` (`treatment_uid`, `treatment_code`, `treatment_name`, `class_1_cost_per_unit`, `class_2_cost_per_unit`, `class_3_cost_per_unit`, `class_Daycare_FPP_per_unit`) VALUES
('1', '2AC', 'PEMERIKSAAN RADIOLOGI AC', '150.00', '60.00', '25.00', '0.00'),
('10', '2G', 'PEMERIKSAAN RADIOLOGI G', '150.00', '40.00', '17.00', '0.00'),
('11', '2H', 'PEMERIKSAAN RADIOLOGI H', '100.00', '40.00', '17.00', '0.00'),
('12', '2I', 'PEMERIKSAAN RADIOLOGI I', '150.00', '40.00', '12.00', '0.00'),
('13', '2J', 'PEMERIKSAAN RADIOLOGI J', '375.00', '100.00', '37.00', '0.00'),
('14', '2K', 'PEMERIKSAAN RADIOLOGI K', '150.00', '40.00', '12.00', '0.00'),
('15', '2L', 'PEMERIKSAAN RADIOLOGI L', '15.00', '5.00', '37.00', '0.00'),
('16', '2M', 'PEMERIKSAAN RADIOLOGI M', '105.00', '30.00', '12.00', '0.00'),
('17', '2N', 'PEMERIKSAAN RADIOLOGI N', '150.00', '40.00', '1.00', '0.00'),
('18', '2O', 'PEMERIKSAAN RADIOLOGI O', '75.00', '20.00', '10.00', '0.00'),
('19', '2O266', 'ENTERAL NUT. FORMULA', '70.00', '70.00', '70.00', '0.00'),
('2', '2AD', 'PEMERIKSAAN RADIOLOGI AD', '300.00', '120.00', '50.00', '0.00'),
('20', '2P', 'RADIOLOGI P(CT PERUT/SPINE/HIP)', '675.00', '200.00', '75.00', '0.00'),
('21', '2PO', 'RADIO.P(CT PERUT/SPINE/LIVER)', '450.00', '450.00', '450.00', '0.00'),
('22', '2Q', 'RADIOLOGI Q(CT KEPALA)', '350.00', '150.00', '60.00', '0.00'),
('23', '2QO', 'RADIOLOGI Q(CT THORAX)', '416.00', '416.00', '570.00', '0.00'),
('24', '2R', 'RADIOLOGI R(CT SCAN/MRI)', '600.00', '240.00', '120.00', '0.00'),
('25', '2RO', 'RADIOLOGI R(MRI SPINE)', '833.00', '833.00', '833.00', '0.00'),
('26', '2S', 'PEMERIKSAAN RADIOLOGI S', '300.00', '120.00', '50.00', '0.00'),
('27', '2T', 'PEMERIKSAAN RADIOLOGI T', '100.00', '40.00', '17.00', '0.00'),
('28', '2U', 'PEMERIKSAAN RADIOLOGI U', '120.00', '48.00', '20.00', '0.00'),
('29', '2V', 'PEMERIKSAAN RADIOLOGI V', '100.00', '40.00', '17.00', '0.00'),
('3', '2AE', 'PEMERIKSAAN RADIOLOGI AE', '100.00', '40.00', '17.00', '0.00'),
('30', '2W', 'PEMERIKSAAN RADIOLOGI W', '150.00', '60.00', '25.00', '0.00'),
('31', '2X', 'PEMERIKSAAN RADIOLOGI X', '75.00', '30.00', '12.00', '0.00'),
('32', '2XO1', 'ABDOMEN (BERDIRI/BARING)', '80.00', '80.00', '80.00', '0.00'),
('33', '2XO10', 'BRONCHOGRAM', '140.00', '140.00', '140.00', '0.00'),
('34', '2XO11', 'KALKANIUM LATERAL-AXIAL', '40.00', '40.00', '40.00', '0.00'),
('35', '2XO12', 'TULANG CERVICAL(ANTERO.+LAT+2)', '60.00', '60.00', '60.00', '0.00'),
('36', '2XO13', 'TULANG CERVICAL(PRO. LATERAL)', '30.00', '30.00', '30.00', '0.00'),
('37', '2XO14', 'TULANG CERVICAL(ANTERO+LAT.)', '40.00', '40.00', '40.00', '0.00'),
('38', '2XO15', 'X-RAY DADA-2 PROJEKSI)', '50.00', '50.00', '50.00', '0.00'),
('39', '2XO16', 'X-RAY DADA-1 PROJEKSI', '40.00', '40.00', '40.00', '0.00'),
('4', '2B', 'PEMERIKSAAN RADIOLOGI B', '90.00', '25.00', '12.00', '0.00'),
('40', '2XO17', 'KLAVIKAL(ANTEROPOSTERIOR+APICA', '40.00', '40.00', '40.00', '0.00'),
('41', '2XO18', 'SISTOGRAM', '120.00', '120.00', '120.00', '0.00'),
('42', '2XO19', 'SENDI SIKU(ASTERO + LATERAL', '40.00', '40.00', '40.00', '0.00'),
('43', '2XO2', 'ABDOMEN UTK MENGESAN KEHAMILAN', '35.00', '35.00', '35.00', '0.00'),
('44', '2XO20', 'TULANG MUKA(PRO.OCC.MENTEL)', '40.00', '40.00', '40.00', '0.00'),
('45', '2XO21', 'TULANG PAHA(ANTERO + LATERAL)', '40.00', '40.00', '40.00', '0.00'),
('5', '2C', 'PEMERIKSAAN RADIOLOGI C', '90.00', '25.00', '12.00', '0.00'),
('6', '2CO', 'CT BRAIN/HEAD', '915.00', '915.00', '915.00', '0.00'),
('7', '2D', 'PEMERIKSAAN RADIOLOGI D', '75.00', '20.00', '10.00', '0.00'),
('8', '2E', 'PEMERIKSAAN RADIOLOGI E', '25.00', '6.00', '2.00', '0.00'),
('9', '2F', 'PEMERIKSAAN RADIOLOGI F', '90.00', '25.00', '12.00', '0.00');

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
  `max_age` int(11) DEFAULT NULL,
  `batch` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lookup_ward`
--

INSERT INTO `lookup_ward` (`ward_uid`, `ward_code`, `ward_name`, `sex`, `min_age`, `max_age`, `batch`) VALUES
('1', '01', 'FIRST CLASS WARD', 'C', 12, 150, NULL),
('10', '14', 'LABOUR WARD', 'P', 12, 150, NULL),
('11', '15', 'MATERNITY1', 'P', 12, 45, NULL),
('12', '16', 'GYNAE WARD', 'P', 12, 150, NULL),
('13', '17', 'EYE WARD', 'C', 0, 150, NULL),
('14', '18', 'A&E UNIT', 'C', 0, 150, NULL),
('15', '19', 'CTW', 'C', 0, 150, NULL),
('16', '1U', 'UROLOGY WARD', NULL, 0, 100, NULL),
('17', '1V', 'VASCULAR WARD', NULL, 0, 100, NULL),
('18', '20', 'FEMALE RTU', 'P', 12, 100, NULL),
('19', '21', 'MALE RTU', 'L', 12, 100, NULL),
('2', '02', 'FEMALE 2ND CLASS', 'P', 12, 150, NULL),
('20', '22', 'RADIOACTIVE WAD', NULL, 12, 150, NULL),
('21', '23', 'VIP', 'C', 12, 150, NULL),
('22', '24', 'MALE 2ND CLASS', 'L', 12, 150, NULL),
('23', '25', 'EMT WARD', 'C', 12, 150, NULL),
('24', '26', 'BURN UNIT', 'C', 0, 150, NULL),
('25', '27', 'AMBULATORY (RTU)', NULL, 0, 150, NULL),
('26', '28', 'CICU', 'C', 0, 90, NULL),
('27', '29', 'BABY MAT 1', 'C', 0, 1, NULL),
('28', '2A', 'PAED ONCOLOGY', NULL, 0, 100, NULL),
('29', '2B', 'PAED MEDICAL', NULL, 0, 70, NULL),
('3', '03', 'ICU WARD', 'C', 0, 150, NULL),
('30', '30', 'PAED ICU', NULL, 0, 70, NULL),
('31', '31', 'PAED MDU', 'C', 0, 12, NULL),
('32', '32', 'WAD ISC.KHAS', NULL, 0, 60, NULL),
('33', '33', 'INF DISEASE WAD', NULL, 12, 100, NULL),
('34', '34', 'PCW (RTU)', NULL, 0, 100, NULL),
('35', '35', 'MED. ISO SEMEMTARA', NULL, 0, 100, NULL),
('36', '36', 'F/ORT. WARD', 'P', 12, 100, NULL),
('37', '37', 'NEUROSX WD', NULL, 0, 100, NULL),
('38', '38', 'NEURO KDU', NULL, 1, 9, NULL),
('39', '3A', 'PAED ISO', NULL, 0, 100, NULL),
('4', '04', 'FEMALE MEDICAL', 'P', 12, 150, NULL),
('40', '3B', 'CTW', NULL, 12, 100, NULL),
('41', '42', 'CSSD', NULL, 0, 0, NULL),
('42', '45', 'FARMASI SATELLITE', NULL, 0, 0, NULL),
('43', '46', 'FARMASI OPD', NULL, 0, 0, NULL),
('44', '47', 'FARMASI WARD SUPPLY', NULL, 0, 0, NULL),
('45', '48', 'FARMASI INJECTION', NULL, 0, 0, NULL),
('5', '05', 'MALE MEDICAL', 'L', 12, 100, NULL),
('6', '09', 'NURSERY WARD', 'C', 0, 0, NULL),
('7', '10', 'MALE SURGICAL', 'L', 12, 150, NULL),
('8', '11', 'FEMALE SURGICAL', 'P', 12, 150, NULL),
('9', '13', 'M/CRT.WARD', 'L', 12, 150, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `new_user`
--

CREATE TABLE `new_user` (
  `user_uid` varchar(64) NOT NULL,
  `username` varchar(100) NOT NULL,
  `user_password` varchar(64) NOT NULL,
  `role_cashier` tinyint(1) NOT NULL DEFAULT 0,
  `role_clerk` tinyint(1) NOT NULL DEFAULT 0,
  `role_admin` tinyint(1) NOT NULL DEFAULT 0,
  `role_guest_print` tinyint(1) NOT NULL,
  `Case_Note` varchar(64) DEFAULT NULL,
  `Registration` varchar(64) DEFAULT NULL,
  `Charge_Sheet` varchar(64) DEFAULT NULL,
  `Sticker_Label` varchar(64) DEFAULT NULL,
  `retire` tinyint(1) NOT NULL DEFAULT 0,
  `authKey` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `new_user`
--

INSERT INTO `new_user` (`user_uid`, `username`, `user_password`, `role_cashier`, `role_clerk`, `role_admin`, `role_guest_print`,`Case_Note`,`Registration`,`Charge_Sheet`,`Sticker_Label`, `retire`, `authKey`) VALUES
('011BJIjHHpoDWrsDWRyk_dkHc2GUwDBG', 'administrator1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 1, 0,NULL,NULL,NULL,NULL, 0, '12345b'),
('2wHPf777EC532SCrMDSR47dTw4nRqx2V', 'cashier1', '7b9efcfad5bc24b82b5acbe6175842f2', 1, 0, 0, 0,NULL,NULL,NULL,NULL, 0, '12345a'),
('3BUf9deDPpjBuaD7YO3_7vPrmxE4THBo', 'clerk1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 1, 0, 0,NULL,NULL,NULL,NULL, 0, '12345c'),
('iwJ4pQTEP0chTyfqzfr8KvpSo7XlMQ3S', 'guest_print1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 0, 1,NULL,NULL,NULL,NULL, 0, 'pyLoI1aXGp7sAq72FW-D5u9RxSxub71p');


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
  `medical_legal_code` tinyint(1) DEFAULT 0,
  `reminder_given` int(11) NOT NULL DEFAULT 0,
  `guarantor_name` varchar(200) DEFAULT NULL,
  `guarantor_nric` varchar(20) DEFAULT NULL,
  `guarantor_phone_number` varchar(100) DEFAULT NULL,
  `guarantor_email` varchar(100) DEFAULT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `race` varchar(20) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `job` varchar(20) DEFAULT NULL,
  `DOB` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `nok_email` varchar(100) DEFAULT NULL,
  `nok_address1` varchar(100) DEFAULT NULL,
  `nok_address2` varchar(100) DEFAULT NULL,
  `nok_address3` varchar(100) DEFAULT NULL,
  `nok_datetime_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `receipt_content_datetime_paid` datetime NOT NULL,
  `receipt_content_payer_name` varchar(200) NOT NULL,
  `receipt_content_payment_method` varchar(20) NOT NULL,
  `payment_method_number` varchar(30) DEFAULT NULL,
  `receipt_responsible` varchar(64) NOT NULL,
  `receipt_serial_number` varchar(20) DEFAULT NULL,
  `kod_akaun` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `serial_number`
--

CREATE TABLE `serial_number` (
  `serial_name` varchar(11) NOT NULL,
  `prepend` varchar(11) NOT NULL,
  `digit_length` int(8) NOT NULL,
  `running_value` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `serial_number`
--

INSERT INTO `serial_number` (`serial_name`, `prepend`, `digit_length`, `running_value`) VALUES
('bill', 'B', 6, 0),
('receipt', 'R', 6, 0);

-- --------------------------------------------------------
--
-- Table structure for table `reminder_letter`
--
CREATE TABLE `reminder_letter` (
  `batch_uid` varchar(64) NOT NULL,
  `batch_datetime` datetime NOT NULL,
  `reminder1` datetime DEFAULT NULL,
  `reminder2` datetime DEFAULT NULL,
  `reminder3` datetime DEFAULT NULL,
  `responsible` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `ward_number_of_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`bill_uid`),
  ADD KEY `rn` (`rn`);

--
-- Indexes for table `cancellation`
--
ALTER TABLE `cancellation`
  ADD PRIMARY KEY (`cancellation_uid`);

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
  ADD PRIMARY KEY (`lookup_general_uid`);

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
-- Indexes for table `new_user`
--
ALTER TABLE `new_user`
  ADD PRIMARY KEY (`user_uid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_uid` (`user_uid`);

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
  ADD PRIMARY KEY (`patient_uid`);

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
-- Indexes for table `reminder_letter`
--
ALTER TABLE `reminder_letter`
  ADD PRIMARY KEY (`batch_uid`);


--
-- Indexes for table `serial_number`
--
ALTER TABLE `serial_number`
  ADD PRIMARY KEY (`serial_name`);

--
-- Indexes for table `treatment_details`
--
ALTER TABLE `treatment_details`
  ADD PRIMARY KEY (`treatment_details_uid`),
  ADD KEY `bill_uid` (`bill_uid`);

--
-- Indexes for table `ward`
--
ALTER TABLE `ward`
  ADD PRIMARY KEY (`ward_uid`),
  ADD KEY `bill_uid` (`bill_uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batch`
--
ALTER TABLE `batch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`rn`) REFERENCES `patient_admission` (`rn`);

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
