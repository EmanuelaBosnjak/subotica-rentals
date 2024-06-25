-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 25, 2024 at 10:47 AM
-- Server version: 8.0.37-0ubuntu0.20.04.3
-- PHP Version: 8.2.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nda`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `ad_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `property_id` bigint NOT NULL,
  `status` enum('DRAFT','PENDING','PUBLIC','RENTED','BLOCKED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'DRAFT',
  `rental_period` int NOT NULL COMMENT 'period in months',
  `rental_price` bigint NOT NULL,
  `description` varchar(4096) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`ad_id`, `user_id`, `property_id`, `status`, `rental_period`, `rental_price`, `description`) VALUES
(3, 1, 5, 'PUBLIC', 0, 9077, 'some modified description blahblahblahblahblahblahblahblahblahblahblahblah'),
(4, 1, 4, 'RENTED', 12, 12000, 'vdavjdtfjdtfdbfwdtfutndfuy3fud53fd5nfa675dfj7fz8cfznmfe67fzjwef5wnza7f6z5fgzfnzyufgz8tfgz8nfc5z85fgz85f8dczj8c5gfz56n8365n8356865zgec8n5mz85cgz875cgnz87en58z756fgnz85fgz8fg5zd58gd85fg5zg!'),
(5, 1, 7, 'PUBLIC', 12, 5000, 'Sample description lorem ipsum dolor sit amet. blah blah blah.'),
(6, 1, 8, 'PENDING', 4, 20000, 'somewhereee far far away somewhereee far far away somewhereee far far away somewhereee far far away somewhereee far far away '),
(7, 9, 9, 'RENTED', 6, 25000, 'Charming 2 bedroom house in a desirable neighborhood. Bright and airy with amazing view, perfect for entertaining. Move-in ready and close to shops, parks, public transport. Don\'t miss out!'),
(9, 9, 11, 'PUBLIC', 2, 2474, 'Cozy 1-bedroom bungalow with a fireplace, nestled in a quiet neighborhood. Move-in ready and just steps from cafes, parks, and public transportation. Perfect for a low-maintenance lifestyle.'),
(10, 9, 12, 'BLOCKED', 10, 76276753, 'yusfurfggujrsffgujsrbju erfursg urgtu tgurfdfdggfdgfg');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` bigint NOT NULL,
  `location_name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `iframe` varchar(4096) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_name`, `iframe`) VALUES
(1, 'Makova Sedmica', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d37186.9045701641!2d19.642087580576064!3d46.152075918317344!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4743645b0bdc9173%3A0x30e862ce68217b4b!2sMakova%20Sedmica%2C%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718833939112!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(2, 'Zorka', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d22120.39231309009!2d19.614801249856313!3d46.12985303389383!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x474366808a88c721%3A0x1b9685c175d5df3d!2sZorka%2C%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718834070508!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(3, 'Mali Radanovac', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d18605.68837826215!2d19.6828164562257!3d46.11584599282675!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4743615ae93ac95f%3A0xbfdc85cecf559fa4!2sMali%20Radanovac%2C%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718834109472!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(4, 'Radanovac', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d22125.081273142147!2d19.701361549824583!3d46.11817644016864!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47436161f825d04d%3A0xea5113db377f0658!2sRadanovac%2C%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718834185224!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(5, 'Peščara', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d22128.885102320284!2d19.58716374979885!3d46.10870234525917!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47436640dc454c9b%3A0x1019d62beedecba1!2zUGXFocSNYXJhLCBTdWJvdGljYSwgU2VyYmlh!5e0!3m2!1sen!2sin!4v1718835010594!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(6, 'Dudova Šuma', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11064.101296941775!2d19.644198417275398!3d46.11040236158115!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x474366be34a77a91%3A0x76752be5fa1803d7!2sDudova%20%C5%A0uma%2C%2024000%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718835050200!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(7, 'Željezničko Naselje', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d22121.861993691262!2d19.646944799846352!3d46.126193435860536!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x474366a137602929%3A0x96490a646f3ae9f9!2s%C5%BDeljezni%C4%8Dko%20Naselje%2C%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718835097788!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(8, 'Prozivka', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11068.989738733113!2d19.66582771725893!3d46.086045118138664!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47436129b75e783b%3A0x5ad962a6044f4e16!2sProzivka%2C%2024000%20Subotica%2C%20Serbia!5e0!3m2!1sen!2sin!4v1718835158702!5m2!1sen!2sin\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `type_id` bigint NOT NULL,
  `location_id` bigint NOT NULL,
  `street_address` text COLLATE utf8mb4_general_ci NOT NULL,
  `capacity` int NOT NULL DEFAULT '1',
  `parking` tinyint(1) NOT NULL DEFAULT '0',
  `pets_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(4096) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `user_id`, `name`, `type_id`, `location_id`, `street_address`, `capacity`, `parking`, `pets_allowed`, `description`) VALUES
