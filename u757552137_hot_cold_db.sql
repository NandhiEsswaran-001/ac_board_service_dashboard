-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 17, 2026 at 03:06 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u757552137_hot_cold_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `board_services`
--

CREATE TABLE `board_services` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `ac_brand` varchar(100) DEFAULT NULL,
  `ac_model` varchar(100) DEFAULT NULL,
  `problem` text DEFAULT NULL,
  `customer_remarks` text DEFAULT NULL,
  `remark_checks` text DEFAULT NULL,
  `parts_inside` text DEFAULT NULL,
  `approx_amount` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) DEFAULT 0.00,
  `parts_replaced` text DEFAULT NULL,
  `status` enum('Pending','In Process','Completed','Delivered','Return') DEFAULT 'Pending',
  `payment_status` enum('Pending','Paid','Partial') DEFAULT 'Pending',
  `payment_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `board_services`
--

INSERT INTO `board_services` (`id`, `customer_name`, `phone`, `address`, `ac_brand`, `ac_model`, `problem`, `customer_remarks`, `remark_checks`, `parts_inside`, `approx_amount`, `final_amount`, `parts_replaced`, `status`, `payment_status`, `payment_amount`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Nandhi', '7418617369', 'No. 12, Anna Nagar 3rd Street,\r\nAnna Nagar East,\r\nChennai – 600102, Tamil Nadu', 'LG', '1098', 'Heating Issue\r\nQuickly the machine is getting hot', '', 'EEV', '', 1200.00, 1400.00, '', 'Completed', 'Paid', 0.00, '', 1, '2026-03-26 13:41:31', '2026-03-26 13:42:50'),
(2, 'Kamala', '0987654321', 'Gandhi Puram\r\nFood Street 2\r\nRamdoor complex, Door No: 30\r\nCoimbatore, TN', 'Voltas', '9876PBN78001', '', '', '', '', 2000.00, 0.00, NULL, 'Pending', 'Pending', 0.00, NULL, 3, '2026-03-26 14:07:19', '2026-03-26 14:07:19'),
(3, 'kumar', '9677317513', '', '', '', '', '', 'Display PCB', '', 1500.00, 0.00, NULL, 'Pending', 'Pending', 0.00, NULL, 1, '2026-03-27 05:44:59', '2026-03-27 05:44:59'),
(4, 'Kumar', '9677317513', '123gandipuran', 'Lg', '124', '', '', 'Display PCB, Room Sensor-ID, Coil Sensor-OD', '', 1000.00, 0.00, '', 'Return', 'Pending', 0.00, '', 1, '2026-03-27 07:34:30', '2026-04-14 11:19:01'),
(5, 'Elvin engineering', '9842202252', 'Sidhapudhur \r\nCoimbatore', 'Voltas', 'MITE50AB-VT 3STAR/MIV50VT-W3S', 'Outdoor not working', 'Outdoor not working', 'Indoor PCB, Outdoor PCB, Display PCB, Dis Sensor-OD, Room Sensor-ID, Coil Sensor-OD, Back Cover-OD, Swing MTR, Coil Sensor-ID, AMB Sensor-OD', 'All parts', 1500.00, 0.00, '', 'Delivered', 'Pending', 0.00, '', 1, '2026-03-30 08:12:11', '2026-04-04 02:37:26'),
(6, 'Murugesan', '9940713900', 'Somayampalayam\r\nVadavalli', 'Daikin', 'Ftkl indoor', 'Not working', 'Not working', 'Indoor PCB, Display PCB, Room Sensor-ID, Back Cover-OD, Coil Sensor-ID', '', 500.00, 0.00, '', 'Delivered', 'Paid', 0.00, '', 1, '2026-03-30 12:19:11', '2026-03-30 12:20:28'),
(7, 'Suresh', '6380109621', 'Kannapanagar \r\nCoimbatore', 'Daikin', 'Ftkl', 'Fan motor complaint', 'Fan motor not working', 'Indoor PCB, Room Sensor-ID, Coil Sensor-OD', '', 250.00, 0.00, '', 'Delivered', 'Paid', 0.00, '', 1, '2026-03-30 12:44:22', '2026-04-04 02:38:06'),
(8, 'Kumar', '9942116822', 'Vk engineering \r\nGandhipuram', 'Panasonic', 'H16c0', 'Compressor suddenly off', 'Compressor suddenly off', 'Indoor PCB, Outdoor PCB, Display PCB, Dis Sensor-OD, Room Sensor-ID, Coil Sensor-OD, Back Cover-OD, Coil Sensor-ID, AMB Sensor-OD', 'All', 1500.00, 0.00, NULL, 'Pending', 'Pending', 0.00, NULL, 1, '2026-04-02 06:05:55', '2026-04-02 06:05:55'),
(9, 'Kumar', '9942116822', 'Vk engineering \r\nGandhipuram', 'Haier', 'F1 error', 'Compressor not working', '', 'Indoor PCB, Outdoor PCB, Display PCB, Dis Sensor-OD, Room Sensor-ID, Coil Sensor-OD, Coil Sensor-ID, AMB Sensor-OD', '', 1500.00, 0.00, '', 'Completed', 'Pending', 0.00, 'Pcb problem rectified', 1, '2026-04-02 06:07:48', '2026-04-02 08:41:04'),
(10, 'Kumar', '9942116822', 'Saravanampatti', 'Samsung', 'Forter', 'Dead pcb', 'Dead pcb', 'Indoor PCB, Display PCB, Room Sensor-ID, Coil Sensor-ID', '', 500.00, 0.00, '', 'Completed', 'Pending', 0.00, '', 1, '2026-04-07 07:34:32', '2026-04-07 07:35:15'),
(11, 'Ravi thomas', '9790555422', 'Arul Ac service \r\nHope college \r\nCoimbatore', 'Lg', 'EBR74149620', 'CH44 error', 'Error CH44', 'Compressor Jack, Indoor PCB, Outdoor PCB, Display PCB, Dis Sensor-OD, Room Sensor-ID, Coil Sensor-OD, Coil Sensor-ID, AMB Sensor-OD', 'Ok', 1500.00, 0.00, '', 'Delivered', 'Pending', 0.00, '', 1, '2026-04-07 10:48:26', '2026-04-15 07:33:17'),
(12, 'Hidhayuthulla', '7502950667', 'Karumbukadai \r\nCoimbatore', 'Voltas', 'El00', 'E1 error', 'Communication problem', 'Indoor PCB, Outdoor PCB, Display PCB, Room Sensor-ID, Back Cover-OD, Coil Sensor-ID, Transformer', '', 1500.00, 0.00, '', 'Delivered', 'Paid', 0.00, '', 1, '2026-04-07 11:44:46', '2026-04-14 10:24:04'),
(13, 'Alwar', '8190935314', 'Arun ac service \r\nKannappa nagar \r\nCoimbatore', 'Daikin', 'Ex13025-48(D)', 'Compressor not working', 'Compressor not working', 'Indoor PCB, Outdoor PCB, Display PCB, Dis Sensor-OD, Room Sensor-ID, Coil Sensor-OD, Back Cover-OD, Coil Sensor-ID, AMB Sensor-OD', '', 1500.00, 0.00, '', 'Delivered', 'Paid', 0.00, '', 1, '2026-04-07 11:48:36', '2026-04-14 10:24:23'),
(14, 'Abdul Hakkim', '9092909307', 'Karumbukadai \r\nCoimbatore', 'Onida', 'Nil', 'Pcb dead', 'Pcb dead', 'Indoor PCB, Display PCB, Room Sensor-ID, Coil Sensor-ID', '', 600.00, 0.00, '', 'Delivered', 'Paid', 0.00, '', 1, '2026-04-13 06:18:23', '2026-04-14 10:23:33'),
(15, 'Muthuraj', '9360599643', 'Avarampalayam \r\nCoimbatore', 'Daikin', 'Ex13025-48(D)', 'Compressor suddenly off', 'Compressor suddenly off', 'Outdoor PCB, Back Cover-OD', '', 1500.00, 0.00, '', 'Completed', 'Pending', 0.00, '', 1, '2026-04-15 07:31:15', '2026-04-15 07:32:52');

-- --------------------------------------------------------

--
-- Table structure for table `field_services`
--

CREATE TABLE `field_services` (
  `id` int(11) NOT NULL,
  `service_report_no` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `map_link` text DEFAULT NULL,
  `service_date` date DEFAULT NULL,
  `assigned_employee` int(11) DEFAULT NULL,
  `ac_type` varchar(100) DEFAULT NULL,
  `product_company` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `unit_location` varchar(150) DEFAULT NULL,
  `problem` text DEFAULT NULL,
  `work_done` text DEFAULT NULL,
  `parts_used` text DEFAULT NULL,
  `service_charge` enum('Yes','No') DEFAULT 'No',
  `service_call_items` text DEFAULT NULL,
  `ampere` varchar(30) DEFAULT NULL,
  `voltage` varchar(30) DEFAULT NULL,
  `grill_temp` varchar(30) DEFAULT NULL,
  `sd_pressure` varchar(30) DEFAULT NULL,
  `warranty_text` varchar(100) DEFAULT NULL,
  `service_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('Pending','Paid','Partial') DEFAULT 'Pending',
  `payment_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('Scheduled','In Progress','Completed') DEFAULT 'Scheduled',
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `field_services`
--

INSERT INTO `field_services` (`id`, `service_report_no`, `customer_name`, `phone`, `address`, `map_link`, `service_date`, `assigned_employee`, `ac_type`, `product_company`, `purchase_date`, `unit_location`, `problem`, `work_done`, `parts_used`, `service_charge`, `service_call_items`, `ampere`, `voltage`, `grill_temp`, `sd_pressure`, `warranty_text`, `service_amount`, `payment_status`, `payment_amount`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'John Kitty', '7418617369', '22, Salem Bypass Road,\r\nFairlands,\r\nSalem – 636016, Tamil Nadu', 'https://maps.app.goo.gl/b3YAuFt4UoJrmPVJ8', '2026-03-26', 3, 'Refrigerator', '', '2026-03-07', '', '', '', '', 'Yes', '', '', '', '', '', '', 1300.00, 'Paid', 0.00, 'Completed', '', 3, '2026-03-26 14:04:05', '2026-03-26 14:04:46'),
(2, 2, 'HILL side educational trust', '+918973341400', 'Hillside Educational Trust, 9/105C, Alankar Garden, Vellakinar Pirivu, Mettupalayam Road, Coimbatore - 641029', '', '2026-04-01', 2, 'Commercial', 'Daikin VRV x 16hp', '2026-04-01', 'Class room', 'No cooling beep sound continued', 'Heat sensing sink seat..', '', 'Yes', 'Check pcb, and heat sink seat replacing', '8.5', '410', '', '380psi', 'Nil', 3500.00, 'Pending', 0.00, 'Scheduled', '', 1, '2026-04-01 10:54:26', '2026-04-01 10:54:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('owner','staff','technician') DEFAULT 'staff',
  `must_change_password` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `must_change_password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Owner', 'owner', 1, '2026-03-26 13:33:25'),
(2, 'staff1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff One', 'staff', 1, '2026-03-26 13:33:25'),
(3, 'ritu', '$2y$10$kWK8quwjPMTOaBKmuawXAON07Q2/cTgJ1aM52/IZeJkBbIhcU9AKq', 'Ritu', 'technician', 1, '2026-03-26 13:43:38'),
(4, 'Regan', '$2y$10$Gple3Zfu8H8uqoKv0uSseOHNvQYh9Xu1I.BCR0WmDmauC5iHp9MrK', 'Regan', 'technician', 1, '2026-03-27 07:38:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `board_services`
--
ALTER TABLE `board_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `field_services`
--
ALTER TABLE `field_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_employee` (`assigned_employee`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `board_services`
--
ALTER TABLE `board_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `field_services`
--
ALTER TABLE `field_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `board_services`
--
ALTER TABLE `board_services`
  ADD CONSTRAINT `board_services_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `field_services`
--
ALTER TABLE `field_services`
  ADD CONSTRAINT `field_services_ibfk_1` FOREIGN KEY (`assigned_employee`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `field_services_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
