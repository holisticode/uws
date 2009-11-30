<?php
/*
 * UWS - Universal Wealth System
 * import_db.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * Willi has been running his UWS with plain html files so far.
 * The main history is at www.ressort.info/de/uws.htm
 * 
 * This file parses that uws.htm file in order to create an UWS cell
 * and store all those entries inside the database, creating all the
 * mentioned members, services and assets.
 */ 

$redirectto = "index.php";

include "config.php";

//check if the file has already been imported
$query 		= "SELECT original_imported FROM settings;";
$result 	= mysql_query($query);
$imported	= false;

if (!$result) {
	$msg = "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
	//print $msg."<br>";
	$redirectto = "error.php?error=" . $msg;
	return;
} else {
	//print "Checking import flag.<br>";
	$result = mysql_fetch_row($result);
	$imported = $result[0];
	//print "Flag checked.\n<br>";
	if ($imported == 1)
	{
		//if it has been imported, show error message
		$msg = "The database has already been imported! You can only import it once.";
		//print $msg;
		header("Location: ".$errorpage.$msg);
	}
	else 
	{
		//else start import
		//print "Flag OK. Import can start.<br>";
		$importer = new UWS_InitialImport();
		$importer->import();		
	}
}	

/*
 * This class handles the import of the uws.htm file. It uses regular expressions.
 */
class UWS_InitialImport
{
	
	private $keys_found = array();	
	//the address from which to import
	private $url = "http://www.ressort.info/de/uws.htm";
	//the regular expression which gets the single entries
	private $trmatch = "/<tr \w*>.*<\/tr>/ismxU";
	private $errorpage = "error.php?error=";
	private $cell_id = "";
	private $service_type = 0;
	private $inventorize_type = 0;
	private $consume_type = 0;	
		
	function import()
	{
		global $redirectto;
		//global $dbh;
		
		try
		{	
			//print "Starting import.<br>";
			//first clear the local cache
			clearstatcache();
			//download the file into a string
			$htmlfile = file_get_contents($this->url);
			
			//no need for transactions here, as this is the initial import;
			//if something fails, just delete the whole db and restart
			//$dbh->beginTransaction(); 
			
			//initialize the import
			$this->init();
			
			//apply the regular expression
			if (preg_match_all($this->trmatch,$htmlfile, $matches))
			{
				unset($matches[0][0]); //first row is the header
				
				//for each entry
				foreach ($matches as $match)
				{			 
					foreach($match as $elem)
					{
						//get rid of html tags			
						$data = strip_tags($elem);
						//put all entries as single values of an array
						$entry = explode("\n", $data);
						//print $entry[3] . "<br>";			
						
						switch ($entry[3])		
						{
							//create now the correct SQL statement to add
							//the data into the database; the statement differs
							//depending on data type			
							case "L": //it is a service, or...
								$this->create_service_statement($entry);				
								break;
							case "I": //an inventorization, or...
								$this->create_inventory_statement($entry);
								break;
							case "K": //a consumation
								$this->create_consume_statement($entry);				 
								break;
						}
					}	
				}			
			
				//the database has been imported, disable further importing
				$query 	= "UPDATE settings SET original_imported='1';";
				$this->do_query($query);
				
				//$dbh->commit();
				
			} // if regexp matches
		} catch (Exception $e) {
			//something went wrong during the import
			//$dbh->rollback();
			$msg = $e->getMessage();
			//print "Exception: ". $msg;
			$redirectto = "error.php?error=" . $msg;
		}
	}//import
	
	function init()
	{
		//initialize the import
		
		//global $dbh;
		//print "Initialising...\n<br>";
		
		//first create the cell
		$this->create_cell();
		
		//get the type codes for the different transaction types
		$sql			= "SELECT type_code FROM transaction_type where type_desc='Service'";
		$query			= mysql_query($sql);
		$result			= mysql_fetch_row($query);
		$this->service_type = $result[0];
		
		
		$sql			= "SELECT type_code FROM transaction_type where type_desc='Inventorization'";
		$query			= mysql_query($sql);
		$result			= mysql_fetch_row($query);
		$this->inventorize_type = $result[0];
		
		
		$sql			= "SELECT type_code FROM transaction_type where type_desc='Consume'";
		$query			= mysql_query($sql);
		$result			= mysql_fetch_row($query);	
		$this->consume_type = $result[0];
		
		
		//print "Done.\n<br>";
	}
	
	function create_cell()
	{
		//create the UWS cell
		
		global $DEFAULT_CELL_ID;
		//global $dbh;
		
		$cell_name = "OS";
		$query = "INSERT INTO network VALUES('','$cell_name','')";
		$this->cell_id = $this->do_query($query);
		//$dbh->exec($query);
		$DEFAULT_CELL_ID = $this->cell_id;
	}
	