(4, 1, 'Emma\'s Home', 5, 1, 'Backa 39, Subotica 24000', 2, 1, 1, 'daaaaaaaaaaaaaauisghbdfufsed usfieuedfiusef fes  esffuseuifesbiusef fse usefbuijfes u sfuebu efjbszjubdefjusedu  se'),
(5, 1, 'hjjkl', 3, 6, 'tfybtyfakrtyakrkrytufrktag54', 3, 1, 1, 'vtfvjtrfirf65fr56wfr56wur6w8r6wr6wrg68sgif7sifgu6fdgz6fd6rg437grisgrfjsefgduytyfjy'),
(7, 1, 'PROPERYTYEAFDAJ', 4, 8, 'hgavtfdvatyi7bn6ghHGHGCHC', 7, 1, 1, 'GFVJDHGVJFDGVJGGcjhfcjhcrcr  uyrcuy bcuc cjt jctb bttb ct cttcbbucybucyr j  tycybuycr5f5bu7u56uc b nc.'),
(8, 1, 'something', 2, 2, 'Somewhere 23a subotica', 3, 1, 1, 'somewhereee far far away somewhereee far far away somewhereee far far away somewhereee far far away somewhereee far far away '),
(9, 9, 'Winters House', 1, 6, 'Aleja Marsala tita 3a Subotica 24000', 2, 1, 1, 'Charming 2 bedroom house in a desirable neighborhood. Bright and airy with amazing view, perfect for entertaining. Move-in ready and close to shops, parks, public transport. Don\'t miss out!'),
(10, 9, 'Harry Poah', 1, 8, 'Prozivka , Ulica ima Ime 48 Subotica', 2, 1, 1, 'Bright and airy 2-bedroom house with hardwood floors in a desirable neighborhood. Perfect for entertaining and close to shops, parks, and public transportation. Move-in ready!'),
(11, 9, 'Cozys Apartment', 5, 2, 'Zorka, Subotica, Ulica Somewhere 23', 8, 1, 1, 'Cozy 1-bedroom bungalow with a fireplace, nestled in a quiet neighborhood. Move-in ready and just steps from cafes, parks, and public transportation. Perfect for a low-maintenance lifestyle.'),
(12, 9, 'Winters House', 3, 4, 'hfhfhfdafh suubadshvf 1654', 5, 1, 1, 'ghsdfagjhsfdg sdfahgsfdghdsgfh fsdavjfdshagjfhdsvav');

-- --------------------------------------------------------

--
-- Table structure for table `property_types`
--

