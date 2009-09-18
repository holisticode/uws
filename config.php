<?php 
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
