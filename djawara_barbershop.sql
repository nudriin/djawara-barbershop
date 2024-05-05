-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2024 at 11:14 PM
-- Server version: 8.1.0
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `djawara_barbershop`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCategory` (`cat_id` INT)   BEGIN
    DELETE FROM schedules WHERE category_id = cat_id;
    DELETE FROM order_details WHERE category_id = cat_id;
    DELETE FROM categories WHERE id = cat_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteSchedules` (`sch_id` INT)   BEGIN 
	DELETE FROM orders WHERE schedule_id = sch_id;
	DELETE FROM schedules WHERE id = sch_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_kapster` (`kap_id` INT)   BEGIN 
    DELETE FROM orders WHERE schedule_id IN (SELECT id FROM schedules WHERE kapster_id = kap_id);
	DELETE FROM schedules WHERE kapster_id = kap_id;
	DELETE FROM kapsters WHERE id = kap_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `insertOrders` (`acc_id` INT, `sch_id` INT)   BEGIN
    INSERT INTO orders (account_id, schedule_id, total_price)
    VALUES (acc_id, sch_id, (SELECT cat.price FROM categories AS cat JOIN schedules ON (cat.id = schedules.category_id) WHERE schedules.id = sch_id));
    UPDATE schedules
	SET status = "BOOKED"
	WHERE id = sch_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('USER','ADMIN','OWNER') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USER',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_pic` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `email`, `name`, `phone`, `role`, `address`, `profile_pic`) VALUES
(1, 'nudriin', '$2y$10$d7GOxiFNe56jmipkwI98W.6IUnkP8.we5/jp6m9c3LqIfnUJuYOwe', 'nudriin@gmai.com', 'Nurdin', '081549193834', 'OWNER', 'JL. Pangeran Samudera', 'https://firebasestorage.googleapis.com/v0/b/mern-auth-5a53c.appspot.com/o/profile.svg?alt=media&token=37afdff7-242d-4f97-9062-677c7cdd898d'),
(2, 'scn.zin', '$2y$10$j/w7cKGxQaNiPEWrb7UyZ.anrqsoQJpYwWT.eeN/yhgZP3vcxIfzC', 'mrsunnysummer@gmail.com', 'Nurdin', '081549193839', 'USER', 'JL. Pangeran Samudera', 'https://firebasestorage.googleapis.com/v0/b/mern-auth-5a53c.appspot.com/o/profile.svg?alt=media&token=37afdff7-242d-4f97-9062-677c7cdd898d');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `price`) VALUES
(11, 'Hair Color', 120000),
(12, 'Haircut ', 20000),
(13, 'Washing', 25000),
(14, 'Kids', 15000);

-- --------------------------------------------------------

