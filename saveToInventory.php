<?php
/*
 * UWS - Universal Wealth System
 * saveToInventory.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * The user clicked save on addToInventory.php. The form gets submitted
 * to this file and the entries written to the database.
 */
 
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
	
	$member_id	= get_member_id_from_name($user);
	$sql 		= "SELECT balance FROM members WHERE member_id='$member_id'";
	$query		= mysql_query($sql);
	$result		= mysql_fetch_row($query);
	$balance	= $result[0];	
	
	$timestamp	= time();

	try
	{
		//start the PDO transaction
		$dbh->beginTransaction();
	
		//create a new transaction entry...
		$sql 		= "INSERT INTO transactions VALUES ".
				   			"('','$timestamp','$INVENTORIZE_TYPE','0','$member_id','$desc','$factor','$link','$balance')";
		//print $sql."<br><br>";
		//$ta_id 		= do_query($sql);
		$dbh->exec($sql);
		$ta_id = $dbh->lastInsertId();
		
		//...with its id as foreign key create a new inventorize entry... 		
		//print "asset_id: ".$asset_id;							  
		$sql 		= "INSERT INTO inventorize VALUES('','$ta_id','$asset_id','$is_donation','$amount_physical','$amount_inventory')";
		//$inv_id 	= do_query($sql);
		$dbh->exec($sql);	
		$inv_id = $dbh->lastInsertId();
		
		//...write back the latter's id to the transaction entry 
		$sql		= "UPDATE transactions SET transaction_id='$inv_id' where journal_id='$ta_id'";
		$dbh->exec($sql);
		//do_query($sql);
		
		//update the total inventory
		$sql		= "UPDATE totals SET total_inventory=total_inventory + $amount_inventory";
		$dbh->exec($sql);
		//do_query($sql);
		
		//update the total inventory of the correspondent asset
		$sql		= "UPDATE assetlist SET inventory=inventory + $amount_inventory where asset_id='$asset_id'";
		$dbh->exec($sql);
		//do_query($sql);
		
		//update the total physical amount of the correspondent asset
		$sql		= "UPDATE assetlist SET physical=physical + $amount_physical where asset_id='$asset_id'";
		$dbh->exec($sql);
		//do_query($sql);
		
		if ($is_donation == 0)
		{	
			//if it is not a donation, the user gets credited service units
			//according to the UWS formula!!!
			
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
				//the UWS formula needs the totals
				while ($result = mysql_fetch_array($query)) 
				{
					$total_services 	= $result['total_services'];
					$total_inventory 	= $result['total_inventory'];
				}
				//echo "weighted: " . $weighted_val;
				
				//THIS IS THE UWS FORMULA, the correspondent service units to be
				//credited are calculated
				$service_units = $amount_inventory * $total_services / $total_inventory;
				//echo "service units earned = $service_units";
				
				//update the members balance
				$sql = "UPDATE members SET balance=balance+$service_units where member_id='$member_id'";
				$dbh->exec($sql);
				//do_query($sql);
				$old_balance = $balance;
				$balance = $balance + $service_units;
				
				//update the transaction entry with this new balance for the account history
				$sql = "UPDATE transactions SET balance='$balance' where journal_id='$ta_id'";
				//do_query($sql);
				$dbh->exec($sql);
				
				//update the total services
				$sql = "UPDATE totals SET total_services=total_services + $service_units";
				//do_query($sql);
				$dbh->exec($sql);
				
				//a lot of implicit changes happened; the user needs to see
				//a summary (only if it is not a donation)
				$url = "inventoryConfirm.php?user=$user&asset=$asset&donate=$donate&physical=$amount_physical&inventory=$amount_inventory&su=$service_units&balance=$balance&old_balance=$old_balance";
				header("Location: ".$url);
																
			} //else
		
		} //is donation
	
	//ONLY IF ALL STEPS SUCCEEDED VALUES ARE ENTERED INTO THE DATABASE...
	$dbh->commit();	
		
	} catch (Exception $e)
	{
		//...otherwise the transaction failed, nothing written to db
		$dbh->rollback();
		header("Location: ".$errorpage.$e->getMessage() );
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
