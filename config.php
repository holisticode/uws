<?php
/*
 * UWS - Universal Wealth System
 * config.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * Main configuration file. Watch out for the passwords!
 * Also check depending on location, e.g. hosting requires
 * different passwords and database names!
 */
$hostname="localhost"; 
$dbuser="root"; 
$dbpass="mysqlroot";
$dbname="uws_devel";
//$dbName="uws";


mysql_connect($hostname, $dbuser, $dbpass)  or  die( "Unable  to  connect to  SQL  server");
@mysql_select_db("$dbname")  or  die( "Unable  to  select  database $dbName"); 

include "data_access.php";
include_once "globals.php";

?>
