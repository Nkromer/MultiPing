-- phpMyAdmin SQL for MultiPing DB setup
-- version 4.0.10.7
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Feb 26, 2016 at 09:07 AM
-- Server version: 5.5.48-cll
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `workerpi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `time_begin` int(11) NOT NULL,
  `time_end` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `manager` varchar(128) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `people`
--

CREATE TABLE IF NOT EXISTS `people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(64) NOT NULL,
  `lastname` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `specialty` varchar(128) NOT NULL,
  `priority` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `people_phone_numbers`
--

CREATE TABLE IF NOT EXISTS `people_phone_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `primary` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `person_id` (`person_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `ping_recipient_log`
--

CREATE TABLE IF NOT EXISTS `ping_recipient_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `ping_request_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `status_name` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_3` (`id`),
  KEY `id_2` (`id`),
  KEY `id_4` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `ping_recipients`
--

CREATE TABLE IF NOT EXISTS `ping_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `status_name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

-- --------------------------------------------------------

--
-- Table structure for table `ping_requests`
--

CREATE TABLE IF NOT EXISTS `ping_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_initiated` int(11) NOT NULL,
  `time_closed` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `status_name` text NOT NULL,
  `message_body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Table structure for table `twilio_responses`
--

CREATE TABLE IF NOT EXISTS `twilio_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) NOT NULL,
  `response_body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `twilio_text_responses`
--

CREATE TABLE IF NOT EXISTS `twilio_text_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_received` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `MessageSid` varchar(255) NOT NULL,
  `SmsSid` varchar(255) NOT NULL,
  `AccountSid` varchar(255) NOT NULL,
  `MessagingServiceSid` varchar(255) NOT NULL,
  `From` varchar(255) NOT NULL,
  `To` varchar(255) NOT NULL,
  `Body` varchar(255) NOT NULL,
  `NumMedia` varchar(255) NOT NULL,
  `FromCity` varchar(255) NOT NULL,
  `FromState` varchar(255) NOT NULL,
  `FromCountry` varchar(255) NOT NULL,
  `FromZip` varchar(255) NOT NULL,
  `ToCity` varchar(255) NOT NULL,
  `ToState` varchar(255) NOT NULL,
  `ToZip` varchar(255) NOT NULL,
  `ToCountry` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `status_name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
