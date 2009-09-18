<?php	
	session_start();
	include "config.php";

try
{
	$content = "";
	//echo $_COOKIE['Key_my_site'];	
	//This code runs if the form has been submitted
	if (isset($_POST['submit'])) 
	{	
		//This makes sure they did not leave any fields blank
		if (!$_POST['oldpwd'] | !$_POST['newpass'] | !$_POST['newpass2'] ) 
		{
			throw new Exception('You did not complete all of the required fields.');
		}		

		//oldpwd does not match registered password!	
		$member_id 	= $_SESSION['member_id'];
		$sql 		= "SELECT password FROM members WHERE member_id='$member_id'";
		$query 		= mysql_query($sql);
		$result 	= mysql_fetch_row($query);
		$pass 		= $result[0];
		
		if (md5($_POST['oldpwd']) != $pass)
		{
			throw new Exception('Your current passwort does not match. Cannot change to new one.');
		}
		// this makes sure both passwords entered match
		if ($_POST['newpass'] != $_POST['newpass2']) 
		{
			throw new Exception('Your passwords did not match. ');
		}
	
		// here we encrypt the password and add slashes if needed
		$_POST['newpass'] = md5($_POST['newpass']);
		if (!get_magic_quotes_gpc()) 
		{
			$_POST['newpass'] = addslashes($_POST['newpass']);
		}
	
		// now we insert it into the database
		
		$hour = time() + 3600;
		$newpass = $_POST['newpass'];
		$sql = "UPDATE members SET password='$newpass'";
		do_query($sql);
		setcookie("Key_my_site", $_POST['newpass'] , $hour);			
		$content = "<h1>". translate("uws:newpwdok"). "</h1>";
	
	} // if
}//try
catch (Exception $e)
{
	$redirect = $errorpage.$e->getMessage();
	header("Location:".$redirect);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--

	terrafirma1.0 by nodethirtythree design
	http://www.nodethirtythree.com

-->
<html>
<head>


<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Universal Wealth System UWS - <?php echo translate("uws:register") ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:register") ?></h3>
					<div class="date">
<?php echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">

<?php 
	echo $content;
	if (! isset($_POST['submit'])) 
	{		
?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table class="nicetable" cellspacing="0">
<tr><td><?php echo translate("uws:oldpwd") ?>:</td><td>
<input type="password" name="oldpwd" maxlength="60">
</td></tr>
<tr><td><?php echo translate("uws:pwd") ?>:</td><td>
<input type="password" name="newpass" maxlength="10">
</td></tr>
<tr><td><?php echo translate("uws:pwd-confirm") ?>:</td><td>
<input type="password" name="newpass2" maxlength="10">
</td></tr>
<tr><td colspan=2><input type="submit" name="submit" value="<?php echo translate("uws:changepwd") ?>"></td></tr> </table>
</form><br><br>

<?php

	} //else
	

?> 
</div>			
				<div class="footer">
					<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div>
			</div>
	
		</div>
		
		<div id="secondarycontent">

		<!-- Displaying lists links -->
 		<?php //include "lists.php" ?> 
		<!-- Displaying action links -->
		<?php //include "actions.php" ?>

		<!-- secondary content end -->

		</div>
	
		<div id="footer">
		
			&copy; UWS. </a>.
		
		</div>

	</div>

</div>

</body>
</html>
