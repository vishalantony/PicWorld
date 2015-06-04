-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2015 at 08:21 PM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `picworld`
--

CREATE DATABASE IF NOT EXISTS picworld;
USE picworld;

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE IF NOT EXISTS `activities` (
  `activity_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `emailid` varchar(100) DEFAULT NULL,
  `activity` mediumtext,
  `activity_date` datetime DEFAULT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `emailid` (`emailid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `date_of_creat` datetime NOT NULL,
  `name` varchar(64) DEFAULT 'my album',
  `owner` varchar(100) NOT NULL,
  `description` mediumtext,
  `type` enum('private','public','friends') DEFAULT 'public',
  PRIMARY KEY (`aid`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) DEFAULT NULL,
  `comment` mediumtext,
  `comment_by` varchar(100) DEFAULT NULL,
  `date_of_comment` datetime DEFAULT NULL,
  PRIMARY KEY (`cid`),
  KEY `comment_by` (`comment_by`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `sentby` varchar(100) NOT NULL,
  `acceptedby` varchar(100) NOT NULL,
  `requested_date` date NOT NULL,
  `accepted_date` date NOT NULL,
  PRIMARY KEY (`sentby`,`acceptedby`),
  KEY `acceptedby` (`acceptedby`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE IF NOT EXISTS `likes` (
  `pid` bigint(20) NOT NULL DEFAULT '0',
  `liked_by` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`pid`,`liked_by`),
  KEY `liked_by` (`liked_by`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notif_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `emailid` varchar(100) DEFAULT NULL,
  `notification` mediumtext,
  `notif_date` datetime DEFAULT NULL,
  `read_state` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`notif_id`),
  KEY `emailid` (`emailid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pending`
--

CREATE TABLE IF NOT EXISTS `pending` (
  `sent_by` varchar(100) NOT NULL DEFAULT '',
  `sent_to` varchar(100) NOT NULL DEFAULT '',
  `requested_date` datetime DEFAULT NULL,
  PRIMARY KEY (`sent_by`,`sent_to`),
  KEY `sent_to` (`sent_to`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `pid` bigint(20) NOT NULL AUTO_INCREMENT,
  `no_likes` int(11) DEFAULT '0',
  `owner` varchar(100) NOT NULL,
  `aid` int(11) NOT NULL,
  `pic_path` varchar(255) DEFAULT NULL,
  `upload_date` datetime NOT NULL,
  `type` enum('private','public','friends') DEFAULT 'private',
  `story` mediumtext,
  `height` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `aid` (`aid`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profile_pics`
--

CREATE TABLE IF NOT EXISTS `profile_pics` (
  `owner` varchar(100) NOT NULL,
  `pro_pic` varchar(255) NOT NULL DEFAULT 'pictures/profile_pictures/default_dp_M.jpg',
  `set_date` datetime DEFAULT NULL,
  `height` int(11) DEFAULT '200',
  `width` int(11) DEFAULT '200',
  `ppid` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`owner`),
  KEY `ppid` (`ppid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `thumbs`
--

CREATE TABLE IF NOT EXISTS `thumbs` (
  `thumbid` bigint(20) NOT NULL AUTO_INCREMENT,
  `thumb_path` varchar(255) DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`thumbid`),
  UNIQUE KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `emailid` varchar(100) NOT NULL,
  `user_passwd` tinyblob NOT NULL,
  `bday` date DEFAULT NULL,
  `sex` enum('M','F','O') NOT NULL,
  `place_of_work` varchar(50) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `college` varchar(150) DEFAULT NULL,
  `description` mediumtext,
  PRIMARY KEY (`emailid`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`emailid`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `album_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`comment_by`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `photos` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`sentby`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`acceptedby`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`liked_by`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`pid`) REFERENCES `photos` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`emailid`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pending`
--
ALTER TABLE `pending`
  ADD CONSTRAINT `pending_ibfk_1` FOREIGN KEY (`sent_by`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pending_ibfk_2` FOREIGN KEY (`sent_to`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`aid`) REFERENCES `album` (`aid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `photos_ibfk_2` FOREIGN KEY (`owner`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profile_pics`
--
ALTER TABLE `profile_pics`
  ADD CONSTRAINT `profile_pics_ibfk_1` FOREIGN KEY (`ppid`) REFERENCES `photos` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profile_pics_ibfk_2` FOREIGN KEY (`owner`) REFERENCES `user` (`emailid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thumbs`
--
ALTER TABLE `thumbs`
  ADD CONSTRAINT `thumbs_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `photos` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
