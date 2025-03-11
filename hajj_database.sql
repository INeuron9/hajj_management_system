-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 07:48 PM
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
-- Database: `hajj_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `airline_seats`
--

CREATE TABLE `airline_seats` (
  `seat_id` int(5) NOT NULL,
  `airline` varchar(20) NOT NULL,
  `seat_no` int(3) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `airline_seats`
--

INSERT INTO `airline_seats` (`seat_id`, `airline`, `seat_no`, `status`) VALUES
(1, 'PIA', 1, 13),
(2, 'PIA', 2, 14),
(3, 'PIA', 3, 0),
(4, 'PIA', 4, 0),
(5, 'PIA', 5, 0),
(6, 'Qatar Airways', 1, 0),
(7, 'Qatar Airways', 2, 0),
(8, 'Qatar Airways', 3, 0),
(9, 'Qatar Airways', 4, 0),
(10, 'Qatar Airways', 5, 0),
(11, 'Hijaz Airline', 1, 0),
(12, 'Hijaz Airline', 2, 0),
(13, 'Hijaz Airline', 3, 0),
(14, 'Hijaz Airline', 4, 0),
(15, 'Hijaz Airline', 5, 0);

--
-- Triggers `airline_seats`
--
DELIMITER $$
CREATE TRIGGER `airline_sys` AFTER INSERT ON `airline_seats` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Insert","airline_seats")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `airline_sys_2` AFTER UPDATE ON `airline_seats` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Update","airline_seats")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `airline_sys_3` AFTER DELETE ON `airline_seats` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Delete","airline_seats")
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `food_booking`
--

CREATE TABLE `food_booking` (
  `user_id` int(5) NOT NULL,
  `restaurant_id` int(5) NOT NULL,
  `preference` varchar(50) NOT NULL,
  `venue` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_booking`
--

INSERT INTO `food_booking` (`user_id`, `restaurant_id`, `preference`, `venue`) VALUES
(13, 8, 'Vegan & Non-Vegan (Mix)', 'Walk-in'),
(14, 4, 'Vegan & Non-Vegan (Mix)', 'Walk-in');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_details`
--

CREATE TABLE `hotel_details` (
  `room_id` int(11) NOT NULL,
  `hotel` varchar(20) NOT NULL,
  `room_no` int(3) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_details`
--

INSERT INTO `hotel_details` (`room_id`, `hotel`, `room_no`, `status`) VALUES
(1, 'Makkah hotel 1', 1, 13),
(2, 'Makkah hotel 1', 2, 14),
(3, 'Makkah hotel 1', 3, 0),
(4, 'Makkah hotel 1', 4, 0),
(5, 'Makkah hotel 1', 5, 0),
(6, 'Makkah hotel 2', 1, 0),
(7, 'Makkah hotel 2', 2, 0),
(8, 'Makkah hotel 2', 3, 0),
(9, 'Makkah hotel 2', 4, 0),
(10, 'Makkah hotel 2', 5, 0);

--
-- Triggers `hotel_details`
--
DELIMITER $$
CREATE TRIGGER `hotel_sys` AFTER INSERT ON `hotel_details` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Insert","hotel_details")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `hotel_sys_2` AFTER UPDATE ON `hotel_details` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Update","hotel_details")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `hotel_sys_3` AFTER DELETE ON `hotel_details` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Delete","hotel_details")
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_details`
--

CREATE TABLE `restaurant_details` (
  `restaurant_id` int(5) NOT NULL,
  `name` varchar(20) NOT NULL,
  `location` varchar(20) NOT NULL,
  `contact` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_details`
--

INSERT INTO `restaurant_details` (`restaurant_id`, `name`, `location`, `contact`) VALUES
(1, 'Marhaba Foods', 'Haji tower 1', '+996-1234560'),
(2, 'Salwa Foods', 'Haji tower 1', '+923124482933'),
(3, 'Salateen Foods', 'Haji tower 1', '+996-1234562'),
(4, 'Makkah Foods', 'Haji tower 2', '+996-1234563'),
(5, 'Bukhari Foods', 'Haji tower 2', '+996-1234564'),
(6, 'Zam Zam Foods', 'Main market', '+996-1234567'),
(8, 'Al Baik', 'Main city', '0238894299');

--
-- Triggers `restaurant_details`
--
DELIMITER $$
CREATE TRIGGER `restaurant_sys` AFTER INSERT ON `restaurant_details` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Insert","restaurant_details")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restaurant_sys_2` AFTER UPDATE ON `restaurant_details` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Update","restaurant_details")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `restaurant_sys_3` AFTER DELETE ON `restaurant_details` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Delete","restaurant_details")
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `room_booking`
--

