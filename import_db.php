<?php
/*
 * Created on 22-lug-2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
*/ 

$redirectto = "admin.php";

include "config.php";

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
		$msg = "The database has already been imported! You can only import it once.";
		//print $msg;
		$redirectto = "error.php?error=$msg";
	}
	else 
	{
		//print "Flag OK. Import can start.<br>";
		$importer = new UWS_InitialImport();
		$importer->import();		
	}
}	

class UWS_InitialImport
{
		
	private $keys_found = array();	
	private $url = "http://www.ressort.info/de/uws.htm";
	private $trmatch = "/<tr \w*>.*<\/tr>/ismxU";
	private $errorpage = "error.php?error=";
	private $cell_id = "";
	private $service_type = 0;
	private $inventorize_type = 0;
	private $consume_type = 0;
		
	function import()
	{
		global $redirectto;
		try
		{	
			//print "Starting import.<br>";
			$htmlfile = file_get_contents($this->url);
			
			$this->init();
			
			if (preg_match_all($this->trmatch,$htmlfile, $matches))
			{
				unset($matches[0][0]); //first row is the header
				
				foreach ($matches as $match)
				{			 
					foreach($match as $elem)
					{			
						$data = strip_tags($elem);
						$entry = explode("\n", $data);
						//print $entry[3] . "<br>";			
						
						switch ($entry[3])		
						{			
							case "L":
								$this->create_service_statement($entry);				
								break;
							case "I":
								$this->create_inventory_statement($entry);
								break;
							case "K":
								$this->create_consume_statement($entry);				 
								break;
						}
					}	
				}			
			
				$query 	= "UPDATE settings SET original_imported='1';";
				$this->do_query($query);
			} // if regexp matches
		} catch (Exception $e) {
			$msg = $e->getMessage();
			//print "Exception: ". $msg;
			$redirectto = "error.php?error=" . $msg;
		}
	}//import
	
	function init()
	{
		//print "Initialising...\n<br>";
		$this->create_cell();
		
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
		global $DEFAULT_CELL_ID;
		$cell_name = "OS";
		$query = "INSERT INTO network VALUES('','$cell_name','')";
		$this->cell_id = $this->do_query($query);
		$DEFAULT_CELL_ID = $this->cell_id;
	}
	
	function create_service_statement($entry)
	{	
		$service 	= $this->insert_if_missing_service($entry[5]);
		$member_id	= $this->insert_if_missing_contributor($entry[4]);
		$timestamp	= $this->format_date($entry[2]);
		//$timestamp	= date( 'Y-m-d H:i:s', $timestamp );
		$lifetime 	= $entry[6];
		$factor 	= $entry[7];
		$link 		= $this->sanitize_string($entry[8]);
		$htm		= ".htm";
		$desc		= "";
		$ends	    = substr($link, strlen($link) - strlen ($htm)); 
		if (strcmp($ends, $htm))//if $link doesn't end in .htm, put text into description field
		{
			$desc = $link;
			$link = "";
		}
		 
		$sql		= "SELECT balance FROM members where member_id='$member_id'";
		$query		= mysql_query($sql);
		$balance	= mysql_fetch_row($query);
		$service_units = $factor * $lifetime; 		
		$balance	= $service_units + $balance[0];
		
		
		$sql 	= "INSERT INTO transactions VALUES ".
			   		"('','$timestamp','$this->service_type','0','$member_id','$desc','$factor','$link','$balance')";
	    //print $sql."<br><br>";
		$ta_id 	= $this->do_query($sql);		
		$sql 	= "INSERT INTO service VALUES('','$ta_id','','','$service','$lifetime')";
		$srv_id = $this->do_query($sql);
		
		$sql	= "UPDATE transactions SET transaction_id='$srv_id' where journal_id='$ta_id'";
		$this->do_query($sql);
		
		$sql	= "UPDATE totals SET total_services=total_services + $service_units";
		$this->do_query($sql);
		
		$sql	= "UPDATE servicelist SET provided=provided + $service_units where service_id='$service'";
		$this->do_query($sql);
		
		$sql	= "UPDATE members SET balance=balance + $service_units where member_id='$member_id'";
		$this->do_query($sql);
		//return $statement;
	}
	
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
		
		$asset_id	= $this->insert_if_missing_unit($unit, $factor);
		$member_id	= $this->insert_if_missing_contributor($entry[4]);
		$timestamp	= $this->format_date($entry[2]);
		//$timestamp	= date( 'Y-m-d H:i:s', $timestamp );
		$amount_physical  = $entry[6];
		$amount_inventory = $amount_physical * $factor;
		$balance	= 0;
		$link 		= "Ritual Beitrag";//$this->sanitize_string($entry[8]);
		
