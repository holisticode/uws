<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
	
	include "config.php";
	$target = "home.php";
	
try
{
	$contribution 	= $_POST['contribution'];
	$desc 			= $_POST['desc'];
	$user	 		= $_POST['user_1'];
	$receiver 		= $_POST['user_2'];
	$factor 		= $_POST['factor_1'];
	$lifetime 		= $_POST['work_1'];
	$service_units 	= $_POST['weighted_perf'];
	$link 			= $_POST['storyLink'];
	
	$member_id		= get_member_id_from_name($user);
	$receiver_id	= null;
	$ta_has_receiver = null;
	if ((isset($_POST['user_2'])) && $_POST['user_2'] != "")
	{
		$ta_has_receiver = 1;		
		$receiver_id 	= get_member_id_from_name($receiver);
		
		$sql 	= "SELECT balance FROM members WHERE member_id='$receiver_id'";
		$query  = mysql_query($sql);
		$result = mysql_fetch_row($query);
		$balance = $result[0];
		
		if ($balance < $service_units)
		{
			throw new Exception(translate("uws:insufficient_balance"));	
		}
		
	}
	
	//checking for the existence of the service should not be 
	//necessary anymore, as services can only be added through the
	//appropriate page
	
	//first check that the service exists; if not: add
	//TODO: Check if user exists?
//	$sql = "SELECT service FROM servicelist WHERE service = '$contribution'";
//	$query = mysql_query($sql);
//	$rows = mysql_num_rows($query);
//	
//	
//	if ($rows == 0) {
//		$date = time();
//		$query = "INSERT into servicelist values ('','$date','$contribution','0','')";
//		$result = mysql_query($query);
//		if (!$result) {
//			print("Query failed: " . mysql_error());
//		} else {
//			$user_1_journal_id = mysql_insert_id();
//		}
//	}
	
	$timestamp 	= time();
	$service_id = get_service_id_from_name($contribution);
	
	$sql 	= "INSERT INTO transactions VALUES ".
		   		"('','$timestamp','$SERVICE_TYPE','0','$member_id','$desc','$factor','$link','0')";
    //print $sql."<br><br>";
	$ta_id 	= do_query($sql);		
	$sql 	= "INSERT INTO service VALUES('','$ta_id','','','$service_id','$lifetime')";
	$srv_id = do_query($sql);
	
	$sql	= "UPDATE transactions SET transaction_id='$srv_id' where journal_id='$ta_id'";
	do_query($sql);
	
	$sql	= "UPDATE members SET balance=balance + $service_units where member_id='$member_id'";
	do_query($sql);
	
	if (! $ta_has_receiver)
	{
		$sql	= "UPDATE totals SET total_services=total_services + $service_units";
		do_query($sql);
	
		$sql	= "UPDATE servicelist SET provided=provided + $service_units where service_id='$service'";
		do_query($sql);
	}
	else
	{
		$sql	= "UPDATE members SET balance=balance - $service_units where member_id='$receiver_id'";
		do_query($sql);
	}
	
	
	$sql 	= "SELECT balance FROM members WHERE member_id='$member_id'";
	$query  = mysql_query($sql);
	$result = mysql_fetch_row($query);
	$new_balance = $result[0];
	
	$sql	= "UPDATE transactions SET transaction_id='$srv_id',balance='$new_balance' where journal_id='$ta_id'";
	do_query($sql);
	
	
	if ($ta_has_receiver)
	{
		$sql 	= "SELECT balance FROM members WHERE member_id='$receiver_id'";
		$query  = mysql_query($sql);
		$result = mysql_fetch_row($query);
		$new_balance = $result[0];
		
		$sql	= "UPDATE service SET receiver_id='$receiver_id',receiver_balance='$new_balance' where journal_id='$srv_id'";
		do_query($sql);
	}
} catch (Exception $e)
{
	$msg = $e->getMessage();
	$target = $errorpage.$msg;
}
?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>UWS</title>
<link href="basic.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!-- Begin
	document.location.href = "<?php echo $target ?>";
	
	
// End -->
</script>
</head>

<body>
</body>
</html>
