<?php 

include "config.php";

//if the login form is submitted
if (isset($_POST['submit'])) { // if form has been submitted

	// makes sure they filled it in
	if(!$_POST['uname'] | !$_POST['pwd']) {
		die(translate("uws:fill-in"));
	}
	// checks it against the database

	if (!get_magic_quotes_gpc()) {
		$_POST['email'] = addslashes($_POST['email']);
	}
	$check = mysql_query("SELECT * FROM contributors WHERE contributors = '".$_POST['uname']."'")or die(mysql_error());

	//Gives error if user dosen't exist
	$check2 = mysql_num_rows($check);
	if ($check2 == 0) {
		die(translate("uws:no-such-user"));
	}
	while($info = mysql_fetch_array( $check ))
	{
		$_POST['pwd'] = stripslashes($_POST['pwd']);
		$info['password'] = stripslashes($info['password']);
		$_POST['pass'] = md5($_POST['pass']);

		//gives error if the password is wrong
		if ($_POST['pass'] != $info['password']) {
			die(translate("uws:wrong_pwd"));
		}
		else
		{

			// if login is ok then we add a cookie
			$_POST['uname'] = stripslashes($_POST['uname']);
			$hour = time() + 3600;
			setcookie(ID_my_site, $_POST['name'], $hour);
			setcookie(Key_my_site, $_POST['pass'], $hour);

			//then redirect them to the home area
			header("Location: home.php");
		}
	}
}
else
{
	header("Location: index.php");
	// if they are not logged in
?>
<!--
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<table border="0">
<tr><td colspan=2><h1>Login</h1></td></tr>
<tr><td>Username:</td><td>
<input type="text" name="username" maxlength="40">
</td></tr>
<tr><td>Password:</td><td>
<input type="password" name="pass" maxlength="50">
</td></tr>	
<tr><td colspan="2" align="right">
<input type="submit" name="submit" value="Login">
</td></tr>
</table>
</form>
-->
<?php
}

?> 