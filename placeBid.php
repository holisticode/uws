<?php
/*
 * UWS - Universal Wealth System
 * placeBid.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * The original UWS design foresees auctions in order to manage
 * offer and demand of goods and assets in the system. This file was
 * meant to place a bid into the database. As auctions are disabled
 * resp. not designed in the system, the file is currently superflous,
 * but kept here for future development.
 */
	session_start();
	include "config.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
	$target = "bidList.php";
	
	$username 	= $_SESSION['uname'];
	$member_id	= $_SESSION['member_id'];
	
	$date 	= time();
	$price 	= $_POST['price'];
	$bid	= $_POST['my_bid_amount'];
	$factor	= $_POST['my_factor'];
	$unit	= $_POST['unit'];
	
	$sql = "INSERT INTO bid VALUES('','$date','$member_id','$asset_id','$bid','$price','$factor')";
	$result = mysql_query($sql);
	if (!$result) {
		$target = $errorpage . mysql_error();
	} else {
		$bid_id = mysql_insert_id();
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
