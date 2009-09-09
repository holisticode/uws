<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
	include_once "config.php";
	
	$storyLink 	= $_GET['storyLink'];
	
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
		$date = time();
	
		foreach($_GET as $key=>$value)
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
			$sql = "SELECT * FROM uwscontributors WHERE contributor = '$user'";
			//print "SQL: ".$sql."<br>";
			$query = mysql_query($sql);
			while ($result = mysql_fetch_array($query)) {
				$user_id = $result['contributorID'];
			}
			if ($user_id == "") 
			{
				//neuer user
				$timestamp = time();			
				$query = "INSERT into uwscontributors values ('', '$timestamp', '$user','0','','')";
				$result = mysql_query($query);
				if (!$result) {
					print("Query failed: " . mysql_error());
				} else {
					$user_id = mysql_insert_id();
				}
			}
			
			$work 	= $data[$time_key];
			$factor = $data[$factor_key];
			$desc	= implode(" - ",array_keys($users));
			$query 	= "INSERT into uwsservice values ('','$date','$user','$contribution','$desc','$work','$factor','$storyLink')";
			
			$result = mysql_query($query);
			if (!$result) 
			{
				print("Query failed: " . mysql_error());
			} else 
			{
				$user_journal_id = mysql_insert_id();
			}
		} 
	} catch (Exception $e)
	{
		$redirectto = "error.php?error=$e";
	}
		
			//Alte Story
	//should do nothing for now, as it would need roll-back...	
	//$errormsg = "You are trying to update an existing chat story. \
	//	     This is currently not possible.";

	//$redirectto = "error.php?error=$errormsg";	
		
?>


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
