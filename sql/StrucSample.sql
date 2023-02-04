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
  MODIFY `idDbUser` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `debtorofthemonth`
--
ALTER TABLE `debtorofthemonth`
  MODIFY `idDotM` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `debtors`
--
ALTER TABLE `debtors`
  MODIFY `idDebtor` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `debts`
--
ALTER TABLE `debts`
  MODIFY `idDebt` int(11) NOT NULL AUTO_INCREMENT;

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