		$sql 		= "INSERT INTO transactions VALUES ".
			   			"('','$timestamp','$this->inventorize_type','0','$member_id','','$factor','$link','$balance')";
		//print $sql."<br><br>";
		$ta_id 		= $this->do_query($sql);
		$is_donation= 1;
		//print "asset_id: ".$asset_id;							  
		$sql 		= "INSERT INTO inventorize VALUES('','$ta_id','$asset_id','$is_donation','$amount_physical','$amount_inventory')";
		$inv_id 	= $this->do_query($sql);
		
		$sql		= "UPDATE transactions SET transaction_id='$inv_id' where journal_id='$ta_id'";
		$this->do_query($sql);
		
		$sql		= "UPDATE totals SET total_inventory=total_inventory + $amount_inventory";
		$this->do_query($sql);
		
		$sql		= "UPDATE assetlist SET inventory=inventory + $amount_inventory where asset_id='$asset_id'";
		$this->do_query($sql);
		
		$sql		= "UPDATE assetlist SET physical=physical + $amount_physical where asset_id='$asset_id'";
		$this->do_query($sql);
	}
	
	function create_consume_statement($entry)
	{
		//Not needed yet, but for completion sake here
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
		$sql 	= "INSERT INTO transactions VALUES ".
			   		"('','$timestamp','$CONSUME_TYPE','0','$member_id','$desc','$factor','$link','$balance')";
		$ta_id 	= do_query($sql);
	
		$sql 	= "INSERT INTO consume VALUES('','$date','$ta_id','$asset_id','$bid','$price')";
		$bid_id = do_query($sql);
	
		$sql	= "UPDATE totals SET total_inventory=total_inventory - $price";
		do_query($sql);
		
		$sql	= "UPDATE assetlist SET inventory=inventory - $price where asset_id='$asset_id'";
		do_query($sql);
	
		$sql	= "UPDATE assetlist SET physical=physical - $bid where asset_id='$asset_id'";
		do_query($sql);
		
		$sql	= "UPDATE members SET balance=balance - $price where member_id='$member_id'";
		do_query($sql);
	
		$sql 	= "SELECT balance FROM members WHERE member_id='$member_id'";
		$query  = mysql_query($sql);
		$result = mysql_fetch_row($query);
		$new_balance = $result[0];
	
		$sql	= "UPDATE transactions SET transaction_id='$bid_id',balance='$new_balance' where journal_id='$ta_id'";
		$this->do_query($sql);
		
	}
	
	function insert_if_missing_unit($name, $factor)
	{
		$table_name = "assetlist";
		$field	    = "asset";	
		
		if ($this->entry_missing($name,$table_name,$field))
		{
			$time 	= time();
	   		$query 	= "INSERT into assetlist values('','$time','$name','0','0','$factor','')";	   			   		
	   		$id 	= $this->do_query($query);
	   		$this->update_cache($name,$id);
			return $id;
		}
		else
		{
			return $this->keys_found[$name];
		}
	}
	
	
	function insert_if_missing_service($name)
	{
		$table_name = "servicelist";
		$field	    = "service";
		//print "insert_if_missing_service: " . $name ."\n<br>";
	   	if ($this->entry_missing($name,$table_name,$field))
	   	{
	   		$time 	= time();
	   		$query 	= "INSERT into servicelist values('','$time','$name','0','')"; 		
	   		$id     = $this->do_query($query);
	   		$this->update_cache($name,$id);
			return $id;
	   	}
	   	else 
	   	{
	   		return $this->keys_found[$name];
	   	}
	}
	
	
	
	function insert_if_missing_contributor($name)
	{
		$table_name = "members";
		$field	    = "name";
		$standard_password_extension = "123";
		
		//print "insert_if_missing_contributor: ".$name."\n<br>";
	   	if ($this->entry_missing($name, $table_name, $field))
	   	{
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
	   		return $this->keys_found[$name];
	   	}
	}
	
	
	function entry_missing($name, $table_name, $field)
	{
		//print "Size keys: ". count($keys_found)."<br>";
		if (array_key_exists($name, $this->keys_found))
		{
			//print "User: " . $name . "already exists. No INSERT<br><br>";
			return false;// corresponds to NOT missing	
		}
		
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
	
	function update_cache($name, $id)
	{		
		$this->keys_found[$name] = $id;		
		//print "Unit: " . $name . " INSERTED.<br>";
	}
	
	function sanitize_string($value)
	{
		$ok = preg_replace("/'/", "''", $value);
		//print "value: " . $value . "--- sanitized: " . $ok;
		return $ok;
	}
	
	
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
		if (preg_match("/^(\d+)\.(\d+)\.(\d+)\s*(\d+):(\d+):(\d+)/",$no_format , $matches)) 
		{
			//print $no_format . " --- ";		
	  		$tag=$matches[3];
	  		$monat=$matches[2];
	  		$jahr=$matches[1];
	  		$std=$matches[4];
	  		$min=$matches[5];
	  		$sek=$matches[6];
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

