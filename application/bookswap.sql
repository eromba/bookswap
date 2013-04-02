SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `books` (
  `bid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `isbn` varchar(13) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `edition` varchar(63) DEFAULT NULL,
  `authors` varchar(127) DEFAULT NULL,
  `publisher` varchar(63) DEFAULT NULL,
  `publication_date` varchar(31) DEFAULT NULL,
  `binding` varchar(31) DEFAULT NULL,
  `product_type` tinyint(4) NOT NULL DEFAULT '0',
  `image_url` varchar(127) DEFAULT NULL,
  `bookstore_id` varchar(15) DEFAULT NULL,
  `bookstore_part_number` varchar(15) DEFAULT NULL,
  `bookstore_new_price` decimal(5,2) DEFAULT NULL,
  `bookstore_used_price` decimal(5,2) DEFAULT NULL,
  `amazon_url` varchar(255) DEFAULT NULL,
  `amazon_list_price` decimal(5,2) DEFAULT NULL,
  `amazon_new_price` decimal(5,2) DEFAULT NULL,
  `amazon_used_price` decimal(5,2) DEFAULT NULL,
  `amazon_updated` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bid`),
  UNIQUE KEY `bookstore_product_id` (`bookstore_id`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS `courses` (
  `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `did` int(11) unsigned NOT NULL,
  `code` varchar(15) NOT NULL,
  `name` varchar(31) NOT NULL,
  `bookstore_id` int(11) DEFAULT NULL,
  `scrape_status` tinyint(4) NOT NULL,
  `scraped` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `Department_Course` (`did`,`bookstore_id`),
  KEY `Department_ID` (`did`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `departments` (
  `did` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(11) unsigned NOT NULL,
  `code` varchar(15) NOT NULL,
  `bookstore_id` int(11) NOT NULL,
  `scrape_status` tinyint(4) NOT NULL,
  `scraped` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`did`),
  UNIQUE KEY `Term_Department` (`tid`,`code`),
  KEY `Term_ID` (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `posts` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `notes` text CHARACTER SET utf8 NOT NULL,
  `edition` varchar(255) CHARACTER SET utf8 NOT NULL,
  `condition` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sections` (
  `sid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(11) unsigned NOT NULL,
  `code` varchar(15) NOT NULL,
  `bookstore_id` varchar(15) NOT NULL,
  `scrape_status` tinyint(4) NOT NULL,
  `scraped` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `Course_Class` (`cid`,`bookstore_id`),
  KEY `Course_ID` (`cid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sections_books` (
  `weight` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) unsigned NOT NULL,
  `bid` int(11) unsigned DEFAULT NULL,
  `required_status` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`weight`),
  UNIQUE KEY `Class_Item` (`sid`,`bid`),
  KEY `Item_ID` (`bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `ip_address` varchar(45) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `user_agent` varchar(120) CHARACTER SET utf8 NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `terms` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `bookstore_id` int(11) NOT NULL,
  `scrape_status` tinyint(4) NOT NULL,
  `scraped` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `netid` varchar(7) CHARACTER SET utf8 NOT NULL,
  `email` varchar(63) CHARACTER SET utf8 DEFAULT NULL,
  `first_name` varchar(31) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `netid` (`netid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`did`) REFERENCES `departments` (`did`) ON DELETE CASCADE;

ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `terms` (`tid`) ON DELETE CASCADE;

ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `courses` (`cid`) ON DELETE CASCADE;

ALTER TABLE `sections_books`
  ADD CONSTRAINT `sections_books_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `sections` (`sid`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
