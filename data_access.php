<?php
/*
 * Created on 11.09.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 //include "config.php";
 
 
 
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
