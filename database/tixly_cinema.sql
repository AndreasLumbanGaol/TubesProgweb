-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 04:43 PM
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
-- Database: `tixly_cinema`
--
CREATE DATABASE IF NOT EXISTS `tixly_cinema`;
USE `tixly_cinema`;
-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `MovieID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Duration` int(11) DEFAULT NULL,
  `Genre` varchar(100) DEFAULT NULL,
  `Rating` decimal(3,1) DEFAULT NULL,
  `PosterURL` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`MovieID`, `Title`, `Duration`, `Genre`, `Rating`, `PosterURL`) VALUES
(1, 'AVATAR: The Way of Water', 192, 'Aksi, Drama, Sci-Fi', 8.4, 'https://upload.wikimedia.org/wikipedia/id/5/54/Avatar_The_Way_of_Water_poster.jpg'),
(2, 'JOKER: Put On A Happy Face', 60, 'Drama, Thriller', 8.4, 'https://upload.wikimedia.org/wikipedia/en/e/el/Joker_%282019_film%29_poster.jpg'),
(3, 'Wonka', 116, 'Adventure, Comedy, Family', 6.9, 'https://image.tmdb.org/t/p/w500/qhb1qOilapbapxWQn9jtRCMwXJF.jpg'),
(4, 'Guardians of The Galaxy', 121, 'Aksi, Petualangan, Komedi', 8.0, 'https://image.tmdb.org/t/p/w500/r7XifzvtezNt3lypvsmb60qxw49.jpg'),
(5, 'Avengers', 181, 'Aksi, Sci-Fi', 8.5, 'https://upload.wikimedia.org/wikipedia/en/8/8a/The_Avengers_%282012_film%29_poster.jpg'),
(6, 'Ghost In The Cell', 106, 'Horror, Mystery', 7.5, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5421e6fbe0a18094aa35cfacf23a23d3.jpg'),
(7, 'Jumbo', 102, 'Animasi, Keluarga', 7.0, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_9cdf1156e0f7b0ee9a45f143ce11976e.jpg'),
(8, 'Yang Lain Boleh Hilang, Asal Kau Jangan', 113, 'Romantis, Drama', 7.2, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_d4bc3b7b9035989df6277825cf5fefd5.jpg'),
(9, '2nd Miracle In Cell No 7', 147, 'Drama, Keluarga', 8.3, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_38ea256af71e0767b204e1c300fc4f09.png'),
(10, 'Sekawan Limo', 112, 'Komedi, Horror', 7.8, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_840a7c47b45221974b785876428c61f6.jpeg'),
(11, 'The Devil Wears Prada 2', 119, 'Drama, Komedi', 7.4, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_5cd2d563ce3516fc292cb495c3d666e2.jpg'),
(12, 'SALMOKJI: WHISPERING WATER', 101, 'Thriller, Misteri', 6.8, 'https://lsf.go.id/storage/app/resources/resize/300_450_0_0_crop/img_3a0e2045bc02d6479c7fade242ce64ed.png'),
(13, 'SONIC 4: The Hedgehog', 120, 'Animasi, Aksi, Komedi', 0.0, 'https://bit.ly/4v8IiMK'),
(14, 'Spider-man: Across The Spider Verse', 140, 'Animasi, Aksi, Petualangan', 8.7, 'https://cinemags.org/?attachment_id=192228'),
(15, 'Man Of Tomorrow', 110, 'Aksi, Sci-Fi', 0.0, 'https://posterspy.com/wp-content/uploads/2024/01/PosterSpy-Man-of-Tomorrow-Teaser-version-site.jpg'),
(16, 'Minion & Monsters', 95, 'Animasi, Komedi, Keluarga', 0.0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTaU3-BIeHwacBlxlpr2juR3RH-yhNN06rgdw&s'),
(17, 'Frozen III', 105, 'Animasi, Petualangan, Keluarga', 0.0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTBVpQ2G6WhTrJfC010_NiJjxh10EncCx3ujg&s');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PaymentID` int(11) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Account_Number` varchar(50) DEFAULT NULL,
  `Payment_Proof` varchar(255) DEFAULT NULL,
  `TransactionID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seat`
--

CREATE TABLE `seat` (
  `SeatID` int(11) NOT NULL,
  `RowNumber` char(2) NOT NULL,
  `SeatNumber` int(11) NOT NULL,
  `StudioID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `showtime`
--

CREATE TABLE `showtime` (
  `ShowtimeID` int(11) NOT NULL,
  `StartTime` time NOT NULL,
  `PlayDate` date NOT NULL,
  `MovieID` int(11) DEFAULT NULL,
  `StudioID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studio`
--

CREATE TABLE `studio` (
  `StudioID` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  `TheaterID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studio`
--

INSERT INTO `studio` (`StudioID`, `Name`, `Type`, `Capacity`, `TheaterID`) VALUES
(1, 'Studio 1', 'Regular', 100, 1),
(2, 'Studio 2', 'Gold Class', 40, 1),
(3, 'Studio 1', 'Regular', 120, 2),
(4, 'Studio Velvet', 'Velvet', 30, 2),
(5, 'Studio 1', 'Regular', 100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `theater`
--

CREATE TABLE `theater` (
  `TheaterID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theater`
--

INSERT INTO `theater` (`TheaterID`, `Name`, `Location`) VALUES
(1, 'Tixly Central', 'Bandung'),
(2, 'CGV Paskal 23', 'Bandung'),
(3, 'XXI Botanica Mall', 'Bandung');

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `TicketID` int(11) NOT NULL,
  `FirstPrice` int(11) NOT NULL,
  `Status` varchar(50) DEFAULT 'aktif',
  `IsResale` tinyint(1) DEFAULT 0,
  `SecondPrice` int(11) DEFAULT NULL,
  `TransactionID` int(11) DEFAULT NULL,
  `ShowtimeID` int(11) DEFAULT NULL,
  `StudioID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `TransactionID` int(11) NOT NULL,
  `TransDate` datetime DEFAULT current_timestamp(),
  `TotalPrice` int(11) NOT NULL,
  `PaymentStatus` varchar(50) DEFAULT 'pending',
  `UserID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `Nama` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Nama`, `Email`, `Password`, `Phone`, `Role`) VALUES
(1, 'Administrator Tixly', 'admin@tixly.com', 'admin123', '081122334455', 'admin'),
(2, 'Andreas Lumban', 'andreas@gmail.com', 'andreas123', '081766632', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`MovieID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `TransactionID` (`TransactionID`);

--
-- Indexes for table `seat`
--
ALTER TABLE `seat`
  ADD PRIMARY KEY (`SeatID`),
  ADD KEY `StudioID` (`StudioID`);

--
-- Indexes for table `showtime`
--
ALTER TABLE `showtime`
  ADD PRIMARY KEY (`ShowtimeID`),
  ADD KEY `MovieID` (`MovieID`),
  ADD KEY `StudioID` (`StudioID`);

--
-- Indexes for table `studio`
--
ALTER TABLE `studio`
  ADD PRIMARY KEY (`StudioID`),
  ADD KEY `TheaterID` (`TheaterID`);

--
-- Indexes for table `theater`
--
ALTER TABLE `theater`
  ADD PRIMARY KEY (`TheaterID`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`TicketID`),
  ADD KEY `TransactionID` (`TransactionID`),
  ADD KEY `ShowtimeID` (`ShowtimeID`),
  ADD KEY `StudioID` (`StudioID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `MovieID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seat`
--
ALTER TABLE `seat`
  MODIFY `SeatID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `showtime`
--
ALTER TABLE `showtime`
  MODIFY `ShowtimeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studio`
--
ALTER TABLE `studio`
  MODIFY `StudioID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theater`
--
ALTER TABLE `theater`
  MODIFY `TheaterID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `TicketID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`TransactionID`) REFERENCES `transaction` (`TransactionID`) ON DELETE CASCADE;

--
-- Constraints for table `seat`
--
ALTER TABLE `seat`
  ADD CONSTRAINT `seat_ibfk_1` FOREIGN KEY (`StudioID`) REFERENCES `studio` (`StudioID`) ON DELETE CASCADE;

--
-- Constraints for table `showtime`
--
ALTER TABLE `showtime`
  ADD CONSTRAINT `showtime_ibfk_1` FOREIGN KEY (`MovieID`) REFERENCES `movie` (`MovieID`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtime_ibfk_2` FOREIGN KEY (`StudioID`) REFERENCES `studio` (`StudioID`) ON DELETE CASCADE;

--
-- Constraints for table `studio`
--
ALTER TABLE `studio`
  ADD CONSTRAINT `studio_ibfk_1` FOREIGN KEY (`TheaterID`) REFERENCES `theater` (`TheaterID`) ON DELETE CASCADE;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`TransactionID`) REFERENCES `transaction` (`TransactionID`) ON DELETE SET NULL,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`ShowtimeID`) REFERENCES `showtime` (`ShowtimeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`StudioID`) REFERENCES `studio` (`StudioID`) ON DELETE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