CREATE TABLE `property_types` (
  `type_id` bigint NOT NULL,
  `type_name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(512) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_types`
--

INSERT INTO `property_types` (`type_id`, `type_name`, `description`) VALUES
(1, '1 BHK Apartment', 'An apartment with a bedroom, kitchen and living room.'),
(2, '2 BHK Apartment', 'An apartment with 2 bedrooms, a kitchen and a living room.'),
(3, '3 BHK Apartment', 'An apartment with 3 bedrooms, a kitchen and a living room.'),
(4, '4 BHK Apartment', 'An apartment with 4 bedrooms, a kitchen and a living room.'),
(5, 'Small Villa', 'A small villa with boundary walls.');

-- --------------------------------------------------------

--
-- Table structure for table `rented_by`
--

CREATE TABLE `rented_by` (
  `property_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `rented_on` date NOT NULL DEFAULT (curdate()),
  `rental_period` int NOT NULL COMMENT 'period in months',
  `rental_price` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rented_by`
--

INSERT INTO `rented_by` (`property_id`, `user_id`, `rented_on`, `rental_period`, `rental_price`) VALUES
(4, 4, '2024-06-24', 12, 12000),
(9, 4, '2024-06-25', 6, 25000);

-- --------------------------------------------------------

--
-- Table structure for table `task2_login_failure`
--

CREATE TABLE `task2_login_failure` (
  `id_login_failure` bigint NOT NULL,
  `username` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task2_login_failure`
--

INSERT INTO `task2_login_failure` (`id_login_failure`, `username`, `password`, `date_time`) VALUES
(1, 'r3dacted42', '12345', '2024-04-04 15:16:45'),
(2, 'r3dacted42', '3', '2024-04-04 15:18:53'),
(3, 'r3dacted42', '5', '2024-04-04 15:19:55'),
(4, 'emma', '123', '2024-04-04 19:04:47'),
(5, 'emma', '123', '2024-04-04 19:07:18');

-- --------------------------------------------------------

--
-- Table structure for table `task2_users`
--

CREATE TABLE `task2_users` (
  `id_user` bigint NOT NULL,
  `username` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task2_users`
--

INSERT INTO `task2_users` (`id_user`, `username`, `password`, `name`) VALUES
(1, 'r3dacted42', '$2y$08$F3ja1.sq2uv0mcark5KiU.EpZGZpd0jWSUuIjo5bLM5OYOktFD9.S', 'Priyansh Agrahari'),
(2, 'emma', '$2y$08$ppteGSuT.hupPVYxhXNiJO0/JAoFLR8m7n7yXEvLmQytd6SROE2RW', 'Emanuela Bosnjak'),
(3, 'dementia', '$2y$08$pl4/pdFlvVN/mP428mmPSeBS2nOAbZL9MjZ/fX4tD29EnbJRATTHa', 'Dementia I Forgot');

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

CREATE TABLE `user_accounts` (
  `user_id` bigint NOT NULL,
  `email` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `account_state` enum('NOT_VERIFIED','ACTIVE','BLOCKED','ADMIN') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NOT_VERIFIED',
  `first_name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`user_id`, `email`, `password_hash`, `account_state`, `first_name`, `last_name`, `phone_number`) VALUES
(1, 'priyansh.agr4@gmail.com', '$2y$10$quFh0Utcq5OKqGxziTLNSOmEJwySfJN3dx7gnq/3U7vG0PHrB8Bc2', 'ACTIVE', 'Priyansh', 'Agrahari', 917991890),
(4, 'bemanuela3@gmail.com', '$2y$10$6oVQjaWd1JQvp8aa0d.U1OJVt4QBWGRKQyF5es/u1xsZwg5hi7BAC', 'ADMIN', 'Emanuela', 'Bosnjak', 787878787),
(7, 'greg.gregly@hotmail.com', '$2y$10$fRqZg2asIEH5pmoHL/2tc.RGNpEJYCyPJQxGZA75i8dW2wTOgUWQe', 'BLOCKED', 'Greg', 'Gregly', 765456209),
(8, 'earth.c37@gmail.com', '$2y$10$MccMJkyavpjP290DdQLiJOdFzIsEhH5cLe9/z.eDB2Es8lCWKi9Ie', 'NOT_VERIFIED', 'Rick', 'Sanchez', 737373737),
(9, 'cyddwrqz@fmailler.net', '$2y$10$2jBeFn5I/s6yoXOnfR7ifO2LXawJs1h/k08.qWUJuiQ65Z6kKa3H2', 'ACTIVE', 'Mao', 'MaoMao', 638060737),
(10, 'something@gmail.com', '$2y$10$r8DENPCOWMuUU6QbugKS2eFeBmjYozMLgsf1GObJBwe228WJJx9cS', 'ACTIVE', 'Neko', 'Nesto', 638060738);

-- --------------------------------------------------------

--
-- Table structure for table `week8_messages`
--

CREATE TABLE `week8_messages` (
  `id_message` int NOT NULL,
  `name` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `message` varchar(512) COLLATE utf8mb4_general_ci NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `week8_messages`
--

INSERT INTO `week8_messages` (`id_message`, `name`, `message`, `date_time`) VALUES
(3, 'Emanuela Bosnjak', 'YAWWWNNNNNNNNNN', '2024-04-14 03:58:39'),
(4, 'Emanuela Bosnjak', 'YAWWWNNNNNNNNNN', '2024-04-14 04:00:09'),
(5, 'Emanuela Bosnjak', 'YAWWWNNNNNNNNNN', '2024-04-14 04:00:36'),
(6, 'Emanuela Bosnjak', 'YAWWWNNNNNNNNNN', '2024-04-14 04:01:25'),
(7, 'Emanuela Bosnjak', 'YAWWWNNNNNNNNNN', '2024-04-14 04:01:38'),
(8, 'Emanuela Bosnjak', 'YAWWWNNNNNNNNNN', '2024-04-14 04:01:47');

-- --------------------------------------------------------

--
-- Table structure for table `week9_files`
--

CREATE TABLE `week9_files` (
  `id_file` int NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`ad_id`),
  ADD UNIQUE KEY `unq_property_id` (`property_id`),
  ADD KEY `fk_a_user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`),
  ADD KEY `fk_p_user_id` (`user_id`),
  ADD KEY `fk_p_type_id` (`type_id`),
  ADD KEY `fk_p_location_id` (`location_id`);

--
-- Indexes for table `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `rented_by`
--
ALTER TABLE `rented_by`
  ADD PRIMARY KEY (`property_id`,`user_id`),
  ADD KEY `fk_r_user_id` (`user_id`);

--
-- Indexes for table `task2_login_failure`
--
ALTER TABLE `task2_login_failure`
  ADD PRIMARY KEY (`id_login_failure`);

--
-- Indexes for table `task2_users`
--
ALTER TABLE `task2_users`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `user_accounts`
--
ALTER TABLE `user_accounts`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `week8_messages`
--
ALTER TABLE `week8_messages`
  ADD PRIMARY KEY (`id_message`);

--
-- Indexes for table `week9_files`
--
ALTER TABLE `week9_files`
  ADD PRIMARY KEY (`id_file`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `ad_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `property_types`
--
ALTER TABLE `property_types`
  MODIFY `type_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `task2_login_failure`
--
ALTER TABLE `task2_login_failure`
  MODIFY `id_login_failure` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `task2_users`
--
ALTER TABLE `task2_users`
  MODIFY `id_user` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_accounts`
--
ALTER TABLE `user_accounts`
  MODIFY `user_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `week8_messages`
--
ALTER TABLE `week8_messages`
  MODIFY `id_message` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `week9_files`
--
ALTER TABLE `week9_files`
  MODIFY `id_file` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD CONSTRAINT `fk_property_id` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `fk_p_location_id` FOREIGN KEY (`location_id`) REFERENCES `locations` (`location_id`),
  ADD CONSTRAINT `fk_p_type_id` FOREIGN KEY (`type_id`) REFERENCES `property_types` (`type_id`),
  ADD CONSTRAINT `fk_p_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `rented_by`
--
ALTER TABLE `rented_by`
  ADD CONSTRAINT `fk_r_property_id` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_r_user_id` FOREIGN KEY (`user_id`) REFERENCES `user_accounts` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