	function create_service_statement($entry)
	{	
		//create a SQL statement for a service
		
		//first check that the service already exists in the database
		$service 	= $this->insert_if_missing_service($entry[5]);
		
		//check if the user exists
		$member_id	= $this->insert_if_missing_contributor($entry[4]);
		//format the date
		$timestamp	= $this->format_date($entry[2]);
		//$timestamp	= date( 'Y-m-d H:i:s', $timestamp );
		
		//get the different fields from the entry array
		$lifetime 	= $entry[6];
		$factor 	= $entry[7];
		$link 		= $this->sanitize_string($entry[8]);
		$htm		= ".htm";
		$desc		= "";
		$ends	    = substr($link, strlen($link) - strlen ($htm)); 
		/*if (strcmp($ends, $htm))//if $link doesn't end in .htm, put text into description field
		{
			$desc = $link;
			$link = "";
		}
		*/
		$desc = $link;
		try
		{
			//$dbh->beginTransaction();
		 
		 	//now start entering the values into the database
		 	
			$sql		= "SELECT balance FROM members where member_id='$member_id'";
			$query		= mysql_query($sql);
			$balance	= mysql_fetch_row($query);
			//$result = $dbh->query($sql)->fetch();
			//$balance = $result['balance'];
			
			$service_units = $factor * $lifetime; 		
			$balance	= $service_units + $balance[0];		
	
			//save a transaction entry
			$sql 	= "INSERT INTO transactions VALUES ".
				   		"('','$timestamp','$this->service_type','0','$member_id','$desc','$factor','$link','$balance')";
		    //print $sql."<br><br>";
			$ta_id 	= $this->do_query($sql);
			//$dbh->exec($sql);
			//$ta_id = $dbh->lastInsertId();
					
			//with that id, save the service entry
			$sql 	= "INSERT INTO service VALUES('','$ta_id','','','$service','$lifetime')";
			$srv_id = $this->do_query($sql);
			//$dbh->exec($sql);
			//$srv_id = $dbh->lastInsertId();
			
			//write back into the transaction table the service id as foreign key
			$sql	= "UPDATE transactions SET transaction_id='$srv_id' where journal_id='$ta_id'";
			//$dbh->exec($sql);
			$this->do_query($sql);
			
			//update the total services
			$sql	= "UPDATE totals SET total_services=total_services + $service_units";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//update the total for the correspondent service
			$sql	= "UPDATE servicelist SET provided=provided + $service_units where service_id='$service'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//update the member's balance
			$sql	= "UPDATE members SET balance=balance + $service_units where member_id='$member_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//$dbh->commit();
			
			//return $statement;
		} catch (Exception $e)
		{
			//oops, something went wrong
			global $errorpage;
			//$dbh->rollback();
			header("Location: ". $errorpage .$e->getMessage());
		}
 		
	}
	
	//create an SQL inventory statement
	function create_inventory_statement($entry)
	{
		
		//$name 	= $this->insert_if_missing_contributor($entry[4]);
		//HACK: uws.htm table doesn't show unit of inventorization;looking at id for now...
		$id 	= (int) trim($entry[1]);
		//print "ID: " . $id . "<br>";	
		$factor		= 0; 
		$unit		= "";
		if ($id < 198) //then it's record ids 193-197, Brazilian Reals
		{
			$unit 	= "BRL";
			$factor = 1; 	
		} else //else records 198-199, Swiss Francs
		{
			$unit 	= "CHF";
			$factor = 1.613423;
		}
		
		//check if the asset already exists in the database
		$asset_id	= $this->insert_if_missing_unit($unit, $factor);
		//check if the user exists
		$member_id	= $this->insert_if_missing_contributor($entry[4]);
		//format date
		$timestamp	= $this->format_date($entry[2]);
		//$timestamp	= date( 'Y-m-d H:i:s', $timestamp );
		
		//get the other fields
		$amount_physical  = $entry[6];
		$amount_inventory = $amount_physical * $factor;
		$balance	= 0;
		//hardcoded as for now uws.htm inventorizations are Ritual Beitrag,
		//but are shown on another URL and not inside uws.htm
		//TODO: should future inventorizations take place, this is an error!
		$link 		= "Ritual Beitrag";//$this->sanitize_string($entry[8]);		
		
		//$dbh->beginTransaction();
		try 
		{
			//create a transaction entry
			$sql 		= "INSERT INTO transactions VALUES ".
				   			"('','$timestamp','$this->inventorize_type','0','$member_id','','$factor','$link','$balance')";
			//print $sql."<br><br>";
			$ta_id 		= $this->do_query($sql);
			//$dbh->exec($sql);
			//$ta_id = $dbh->lastInsertId();
			
			//TODO: hard-coded, inventorizations in uws.htm for now are only donations
			//could be wrong in future
			$is_donation= 1;
			
			//with the transaction id, create a new inventory entry with it as foreign key
			//print "asset_id: ".$asset_id;							  
			$sql 		= "INSERT INTO inventorize VALUES('','$ta_id','$asset_id','$is_donation','$amount_physical','$amount_inventory')";
			//$dbh->exec($sql);
			$inv_id 	= $this->do_query($sql);
			//$inv_id = $dbh->lastInsertId();
			
			//write back the transaction id of the inventorization into the transaction table as foreign key
			$sql		= "UPDATE transactions SET transaction_id='$inv_id' where journal_id='$ta_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//update the inventory total
			$sql		= "UPDATE totals SET total_inventory=total_inventory + $amount_inventory";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//update the inventory total of the correspondent asset
			$sql		= "UPDATE assetlist SET inventory=inventory + $amount_inventory where asset_id='$asset_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//update the physical amount of the correspondent asset
			$sql		= "UPDATE assetlist SET physical=physical + $amount_physical where asset_id='$asset_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//$dbh->commit();
			
		} catch (Exception $e)
		{	
			//oops, something went wrong
			global $errorpage;
			//$dbh->rollback();
			header("Location: ". $errorpage . $e->getMessage());
		}
		
	}
	
