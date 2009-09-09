<?php
	$username		= $_SESSION['uname'];

	$unit 			= $_POST['unit'];
	$desc 			= $_POST['desc'];
	$user 			= $_POST['user'];
	$donate			= $_POST['donate'];
	$factor 		= $_POST['factor'];
	$value			= $_POST['value'];
	$weighted_val 	= $_POST['weighted_val'];
	$storyLink 		= $_POST['storyLink'];

	include "config.php";
	
	//first check that the unit exists; if not: add.
	$sql 	= ("SELECT unit FROM uwsunits WHERE unit = '$unit'");
	$query 	= mysql_query($sql);
	$exists = '';
	
	while ($result = mysql_fetch_array($query)) {
		$exists = $result['unit'];
	}
	$date = time();
	if ($exists == '') {
		$query = "INSERT into uwsunits values ('','$date','$unit','0','0','$factor','')";
		$result = mysql_query($query);
		if (!$result) {
			$header("Location:error.php?error=Query failed: " . mysql_error());
		} else {
			$user_1_journal_id = mysql_insert_id();
		}
	}
	
	
	$date = time();

	$query = "INSERT into uwsinventorize values ('','$date','$username','$unit','$desc','$value','$factor','')";
	$result = mysql_query($query);
	if (!$result) {
		print("Query failed: " . mysql_error());
	} else {
		$user_1_journal_id = mysql_insert_id();
	}
	
	
	if (! isset($_POST['donate']))
	{		
		$sql = "SELECT * FROM uwstotals";
		$query = mysql_query($sql);
		
		$total_services 	= 0;
		$total_inventory 	= 0;
		if (!$result) {
			$header("Location:error.php?error=Query failed: " . mysql_error());
		} else {
			while ($result = mysql_fetch_array($query)) {
				$total_services 	= $result['total_services'];
				$total_inventory 	= $result['total_inventory'];
			}
			echo "weighted: " . $weighted_val;
			$service_units = $weighted_val * $total_services / $total_inventory;
			echo "service units earned = $service_units";
			$sql = "UPDATE uwscontributors SET balance=balance+$service_units where contributor='$user'";
			$result = mysql_query($sql);
		
			if (!$result) {
				$header("Location:error.php?error=Query failed: " . mysql_error());
			} else {
				$user_1_journal_id = mysql_insert_id();
			}
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

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
