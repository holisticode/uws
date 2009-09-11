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
-- DROP Database: 'uws'
--
DROP DATABASE IF EXISTS uws-devel;

--
-- CREATE Database: 'uws'
--
CREATE DATABASE uws-devel;
CONNECT uws-devel;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table 'uwssettings'
-- Miscellaneous settings
--

CREATE TABLE 'uws_settings' (
  'original_imported' boolean NOT NULL default '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------
--
-- Table structure for table 'uwsservice'
-- Someone created wealth by offering a service
--

CREATE TABLE 'uws_cell' (
	'cell_id' INT UNSIGNED NOT NULL AUTO_INCREMENT,
	'cell_name' VARCHAR(255) NOT NULL DEFAULT 'uws',
	'cell_clearing_id' VARCHAR(255) NOT NULL DEFAULT '',
	PRIMARY KEY  ('cell_id')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Table structure for table 'uwsservice'
-- Someone created wealth by offering a service
--
	
CREATE TABLE 'uws_member_attributes' (
	'avatar_link' VARCHAR(255) NOT NULL DEFAULT '',
	'color' CHAR(8) NOT NULL DEFAULT '0x000000',
	FOREIGN KEY  


-- --------------------------------------------------------
--
-- Table structure for table 'uwsservice'
-- Someone created wealth by offering a service
--


	
CREATE TABLE 'uws_member' (
	'member_id' INT UNSIGNED NOT NULL AUTO_INCREMENT,
	'join_date' TIMESTAMP NOT NULL DEFAULT '0',
	'leave_date' TIMESTAMP NOT NULL DEFAULT '0',
	'name' VARCHAR(255) NOT NULL DEFAULT '0',
	'password' VARCHAR(255) NOT NULL DEFAULT '',
	
	
)
-- --------------------------------------------------------
--
-- Table structure for table 'uwsservice'
-- Someone created wealth by offering a service
--

CREATE TABLE 'uws_service' (
  'journalID' mediumint(9) NOT NULL auto_increment,
  'date' INT NOT NULL default '0',
  'contributor' varchar(255) NOT NULL default '',
  'uwsservice' varchar(255) NOT NULL default '',
  'description' mediumtext NOT NULL,
  'lifetime' DECIMAL(20,6) NOT NULL default '0',
  'factor' DECIMAL(20,6) NOT NULL default '0',
  'link' varchar(255) NOT NULL,
  PRIMARY KEY  ('journalID')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'uwsservice'
-- Someone created wealth by offering a service
--

CREATE TABLE 'uwstotals' (
  'total_services' DECIMAL(20,6) NOT NULL default '0',
  'total_inventory' DECIMAL(20,6) NOT NULL default '0'
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

INSERT INTO uwstotals VALUES ('0','0');
-- --------------------------------------------------------
--
-- Table structure for table 'uwsbid'
-- Someone is using up a resource
--

CREATE TABLE 'uwsbid' (
  'bidID' mediumint(9) NOT NULL auto_increment,
  'date' INT NOT NULL default '0',
  'user' varchar(255) NOT NULL default '',
  'uwsunit' varchar(255) NOT NULL default '',
  'amount' DECIMAL(20,6) NOT NULL default '0',
  'price' DECIMAL(20,6) NOT NULL default '0',
  'factor' DECIMAL(20,6) NOT NULL default '0',
  FOREIGN KEY (uwsunit) REFERENCES uwsunits (unitID),
  PRIMARY KEY  ('bidID')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'uwsinventorize'
-- Someone created wealth by inventorizing some physical goods
--

CREATE TABLE 'uwsinventorize' (
  'journalID' mediumint(9) NOT NULL auto_increment,
  'date' INT NOT NULL default '0',
  'contributor' varchar(255) NOT NULL default '',
  'uwsunit' varchar(255) NOT NULL default '',
  'description' mediumtext NOT NULL,
  'amount' DECIMAL(20,6) NOT NULL default '0',
  'factor' DECIMAL(20,6) NOT NULL default '0',
  'link' varchar(255) NOT NULL,
  PRIMARY KEY  ('journalID')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
--
-- Table structure for table 'uwsconsume'
-- Someone is using up a resource
--

CREATE TABLE 'uwsconsume' (
  'journalID' mediumint(9) NOT NULL auto_increment,
  'date' INT NOT NULL default '0',
  'contributor' varchar(255) NOT NULL default '',
  'uwsunit' varchar(255) NOT NULL default '',
  'description' mediumtext NOT NULL,
  'amount' DECIMAL(20,6) NOT NULL default '0',
  'factor' DECIMAL(20,6) NOT NULL default '0',
  'price' DECIMAL(20,6) NOT NULL default '0',
  'link' varchar(255) NOT NULL,
  PRIMARY KEY  ('journalID')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'uwscontributors'
--

CREATE TABLE 'uwscontributors' (
  'contributorID' mediumint(9) NOT NULL auto_increment,
  'joindate' INT NOT NULL default '0',
  'contributor' varchar(255) NOT NULL default '',
  'password' varchar(255) NOT NULL default '',
  'balance' DECIMAL(20,6) NOT NULL default '0',
  'color' varchar(255) NOT NULL,
  'description' mediumtext NOT NULL,
  PRIMARY KEY  ('contributorID')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Table structure for table 'uwsunits'
-- List of units of available resources
--

CREATE TABLE 'uwsunits' (
  'unitID' mediumint(9) NOT NULL auto_increment,
  'datecreated' INT NOT NULL default '0',
  'unit' varchar(255) NOT NULL default '',
  'inventory' DECIMAL(20,6) NOT NULL default '0',
  'physical' DECIMAL(20,6) NOT NULL default '0',
  'factor' DECIMAL(20,6) NOT NULL default '1',
  'description' mediumtext NOT NULL,
  PRIMARY KEY  ('unitID')
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table 'uwsservices'
-- List of available services
--

CREATE TABLE 'uwsservices' (
  'serviceID' mediumint(9) NOT NULL auto_increment,
  'datecreated' INT NOT NULL default '0',
  'service' varchar(255) NOT NULL default '',
  'delivered' DECIMAL(20,6) NOT NULL default '0',
  'description' mediumtext NOT NULL,
  PRIMARY KEY  ('serviceID')
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
-- TRIGGERS
-- --------------------------------------------------------

delimiter //
CREATE TRIGGER uwsservice_new AFTER INSERT ON uwsservice
FOR EACH ROW
	BEGIN
		CALL update_balance(NEW.contributor, NEW.lifetime*NEW.factor);
		CALL update_services(NEW.uwsservice, NEW.lifetime*NEW.factor);
	END//
delimiter ;

-- --------------------------------------------------------

delimiter //
CREATE TRIGGER uwsinventorize_new AFTER INSERT ON uwsinventorize
FOR EACH ROW
	BEGIN
		CALL update_inventory(NEW.uwsunit, NEW.amount, NEW.factor);
	END//
delimiter ;

-- --------------------------------------------------------

delimiter //
CREATE TRIGGER uwsconsume_new AFTER INSERT ON uwsconsume
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
		UPDATE uwscontributors SET balance = balance - amount WHERE contributor=who;
		UPDATE uwstotals SET total_services = total_services - amount;
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_balance(who varchar(255), amount DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE uwscontributors SET balance = balance + amount WHERE contributor=who;
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_inventory(which varchar(255), amount DECIMAL(20,6) UNSIGNED, factor DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE uwsunits SET inventory = inventory + (amount*factor) WHERE unit=which;
		UPDATE uwsunits SET physical = physical + amount WHERE unit=which;
		UPDATE uwstotals SET total_inventory = total_inventory + (amount*factor);
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE decrease_units(what varchar(255), amount DECIMAL(20,6) UNSIGNED, factor DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE uwsunits SET inventory = inventory - (amount*factor) WHERE unit=what;
		UPDATE uwsunits SET physical = physical - amount WHERE unit=what;
		UPDATE uwstotals SET total_inventory = total_inventory - (amount*factor);
	END$$
delimiter ;

-- --------------------------------------------------------

delimiter $$
CREATE PROCEDURE update_services(what varchar(255), amount DECIMAL(20,6) UNSIGNED)
	BEGIN
		UPDATE uwsservices SET delivered = delivered + amount WHERE service=what;
		UPDATE uwstotals SET total_services = total_services + amount;
	END$$
delimiter ;

-- --------------------------------------------------------
