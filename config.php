<?php 
$hostname="localhost"; 
$dbuser="root"; 
$dbpass="mysqlroot";
$dbName="uws_devel";


mysql_connect($hostname, $dbuser, $dbpass)  or  die( "Unable  to  connect to  SQL  server");
@mysql_select_db("$dbName")  or  die( "Unable  to  select  database $dbName"); 


include_once "globals.php";

?>
