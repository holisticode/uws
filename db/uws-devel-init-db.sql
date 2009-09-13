-- phpMyAdmin SQL Dump
-- version 2.11.9.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 27, 2009 at 05:25 PM
-- Server version: 5.0.45
-- PHP Version: 4.3.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- DROP Database: `uws`
--
DROP DATABASE IF EXISTS uws_devel;

--
-- CREATE Database: `uws`
--
CREATE DATABASE uws_devel;
CONNECT uws_devel;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `uwssettings`
-- Miscellaneous settings
--

CREATE TABLE `settings` (
  `original_imported` boolean NOT NULL default '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `uwsservice`
-- Someone created wealth by offering a service
--
CREATE TABLE `transaction_type` (
	`type_code` SMALLINT NOT NULL,
	`type_desc` VARCHAR(255),
	PRIMARY KEY  (`type_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO transaction_type VALUES ('1','Service');
INSERT INTO transaction_type VALUES ('2','Inventorization');
INSERT INTO transaction_type VALUES ('3','Consumation');
-- --------------------------------------------------------
--
-- Table structure for table `uwsservice`
-- Someone created wealth by offering a service
--
CREATE TABLE `balance_history` (
	`history_id` INT NOT NULL,
	`transaction_type` SMALLINT NOT NULL,
	`transaction_id` INT NOT NULL,
--	`user_id` INT NOT NULL,
	`contributor` VARCHAR(255) NOT NULL,
	`balance` DECIMAL(15,6) DEFAULT '0',
--	CONSTRAINT `user_id`
--    FOREIGN KEY ()
--    REFERENCES `uws`.`contributors` ()
--    ON DELETE CASCADE
--    ON UPDATE CASCADE,
	CONSTRAINT `fk_transaction_type`
    FOREIGN KEY (transaction_type)
    REFERENCES transaction_type(type_code)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB;

-- --------------------------------------------------------
--
-- Table structure for table `uwsservice`
-- Someone created wealth by offering a service
--
CREATE TABLE `service` (
  `journalID` mediumint(9) NOT NULL auto_increment,
  `date` INT NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `uwsservice` varchar(255) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `lifetime` DECIMAL(20,6) NOT NULL default '0',
  `factor` DECIMAL(20,6) NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`journalID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uwsservice`
-- Someone created wealth by offering a service
--

CREATE TABLE `totals` (
  `total_services` DECIMAL(20,6) NOT NULL default '0',
  `total_inventory` DECIMAL(20,6) NOT NULL default '0'
) ENGINE=InnoDB;

INSERT INTO totals VALUES ('0','0');
-- --------------------------------------------------------
--
-- Table structure for table `uwsbid`
-- Someone is using up a resource
--

CREATE TABLE `bid` (
  `bidID` mediumint(9) NOT NULL auto_increment,
  `date` INT NOT NULL default '0',
  `user` varchar(255) NOT NULL default '',
  `uwsunit` varchar(255) NOT NULL default '',
  `amount` DECIMAL(20,6) NOT NULL default '0',
  `price` DECIMAL(20,6) NOT NULL default '0',
  `factor` DECIMAL(20,6) NOT NULL default '0',
 -- FOREIGN KEY (uwsunit) REFERENCES units(unitID),
  PRIMARY KEY  (`bidID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uwsinventorize`
-- Someone created wealth by inventorizing some physical goods
--

CREATE TABLE `inventorize` (
  `journalID` mediumint(9) NOT NULL auto_increment,
  `date` INT NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `uwsunit` varchar(255) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `amount` DECIMAL(20,6) NOT NULL default '0',
  `factor` DECIMAL(20,6) NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`journalID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `uwsconsume`
-- Someone is using up a resource
--

CREATE TABLE `consume` (
  `journalID` mediumint(9) NOT NULL auto_increment,
  `date` INT NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `uwsunit` varchar(255) NOT NULL default '',
  `description` mediumtext NOT NULL,
  `amount` DECIMAL(20,6) NOT NULL default '0',
  `factor` DECIMAL(20,6) NOT NULL default '0',
  `price` DECIMAL(20,6) NOT NULL default '0',
  `link` varchar(255) NOT NULL,
  PRIMARY KEY  (`journalID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uwscontributors`
--

CREATE TABLE `contributors` (
  `contributorID` mediumint(9) NOT NULL auto_increment,
  `joindate` INT NOT NULL default '0',
  `contributor` varchar(255) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `balance` DECIMAL(20,6) NOT NULL default '0',
  `color` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`contributorID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `uwsunits`
-- List of units of available resources
--

CREATE TABLE `units` (
  `unitID` mediumint(9) NOT NULL auto_increment,
  `datecreated` INT NOT NULL default '0',
  `unit` varchar(255) NOT NULL default '',
  `inventory` DECIMAL(20,6) NOT NULL default '0',
  `physical` DECIMAL(20,6) NOT NULL default '0',
  `factor` DECIMAL(20,6) NOT NULL default '1',
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`unitID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uwsservices`
-- List of available services
--

CREATE TABLE `services` (
  `serviceID` mediumint(9) NOT NULL auto_increment,
  `datecreated` INT NOT NULL default '0',
  `service` varchar(255) NOT NULL default '',
  `delivered` DECIMAL(20,6) NOT NULL default '0',
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`serviceID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- TRIGGERS
-- --------------------------------------------------------

delimiter //
CREATE TRIGGER uwsservice_new AFTER INSERT ON service
FOR EACH ROW
	BEGIN		
		CALL update_balance(NEW.contributor, NEW.lifetime*NEW.factor);
		CALL update_services(NEW.uwsservice, NEW.lifetime*NEW.factor);
		CALL update_balance_history(NEW.contributor,NEW.journalID, 1);
	END//
delimiter ;

-- --------------------------------------------------------

delimiter //
CREATE TRIGGER uwsinventorize_new AFTER INSERT ON inventorize
FOR EACH ROW
	BEGIN
		CALL update_inventory(NEW.uwsunit, NEW.amount, NEW.factor);
	END//
delimiter ;

-- --------------------------------------------------------

delimiter //
CREATE TRIGGER uwsconsume_new AFTER INSERT ON consume
FOR EACH ROW
	BEGIN
		CALL decrease_units(NEW.uwsunit, NEW.amount, NEW.factor);
		CALL decrease_balance(NEW.contributor, NEW.price);
	END//
delimiter ;

-- --------------------------------------------------------
-- PROCEDURES
-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE decrease_balance(who varchar(255), amount DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE contributors SET balance = balance - amount WHERE contributor=who;
		UPDATE totals SET total_services = total_services - amount;
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_balance(who varchar(255), amount DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE contributors SET balance = balance + amount WHERE contributor=who;
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_balance_history(who varchar(255), which INT, ta_type SMALLINT)
	BEGIN
		UPDATE balance_history SET transaction_type=ta_type, contributor=who, transaction_id=which;
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_inventory(which varchar(255), amount DECIMAL(20,6) UNSIGNED, factor DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE units SET inventory = inventory + (amount*factor) WHERE unit=which;
		UPDATE units SET physical = physical + amount WHERE unit=which;
		UPDATE totals SET total_inventory = total_inventory + (amount*factor);
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE decrease_units(what varchar(255), amount DECIMAL(20,6) UNSIGNED, factor DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE units SET inventory = inventory - (amount*factor) WHERE unit=what;
		UPDATE units SET physical = physical - amount WHERE unit=what;
		UPDATE totals SET total_inventory = total_inventory - (amount*factor);
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_services(what varchar(255), amount DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE services SET delivered = delivered + amount WHERE service=what;
		UPDATE totals SET total_services = total_services + amount;
	END$$
delimiter ;

-- --------------------------------------------------------
