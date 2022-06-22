-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2022 at 04:54 AM
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
-- Database: `dbhis`
--

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
  `retire` tinyint(1) DEFAULT 0,
  `authKey` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `new_user`
--

INSERT INTO `new_user` (`user_uid`, `username`, `user_password`, `role_cashier`, `role_clerk`, `role_admin`, `retire`, `authKey`) VALUES
('011BJIjHHpoDWrsDWRyk_dkHc2GUwDBG', 'administrator1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 0, 1, '12345b'),
('2wHPf777EC532SCrMDSR47dTw4nRqx2V', 'cashier1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 0, 1, '12345a'),
('3BUf9deDPpjBuaD7YO3_7vPrmxE4THBo', 'clerk1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 0, 1, '12345c');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `new_user`
--
ALTER TABLE `new_user`
  ADD PRIMARY KEY (`user_uid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_uid` (`user_uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
