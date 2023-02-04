-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 04 Lut 2023, 17:29
-- Wersja serwera: 10.4.25-MariaDB
-- Wersja PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `debtregister`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dbusers`
--

CREATE TABLE `dbusers` (
  `idDbUser` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `dbusers`
--

INSERT INTO `dbusers` (`idDbUser`, `login`, `password`) VALUES
(1, 'TestAdmin', '1f3c53ae14626035383b39c207564d32d083e8fd');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `debtorofthemonth`
--

CREATE TABLE `debtorofthemonth` (
  `idDotM` int(11) NOT NULL,
  `month` int(2) NOT NULL,
  `year` int(4) NOT NULL,
  `idDebtor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `debtorofthemonth`
--

INSERT INTO `debtorofthemonth` (`idDotM`, `month`, `year`, `idDebtor`) VALUES
(1, 1, 2023, 6),
(2, 2, 2023, 6),
(3, 3, 2023, 6),
(4, 4, 2023, 3),
(5, 5, 2023, 6),
(6, 6, 2023, 6),
(7, 7, 2023, 1),
(8, 8, 2023, 6);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `debtors`
--

CREATE TABLE `debtors` (
  `idDebtor` int(11) NOT NULL,
  `firstName` varchar(35) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `townOfOrigin` varchar(100) NOT NULL,
  `university` varchar(60) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `number` int(9) DEFAULT NULL,
  `photo` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `debtors`
--

INSERT INTO `debtors` (`idDebtor`, `firstName`, `lastName`, `birthday`, `townOfOrigin`, `university`, `occupation`, `number`, `photo`) VALUES
(1, 'John', 'Doe', '2002-03-07', 'London', 'University of West London', NULL, 123456789, 'jdoe.jpg'),
(2, 'Brian', 'Smith', '2002-05-20', 'London', 'University of London', 'Service Desk Specialist', 987654321, 'bsmith.jpg'),
(3, 'Will', 'Jones', '2002-12-11', 'Manchester', NULL, NULL, 234567891, 'wjones.jpg'),
(4, 'Brian', 'Evans', '2002-11-21', 'Liverpool', NULL, 'Warehouseman', 345678912, 'bevans.jpg'),
(5, 'Thomas', 'Thomas', '2002-05-19', 'Leeds', NULL, NULL, 456789123, 'tthomas.jpg'),
(6, 'Wayne', 'Davies', '2002-02-12', 'Manchester', 'University of Manchester', NULL, 567891234, 'wdavies.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `debts`
--

CREATE TABLE `debts` (
  `idDebt` int(11) NOT NULL,
  `cause` varchar(150) NOT NULL,
  `idDebtor` int(11) NOT NULL,
  `date` date NOT NULL,
  `value` float NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `description` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `debts`
--

INSERT INTO `debts` (`idDebt`, `cause`, `idDebtor`, `date`, `value`, `paid`, `description`) VALUES
(1, 'Netflix - January 2023', 1, '2023-01-04', 12, 1, NULL),
(2, 'Spotify - January 2023', 1, '2023-01-09', 5, 1, NULL),
(3, 'Netflix - January 2023', 2, '2023-01-04', 12, 1, NULL),
(4, 'Spotify - January 2023', 2, '2023-01-09', 5, 1, NULL),
(5, 'Netflix - January 2023', 4, '2023-01-04', 12, 1, NULL),
(6, 'Spotify - January 2023', 4, '2023-01-04', 5, 1, NULL),
(7, 'Netflix - January 2023', 5, '2023-01-09', 12, 1, NULL),
(8, 'Spotify - January 2023', 6, '2023-01-09', 5, 1, NULL),
(9, 'Spotify - January 2023', 3, '2023-01-09', 5, 1, NULL),
(10, 'Netflix - February 2023', 1, '2023-02-04', 12, 1, NULL),
(11, 'Netflix - February 2023', 2, '2023-02-04', 12, 1, NULL),
(12, 'Netflix - February 2023', 4, '2023-02-04', 12, 1, NULL),
(13, 'Netflix - February 2023', 5, '2023-02-04', 12, 1, NULL),
(14, 'Spotify - February 2023', 1, '2023-02-09', 5, 1, NULL),
(15, 'Spotify - February 2023', 2, '2023-02-09', 5, 1, NULL),
(16, 'Spotify - February 2023', 4, '2023-02-09', 5, 1, NULL),
(17, 'Spotify - February 2023', 3, '2023-02-09', 5, 1, NULL),
(18, 'Spotify - February 2023', 6, '2023-02-09', 5, 1, NULL),
(19, 'Netflix - March 2023', 1, '2023-03-04', 12, 1, NULL),
(20, 'Netflix - March 2023', 2, '2023-03-04', 12, 1, NULL),
(21, 'Netflix - March 2023', 4, '2023-03-04', 12, 1, NULL),
(22, 'Netflix - March 2023', 5, '2023-03-04', 12, 1, NULL),
(23, 'Spotify - March 2023', 1, '2023-03-09', 5, 1, NULL),
(24, 'Spotify - March 2023', 2, '2023-03-09', 5, 1, NULL),
(25, 'Spotify - March 2023', 3, '2023-03-09', 5, 1, NULL),
(26, 'Spotify - March 2023', 4, '2023-03-09', 5, 1, NULL),
(27, 'Spotify - March 2023', 6, '2023-03-09', 5, 1, NULL),
(28, 'Netflix - April 2023', 1, '2023-04-04', 12, 1, NULL),
(29, 'Netflix - April 2023', 2, '2023-04-04', 12, 1, NULL),
(30, 'Netflix - April 2023', 4, '2023-04-04', 12, 0, NULL),
(31, 'Netflix - April 2023', 5, '2023-04-04', 12, 1, NULL),
(32, 'Spotify - April 2023', 1, '2023-04-09', 5, 1, NULL),
(33, 'Spotify - April 2023', 2, '2023-04-09', 5, 1, NULL),
(34, 'Spotify - April 2023', 3, '2023-04-09', 5, 1, NULL),
(35, 'Spotify - April 2023', 4, '2023-04-09', 5, 0, NULL),
(36, 'Spotify - April 2023', 6, '2023-04-09', 5, 1, NULL),
(37, 'Netflix - May 2023', 1, '2023-05-04', 12, 1, NULL),
(38, 'Netflix - May 2023', 2, '2023-05-04', 12, 1, NULL),
(39, 'Netflix - May 2023', 4, '2023-05-04', 12, 0, NULL),
(40, 'Netflix - May 2023', 5, '2023-05-04', 12, 1, NULL),
(41, 'Spotify - May 2023', 1, '2023-05-09', 5, 1, NULL),
(42, 'Spotify - May 2023', 2, '2023-05-09', 5, 1, NULL),
(43, 'Spotify - May 2023', 3, '2023-05-09', 5, 1, NULL),
(44, 'Spotify - May 2023', 4, '2023-05-09', 5, 0, NULL),
(45, 'Spotify - May 2023', 6, '2023-05-09', 5, 1, NULL),
(46, 'Netflix - June 2023', 1, '2023-06-04', 12, 1, NULL),
(47, 'Netflix - June 2023', 2, '2023-06-04', 12, 1, NULL),
(48, 'Netflix - June 2023', 4, '2023-06-04', 12, 0, NULL),
(49, 'Netflix - June 2023', 5, '2023-06-04', 12, 1, NULL),
(50, 'Spotify - June 2023', 1, '2023-06-09', 5, 1, NULL),
(51, 'Spotify - June 2023', 2, '2023-06-09', 5, 1, NULL),
(52, 'Spotify - June 2023', 3, '2023-06-09', 5, 1, NULL),
(53, 'Spotify - June 2023', 4, '2023-06-09', 5, 0, NULL),
(54, 'Spotify - June 2023', 6, '2023-06-09', 5, 1, NULL),
(55, 'Netflix - July 2023', 1, '2023-07-04', 12, 1, NULL),
(56, 'Netflix - July 2023', 2, '2023-07-04', 12, 0, NULL),
(57, 'Netflix - July 2023', 4, '2023-07-04', 12, 0, NULL),
(58, 'Netflix - July 2023', 5, '2023-07-04', 12, 1, NULL),
(59, 'Spotify - July 2023', 1, '2023-07-09', 5, 1, NULL),
(60, 'Spotify - July 2023', 2, '2023-07-09', 5, 0, NULL),
(61, 'Spotify - July 2023', 3, '2023-07-09', 5, 0, NULL),
(62, 'Spotify - July 2023', 4, '2023-07-09', 5, 0, NULL),
(63, 'Spotify - July 2023', 6, '2023-07-09', 5, 1, NULL),
(64, 'Netflix - August 2023', 1, '2023-08-04', 12, 1, NULL),
(65, 'Netflix - August 2023', 2, '2023-08-04', 12, 0, NULL),
(66, 'Netflix - August 2023', 4, '2023-08-04', 12, 0, NULL),
(67, 'Netflix - August 2023', 5, '2023-08-04', 12, 0, NULL),
(68, 'Spotify - August 2023', 1, '2023-08-09', 5, 1, NULL),
(69, 'Spotify - August 2023', 2, '2023-08-09', 5, 0, NULL),
(70, 'Spotify - August 2023', 3, '2023-08-09', 5, 0, NULL),
(71, 'Spotify - August 2023', 4, '2023-08-09', 5, 0, NULL),
(72, 'Spotify - August 2023', 6, '2023-08-09', 5, 1, NULL),
(73, 'Spotify - September 2023', 1, '2023-09-09', 5, 1, NULL),
(74, 'Spotify - September 2023', 2, '2023-09-09', 5, 0, NULL),
(75, 'Spotify - September 2023', 3, '2023-09-09', 5, 0, NULL),
(76, 'Spotify - September 2023', 4, '2023-09-09', 5, 0, NULL),
(77, 'Spotify - September 2023', 6, '2023-09-09', 5, 1, NULL),
(78, 'Netflix - September 2023', 1, '2023-09-04', 12, 1, NULL),
(79, 'Netflix - September 2023', 2, '2023-09-04', 12, 0, NULL),
(80, 'Netflix - September 2023', 4, '2023-09-04', 12, 0, NULL),
(81, 'Netflix - September 2023', 5, '2023-09-04', 12, 0, NULL),
(82, 'Spotify - October 2023', 1, '2023-10-09', 5, 1, NULL),
(83, 'Spotify - October 2023', 2, '2023-10-09', 5, 0, NULL),
(84, 'Spotify - October 2023', 3, '2023-10-09', 5, 0, NULL),
(85, 'Spotify - October 2023', 4, '2023-10-09', 5, 0, NULL),
(86, 'Spotify - October 2023', 6, '2023-10-09', 5, 0, NULL),
(87, 'Netflix - October 2023', 1, '2023-10-04', 12, 1, NULL),
(88, 'Netflix - October 2023', 2, '2023-10-04', 12, 0, NULL),
(89, 'Netflix - October 2023', 4, '2023-10-04', 12, 0, NULL),
(90, 'Netflix - October 2023', 5, '2023-10-04', 12, 0, NULL),
(91, 'Spotify - November 2023', 1, '2023-11-09', 5, 0, NULL),
(92, 'Spotify - November 2023', 2, '2023-11-09', 5, 0, NULL),
(93, 'Spotify - November 2023', 3, '2023-11-09', 5, 0, NULL),
(94, 'Spotify - November 2023', 4, '2023-11-09', 5, 0, NULL),
(95, 'Spotify - November 2023', 6, '2023-11-09', 5, 0, NULL),
(96, 'Netflix - November 2023', 1, '2023-11-04', 12, 0, NULL),
(97, 'Netflix - November 2023', 2, '2023-11-04', 12, 0, NULL),
(98, 'Netflix - November 2023', 4, '2023-11-04', 12, 0, NULL),
(99, 'Netflix - November 2023', 5, '2023-11-04', 12, 0, NULL),
(100, 'Spotify - December 2023', 1, '2023-12-09', 5, 0, NULL),
(101, 'Spotify - December 2023', 2, '2023-12-09', 5, 0, NULL),
(102, 'Spotify - December 2023', 3, '2023-12-09', 5, 0, NULL),
(103, 'Spotify - December 2023', 4, '2023-12-09', 5, 0, NULL),
(104, 'Spotify - December 2023', 6, '2023-12-09', 5, 0, NULL),
(105, 'Netflix - December 2023', 1, '2023-12-04', 12, 0, NULL),
(106, 'Netflix - December 2023', 2, '2023-12-04', 12, 0, NULL),
(107, 'Netflix - December 2023', 4, '2023-12-04', 12, 0, NULL),
(108, 'Netflix - December 2023', 5, '2023-12-04', 12, 0, NULL);

-- --------------------------------------------------------

--
-- Zastąpiona struktura widoku `leaderboard`
-- (Zobacz poniżej rzeczywisty widok)
--
CREATE TABLE `leaderboard` (
`firstName` varchar(35)
,`lastName` varchar(50)
,`times` bigint(21)
,`photo` varchar(30)
);

-- --------------------------------------------------------

--
-- Struktura widoku `leaderboard`
--
DROP TABLE IF EXISTS `leaderboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `leaderboard`  AS SELECT `debtors`.`firstName` AS `firstName`, `debtors`.`lastName` AS `lastName`, count(0) AS `times`, `debtors`.`photo` AS `photo` FROM (`debtorofthemonth` join `debtors` on(`debtorofthemonth`.`idDebtor` = `debtors`.`idDebtor`)) GROUP BY `debtorofthemonth`.`idDebtor` ORDER BY count(0) AS `DESCdesc` ASC  ;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dbusers`
--
ALTER TABLE `dbusers`
  ADD PRIMARY KEY (`idDbUser`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `debtorofthemonth`
--
ALTER TABLE `debtorofthemonth`
  ADD PRIMARY KEY (`idDotM`),
  ADD KEY `idDebtor` (`idDebtor`);

--
-- Indeksy dla tabeli `debtors`
--
ALTER TABLE `debtors`
  ADD PRIMARY KEY (`idDebtor`);

--
-- Indeksy dla tabeli `debts`
--
ALTER TABLE `debts`
  ADD PRIMARY KEY (`idDebt`),
  ADD KEY `idDebtor` (`idDebtor`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `dbusers`
--
ALTER TABLE `dbusers`
  MODIFY `idDbUser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `debtorofthemonth`
--
ALTER TABLE `debtorofthemonth`
  MODIFY `idDotM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `debtors`
--
ALTER TABLE `debtors`
  MODIFY `idDebtor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `debts`
--
ALTER TABLE `debts`
  MODIFY `idDebt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `debtorofthemonth`
--
ALTER TABLE `debtorofthemonth`
  ADD CONSTRAINT `debtorofthemonth_ibfk_1` FOREIGN KEY (`idDebtor`) REFERENCES `debtors` (`idDebtor`);

--
-- Ograniczenia dla tabeli `debts`
--
ALTER TABLE `debts`
  ADD CONSTRAINT `debts_ibfk_1` FOREIGN KEY (`idDebtor`) REFERENCES `debtors` (`idDebtor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
