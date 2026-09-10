-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2026 at 12:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `anchorline`
--

-- --------------------------------------------------------

--
-- Table structure for table `consignments`
--

CREATE TABLE `consignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `waybill` varchar(20) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `origin` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `service_type` enum('road','warehouse','cold','international') NOT NULL,
  `status` enum('booked','collected','in_transit','held','delivered') NOT NULL DEFAULT 'booked',
  `eta` varchar(60) DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consignments`
--

INSERT INTO `consignments` (`id`, `waybill`, `sender_name`, `receiver_name`, `origin`, `destination`, `service_type`, `status`, `eta`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'ANC-4471-QLD', 'Kettle & Co Wholesale', 'Eagle Farm Distribution', 'Port Botany, NSW', 'Eagle Farm, QLD', 'road', 'in_transit', '19 Jul 2026, 14:00 AEST', 1, '2026-09-01 01:14:31', '2026-09-01 01:14:31'),
(2, 'ANC-7726-VIC', 'Harbourfield Foods', 'Dandenong Cold Store', 'Alexandria, NSW', 'Dandenong South, VIC', 'cold', 'delivered', 'Delivered 15 Jul 2026, 11:22 AEST', 1, '2026-09-01 01:14:31', '2026-09-01 01:14:31'),
(3, 'ANC-1039-WA', 'Anchorline Sea Freight', 'Fremantle Importers', 'Port Botany, NSW', 'Fremantle, WA', 'international', 'held', 'Awaiting customs release', 1, '2026-09-01 01:14:31', '2026-09-01 01:14:31');

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service` enum('road','warehouse','cold','international','other') NOT NULL,
  `message` text NOT NULL,
  `consent` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('new','contacted','closed') NOT NULL DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enquiries`
--

INSERT INTO `enquiries` (`id`, `full_name`, `email`, `phone`, `service`, `message`, `consent`, `user_id`, `status`, `created_at`) VALUES
(1, 'Test Customer', 'testcustomer@example.com', '0412345678', 'road', 'Need 4 pallets moved from Port Botany to Eagle Farm QLD by next Friday, standard road service please.', 1, 3, 'new', '2026-09-01 01:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `scan_events`
--

CREATE TABLE `scan_events` (
  `id` int(10) UNSIGNED NOT NULL,
  `consignment_id` int(10) UNSIGNED NOT NULL,
  `event_text` varchar(255) NOT NULL,
  `location` varchar(100) NOT NULL,
  `scanned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scan_events`
--

INSERT INTO `scan_events` (`id`, `consignment_id`, `event_text`, `location`, `scanned_at`, `created_by`) VALUES
(1, 1, 'Collected from consignor', 'Port Botany, NSW', '2026-07-15 22:12:00', 2),
(2, 1, 'Scanned into sortation hub', 'Chullora, NSW', '2026-07-16 09:40:00', 2),
(3, 1, 'Departed on line-haul B214', 'Chullora, NSW', '2026-07-16 20:05:00', 2),
(4, 1, 'Arrived changeover depot', 'Coffs Harbour, NSW', '2026-07-17 19:30:00', 2),
(5, 2, 'Collected from consignor', 'Alexandria, NSW', '2026-07-13 21:50:00', 2),
(6, 2, 'Temperature check passed at 4.1C', 'Alexandria, NSW', '2026-07-14 05:10:00', 2),
(7, 2, 'Arrived at destination depot', 'Dandenong South, VIC', '2026-07-14 20:44:00', 2),
(8, 2, 'Delivered, signed by R. Okafor', 'Dandenong South, VIC', '2026-07-15 01:22:00', 2),
(9, 3, 'Container loaded, vessel MV Corella', 'Port Botany, NSW', '2026-07-01 23:00:00', 2),
(10, 3, 'Vessel berthed', 'Fremantle, WA', '2026-07-11 06:30:00', 2),
(11, 3, 'Held for customs inspection', 'Fremantle, WA', '2026-07-12 00:15:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','member','normal') NOT NULL DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Site Admin', 'admin@anchorline.example', '$2y$10$5vxtXbNSdMRxgvhFwrPt3OtMSy8zDMSGibEyVwXqXfyqbUtpylR1S', 'admin', '2026-09-01 01:14:31'),
(2, 'Depot Staff', 'member@anchorline.example', '$2y$10$k6vcVA3b1XBHTAl.5v4rbeoD9WwFUm.BY77JJLhrMO4ycIlwX0r1W', 'member', '2026-09-01 01:14:31'),
(3, 'Test Customer', 'testcustomer@example.com', '$2y$10$mD48My9QyVnmsOHdrW0JwO8Lyt5jTC7nW7Vft29yms5G2OrXHybD.', 'normal', '2026-09-01 01:15:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consignments`
--
ALTER TABLE `consignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `waybill` (`waybill`),
  ADD KEY `fk_consignments_created_by` (`created_by`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enquiries_user` (`user_id`);

--
-- Indexes for table `scan_events`
--
ALTER TABLE `scan_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_scan_events_consignment` (`consignment_id`),
  ADD KEY `fk_scan_events_created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consignments`
--
ALTER TABLE `consignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scan_events`
--
ALTER TABLE `scan_events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consignments`
--
ALTER TABLE `consignments`
  ADD CONSTRAINT `fk_consignments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD CONSTRAINT `fk_enquiries_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `scan_events`
--
ALTER TABLE `scan_events`
  ADD CONSTRAINT `fk_scan_events_consignment` FOREIGN KEY (`consignment_id`) REFERENCES `consignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_scan_events_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
