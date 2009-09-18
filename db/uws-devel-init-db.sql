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
--
-- Table structure for table `settings`
-- Miscellaneous settings for the whole database
--

CREATE TABLE `settings` (
  `original_imported` BOOLEAN NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO settings VALUES('0');

-- --------------------------------------------------------
--
-- Table structure for table `cell`
-- Information about the uws cell
--
CREATE  TABLE `network` (
  `cell_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cell_name` VARCHAR(45) NOT NULL ,
  `cell_clearing_id` VARCHAR(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`cell_id`) 
) ENGINE = InnoDB DEFAULT CHARACTER SET = utf8;


-- --------------------------------------------------------
--
-- Table structure for table `members`
-- Information about a member

CREATE TABLE `members` (
  `member_id` INT UNSIGNED NOT NULL auto_increment,
  `join_date` INT UNSIGNED NOT NULL,
  `leave_date` INT UNSIGNED,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL default '',
  `cell_id` INT UNSIGNED,
  `balance` DECIMAL(15,6) NOT NULL default '0',
  `email` VARCHAR(45),
  `color` CHAR(8) NOT NULL DEFAULT 'OxF0F0F0',
  `avatar_link` VARCHAR(255),
  `description` mediumtext,
  PRIMARY KEY  (`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `transaction_type`
-- UWS knows 3 types of transactions:
-- services, inventorizations, consumations
--
CREATE TABLE `transaction_type` (
	`type_code` SMALLINT UNSIGNED NOT NULL,
	`type_desc` VARCHAR(255),
	PRIMARY KEY  (`type_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO transaction_type VALUES ('1','Service');
INSERT INTO transaction_type VALUES ('2','Inventorization');
INSERT INTO transaction_type VALUES ('3','Consumation');

-- --------------------------------------------------------
--
-- Table structure for table `transactions`
-- The table structure common to all transactions
-- Through the transaction_type and the transaction_id
-- fields, the rest of the the whole transaction record 
-- can be retrieved in the appropriate table
--
CREATE TABLE `transactions` (
	`journal_id` INT UNSIGNED NOT NULL auto_increment,
  	`tstamp` INT UNSIGNED NOT NULL,
  	`transaction_type` SMALLINT UNSIGNED NOT NULL,
  	`transaction_id` INT NOT NULL,
  	`member_id` INT UNSIGNED NOT NULL,
  	`description` mediumtext NOT NULL,
  	`factor` DECIMAL(20,6) NOT NULL default '0',
  	`link` varchar(255) NOT NULL,
  	`balance` DECIMAL(20,6) DEFAULT '0',
  	PRIMARY KEY  (`journal_id`),
  	FOREIGN KEY  (`transaction_type`) REFERENCES transaction_type(`type_code`),
    FOREIGN KEY  (`member_id`) REFERENCES members(`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `assets`
-- List of units of available resources
--

CREATE TABLE `assetlist` (
  `asset_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `datecreated` INT UNSIGNED NOT NULL,
  `asset` varchar(255) NOT NULL,
  `inventory` DECIMAL(20,6) NOT NULL default '0',
  `physical` DECIMAL(20,6) NOT NULL default '0',
  `last_factor` DECIMAL(20,6) NOT NULL default '1',
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `uwsservices`
-- List of available services
--
CREATE TABLE `servicelist` (
  `service_id` INT UNSIGNED NOT NULL auto_increment,
  `datecreated` INT UNSIGNED NOT NULL,
  `service` varchar(255) NOT NULL,
  `provided` DECIMAL(20,6) NOT NULL default '0',
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `service`
-- Someone created wealth by offering a service
--
CREATE TABLE `service` (
  	`journal_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  	`transaction_id` INT UNSIGNED NOT NULL,
  	`receiver_id` INT UNSIGNED,
  	`receiver_balance` DECIMAL(20,6) DEFAULT '0',
  	`service_id` INT UNSIGNED NOT NULL,
  	`lifetime` DECIMAL(20,6) NOT NULL,
  	PRIMARY KEY  (`journal_id`),
  	FOREIGN KEY  (`transaction_id`) REFERENCES transactions(`journal_id`),
  	-- FOREIGN KEY  (`receiver_id`) REFERENCES members(`member_id`),
  	-- check here with a trigger that on insert the receiver is an existing id
  	FOREIGN KEY  (`service_id`) REFERENCES servicelist(`service_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `inventorize`
-- Someone created wealth by inventorizing some physical goods
--

CREATE TABLE `inventorize` (
	`journal_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`transaction_id` INT UNSIGNED NOT NULL,
  	`asset_id` INT UNSIGNED NOT NULL,  	
  	`is_donation` BOOLEAN NOT NULL DEFAULT '0',  	
  	`amount_physical` DECIMAL(20,6) NOT NULL default '0',
  	`amount_inventory` DECIMAL(20,6) NOT NULL default '0',
  	PRIMARY KEY  (`journal_id`),
  	FOREIGN KEY  (`transaction_id`) REFERENCES transactions(`journal_id`),
  	FOREIGN KEY  (`asset_id`) REFERENCES assetlist(`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `consume`
-- Someone is using up a resource
--

CREATE TABLE `consume` (
	`journal_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  	`transaction_id` INT UNSIGNED NOT NULL,
  	`asset_id` INT UNSIGNED NOT NULL,
  	`amount` DECIMAL(20,6) NOT NULL default '0',
  	`price` DECIMAL(20,6) NOT NULL default '0',
  	PRIMARY KEY  (`journal_id`),
  	FOREIGN KEY  (`transaction_id`) REFERENCES transactions(`journal_id`),
  	FOREIGN KEY  (`asset_id`) REFERENCES assetlist(`asset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



-- --------------------------------------------------------
--
-- Table structure for table `balance_history`
-- In order to display running balances, we need to store
-- them for fast retrieval, even if it is a calculated element.
-- The point here is that recalculating the values on each 
-- balance request is totally inefficient here, as uws
-- calculations go over the totals (total_services and 
-- total_inventory). As these change continuosly,
-- it makes no sense to recalculate balances. Therefore,
-- the requirement is that TRANSACTION HISTORY CHANGES
-- ARE NOT ALLOWED. Transactions happen in an unchangeable
-- sequence.

CREATE TABLE `balance_history` (
	`history_id` INT UNSIGNED NOT NULL,
	`transaction_type` SMALLINT UNSIGNED NOT NULL,
	`transaction_id` INT UNSIGNED NOT NULL,
	`member_id` INT UNSIGNED NOT NULL,
	`balance` DECIMAL(15,6) DEFAULT '0',
	FOREIGN KEY (`member_id`) REFERENCES members(`member_id`),
	FOREIGN KEY (`transaction_id`) REFERENCES transactions(`journal_id`),
    FOREIGN KEY (`transaction_type`) REFERENCES transaction_type(`type_code`)
) ENGINE=InnoDB;


-- --------------------------------------------------------
--
-- Table structure for table `totals`
-- UWS balances service and asset markets over the formula:
--
-- consume_units * total_services / total_inventory = service_units
-- 
-- So every price, tagged in service_units, for a purchase
-- of a good (in consume_units) is calculated over the totals.
-- These need to be stored for performance, as it isn't
-- conceivable of re-calculating these totals for every
-- purchase.

CREATE TABLE `totals` (
  	`total_services` DECIMAL(20,6) NOT NULL default '0',
  	`total_inventory` DECIMAL(20,6) NOT NULL default '0'
) ENGINE=InnoDB;

INSERT INTO totals VALUES ('0','0');


-- --------------------------------------------------------
--
-- Table structure for table `bid`
-- Members can buy things buy bidding, adjusting
-- prices to demand and offer.
--
CREATE TABLE `bid` (
  	`bid_id` INT UNSIGNED NOT NULL auto_increment,
  	`tstamp` INT UNSIGNED NOT NULL,
  	`member_id` INT UNSIGNED NOT NULL,
  	`asset_id` INT UNSIGNED NOT NULL,
  	`amount` DECIMAL(20,6) NOT NULL default '0',
  	`price` DECIMAL(20,6) NOT NULL default '0',
  	`factor` DECIMAL(20,6) NOT NULL default '0',
  	PRIMARY KEY (`bid_id`),
 	FOREIGN KEY (`asset_id`) REFERENCES assetlist(`asset_id`), 	
 	FOREIGN KEY (`member_id`) REFERENCES members(`member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
