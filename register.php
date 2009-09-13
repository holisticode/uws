<?php	
	session_start();
	include "config.php";
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
// Connects to your Database
include "config.php";

//This code runs if the form has been submitted
if (isset($_POST['submit'])) {

	//This makes sure they did not leave any fields blank
	if (!$_POST['username'] | !$_POST['pass'] | !$_POST['pass2'] ) {
	die('You did not complete all of the required fields');
	}

	// checks if the username is in use
	if (!get_magic_quotes_gpc()) {
	$_POST['username'] = addslashes($_POST['username']);
	}
	
	$usercheck = $_POST['username'];
	$check = mysql_query("SELECT contributor FROM contributors WHERE contributor = '$usercheck'")
	or die(mysql_error());
	$check2 = mysql_num_rows($check);

	//if the name exists it gives an error
	if ($check2 != 0) {
	die('Sorry, the username '.$_POST['username'].' is already in use.');
	}

	// this makes sure both passwords entered match
	if ($_POST['pass'] != $_POST['pass2']) {
	die('Your passwords did not match. ');
	}

	// here we encrypt the password and add slashes if needed
	$_POST['pass'] = md5($_POST['pass']);
	if (!get_magic_quotes_gpc()) {
	$_POST['pass'] = addslashes($_POST['pass']);
	$_POST['username'] = addslashes($_POST['username']);
	}

	// now we insert it into the database
	$insert = "INSERT INTO contributors (contributor, password)
	VALUES ('".$_POST['username']."', '".$_POST['pass']."')";
	$add_member = mysql_query($insert);
?>


<h1><?php echo translate("uws:registered") ?></h1>
<p><?php echo translate("uws:register-thanks") ?></a>.</p>
<?php
	}
	else
	{
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<table class="nicetable" cellspacing="0">
<tr><td><?php echo translate("uws:user") ?>:</td><td>
<input type="text" name="username" maxlength="60">
</td></tr>
<tr><td><?php echo translate("uws:pwd") ?>:</td><td>
<input type="password" name="pass" maxlength="10">
</td></tr>
<tr><td><?php echo translate("uws:pwd-confirm") ?>:</td><td>
<input type="password" name="pass2" maxlength="10">
</td></tr>
<tr><td colspan=2><input type="submit" name="submit" value="<?php echo translate("uws:register") ?>"></td></tr> </table>
</form><br><br>

<?php
}
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
