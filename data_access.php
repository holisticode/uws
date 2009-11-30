<?php
/*
 * UWS - Universal Wealth System
 * data_access.php
 * class DataAccessor
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * The db design of UWS is quite complex (and could be TODO:significantly improved).
 * Also, when storing transactions a whole series of updates to different tables
 * needs to be done. 
 * 
 * This mandates the usage of transactions-aware database commits in order to keep
 * transaction integrity over the different updates. Only after a commit the whole
 * series of updates will be written to the database. If something fails, a rollback
 * is initiated and the transaction failed.
 * 
 * In PHP we can use PDO to achieve transaction integrity. This file initializes
 * PDO and provides a global variables in order to access the PDO - aware object
 * in order to perform transaction integrity.
 */
 
 class DataAccessor {
 	
 	private $dbh = null;
 	
 	function init_db_connection()
 	{
 		global $dbuser, $dbpass, $dbname, $logger;
		$this->dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $dbuser, $dbpass, 
      			array(PDO::ATTR_PERSISTENT => true));
      	
      	//$logger->log("DB connection successful", PEAR_LOG_INFO);      			
      	error_log("DB connection successful", PEAR_LOG_INFO);
  		  		
  		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  		$this->dbh->setAttribute(PDO::ATTR_AUTOCOMMIT,FALSE);
		 		
 	}
 	
 	function get_connection()
 	{
 		return $this->dbh;
 	}
 	
 	
 }
?>
