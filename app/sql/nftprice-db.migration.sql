-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: nftprice-db
-- Generation Time: Mar 20, 2023 at 09:18 PM
-- Server version: 8.0.27
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nftprice`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerts`
--

CREATE TABLE `alerts` (
  `id_alert` int NOT NULL,
  `floor_price` bigint DEFAULT NULL,
  `symbol` varchar(45) DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `fk_username` varchar(45) DEFAULT NULL,
  `active` tinyint NOT NULL,
  `currency` varchar(20) DEFAULT NULL,
  `token_traits` varchar(300) DEFAULT NULL,
  `compare` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `symbol` varchar(120) NOT NULL,
  `name` varchar(300) NOT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `image` varchar(45) DEFAULT NULL,
  `listedCount` int DEFAULT NULL,
  `totalVolume` bigint DEFAULT NULL,
  `avgPrice24hr` bigint DEFAULT NULL,
  `lastUpdate` datetime DEFAULT NULL,
  `howrare_symbol` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `currency` varchar(45) NOT NULL,
  `value` decimal(10,10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `floorPriceView`
-- (See below for the actual view)
--
CREATE TABLE `floorPriceView` (
`symbol` varchar(120)
,`floorPrice` bigint
);

-- --------------------------------------------------------

--
-- Table structure for table `listed_tokens`
--

CREATE TABLE `listed_tokens` (
  `token_id` varchar(120) NOT NULL,
  `fk_symbol_listed` varchar(120) DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `owner` varchar(45) DEFAULT NULL,
  `price` bigint DEFAULT NULL,
  `rarity` int DEFAULT NULL,
  `listed` tinyint DEFAULT NULL,
  `token_traits` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio`
--

CREATE TABLE `portfolio` (
  `id_portfolio` int NOT NULL,
  `symbol` varchar(45) NOT NULL,
  `purchase_price` decimal(7,2) DEFAULT NULL,
  `currency` varchar(20) NOT NULL,
  `amount_owned` int DEFAULT NULL,
  `dollars_per_coin` decimal(5,2) DEFAULT NULL,
  `fk_username` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trait_types`
--

CREATE TABLE `trait_types` (
  `id_trait` varchar(165) NOT NULL,
  `trait_type` varchar(45) DEFAULT NULL,
  `fk_symbol` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trait_values`
--

CREATE TABLE `trait_values` (
  `value_id` varchar(200) NOT NULL,
  `fk_trait_type` varchar(165) DEFAULT NULL,
  `value` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(45) NOT NULL,
  `password` varchar(250) NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `telegram_id` varchar(45) DEFAULT NULL,
  `role` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure for view `floorPriceView`
--
DROP TABLE IF EXISTS `floorPriceView`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `floorPriceView`  AS SELECT `listed_tokens`.`fk_symbol_listed` AS `symbol`, min(`listed_tokens`.`price`) AS `floorPrice` FROM `listed_tokens` WHERE (`listed_tokens`.`listed` = 1) GROUP BY `listed_tokens`.`fk_symbol_listed` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id_alert`),
  ADD KEY `fk_alerts_username_idx` (`fk_username`),
  ADD KEY `fk_symbol_alerts_idx` (`symbol`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`symbol`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`currency`);

--
-- Indexes for table `listed_tokens`
--
ALTER TABLE `listed_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD KEY `fk_symbol_idx` (`fk_symbol_listed`),
  ADD KEY `fk_symbol_idxq` (`fk_symbol_listed`);

--
-- Indexes for table `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id_portfolio`),
  ADD KEY `fk_username_idx` (`fk_username`);

--
-- Indexes for table `trait_types`
--
ALTER TABLE `trait_types`
  ADD PRIMARY KEY (`id_trait`),
  ADD KEY `fk_symbol_idx` (`fk_symbol`);

--
-- Indexes for table `trait_values`
--
ALTER TABLE `trait_values`
  ADD PRIMARY KEY (`value_id`),
  ADD KEY `fk_trait_type_idx` (`fk_trait_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id_alert` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id_portfolio` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `fk_alerts_username` FOREIGN KEY (`fk_username`) REFERENCES `users` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_symbol_alerts` FOREIGN KEY (`symbol`) REFERENCES `collections` (`symbol`);

--
-- Constraints for table `listed_tokens`
--
ALTER TABLE `listed_tokens`
  ADD CONSTRAINT `fk_symbol_listed` FOREIGN KEY (`fk_symbol_listed`) REFERENCES `collections` (`symbol`);

--
-- Constraints for table `portfolio`
--
ALTER TABLE `portfolio`
  ADD CONSTRAINT `fk_username` FOREIGN KEY (`fk_username`) REFERENCES `users` (`username`);

--
-- Constraints for table `trait_types`
--
ALTER TABLE `trait_types`
  ADD CONSTRAINT `fk_symbol` FOREIGN KEY (`fk_symbol`) REFERENCES `collections` (`symbol`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `trait_values`
--
ALTER TABLE `trait_values`
  ADD CONSTRAINT `fk_trait_type` FOREIGN KEY (`fk_trait_type`) REFERENCES `trait_types` (`id_trait`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
