<?php
/*
 * UWS - Universal Wealth System
 * globals.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * This file contains global variables and functions needed in other
 * sections of the application
 */
 
//constants defining the transaction types
$SERVICE_TYPE 		= 1;
$INVENTORIZE_TYPE 	= 2;
$CONSUME_TYPE 		= 3;

//default cell identification for the default import (import_db.php)
$DEFAULT_CELL = "OS";
$DEFAULT_CELL_ID = 0;

//if 1, the application has already been initialized and now further import needed
$INITIALIZED = 0;
//the link to the error page
$errorpage = "error.php?error=";

//the default language; language can currently only be set this way
$defaultlang = "de";
//$_SESSION['language'] = $defaultlang;

//this function should be called to set a new language;
//switching languages is currently not implemented
if (! isset($_SESSION['language']) ) {
	$_SESSION['language'] = $defaultlang;
}

//initialize the PDO data connection
$data_accessor = new DataAccessor();
$data_accessor->init_db_connection();
$dbh = $data_accessor->get_connection();

//get the translations
$messages = array();

function add_translation($lang, $array) {
	global $messages;

	if (! isset($messages[$lang]) ) {
		$messages[$lang] = $array;
	}
}

//translate a message
function translate($s) {
	$lang = $_SESSION['language'];
	global $messages; 

	if (isset($messages[$lang][$s]) )   {
		return $messages[$lang][$s];
	} else {
		error_log("UWS L10N error. Language: $lang, message: $s");
	}
} 

//include all files only once
function include_all_once ($pattern) {
	foreach (glob($pattern) as $file) {
		$ok = include $file;
	}
}

//include all language files only once
include_all_once('langs/*.php');

//get the transaction label, which is different for every transaction type
function get_transaction($ta_type, $ta_id)
{
	$transaction = "";
	$sql = "";
	switch ($ta_type) {
		case 1: //a service
			$sql 	= "SELECT service_id FROM service WHERE transaction_id='$ta_id'";
			$query 	= mysql_query($sql);
			$result = mysql_fetch_row($query);
			$srv_id = $result[0];
			
			$sql	= "SELECT service FROM servicelist WHERE service_id='$srv_id'";
			$query 	= mysql_query($sql);
			$result = mysql_fetch_row($query);
			$transaction = $result[0];
			break;
		case 2: //an inventorization
			$sql 	= "SELECT asset_id FROM inventorize WHERE transaction_id='$ta_id'";
			$query 	= mysql_query($sql);
			$result = mysql_fetch_row($query);
			$asset_id = $result[0];
			
			$sql	= "SELECT asset FROM assetlist WHERE asset_id='$asset_id'";
			$query 	= mysql_query($sql);
			$result = mysql_fetch_row($query);
			$transaction = $result[0];
			break;
		case 3: // a consumation
			$sql 	= "SELECT asset_id FROM consume WHERE transaction_id='$ta_id'";
			$query 	= mysql_query($sql);
			$result = mysql_fetch_row($query);
			$asset_id = $result[0];
			
			$sql	= "SELECT asset FROM assetlist WHERE asset_id='$asset_id'";
			$query 	= mysql_query($sql);
			$result = mysql_fetch_row($query);
			$transaction = $result[0];
			break;
	} 
	//echo "Transaction: ".$transaction;
	return $transaction;
}

//when looking for members, instead of querying the database repeatedly,
//store member names in this array
$found_members = array();

//get the member name from the id
function get_member_name_from_id($member_id) 
{
	global $found_members;	
	
	if (array_key_exists($member_id, $found_members))
	{
		//it is already in the array, had already been queried previously
		return $found_members [$member_id];
	}
	$sql 	= "SELECT name FROM members WHERE member_id='$member_id'";
	$query 	= mysql_query($sql);
	$set 	= mysql_fetch_row($query);
	$member = $set[0];
	
	//store in the array for in-memory retrieval
	$found_members[$member_id] = $member;
	return $member;
}

