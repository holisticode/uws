<?php
include "config.php";

//Checks if there is a login cookie
if(isset($_COOKIE['uws_login']))

	//if there is, it logs you in and directes you to the members page
	{
	$username = $_COOKIE['uws_login'];
	$pass = $_COOKIE['Key_my_site'];
	$check = mysql_query("SELECT * FROM contributors WHERE contributor = '$username'")or die(mysql_error());
	while($info = mysql_fetch_array( $check ))
	{
		if ($pass != $info['password'])
		{
		}
		else
		{
		header("Location: home.php");
	
		}
	}
}


$error="";

//if the login form is submitted
if (isset($_POST['submit'])) { // if form has been submitted
	//echo "hallo\n";
	// makes sure they filled it in
	if(!$_POST['uname'] | !$_POST['pwd']) {
		$error=translate("uws:fill-in");
		//echo $error;
		//die($error);
	}
	// checks it against the database

	if (!get_magic_quotes_gpc()) {
		$_POST['email'] = addslashes($_POST['email']);
	}
	$check = mysql_query("SELECT * FROM contributors WHERE contributor = '".$_POST['uname']."'")or die(mysql_error());

	//Gives error if user doesn't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) {
		$error = translate("uws:no-such-user");
		//die($error);
	}
	while($info = mysql_fetch_array( $check ))
	{
		$_POST['pwd'] 		= stripslashes($_POST['pwd']);
		$info['password'] 	= stripslashes($info['password']);
		$_POST['pwd'] 		= md5($_POST['pwd']);

		//gives error if the password is wrong
		if ($_POST['pwd'] != $info['password']) {
			$error = translate("uws:wrong_pwd");
			//die($error);
		}
		else
		{		
				
			// if login is ok then we add a cookie
			$_POST['uname'] = stripslashes($_POST['uname']);
			$hour = time() + 3600;
			$user = $_POST['uname'];
			$pass = $_POST['pass'];
			session_start();
			$_SESSION['uname'] = $user;
			setcookie("uws_login", $user, $hour);
			setcookie("Key_my_site", $pass , $hour);
			
			
			//then redirect them to the home area
			header("Location: home.php");
		}
	}
	//print "not set2";
 
	//header("Location: index.php");
	// if they are not logged in
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--

	terrafirma1.0 by nodethirtythree design
	http://www.nodethirtythree.com

-->
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<title>Universal Wealth System UWS - Start</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
<script type="text/javascript">
	function put_errors_invisible()
	{
		//document.getElementById('errors').style.visibility='hidden';
	}
	
	function put_errors_visible()
	{
		//document.getElementById('errors').style.visibility='visible';
	}
</script>
</head>
<body onload=put_errors_invisible()>

<div id="outer">

	<div id="upbg"></div>

	<div id="inner">
	
		<div id="header">
			<h1><span>UWS</span><sup>pre-alpha</sup></h1>
			<h2><?php echo translate("uws:slogan") ?></h2>
		</div>
		
		<div id="splash"></div>
		
		<div id="menu">
			<ul>
			</ul>

	
			<div id="date"><?php echo date('d F Y')?> </div>
		</div>
		
		<div id="primarycontent">
		
			<!-- primary content start -->
		
			<div class="post">
				<div class="header">

							<h3>UWS in development</h3>
					<div class="date">July 3, 2009</div>
				</div>
				<div class="content">
					<img src="images/pic1.jpg" class="picA floatleft" alt="" />
					<p>After different attempts, the UWS electronic system is now finally in development. A first release should be available soon.</p>
					<p></p>
				</div>			
				<div class="footer">
					<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div>
			</div>
		
			<div class="post">
				<div class="header">
					<h3>The UWS explained</h3>
					<div class="date">July 3, 2009</div>
				</div>
				<div class="content">
					<p>The UWS is a completely different way of looking at economy.</p>					<p></p>
				</div>			
				<div class="footer">
					<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div>
			</div>

			<div class="post">
				<div class="header">
					<h3>The UWS in action</h3>
					<div class="date">July 3, 2009</div>
				</div>
				<div class="content">
					<p>At the OS, a beautiful estate in North-East Brazil, the UWS is being applied in practice.</p>
<p>Read on if you want to know more about the experiences in this little paradise.
				</div>			
				<div class="footer">
					<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div>
			</div>

			<!-- primary content end -->
	
		</div>
		
		<div id="secondarycontent">

			<!-- secondary content start -->
		
			<h3><?php echo translate("uws:login") ?></h3>
			<div class="content">
				<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
					<?php echo translate("uws:user") ?>:<br>
					<input type="text" name="uname" id="uname" value=""/>
					<?php echo translate("uws:pwd") ?>:<br>
					<input type="password" name="pwd" id="pwd" value=""/>
					<input type="submit" name="submit" id="submit" value="<?php echo translate("uws:login") ?>"/>
					<a href="register.php"><?php echo translate("uws:register")?></a>
				</form>
				<p></p>
			</div>

			<div id="errors">
				<span style='color:red' name='PHPSpan'>
					<table border='1'>
						<tr>
							<td><?php echo $error ?></td>
						</tr>
					</table>
				</span><br />
			</div>
			
			<h3>Links</h3>
			<div class="content">
				<ul class="linklist">
					<li class="first"><a href="#"></a></li>
					<li><a href="#">Dignissim nec augue </a></li>
					<li><a href="#">Nunc ante elit nulla</a></li>
					<li><a href="#">Aliquam suscipit</a></li>	
					<li><a href="#">Cursus sed arcu sed</a></li>
					<li><a href="#">Aliquam suscipit</a></li>
					<li><a href="#">Donec mollis dolore</a></li>
					<li><a href="#">Eu ante cras at risus</a></li>
				</ul>
			</div>

			<!-- secondary content end -->

		</div>
	
		<div id="footer">
		
			&copy; UWS. </a>.
		
		</div>

	</div>

</div>

</body>
</html>
