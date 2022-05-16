-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2022 at 06:03 PM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lookup_general`
--
ALTER TABLE `lookup_general`
  ADD PRIMARY KEY (`lookup_general_uid`),
  ADD UNIQUE KEY `code` (`code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
