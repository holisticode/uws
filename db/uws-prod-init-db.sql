-- phpMyAdmin SQL Dump
-- version 2.11.9.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 27, 2009 at 05:25 PM
-- Server version: 5.0.45
-- PHP Version: 4.3.11

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- DROP Database: `uws`
--
-- DROP DATABASE IF EXISTS uws;

--
-- CREATE Database: `uws`
--
-- CREATE DATABASE uws;
-- CONNECT usr_web399_4;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `uwschats`
--

CREATE TABLE `uwschats` (
  `uwsID` mediumint(9) NOT NULL auto_increment,
  `storyid` mediumint(9) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `entry` mediumtext NOT NULL,
  `entrydate` date NOT NULL default '0000-00-00',
  `work` mediumint(9) NOT NULL default '0',
  `factor` double NOT NULL default '0',
  `performance` double NOT NULL default '0',
  `shorttext` mediumtext NOT NULL,
  `longtext` longtext NOT NULL,
  `color` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`uwsID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uwsservice`
-- Someone created wealth by offering a service
--

CREATE TABLE `uwsservice` (
  `journalID` mediumint(9) NOT NULL auto_increment,
  `date` double NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `uwsservice` varchar(255) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `lifetime` double NOT NULL default '0',
  `factor` double NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`journalID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Table structure for table `uwsservice`
-- Someone created wealth by offering a service
--

-- --------------------------------------------------------

--
-- Table structure for table `uwsinventorize`
-- Someone created wealth by inventorizing some physical goods
--

CREATE TABLE `uwsinventorize` (
  `journalID` mediumint(9) NOT NULL auto_increment,
  `date` double NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `uwsunit` varchar(255) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `amount` double NOT NULL default '0',
  `factor` double NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`journalID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Table structure for table `uwsconsume`
-- Someone is using up a resource
--

CREATE TABLE `uwsconsume` (
  `journalID` mediumint(9) NOT NULL auto_increment,
  `date` double NOT NULL default '0',
  `user` varchar(255) NOT NULL default '',
  `uwsunit` varchar(255) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `amount` double NOT NULL default '0',
  `factor` double NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`journalID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uwscontributors`
--

CREATE TABLE `uwscontributors` (
  `contributorID` mediumint(9) NOT NULL auto_increment,
  `joindate` double NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `balance` double NOT NULL default '0',
  `color` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`contributorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `uwsstories`
--

CREATE TABLE `uwsstories` (
  `storyid` mediumint(9) NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL,
  `storyname` varchar(255) NOT NULL default '',
  `storydate` double NOT NULL,
  `user1` mediumint(9) NOT NULL default '0',
  `user2` mediumint(9) NOT NULL default '0',
  `factor1` double NOT NULL,
  `factor2` double NOT NULL default '0',
  `work1` double NOT NULL default '0',
  `work2` double NOT NULL default '0',
  `storylink` varchar(255) NOT NULL,
  PRIMARY KEY  (`storyid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------


--
-- Table structure for table `uwsunits`
-- List of units of available resources
--

CREATE TABLE `uwsunits` (
  `unitID` mediumint(9) NOT NULL auto_increment,
  `datecreated` double NOT NULL default '0',
  `unit` varchar(255) NOT NULL default '',
  `inventory` double NOT NULL default '0',
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`unitID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `uwsservices`
-- List of available services
--

CREATE TABLE `uwsservices` (
  `serviceID` mediumint(9) NOT NULL auto_increment,
  `datecreated` double NOT NULL default '0',
  `service` varchar(255) NOT NULL default '',
  `delivered` double NOT NULL default '0',
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`serviceID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

delimiter __ 
CREATE TRIGGER uwsservice_new AFTER INSERT ON uwsservice
FOR EACH ROW
	BEGIN
		CALL update_balance(NEW.contributor, NEW.lifetime*NEW.factor);
		CALL update_services(NEW.uwsservice, NEW.lifetime*NEW.factor);
	END__
delimiter ;

delimiter $$
CREATE TRIGGER uwsinventorize_new AFTER INSERT ON uwsinventorize
FOR EACH ROW
	BEGIN
		CALL update_inventory(NEW.uwsunit, NEW.amount*NEW.factor);
	END$$
delimiter ;

-- delimiter //
-- CREATE TRIGGER uwsservices_new AFTER INSERT ON uwsservice
-- FOR EACH ROW
-- BEGIN
--    CALL update_services(NEW.uwsservice, NEW.lifetime*NEW.factor);
-- END//
--  delimiter ;



delimiter $$
CREATE PROCEDURE update_balance(who varchar(255), amount double)
	BEGIN
		UPDATE uwscontributors SET balance = balance + amount WHERE contributor=who;
	END$$
delimiter ;

delimiter $$
CREATE PROCEDURE update_inventory(which varchar(255), amount double)
	BEGIN
		UPDATE uwsunits SET inventory = inventory + amount WHERE unit=which;
	END$$
delimiter ;

delimiter $$
CREATE PROCEDURE update_services(what varchar(255), amount double)
	BEGIN
		UPDATE uwsservices SET delivered = delivered + amount WHERE service=what;
	END$$
delimiter ;

-- --------------------------------------------------------

--
-- Table structure for table `uwssettings`
-- Miscellaneous settings
--

CREATE TABLE `uwssettings` (
  `original_imported` boolean NOT NULL default '0'
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