--
-- Stand-in structure for view `getallorders`
-- (See below for the actual view)
--
CREATE TABLE `getallorders` (
`id` int
,`account_id` int
,`schedule_id` int
,`order_date` timestamp
,`status` enum('PENDING','CONFIRMED','COMPLETED','CANCELED')
,`account_name` varchar(255)
,`account_email` varchar(255)
,`account_phone` varchar(255)
,`kapster_name` varchar(255)
,`category_name` varchar(255)
,`total_price` int
,`schedule_date` varchar(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `getallschedules`
-- (See below for the actual view)
--
CREATE TABLE `getallschedules` (
`schedule_id` int
,`kapster_id` int
,`category_id` int
,`kapster_name` varchar(255)
,`category_name` varchar(255)
,`category_price` int
,`status` enum('AVAILABLE','BOOKED')
,`dates` date
,`times` time
);

-- --------------------------------------------------------

--
-- Table structure for table `kapsters`
--

CREATE TABLE `kapsters` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_pic` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kapsters`
--

INSERT INTO `kapsters` (`id`, `name`, `phone`, `profile_pic`) VALUES
(10, 'Nurdin', '081549193834', 'https://kfbgqdcxemiokdktlykv.supabase.co/storage/v1/object/public/nudriin/1713953096973Screenshot_20220213-125905944-01-01.jpeg'),
(12, 'Extheriouz', '1151', 'https://kfbgqdcxemiokdktlykv.supabase.co/storage/v1/object/public/nudriin/1713980066045IMG_3519-01_1.jpeg'),
(13, 'Zin', '081549193834', 'https://kfbgqdcxemiokdktlykv.supabase.co/storage/v1/object/public/nudriin/1714321658067Remini20220616115908963.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `account_id` int NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('PENDING','CONFIRMED','COMPLETED','CANCELED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING',
  `schedule_id` int NOT NULL,
  `total_price` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `account_id`, `order_date`, `status`, `schedule_id`, `total_price`) VALUES
(21, 2, '2024-04-30 18:41:34', 'CONFIRMED', 10, 120000),
(22, 2, '2024-04-30 18:43:42', 'PENDING', 14, 20000);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int NOT NULL,
  `kapster_id` int NOT NULL,
  `category_id` int NOT NULL,
  `status` enum('AVAILABLE','BOOKED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AVAILABLE',
  `dates` date NOT NULL,
  `times` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `kapster_id`, `category_id`, `status`, `dates`, `times`) VALUES
(10, 10, 11, 'BOOKED', '2024-04-27', '20:00:00'),
(11, 12, 12, 'AVAILABLE', '2024-04-27', '20:00:00'),
(14, 13, 12, 'BOOKED', '2024-05-01', '18:40:00'),
(16, 10, 11, 'AVAILABLE', '2024-05-03', '06:30:00'),
(17, 13, 13, 'AVAILABLE', '2024-05-01', '17:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `_prisma_migrations`
--

CREATE TABLE `_prisma_migrations` (
  `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `finished_at` datetime(3) DEFAULT NULL,
  `migration_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logs` text COLLATE utf8mb4_unicode_ci,
  `rolled_back_at` datetime(3) DEFAULT NULL,
  `started_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `applied_steps_count` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `_prisma_migrations`
--

INSERT INTO `_prisma_migrations` (`id`, `checksum`, `finished_at`, `migration_name`, `logs`, `rolled_back_at`, `started_at`, `applied_steps_count`) VALUES
('02b8c605-96e0-485c-b1a4-6ef162cd9f0a', '72f0e270e2d4e9a21058f23b360ba44f2dc12fd7072a0582d483997711b6676a', '2024-03-11 08:54:16.742', '20240223164403_djawara_1', NULL, NULL, '2024-03-11 08:54:16.487', 1),
('05ca598d-0625-4dee-8b4f-37997d125e65', '7f3a117198c0f0f155d41eba14de8907527570d163b2a40f7e6bf46a483bae2c', '2024-03-11 08:54:26.825', '20240311085426_djawara_4', NULL, NULL, '2024-03-11 08:54:26.806', 1),
('418509ca-fb8b-4861-8af6-56eb0d0dba3e', '7f3a117198c0f0f155d41eba14de8907527570d163b2a40f7e6bf46a483bae2c', '2024-03-11 08:54:16.868', '20240223165637_djawara_3', NULL, NULL, '2024-03-11 08:54:16.850', 1),
('6364cae9-bfdd-4dd5-ad7a-49e2cd70b756', '393783dedcb4912f01fcd2d3eb55f0541423c2fde9129fff40be4eae22cf07d9', '2024-03-11 08:54:16.848', '20240223165457_djawara_2', NULL, NULL, '2024-03-11 08:54:16.744', 1),
('63f23935-f79a-4d75-b18f-e5c546083914', '1432bda38602a8eab33b2912175cd9ce408d713fe5b28e21ab5a06a1edc11823', '2024-04-05 09:21:52.277', '20240405092104_djawara_7', NULL, NULL, '2024-04-05 09:21:52.219', 1),
('681a1cc1-6e26-41b4-8fe0-8f13dfe00830', '94da87ba87c3fb638d606052cc898b6dc01c0808e4748418a1b23247e1bb96b2', '2024-04-05 09:19:50.773', '20240405091857_djawara_6', NULL, NULL, '2024-04-05 09:19:50.702', 1),
('e60c4fad-4013-45f9-85ee-535373563442', 'eff0f1e34b25b32bba86c2658fb15cab545ef528be4ac9b027db3e6d9a5bd2fd', '2024-03-11 08:55:37.810', '20240311085537_djawara_5', NULL, NULL, '2024-03-11 08:55:37.435', 1);

-- --------------------------------------------------------

--
-- Structure for view `getallorders`
--
DROP TABLE IF EXISTS `getallorders`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `getallorders`  AS SELECT `ords`.`id` AS `id`, `ords`.`account_id` AS `account_id`, `ords`.`schedule_id` AS `schedule_id`, `ords`.`order_date` AS `order_date`, `ords`.`status` AS `status`, `acc`.`name` AS `account_name`, `acc`.`email` AS `account_email`, `acc`.`phone` AS `account_phone`, `kap`.`name` AS `kapster_name`, `cat`.`name` AS `category_name`, `cat`.`price` AS `total_price`, concat(`sch`.`dates`,' ',`sch`.`times`) AS `schedule_date` FROM ((((`orders` `ords` join `schedules` `sch` on((`ords`.`schedule_id` = `sch`.`id`))) join `kapsters` `kap` on((`sch`.`kapster_id` = `kap`.`id`))) join `categories` `cat` on((`sch`.`category_id` = `cat`.`id`))) join `accounts` `acc` on((`ords`.`account_id` = `acc`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `getallschedules`
--
DROP TABLE IF EXISTS `getallschedules`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `getallschedules`  AS SELECT `sch`.`id` AS `schedule_id`, `sch`.`kapster_id` AS `kapster_id`, `sch`.`category_id` AS `category_id`, `kap`.`name` AS `kapster_name`, `cat`.`name` AS `category_name`, `cat`.`price` AS `category_price`, `sch`.`status` AS `status`, `sch`.`dates` AS `dates`, `sch`.`times` AS `times` FROM ((`kapsters` `kap` join `schedules` `sch` on((`kap`.`id` = `sch`.`kapster_id`))) join `categories` `cat` on((`cat`.`id` = `sch`.`category_id`))) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kapsters`
--
ALTER TABLE `kapsters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_account_id_fkey` (`account_id`),
  ADD KEY `FK_schedule_order` (`schedule_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_fkey` (`order_id`),
  ADD KEY `order_details_category_id_fkey` (`category_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_category_id_fkey` (`category_id`),
  ADD KEY `schedules_kapster_id_fkey` (`kapster_id`);

--
-- Indexes for table `_prisma_migrations`
--
ALTER TABLE `_prisma_migrations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `kapsters`
--
ALTER TABLE `kapsters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `FK_schedule_order` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`),
  ADD CONSTRAINT `orders_account_id_fkey` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_category_id_fkey` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `order_details_order_id_fkey` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_category_id_fkey` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_kapster_id_fkey` FOREIGN KEY (`kapster_id`) REFERENCES `kapsters` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
