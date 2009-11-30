<?php
/*
 * UWS - Universal Wealth System
 * logout.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * Logging out the user.
 */ 

$past = time() - 100;
//this makes the time in the past to destroy the cookie
setcookie("uws_login", gone, $past);
setcookie("Key_my_site", gone, $past);
$_SESSION['uname']=null;
$_SESSION['member_id']=null;
header("Location: index.php");
?> 