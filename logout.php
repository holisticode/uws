<?php
$past = time() - 100;
//this makes the time in the past to destroy the cookie
setcookie("uws_login", gone, $past);
setcookie("Key_my_site", gone, $past);
$_SESSION['uname']=null;
$_SESSION['member_id']=null;
header("Location: index.php");
?> 