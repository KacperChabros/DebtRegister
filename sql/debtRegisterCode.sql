CREATE DATABASE debtRegister;
USE DebtRegister;
CREATE TABLE debtors(
	idDebtor int AUTO_INCREMENT NOT NULL PRIMARY KEY,
    firstName varchar(35) NOT NULL,
    lastName varchar(50) NOT NULL,
    birthday date NOT NULL,
    townOfOrigin varchar(100) NOT NULL,
    university   varchar(60),
    occupation varchar(100),
    number int(9),
    photo varchar(30)
);
CREATE TABLE debtorOfTheMonth(
    idDotM int AUTO_INCREMENT NOT NULL PRIMARY KEY,
    month int(2) NOT NULL,
    year int(4) NOT NULL,
    idDebtor int NOT NULL,
    FOREIGN KEY(idDebtor) REFERENCES debtors(idDebtor)
);
CREATE TABLE debts(
	idDebt int AUTO_INCREMENT NOT NULL PRIMARY KEY,
    cause  varchar(150) NOT NULL,
    idDebtor int NOT NULL,
    date date NOT NULL,
    value float NOT NULL,
    paid tinyint(1) NOT NULL,
    description varchar(250),
    FOREIGN KEY(idDebtor) REFERENCES debtors(idDebtor)
);
CREATE TABLE dbUsers(
	idDbUser int AUTO_INCREMENT NOT NULL PRIMARY KEY,
    login varchar(50) NOT NULL UNIQUE,
    password text NOT NULL
);
CREATE VIEW leaderboard AS SELECT debtors.firstName, debtors.lastName, COUNT(*) as times, debtors.photo FROM debtorofthemonth JOIN debtors ON debtorofthemonth.idDebtor=debtors.idDebtor GROUP BY debtorofthemonth.idDebtor ORDER BY times DESC;