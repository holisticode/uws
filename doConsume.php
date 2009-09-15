<?php
	session_start();
	include "config.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
try {
	$target = "home.php";
	
	$username 	= $_SESSION['uname'];
	$member_id	= $_SESSION['member_id'];
	
	$timestamp 	= time();
	$price 		= $_POST['price'];
	$bid		= $_POST['my_bid_amount'];
	$factor		= $_POST['my_factor'];
	$unit		= $_POST['unit'];
	$asset_id 	= $_POST['asset_id'];
	
	$sql 	= "INSERT INTO transactions VALUES ".
			   		"('','$timestamp','$CONSUME_TYPE','0','$member_id','$desc','$factor','$link','$balance')";
	$ta_id 	= do_query($sql);
	
	$sql 	= "INSERT INTO consume VALUES('','$date','$ta_id','$asset_id','$bid','$price')";
	$bid_id = do_query($sql);
	
	//transactions saved, now update balances
	/*UPDATE units SET inventory = inventory - (amount*factor) WHERE unit=what;
		UPDATE units SET physical = physical - amount WHERE unit=what;
		UPDATE totals SET total_inventory = total_inventory - (amount*factor);
		*/
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
	
}catch (Exception $e)
{
	$target = $errorpage . $e->getMessage();
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
