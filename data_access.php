<?php
/*
 * Created on 11.09.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 include "config.php";
 
 
 
 class DataAccessor {
 		
 	function init_db_connection()
 	{
 		global $dbuser, $dbpass, $logger;
		$dbh = new PDO("mysql:host=$hostname;dbname=$dbName", $dbuser, $dbpass, 
      			array(PDO::ATTR_PERSISTENT => true));
      	
      	$logger->log("DB connection successful", PEAR_LOG_INFO);      			
  		  		
  		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		 		
 	}
 }
?>
