<?php
	include_once "config.php";
	
	$INSERT_NEW_USERS = 0;
	
	$storyLink 	= $_POST['storyLink'];
	
	$users 			= array();
	$user_values 	= array();
	$current_user	= null;
	$factor_key		= "factor_";
	$time_key		= "time_";
	$user_key		= "user_";
	$weighted_key	= "weighted_";
	
	$default_factor	= 1.0;
	$contribution	= "Chatten";
	
	$redirectto 	= "home.php";
	
	try
	{
		foreach($_POST as $key=>$value)
		{
			//print "KEY: ".$key." - VALUE: ".$value;
			if (strncmp($user_key,$key, 5) === 0)
			{
				//print "userkey<br>";
				$users[$value] 	= array();			
				$current_user	= $value;						
			}
			else if (strncmp($factor_key,$key,7) == 0)
			{
				//print "factorkey";
				$users[$current_user][$factor_key] = $value;
			}
			else if (strncmp($time_key,$key,5) === 0)
			{
				//print "timekey";
				$users[$current_user][$time_key] 	= $value;
				if (is_null($users[$current_user][$factor_key]))
				{			
					$users[$current_user][$factor_key] 	= $default_factor;
				}						 	
			}		
			else if (strncmp($weighted_key,$key,9) == 0)
			{
				//print "factorkey";
				$users[$current_user][$weighted_key] = $value;
			}
		}	
		
		$count = 1;
		foreach($users as $user=>$data)
		{
			//print "User: ".$user."<br>";	
			//hole user ids
			$user_id = "";
			$sql = "SELECT member_id FROM members WHERE name = '$user'";
			//print "SQL: ".$sql."<br>";
			$query = mysql_query($sql);
			$result = mysql_fetch_row($query);
			$user_id = $result[0];
						
			if ($user_id == "") 
			{
				if ($INSERT_NEW_USERS)
				{			
					//neuer user
					$timestamp = time();
					$password  = md5($user."123");			
					$query = "INSERT into members values (join_date,name, password,cell_id)." .
							 "('', '$timestamp',$user,$password,$DEFAULT_CELL_ID)";
					$result = mysql_query($query);
					if (!$result) {
						print("Query failed: " . mysql_error());
					} else {
						$user_id = mysql_insert_id();
					}
				} else
				{
					$msg = translate("uws:user_not_existing");
					throw new Exception($msg);
				}
			}
			
			$work 	= $data[$time_key];
			$factor = $data[$factor_key];
			$desc	= implode(" - ",array_keys($users));
			
			$sql 	= "SELECT balance FROM members WHERE member_id='$user_id'";
			$query  = mysql_query($sql);
			$result = mysql_fetch_row($query);
			$balance = $result[0];
			
			$service_units = $work * $factor;
			$balance = $balance + $service_units;
			$timestamp = time();
			 
			$service_id = get_service_id_from_name($contribution);
			
			try
			{
				$dbh->beginTransaction();
				
				$sql 	= "INSERT INTO transactions VALUES ".
					   		"('','$timestamp','$SERVICE_TYPE','0','$user_id','$desc','$factor','$link', $balance)";
			    //print $sql."<br><br>";
				//$ta_id 	= do_query($sql);
				$dbh->exec($sql);		
				$ta_id = $dbh->lastInsertId();
				
				$sql 	= "INSERT INTO service VALUES('','$ta_id','','','$service_id','$lifetime')";
				$dbh->exec($sql);
				//$srv_id = do_query($sql);
				$srv_id = $dbh->lastInsertId();
				
				$sql	= "UPDATE transactions SET transaction_id='$srv_id' where journal_id='$ta_id'";
				//do_query($sql);
				$dbh->exec($sql);
				
				$sql	= "UPDATE totals SET total_services=total_services + $service_units ";
				//do_query($sql);
				$dbh->exec($sql);
				
				$sql	= "UPDATE servicelist SET provided=provided + $service_units where service_id='$service_id'";
				//do_query($sql);
				$dbh->exec($sql);
				
				$sql	= "UPDATE members SET balance=balance + $service_units where member_id='$user_id'";
				//do_query($sql);
				$dbh->exec($sql);
				
				$dbh->commit();
				
			} catch (Exception $e)
			{
				$dbh->rollback();
				throw new Exception($e->getMessage());
			}		
		} //foreach user
	} catch (Exception $e)
	{
		header("Location: ".$errorpage.$e->getMessage());
	}
		
			//Alte Story
	//should do nothing for now, as it would need roll-back...	
	//$errormsg = "You are trying to update an existing chat story. \
	//	     This is currently not possible.";

	//$redirectto = "error.php?error=$errormsg";	
		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>UWS</title>
<link href="basic.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!-- Begin
	document.location.href = "<?php echo $redirectto ?>";
	
	
// End -->
</script>
</head>

<body>
</body>
</html>