//get the member id from the name
function get_member_id_from_name($member) 
{
	global $found_members;	
	
	if ($member_id = array_search($member, $found_members))
	{	//it is already in the array, had already been queried previously
		return $member_id;
	}
	$sql 	= "SELECT member_id FROM members WHERE name='$member'";
	$query 	= mysql_query($sql);
	$set 	= mysql_fetch_row($query);
	$member_id = $set[0];

	//store in the array
	$found_members[$member_id] = $member;
	return $member_id;
}

//when retrieving asset names, store in this array
//in order for faster in-memory retrieval instead of
//repeated database querying
$found_assets = array();

//get an asset name from its id
function get_asset_name_from_id($asset_id) 
{
	global $found_assets;	
	
	if (array_key_exists($asset_id, $found_assets))
	{	//it is already in the array, had already been queried previously
		return $found_assets [$asset_id];
	}
	$sql 	= "SELECT asset FROM assetlist WHERE asset_id='$asset_id'";
	$query 	= mysql_query($sql);
	$set 	= mysql_fetch_row($query);
	$asset = $set[0];

	//store in the array	
	$found_members[$asset_id] = $asset;
	return $asset;
}

//get asset id from name
function get_asset_id_from_name($asset) 
{
	global $found_assets;	
	
	if ($asset_id = array_search($asset, $found_assets))
	{
		//it is already in the array, had already been queried previously
		return $asset_id;
	}
	$sql 	= "SELECT asset_id FROM assetlist WHERE asset='$asset'";
	$query 	= mysql_query($sql);
	$set 	= mysql_fetch_row($query);
	$asset_id = $set[0];
	
	//store in the array
	$found_assets[$asset_id] = $asset;
	return $asset_id;
}

//when retrieving service names, store in this array
//in order for faster in-memory retrieval instead of
//repeated database querying
$found_services = array();

//get service id from name
function get_service_id_from_name($service) 
{
	global $found_services;	
	
	if ($service_id = array_search($service, $found_services))
	{
		//it is already in the array, had already been queried previously
		return $service_id;
	}
	$sql 	= "SELECT service_id FROM servicelist WHERE service='$service'";
	$query 	= mysql_query($sql);
	$set 	= mysql_fetch_row($query);
	$service_id = $set[0];
	
	//store in the array
	$found_members[$service_id] = $service;
	return $service_id;
}

function get_service_name_from_id($service_id) 
{
	global $found_services;	
	
	if (array_key_exists($service_id, $found_services))
	{
		//it is already in the array, had already been queried previously
		return $found_services [$service_id];
	}
	$sql 	= "SELECT service FROM servicelist WHERE service_id='$service_id'";
	$query 	= mysql_query($sql);
	$set 	= mysql_fetch_row($query);
	$service = $set[0];
	
	//store in the array
	$found_members[$service_id] = $service;
	return $service;
}


//do a standard query on the database
function do_query($query)
{
	$returnval = 0;
	
	//print $query."\n<br>";
	$result = mysql_query($query);		
	if (!$result) {
		$msg = "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
		//print "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
		throw new Exception($msg);	
	} else {
		$new_entry_id = mysql_insert_id();
		return $new_entry_id;			
	}	
}	

//the following is not working yet. Logging should be enabled for
//auditing and better bug fixing.
// include class
//require_once 'Log.php';

// create Log object
//$logger = &Log::singleton("file", "uws.log");

/*
$priorities = array(
            PEAR_LOG_EMERG   => 'emergency',
            PEAR_LOG_ALERT   => 'alert',
            PEAR_LOG_CRIT    => 'critical',
            PEAR_LOG_ERR     => 'error',
            PEAR_LOG_WARNING => 'warning',
            PEAR_LOG_NOTICE  => 'notice',
            PEAR_LOG_INFO    => 'info',
            PEAR_LOG_DEBUG   => 'debug'
        );
*/
?>
