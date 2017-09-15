-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2017 at 05:23 PM
-- Server version: 5.6.15-log
-- PHP Version: 5.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `casestudy_pretoutil`
--
CREATE DATABASE IF NOT EXISTS `casestudy_pretoutil` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `casestudy_pretoutil`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--
-- Creation: Sep 15, 2017 at 10:45 AM
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comid` int(10) NOT NULL AUTO_INCREMENT,
  `tid` int(10) NOT NULL,
  `rid` int(10) NOT NULL,
  `text` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  PRIMARY KEY (`comid`),
  KEY `tid` (`tid`,`rid`,`createdBy`),
  KEY `rid` (`rid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `comments`:
--   `createdBy`
--       `users` -> `uid`
--   `rid`
--       `requests` -> `rid`
--   `tid`
--       `tools` -> `tid`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--
-- Creation: Sep 15, 2017 at 10:54 AM
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `gid` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `isActive` tinyint(1) NOT NULL,
  `source` varchar(10) NOT NULL,
  `isImported` tinyint(1) NOT NULL,
  PRIMARY KEY (`gid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--
-- Creation: Sep 15, 2017 at 10:46 AM
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `rid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `tid` int(10) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT 'type of request',
  `fromTime` bigint(30) NOT NULL,
  `toTime` bigint(30) NOT NULL,
  `isConfirmed` tinyint(1) NOT NULL DEFAULT '0',
  `isCompleted` tinyint(1) NOT NULL DEFAULT '0',
  `isReturned` tinyint(1) NOT NULL DEFAULT '0',
  `createdBy` int(10) NOT NULL,
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `uid` (`uid`,`tid`,`createdBy`),
  KEY `tid` (`tid`),
  KEY `createdBy` (`createdBy`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `requests`:
--   `createdBy`
--       `users` -> `uid`
--   `tid`
--       `tools` -> `tid`
--   `uid`
--       `users` -> `uid`
--

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--
-- Creation: Sep 15, 2017 at 11:36 AM
-- Last update: Sep 15, 2017 at 11:36 AM
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `sid` varchar(32) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `time` bigint(30) NOT NULL,
  `ip` varchar(40) NOT NULL,
  PRIMARY KEY (`sid`),
  KEY `uid` (`uid`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`sid`, `uid`, `time`, `ip`) VALUES
('f1938a847523b18bbf622ee36fd77ee7', 0, 1505475390, '127.0.0.1'),
('d3c99b0afb63a78230fd35d297e2122d', 0, 1505487586, '127.0.0.1'),
('509551cb9b9495a8db3a5f919cd957ac', 0, 1505488274, '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--
-- Creation: Sep 15, 2017 at 10:47 AM
--

DROP TABLE IF EXISTS `tools`;
CREATE TABLE IF NOT EXISTS `tools` (
  `tid` int(10) NOT NULL AUTO_INCREMENT,
  `ownedBy` int(10) NOT NULL,
  `name` varchar(120) NOT NULL,
  `type` varchar(120) NOT NULL,
  `isGroupExclusive` tinyint(1) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdOn` bigint(30) NOT NULL,
  `createdBy` int(10) NOT NULL,
  `modifiedOn` bigint(30) NOT NULL,
  `modifiedBy` int(10) NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `ownedBy` (`ownedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `tools`:
--   `ownedBy`
--       `users` -> `uid`
--

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--
-- Creation: Sep 15, 2017 at 10:55 AM
--

DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE IF NOT EXISTS `usergroup` (
  `ugid` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `gid` int(10) NOT NULL,
  `addedBy` int(10) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ugid`),
  KEY `uid` (`uid`,`gid`,`addedBy`),
  KEY `gid` (`gid`),
  KEY `addedBy` (`addedBy`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- RELATIONS FOR TABLE `usergroup`:
--   `addedBy`
--       `users` -> `uid`
--   `gid`
--       `groups` -> `gid`
--   `uid`
--       `users` -> `uid`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Sep 15, 2017 at 11:38 AM
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `token` varchar(150) DEFAULT NULL,
  `firstName` varchar(120) NOT NULL,
  `lastName` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(120) NOT NULL,
  `source` varchar(10) NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `createdOn` bigint(30) NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `tools` (`tid`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`rid`) REFERENCES `requests` (`rid`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`createdBy`) REFERENCES `users` (`uid`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `tools` (`tid`),
  ADD CONSTRAINT `requests_ibfk_3` FOREIGN KEY (`createdBy`) REFERENCES `users` (`uid`);

--
-- Constraints for table `tools`
--
ALTER TABLE `tools`
  ADD CONSTRAINT `tools_ibfk_1` FOREIGN KEY (`ownedBy`) REFERENCES `users` (`uid`);

--
-- Constraints for table `usergroup`
--
ALTER TABLE `usergroup`
  ADD CONSTRAINT `usergroup_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  ADD CONSTRAINT `usergroup_ibfk_2` FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`),
  ADD CONSTRAINT `usergroup_ibfk_3` FOREIGN KEY (`addedBy`) REFERENCES `users` (`uid`);
