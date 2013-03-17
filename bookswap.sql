SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `books` (
  `subject` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Department_Code shown/known to students',
  `bid` int(10) unsigned DEFAULT NULL,
  `class_id` int(10) unsigned NOT NULL,
  `bookstore_price` decimal(5,2) DEFAULT NULL COMMENT 'We store here rather than Items, because you could have the same Item being listed at different prices depending  on the bookstore.',
  `isbn` char(13) CHARACTER SET utf8 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `authors` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '''''',
  `publisher` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '''''',
  `edition` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '''''',
  `course_id` int(10) unsigned NOT NULL,
  `class_code` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Class_Code known/shown to students',
  `course` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT 'Course_Code known/shown to students',
  `department_id` int(10) unsigned NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `amzn_link` text,
  `amzn_used_price` decimal(5,2) DEFAULT NULL,
  `amzn_new_price` decimal(5,2) DEFAULT NULL,
  `amzn_list_price` decimal(5,2) DEFAULT NULL,
  `amzn_last_update` timestamp NULL DEFAULT NULL,
  `subj_class` varchar(16) DEFAULT NULL,
  `row_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`row_id`),
  KEY `isbn` (`isbn`),
  KEY `course` (`course`),
  KEY `bid` (`bid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3034 ;

CREATE TABLE IF NOT EXISTS `posts` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `price` int(11) NOT NULL,
  `postdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `notes` text,
  `edition` varchar(7) NOT NULL,
  `condition` enum('Very Good','Good','Ok','Poor') NOT NULL DEFAULT 'Ok',
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `netid` varchar(7) NOT NULL,
  `email` varchar(63) DEFAULT NULL,
  `first_name` varchar(31) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `netid` (`netid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
