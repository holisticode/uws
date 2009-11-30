<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
/*
 * UWS - Universal Wealth System
 * saveEntry.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * A new service delivery has been entered in createEntry.php, and the user
 * clicked save. The form is submitted to this file in order for the entries
 * to be written into the database.
 */
	
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
	
	//start a PDO transaction
	$dbh->beginTransaction();
	
	if ((isset($_POST['user_2'])) && $_POST['user_2'] != "")
	{
		//the transaction has a receiver, is therefore not a community service
		$ta_has_receiver = 1;		
		$receiver_id 	= get_member_id_from_name($receiver);
		
		//get the balance from the receiver
		$sql 	= "SELECT balance FROM members WHERE member_id='$receiver_id'";
		$query  = mysql_query($sql);
		$result = mysql_fetch_row($query);
		$balance = $result[0];
		
		//check if the receiver has enough balance on his account in order to 
		//issue the "payment". If not, the transaction fails!
		if ($balance < $service_units)
		{
			throw new Exception(translate("uws:insufficient_balance"));	
		}
		
	}
	
	
		
	//insert the values into the database
	$timestamp 	= time();
	$service_id = get_service_id_from_name($contribution);
	
	//create a transaction entry
	$sql 	= "INSERT INTO transactions VALUES ".
		   		"('','$timestamp','$SERVICE_TYPE','0','$member_id','$desc','$factor','$link','0')";
    //print $sql."<br><br>";
	//$ta_id 	= do_query($sql);
	$dbh->exec($sql);
	$ta_id = $dbh->lastInsertId();
			
 	//with this id as a foreign key, create a service entry
	$sql 	= "INSERT INTO service VALUES('','$ta_id','','','$service_id','$lifetime')";
	//$srv_id = do_query($sql);
	$dbh->exec($sql);
	$srv_id = $dbh->lastInsertId();
	
	//write back the service transaction id into the transaction entry as a foreign key
	$sql	= "UPDATE transactions SET transaction_id='$srv_id' where journal_id='$ta_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	//update the member's balance'
	$sql	= "UPDATE members SET balance=balance + $service_units where member_id='$member_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	//it is a community service
	if (! $ta_has_receiver)
	{
		//update the totals, as therefore there is not just a swap of units, but
		//new units have been created!
		$sql	= "UPDATE totals SET total_services=total_services + $service_units";
		//do_query($sql);
		$dbh->exec($sql);
	
		//update the total of the correspondent service
		$sql	= "UPDATE servicelist SET provided=provided + $service_units where service_id='$service'";
		//do_query($sql);
		$dbh->exec($sql);
	}
	else
	{
		//it is not a community service
		//update the receiver's balance'
		$sql	= "UPDATE members SET balance=balance - $service_units where member_id='$receiver_id'";
		//do_query($sql);
		$dbh->exec($sql);
	}
	
	//get the new balance of the member
	$sql 	= "SELECT balance FROM members WHERE member_id='$member_id'";
	//$query  = mysql_query($sql);
	//$result = mysql_fetch_row($query);
	$result = $dbh->query($sql)->fetch();
	$new_balance = $result['balance'];
	
	//update its transaction entry with the new balance (for the account history)
	$sql	= "UPDATE transactions SET transaction_id='$srv_id',balance='$new_balance' where journal_id='$ta_id'";
	//do_query($sql);
	$dbh->exec($sql);
	
	if ($ta_has_receiver)
	{
		//the same for the receiver:
		//get her balance...
		$sql 	= "SELECT balance FROM members WHERE member_id='$receiver_id'";
		//$query  = mysql_query($sql);
		//$result = mysql_fetch_row($query);
		$result = $dbh->query($sql)->fetch();
		$new_balance = $result['balance'];
		
		//...and update her entry in the service table for the account history
		$sql	= "UPDATE service SET receiver_id='$receiver_id',receiver_balance='$new_balance' where journal_id='$srv_id'";
		//do_query($sql);
		$dbh->exec($sql);
		
		//only, and only if ALL THESE STEPS SUCCEEDED, write to the database...
		$dbh->commit();
	}
} catch (Exception $e)
{
	//...otherwise roll back, something went wrong and the transaction failed!
	//nothing written to database.
	$dbh->rollback();
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
