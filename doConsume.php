<?php
/*
 * UWS - Universal Wealth System
 * doConsume.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * When a user consumes a good via bidAsset.php, the form will submit
 * the info to this file in order for the consumation to be recorded
 * in the database.
 */
	session_start();
	include "config.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
try {
	//where to go after finishing to update the database
	$target = "home.php";
	
	$username 	= $_SESSION['uname'];
	$member_id	= $_SESSION['member_id'];
	
	$timestamp 	= time();
	$price 		= $_POST['price'];
	$bid		= $_POST['my_bid_amount'];
	$factor		= $_POST['my_factor'];
	$unit		= $_POST['unit'];
	$asset_id 	= $_POST['asset_id'];
	
	//begin the transaction
	$dbh->beginTransaction();
	//create a transaction entry
	$sql 	= "INSERT INTO transactions VALUES ".
			   		"('','$timestamp','$CONSUME_TYPE','0','$member_id','$desc','$factor','$link','$balance')";
	//$ta_id 	= do_query($sql);
	$dbh->exec($sql);
	$ta_id = $dbh->lastInsertId();
	
	//with this id, create a consumation entry
	$sql 	= "INSERT INTO consume VALUES('','$ta_id','$asset_id','$bid','$price')";
	//$bid_id = do_query($sql);
	$dbh->exec($sql);
	$bid_id = $dbh->lastInsertId();
	
	//we need to subtract this from the totals
	$inventory_subtraction = $bid * $factor;
	
	//update the total inventory
	$sql	= "UPDATE totals SET total_inventory=total_inventory - $inventory_subtraction";
	//do_query($sql);
	$dbh->exec($sql);
		
	//update the total of the remaining inventory units for the correspondent asset
	$sql	= "UPDATE assetlist SET inventory=inventory - $inventory_subtraction where asset_id='$asset_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	//update the total the remaining physical amount for the correspondent asset
	$sql	= "UPDATE assetlist SET physical=physical - $bid where asset_id='$asset_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	//update the balance of the member
	$sql	= "UPDATE members SET balance=balance - $price where member_id='$member_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	//update the total of the total services
	$sql	= "UPDATE totals SET total_services=total_services - $price";
	//do_query($sql);
	$dbh->exec($sql);
	
	//get the new balance of the member
	$sql 	= "SELECT balance FROM members WHERE member_id='$member_id'";
	/*
	$query  = mysql_query($sql);
	$result = mysql_fetch_row($query);
	*/
	$result = $dbh->query($sql)->fetch();
	$new_balance = $result['balance'];
	
	//update the transaction table to have the new balance after this transaction
	$sql	= "UPDATE transactions SET transaction_id='$bid_id',balance='$new_balance' where journal_id='$ta_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	//only if all these steps went well, commit the transaction!
	$dbh->commit();
	
}catch (Exception $e)
{
	//if any of these failed, rollback and the whole transaction failed.
	$dbh->rollback();
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
