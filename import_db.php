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
	$redirectto = "error.php?error=" . $msg;
	return;
} else {
	$imported = mysql_fetch_row($result);
	
	if ($imported)
	{
		$msg = "The database has already been imported! You can only import it once.";
		//print msg;
		$redirectto = "error.php?error=$msg";
	}
	else 
	{
		$importer = new UWS_InitialImport();
		$importer->import();		
	}
}	

class UWS_InitialImport
{
		
	private $keys_found = array();	
	private $url = "http://www.ressort.info/de/uws.htm";
	private $trmatch = "/<tr \w*>.*<\/tr>/ismxU";
	
		
	function import()
	{
		$htmlfile = file_get_contents($this->url);
		
		if (preg_match_all($this->trmatch,$htmlfile, $matches))
		{
			unset($matches[0][0]); //first row is the header
			
			$services 			= array();
			$inventarisations 	= array();
			$consumations 		= array();
			
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
							$services[] = $this->create_service_statement($entry);				
							break;
						case "I":
							$inventarisations[] = $this->create_inventory_statement($entry);
							break;
						case "K":
							$consumations[] = $this->create_consume_statement($entry);				 
							break;
					}
				}	
			}
			
			$this->insert_services($services);
			$this->insert_inventarizations($inventarisations);
			$this->insert_consumations($consumations);
		
			$query 	= "INSERT INTO settings VALUES('1');";
			$this->do_query($query);
		} // if regexp matches
	}//import
	
	
	function create_service_statement($entry)
	{	
		$service 	= $this->insert_if_missing_service($entry[5]);
		$contributor 	= $this->insert_if_missing_contributor($entry[4]);
		$statement 		= $this->create_statement($entry);
		return $statement;
	}
	
	function create_inventory_statement($entry)
	{
		
		//$name 	= $this->insert_if_missing_contributor($entry[4]);
		//HACK: uws.htm table doesn't show unit of inventorization;looking at id for now...
		$id 	= (int) trim($entry[1]);
		//print "ID: " . $id . "<br>";	
		if ($id < 198) //then it's record ids 193-197, Brazilian Reals
		{
			$unit 	= "BRL";
			$factor = 1; 	
		} else //else records 198-199, Swiss Francs
		{
			$unit 	= "CHF";
			$factor = 1.613423;
		}
		$entry[5] 	= $unit; //tid corresponds to unit on inventorization		
		$entry[7] 	= $factor;
		$unit 		= $this->insert_if_missing_unit($unit, $factor);
		$entry[8] 	= "Ritual Beitrag";
		$statement 	= $this->create_statement($entry);
		return $statement;
	}
	