CREATE TABLE `room_booking` (
  `user_id` int(11) NOT NULL,
  `room_id` int(5) NOT NULL,
  `stay_days` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_booking`
--

INSERT INTO `room_booking` (`user_id`, `room_id`, `stay_days`) VALUES
(13, 1, 31),
(14, 2, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sys_logs`
--

CREATE TABLE `sys_logs` (
  `log_id` int(11) NOT NULL,
  `time` datetime DEFAULT current_timestamp(),
  `action_type` varchar(50) NOT NULL,
  `table_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sys_logs`
--

INSERT INTO `sys_logs` (`log_id`, `time`, `action_type`, `table_name`) VALUES
(21, '2024-11-27 23:33:06', 'Insert', 'users'),
(22, '2024-11-27 23:33:29', 'Update', 'airline_seats'),
(23, '2024-11-27 23:33:39', 'Update', 'hotel_details'),
(24, '2024-11-27 23:37:59', 'Update', 'restaurant_details'),
(25, '2024-11-27 23:38:16', 'Insert', 'restaurant_details');

-- --------------------------------------------------------

--
-- Table structure for table `travel_booking`
--

CREATE TABLE `travel_booking` (
  `user_id` int(5) NOT NULL,
  `seat_id` int(3) NOT NULL,
  `destination` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `travel_booking`
--

INSERT INTO `travel_booking` (`user_id`, `seat_id`, `destination`) VALUES
(13, 1, 'Makkah'),
(14, 2, 'jeddah');

-- --------------------------------------------------------

--
-- Table structure for table `userinfo`
--

CREATE TABLE `userinfo` (
  `user_id` int(5) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `date_of_birth` date NOT NULL,
  `country` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userinfo`
--

INSERT INTO `userinfo` (`user_id`, `first_name`, `last_name`, `date_of_birth`, `country`) VALUES
(13, 'Rana', 'Ahmed', '2004-02-20', 'Pakistan'),
(14, 'Bilal', 'Ahmed', '2024-11-06', 'Mars');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(5) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `password`) VALUES
(0, 'admin', 'admin'),
(13, 'user1', 'password'),
(14, 'user2', 'asd');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `users_sys` AFTER INSERT ON `users` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Insert","users")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_sys_2` AFTER UPDATE ON `users` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Update","users")
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_sys_3` AFTER DELETE ON `users` FOR EACH ROW INSERT into sys_logs VALUES(NULL,CURRENT_TIMESTAMP,"Delete","users")
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airline_seats`
--
ALTER TABLE `airline_seats`
  ADD PRIMARY KEY (`seat_id`);

--
-- Indexes for table `food_booking`
--
ALTER TABLE `food_booking`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `restaurant_id_fk_1` (`restaurant_id`);

--
-- Indexes for table `hotel_details`
--
ALTER TABLE `hotel_details`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `restaurant_details`
--
ALTER TABLE `restaurant_details`
  ADD PRIMARY KEY (`restaurant_id`);

--
-- Indexes for table `room_booking`
--
ALTER TABLE `room_booking`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `room_id_fk_1` (`room_id`);

--
-- Indexes for table `sys_logs`
--
ALTER TABLE `sys_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `travel_booking`
--
ALTER TABLE `travel_booking`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `seat_id_fk_1` (`seat_id`);

--
-- Indexes for table `userinfo`
--
ALTER TABLE `userinfo`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airline_seats`
--
ALTER TABLE `airline_seats`
  MODIFY `seat_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `hotel_details`
--
ALTER TABLE `hotel_details`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `restaurant_details`
--
ALTER TABLE `restaurant_details`
  MODIFY `restaurant_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_booking`
--
ALTER TABLE `room_booking`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sys_logs`
--
ALTER TABLE `sys_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `travel_booking`
--
ALTER TABLE `travel_booking`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `userinfo`
--
ALTER TABLE `userinfo`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `food_booking`
--
ALTER TABLE `food_booking`
  ADD CONSTRAINT `restaurant_id_fk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurant_details` (`restaurant_id`),
  ADD CONSTRAINT `userid_fk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `room_booking`
--
ALTER TABLE `room_booking`
  ADD CONSTRAINT `room_id_fk_1` FOREIGN KEY (`room_id`) REFERENCES `hotel_details` (`room_id`),
  ADD CONSTRAINT `userid_fk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `travel_booking`
--
ALTER TABLE `travel_booking`
  ADD CONSTRAINT `seat_id_fk_1` FOREIGN KEY (`seat_id`) REFERENCES `airline_seats` (`seat_id`),
  ADD CONSTRAINT `userid_fk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `userinfo`
--
ALTER TABLE `userinfo`
  ADD CONSTRAINT `userid_fk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
