<?php
	session_start();
	include "config.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
	$target = "home.php";
	
	$username 	= $_SESSION['uname'];
	
	$date 	= time();
	$price 	= $_POST['price'];
	$bid	= $_POST['my_bid_amount'];
	$factor	= $_POST['my_factor'];
	$unit	= $_POST['unit'];
	
	$sql = "INSERT INTO consume VALUES('','$date','$username','$unit','','$bid','$factor','$price','')";
	$result = mysql_query($sql);
	if (!$result) {
		$target = "error.php?error=Query failed: " . mysql_error();
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
