<?php
	$username		= $_SESSION['uname'];

	$asset 			= $_POST['asset'];
	$desc 			= $_POST['desc'];
	$user 			= $_POST['user'];
	$donate			= $_POST['donate'];
	$factor 		= $_POST['factor'];
	$amount_physical= $_POST['value'];
	$amount_inventory = $_POST['weighted_val'];
	$link 			= $_POST['storyLink'];
	
	$is_donation	= 1;
	if (! isset($_POST['donate']))
	{
		$is_donation = 0;
	}
	
	include "config.php";
	
	//the following check should not be needed anymore, as the
	//asset comes from a list of existing assets
	
	//first check that the asset exists; if not: add.
//	$sql 	= "SELECT asset FROM assetlist WHERE asset = '$asset'";
//	$query 	= mysql_query($sql);
//	$exists = '';	
//	
//	while ($result = mysql_fetch_array($query)) 
//	{
//		$exists = $result['asset'];
//	}
//	$date 		= time();
//	$asset_id 	= '';
	
//	if ($exists == '') 
//	{
//		$query = "INSERT into assetlist values ('','$date','$asset','0','0','$factor','')";
//		$result = mysql_query($query);
//		if (!$result) {
//			header("Location:error.php?error=Query failed: " . mysql_error());
//		} else {
//			$asset_id = mysql_insert_id();
//		}

	//maybe do something...??? maybe delete this whole section
	
//	} //else 
//	{
	$asset_id	= get_asset_id_from_name($asset);
//  }	
	
	$member_id	= get_member_id_from_name($user);
	$sql 		= "SELECT balance FROM members WHERE member_id='$member_id'";
	$query		= mysql_query($sql);
	$result		= mysql_fetch_row($query);
	$balance	= $result[0];	
	
	$timestamp	= time();

	$sql 		= "INSERT INTO transactions VALUES ".
			   			"('','$timestamp','$INVENTORIZE_TYPE','0','$member_id','$desc','$factor','$link','$balance')";
	//print $sql."<br><br>";
	$ta_id 		= do_query($sql);
	
	//print "asset_id: ".$asset_id;							  
	$sql 		= "INSERT INTO inventorize VALUES('','$ta_id','$asset_id','$is_donation','$amount_physical','$amount_inventory')";
	$inv_id 	= do_query($sql);
	
	$sql		= "UPDATE transactions SET transaction_id='$inv_id' where journal_id='$ta_id'";
	do_query($sql);
	
	$sql		= "UPDATE totals SET total_inventory=total_inventory + $amount_inventory";
	do_query($sql);
	
	$sql		= "UPDATE assetlist SET inventory=inventory + $amount_inventory where asset_id='$asset_id'";
	do_query($sql);
	
	$sql		= "UPDATE assetlist SET physical=physical + $amount_physical where asset_id='$asset_id'";
	do_query($sql);
	
	if ($is_donation == 0)
	{	
		$total_services 	= 0;
		$total_inventory 	= 0;
			
		//error_log("is not a donation, is donation is 0");
		$sql = "SELECT * FROM totals";
		$query = mysql_query($sql);		
		if (!$query) 
		{
			header("Location: ". $errorpage.urlencode("Query failed: ". mysql_error()));
			
		} else 
		{
			while ($result = mysql_fetch_array($query)) 
			{
				$total_services 	= $result['total_services'];
				$total_inventory 	= $result['total_inventory'];
			}
			//echo "weighted: " . $weighted_val;
			$service_units = $amount_inventory * $total_services / $total_inventory;
			//echo "service units earned = $service_units";
			$sql = "UPDATE members SET balance=balance+$service_units where member_id='$member_id'";
			do_query($sql);
			$balance = $balance + $service_units;
			$sql = "UPDATE transactions SET balance='$balance' where journal_id='$ta_id'";
			do_query($sql);
			
			$sql = "UPDATE totals SET total_services=total_services + $service_units";
			do_query($sql);
															
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