//	function create_inventarize_statement($entry)
//	{
//		$date 		= $this->format_date($entry[2]);		
//		$link 		= $this->sanitize_string($entry[8]);
//		$statement 	= "'','$date','$entry[4]','$entry[5]','','$entry[6]','$entry[7]','$link'";
//		return $statement;
//	}
	
	function create_consume_statement($entry)
	{
		//can't consume anything which hasn't been inventorized 
		//-->insert_if_missing is inappropriate here!
		//$unit = insert_if_missing($entry[4]); 
		$statement = $this->create_statement($entry);
	}
	
	function create_statement($entry)
	{	
		//$contributor = $entry[4];
		//$tid = sanitize_string($entry[5]);
		//$lifetime = $entry[6];
		//$factor = $entry[7];
		$date 		= $this->format_date($entry[2]);		
		$link 		= $this->sanitize_string($entry[8]);
		$statement 	= "'','$date','$entry[4]','$entry[5]','','$entry[6]','$entry[7]','$link'";
		//print $statement . "<br>";
		return $statement;
	}
	
	
	function insert_if_missing_unit($name, $factor)
	{
		$table_name = "units";
		$field	    = "unit";	
		
		if ($this->entry_missing($name,$table_name,$field))
		{
			$time = time();
	   		$query = "INSERT into units values('','$time','$name','0','0','$factor','')";
	   		
	   		$this->do_query($query);
	   		$this->update_cache($name);
			return $name;
		}
	}
	
	
	function entry_missing($name, $table_name, $field)
	{
		//print "Size keys: ". count($keys_found)."<br>";
		if (in_array($name, $this->keys_found))
		{
			//print "User: " . $name . "already exists. No INSERT";
			return false;// corresponds to NOT missing	
		}
		
		$key 	= null;
		$sql 	= "SELECT * FROM ". $table_name. " WHERE ". $field ." = '$name'";	
		$query 	= mysql_query($sql);	 
		while ($result = mysql_fetch_array($query)) {
	       		$key = $result['$field'];
	   	}  
	   	//print "KEY is: " .$key."<br>";
	   	return ($key === null);
	}
	
	
	function insert_if_missing_service($name)
	{
		$table_name = "services";
		$field	    = "service";
		//print "insert_if_missing_service: " . $name ."\n<br>";
	   	if ($this->entry_missing($name,$table_name,$field))
	   	{
	   		$time = time();
	   		$query = "INSERT into services values('','$time','$name','0','')"; 		
	   		
	   		$this->do_query($query);
	   		$this->update_cache($name);
			return $name;
	   	}
	}
	
	
	
	function insert_if_missing_contributor($name)
	{
		$table_name = "contributors";
		$field	    = "contributor";
		$standard_password_extension = "123";
		
		//print "insert_if_missing_contributor: ".$name."\n<br>";
	   	if ($this->entry_missing($name, $table_name, $field))
	   	{
	   		$time = time();
	   		$password = md5($name . $standard_password_extension);	   		
	   		$query = "INSERT into contributors values('','$time','$name','$password','0','','')"; 		
	   		//print $query . "<br>";
	   		$this->do_query($query);
	   		$this->update_cache($name);
			return $name;
	   	}
	}
	
	function do_query($query)
	{
		global $redirectto;
		$result = mysql_query($query);		
		if (!$result) {
			$msg = "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
			print "Query failed. Query was: " . $query . ". Error from db: " . mysql_error();
			$redirectto = "error.php?error=" . $msg;	
		} else {
			$new_entry_id = mysql_insert_id();
		}	
	}
	
	function update_cache($name)
	{		
		$this->keys_found[] = $name;		
		//print "Unit: " . $name . " INSERTED.<br>";
	}
	
	function sanitize_string($value)
	{
		$ok = preg_replace("/'/", "''", $value);
		//print "value: " . $value . "--- sanitized: " . $ok;
		return $ok;
	}
	
	function insert_services($statements)
	{
		$sql = "INSERT INTO service VALUES(";	
		$this->insert_statement($statements, $sql);	
	}
	
	function insert_inventarizations($statements)
	{
		$sql = "INSERT INTO inventorize VALUES(";
		$this->insert_statement($statements, $sql);
		
	}
	
	function insert_consumations($statements)
	{
		$sql = "INSERT INTO consume VALUES(";
		$this->insert_statement($statements, $sql);
		
	}
	
	function insert_statement($statements, $sql)
	{
		if (count($statements) == 0)
		{
			//no need to insert anything
			return;
		}
		$values = implode("),(", $statements);
		//print $values;
		$sql.= $values;
		$sql.= ");";
		//print "FINAL SQL STATEMENT: " . $sql;
	
		$this->do_query($sql);
		
	}
	
	
	function format_date($no_format)
	{
		$no_format = trim($no_format);
		if (preg_match("/[A-Za-z]/",$no_format))
		{
			print "digits problem in string.<br>";
			
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
	  		$comp = date("d-m-y H:m:s", $time_stamp);
			//print $comp . "<br>";
			
			return $time_stamp; 
		}
		else
		{
			print $no_format . "didn't match.<br>";
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
	#document.location.href = "<?php echo $redirectto ?>";
	
	
 // End -->
</script>
</head>

<body>
</body>
</html>

