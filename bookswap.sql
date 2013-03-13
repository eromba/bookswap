-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2013 at 09:17 AM
-- Server version: 5.5.10
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bookswap`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `bid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `isbn` varchar(13) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `edition` varchar(63) DEFAULT NULL,
  `authors` varchar(127) DEFAULT NULL,
  `publisher` varchar(63) DEFAULT NULL,
  `publication_date` varchar(31) DEFAULT NULL,
  `binding` varchar(31) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `image_url` varchar(127) DEFAULT NULL,
  `bookstore_product_id` varchar(15) DEFAULT NULL,
  `bookstore_part_number` varchar(15) DEFAULT NULL,
  `bookstore_new_price` decimal(5,2) DEFAULT NULL,
  `bookstore_used_price` decimal(5,2) DEFAULT NULL,
  `amazon_url` varchar(255) DEFAULT NULL,
  `amazon_list_price` decimal(5,2) DEFAULT NULL,
  `amazon_new_price` decimal(5,2) DEFAULT NULL,
  `amazon_used_price` decimal(5,2) DEFAULT NULL,
  `amazon_updated` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bid`),
  UNIQUE KEY `Unique_Item` (`title`,`edition`,`authors`,`publisher`),
  UNIQUE KEY `ISBN` (`isbn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Items includes, but is not limited to isbn-having books.' AUTO_INCREMENT=8302 ;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE IF NOT EXISTS `courses` (
  `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `did` int(11) unsigned NOT NULL,
  `code` varchar(15) NOT NULL COMMENT 'Course_Code known/shown to students',
  `name` varchar(31) NOT NULL,
  `bookstore_id` int(11) DEFAULT NULL COMMENT 'value sent to the Bookstore.  It can be NULL because of the Neebo situation where specific courses are never sent to the bookstore..',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `Department_Course` (`did`,`bookstore_id`),
  KEY `Department_ID` (`did`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3446 ;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `did` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(11) unsigned NOT NULL,
  `code` varchar(15) NOT NULL COMMENT 'Department_Code shown/known to students',
  `bookstore_id` int(11) NOT NULL COMMENT 'value sent to the Bookstore',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`did`),
  UNIQUE KEY `Term_Department` (`tid`,`code`),
  KEY `Term_ID` (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=305 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL,
  `postdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `edition` varchar(255) CHARACTER SET utf8 NOT NULL,
  `condition` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE IF NOT EXISTS `sections` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL,
  `code` varchar(15) NOT NULL COMMENT 'Class_Code known/shown to students',
  `bookstore_id` varchar(15) NOT NULL COMMENT 'value sent to the Bookstore',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `Course_Class` (`cid`,`bookstore_id`),
  KEY `Course_ID` (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7622 ;

-- --------------------------------------------------------

--
-- Table structure for table `sections_books`
--

CREATE TABLE IF NOT EXISTS `sections_books` (
  `weight` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Actually used in sorting, to tell us the importance of Books (based on the order  in which they were received when fetched from bookstore',
  `sid` int(11) unsigned NOT NULL,
  `bid` int(11) unsigned DEFAULT NULL,
  `required_status` tinyint(4) DEFAULT NULL COMMENT 'e.g. "Required", "Recommended", etc.  We grab from Bookstore',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`weight`),
  UNIQUE KEY `Class_Item` (`sid`,`bid`),
  KEY `Item_ID` (`bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17476 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `ip_address` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `user_agent` varchar(120) CHARACTER SET utf8 NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE IF NOT EXISTS `terms` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL COMMENT 'Shown in drop down',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `netid` varchar(7) CHARACTER SET utf8 NOT NULL,
  `email` varchar(63) CHARACTER SET utf8 DEFAULT NULL,
  `first_name` varchar(31) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `netid` (`netid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
