<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
	
	include "config.php";

	$contribution 	= $_POST['contribution'];
	$desc 			= $_POST['desc'];
	$user	 		= $_POST['user_1'];
	$receiver 		= $_POST['user_2'];
	$factor 		= $_POST['factor_1'];
	$work	 		= $_POST['work_1'];
	$weighted_perf 	= $_POST['weighted_perf'];
	$storyLink 		= $_POST['storyLink'];
	
	
	//first check that the service exists; if not: add
	//TODO: Check if user exists?
	$sql = ("SELECT service FROM services WHERE service = '$contribution'");
	$query = mysql_query($sql);
	while ($result = mysql_fetch_array($query)) {
		$service = $result['service'];
	}
	
	
	if ($service == '') {
		$date = time();
		$query = "INSERT into services values ('','$date','$contribution','0','')";
		$result = mysql_query($query);
		if (!$result) {
			print("Query failed: " . mysql_error());
		} else {
			$user_1_journal_id = mysql_insert_id();
		}
	}
	
	$date = time();
	$query = "INSERT into service values ('','$date','$user','$contribution','$desc','$work','$factor','')";
	//print $query;
	$result = mysql_query($query);
	if (!$result) {
		print("Query failed: " . mysql_error());
	} else {
		//print "Query OK";
		$user_1_journal_id = mysql_insert_id();
		
		if ($receiver != "" || $receiver != null)
		{
			$query = "UPDATE contributors SET balance=balance - $weighted_perf where contributor='$receiver'";
			$result = mysql_query($query);
			if (!$result) {
				print("Query failed: " . mysql_error());
			}
		}
	}

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>UWS</title>
<link href="basic.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!-- Begin
	document.location.href = "home.php";
	
	
// End -->
</script>
</head>

<body>
</body>
</html>
