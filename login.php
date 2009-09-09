<?php
	include "config.php";
	session_start();	
	
	$redirectto = "index.html";
		
	$username = $_POST['uname'];
	$pwd      = $_POST['pwd'];
	
	//TODO: Needs proper registration process :)
	$sql = ("SELECT contributorID from uwscontributors where contributor = '$username'");
   	$query = mysql_query($sql);
   	$result = mysql_fetch_array($query); 
   	//echo $result;
    if (!$result) //user does not exist in the db yet
    {
    	//echo "creating new user";
    	$timestamp = time();
    	$query = "INSERT into uwscontributors values ('', '$timestamp','$username','0','', '')";
    	
    	$result = mysql_query($query);
		if (!$result) {
			print("Query failed: " . mysql_error());
		} else {
			$user_2_id = mysql_insert_id();
		}
    }
    else {
    	//print "user exists.";
    }
	
	
	$successful = TRUE; //TODO: here the proper login method will be implemented

	if ($successful) {
		$_SESSION['uname'] = $username;
		$redirectto = "home.php";
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
	document.location.href = "<?php echo $redirectto ?>";
	
	
 // End -->
</script>
</head>

<body>
</body>
</html>