	//create a consumation SQL statement
	function create_consume_statement($entry)
	{
		//Not needed yet, but for completion sake here,
		//as uws.htm does not have consumations yet
		$timestamp	= $this->format_date($entry[2]);
		$member_id 	= $entry[4];		
		$asset_id	= $entry[5];
		$bid 		= $entry[6];
		$factor 	= $entry[7];
		
		//it is not clear which field would have the price
		//for now we'd assume it is in the link field, the only left
		//so there's no link field left - anyway, this func will
		//probably never be used
		$price		= $entry[8];
		//$link 		= $this->sanitize_string($entry[8]);
		
		//can't consume anything which hasn't been inventorized 
		//-->insert_if_missing is inappropriate here!
		//$unit = insert_if_missing($entry[4]);
		
		try
		{	
			//$dbh->beginTransaction();	
		 
			$sql 	= "INSERT INTO transactions VALUES ".
				   		"('','$timestamp','$CONSUME_TYPE','0','$member_id','$desc','$factor','$link','$balance')";
			$ta_id 	= $this->do_query($sql);
			//$dbh->exec($sql);
			//$ta_id = $dbh->lastInsertId();
		
			$sql 	= "INSERT INTO consume VALUES('','$date','$ta_id','$asset_id','$bid','$price')";
			$bid_id = $this->do_query($sql);
			//$dbh->exec($sql);
			//$bid_id = $dbh->lastInsertId();
		
			$sql	= "UPDATE totals SET total_inventory=total_inventory - $price";
			do_query($sql);
			//$dbh->exec($sql);
			
			$sql	= "UPDATE assetlist SET inventory=inventory - $price where asset_id='$asset_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
		
			$sql	= "UPDATE assetlist SET physical=physical - $bid where asset_id='$asset_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			$sql	= "UPDATE members SET balance=balance - $price where member_id='$member_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
		
			$sql 	= "SELECT balance FROM members WHERE member_id='$member_id'";
			$query  = mysql_query($sql);
			$result = mysql_fetch_row($query);
			//$result = $dbh->query($sql)->fetch();
			$new_balance = $result[0];
		
			$sql	= "UPDATE transactions SET transaction_id='$bid_id',balance='$new_balance' where journal_id='$ta_id'";
			$this->do_query($sql);
			//$dbh->exec($sql);
			
			//$dbh->commit();
			
		} catch (Exception $e)
		{
			global $errorpage;
			//$dbh->rollback();
			header("Location: " .$errorpage.$e->getMessage());
		}	
	}
	
	//a new asset is being added, and doesn't exist yet in the database
	function insert_if_missing_unit($name, $factor)
	{
		$table_name = "assetlist";
		$field	    = "asset";	
		
		if ($this->entry_missing($name,$table_name,$field))
		{
			//the asset is not there and needs to be retrieved from the database
			$time 	= time();
	   		$query 	= "INSERT into assetlist values('','$time','$name','0','0','$factor','')";	   			   		
	   		$id 	= $this->do_query($query);
	   		$this->update_cache($name,$id);
			return $id;
		}
		else
		{
			//it is already retrieved and stored in the in-memory array for faster retrieval
			return $this->keys_found[$name];
		}
	}
	
