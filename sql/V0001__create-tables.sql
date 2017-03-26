-- phpMyAdmin SQL Dump
-- version 4.6.5
-- https://www.phpmyadmin.net/
--
-- Host: mysql.tetris.tmhmml.com
-- Generation Time: Mar 26, 2017 at 07:29 AM
-- Server version: 5.6.34-log
-- PHP Version: 7.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `tetrisdb`
--
CREATE DATABASE IF NOT EXISTS `tetrisdb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tetrisdb`;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `locationid` int(15) NOT NULL,
  `locationname` varchar(30) DEFAULT NULL,
  `address` varchar(60) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `createdby` int(15) NOT NULL,
  `locationdescription` varchar(255) DEFAULT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(50) DEFAULT NULL
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `playerid` int(15) NOT NULL,
  `firstname` varchar(15) DEFAULT NULL,
  `lastname` varchar(15) DEFAULT NULL,
  `username` varchar(15) NOT NULL,
  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `playermatch`
--

CREATE TABLE `playermatch` (
  `matchid` int(15) NOT NULL,
  `playerid` int(15) NOT NULL,
  `lines` int(5) NOT NULL,
  `time` int(5) NOT NULL,
  `wrank` int(5) NOT NULL,
  `erank` int(5) NOT NULL
) DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tntmatch`
--

CREATE TABLE `tntmatch` (
  `matchid` int(15) NOT NULL,
  `matchdate` date NOT NULL,
  `inputstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `enteredby` int(15) NOT NULL,
  `location` int(15) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `universe` int(15) NOT NULL
) DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`locationid`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`playerid`);

--
-- Indexes for table `playermatch`
--
ALTER TABLE `playermatch`
  ADD PRIMARY KEY (`matchid`,`playerid`);

--
-- Indexes for table `tntmatch`
--
ALTER TABLE `tntmatch`
  ADD PRIMARY KEY (`matchid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `locationid` int(15) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `playerid` int(15) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tntmatch`
--
ALTER TABLE `tntmatch`
  MODIFY `matchid` int(15) NOT NULL AUTO_INCREMENT;