	//a new service is being added, and doesn't exist in the database yet
	function insert_if_missing_service($name)
	{
		$table_name = "servicelist";
		$field	    = "service";
		//print "insert_if_missing_service: " . $name ."\n<br>";
	   	if ($this->entry_missing($name,$table_name,$field))
	   	{
	   		//needs to be retrieved from the database
	   		$time 	= time();
	   		$query 	= "INSERT into servicelist values('','$time','$name','0','')"; 		
	   		$id     = $this->do_query($query);
	   		$this->update_cache($name,$id);
			return $id;
	   	}
	   	else 
	   	{
	   		//had already been entered, so it is in the in-memory array for faster retrieval
	   		return $this->keys_found[$name];
	   	}
	}
	
	
	//insert new member if not already in the database
	function insert_if_missing_contributor($name)
	{
		$table_name = "members";
		$field	    = "name";
		//on import, every user gets per default the password with
		//his or her name and this password extension; e.g. Ruedi - Ruedi123
		$standard_password_extension = "123";
		
		//print "insert_if_missing_contributor: ".$name."\n<br>";
	   	if ($this->entry_missing($name, $table_name, $field))
	   	{
	   		//the user does not exist in the database yet
	   		$time = time();
	   		$password = md5($name . $standard_password_extension);	   		
	   		$query = "INSERT into members values('','$time','','$name','$password','$this->cell_id','0','','','','')"; 		
	   		//print $query . "<br>";
	   		$id = $this->do_query($query);
	   		$this->update_cache($name,$id);
			return $id;
	   	}
	   	else 
	   	{
	   		//the user already exists and can be retrieved from the in-memory array
	   		return $this->keys_found[$name];	   		
	   	}
	}
	
	//is a member, service or asset already in the 
	//database? If yes, it is stored in the keys_found array
	//for faster retrieval.
	function entry_missing($name, $table_name, $field)
	{
		//print "Size keys: ". count($keys_found)."<br>";
		if (array_key_exists($name, $this->keys_found))
		{
			//it is in the array, therefore not missing
			//print "User: " . $name . "already exists. No INSERT<br><br>";
			return false;// corresponds to NOT missing	
		}
		//it is not in the array, check if it exists in the
		//database
		$key 	= null;
		$sql 	= "SELECT $field FROM ". $table_name. " WHERE ". $field ." = '$name'";
		//print "***".$sql."<br>";	
		$query 	= mysql_query($sql);	 
		while ($result = mysql_fetch_array($query)) {
	       		$key = $result['$field'];
	   	}  
	   	//print "KEY is null: " .is_null($key)."<br>";
	   	return ($key === null);
	}
	
	
	//do a standard query on the database
	function do_query($query)
	{
		$returnval = 0;
		global $redirectto;
		//print $query."\n<br>";
		$result = mysql_query($query);		
		if (!$result) {
			$msg = "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
			//print "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
			$redirectto = "error.php?error=" . $msg;	
		} else {
			$new_entry_id = mysql_insert_id();
			return $new_entry_id;			
		}	
	}
	
	//everytime a service, member or asset has been added,
	//update the local keys_found array for faster retrieval
	function update_cache($name, $id)
	{		
		$this->keys_found[$name] = $id;		
		//print "Unit: " . $name . " INSERTED.<br>";
	}
	
	
	//sanitize dodgy character strings
	function sanitize_string($value)
	{
		$ok = preg_replace("/'/", "''", $value);
		//print "value: " . $value . "--- sanitized: " . $ok;
		return $ok;
	}
	
	//format date
	function format_date($no_format)
	{
		$no_format = trim($no_format);
		global $redirectto;
		
		if (preg_match("/[A-Za-z]/",$no_format))
		{
			$msg = "Format date: digits problem in string.<br>";
			throw new Exception($msg);
			$redirectto = $this->errorpage . $msg;
			//print "Format date: digits problem in string.<br>";
			
		}
		//regular expression to get the date format from the string in uws.htm
		if (preg_match("/^(\d+)\.(\d+)\.(\d+)\s*(\d+):(\d+):(\d+)/",$no_format , $matches)) 
		{
			//print $no_format . " --- ";		
	  		$tag=$matches[3];
	  		$monat=$matches[2];
	  		$jahr=$matches[1];
	  		$std=$matches[4];
	  		$min=$matches[5];
	  		$sek=$matches[6];
	  		//make a standard PHP time stamp
	  		$time_stamp = mktime($std,$min,$sek,$monat,$tag,$jahr);
	  		//$comp = date("d-m-y H:m:s", $time_stamp);
			//print $comp . "<br>";
			
			return $time_stamp; 
		}
		else
		{
			$msg = $no_format . "Format date: supplied date didn't match.<br>";
			throw new Exception($msg);
			$redirectto = $this->errorpage . $msg;
			//print $no_format . "Format date: supplied date didn't match.<br>";
		}
	}
} //class 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>UWS</title>
<link href="basic.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!-- Begin
	document.location.href = "<?php echo $redirectto ?>";
	
	
 // End -->
</script>
</head>

<body>
</body>
</html>

