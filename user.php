<?php	
	session_start();
	include "config.php";
	
	$username="";
	if(isset($_COOKIE['uws_login']))
	//if there is, it logs you in and directes you to the members page
	{
		$username = $_COOKIE['uws_login'];		
	}
	elseif (isset($_SESSION['uname']))
	{		
		$username = $_SESSION['uname'];		
	}
	else 
	{
		header("Location: index.php");
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
<title>Universal Wealth System UWS - User info</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>

				<h3><?php echo translate("uws:overview") ?></h3>
				<div class="date"><?php echo date('d F Y') ?></div><!--date-->
				</div><!--header-->
				<div class="content">
<?php		
	
	$username = $_GET['user'];
	$sql = ("SELECT balance from contributors where contributor = '$username'");
   	$query = mysql_query($sql);
   	$userbalance = 0;
   	while ($result = mysql_fetch_array($query)) {
       		$userbalance = $result[0];
   	}
   	$totalServices = 0;
   	$sql = ("SELECT * from contributors");
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) {
       		$totalServices = $totalServices + $result['balance'];
   	}

   	$totalInventory = 0;
   	$sql = ("SELECT * from units");
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) {
       		$totalInventory = $totalInventory + $result['inventory'];
   	}

?>
	<table class="nicetable" cellspacing="0">
	<tr>
<?php
	$rowspec = '<th scope="row" class="spec">';
	$rowspecalt = '<th scope="row" class="specalt">';
	echo $rowspec;
	echo translate("uws:balance");
   	echo '</th><td class="spec"> ' . $userbalance . "</td></tr>";
   	echo "<tr>";
	echo $rowspecalt  . translate("uws:total-srv") . '</th><td class="spec">' . $totalServices . "</td>";
   	$share = 0;
   	if ($totalServices != 0) {
        	$share = (100/$totalServices)*$userbalance;
   	}
   	echo "<tr>" . $rowspec . translate("uws:share") . "</th>";
   	echo '<td class="spec">' . (" " . number_format($share,2));
   	echo " % " . "</td></tr>";
   	echo "<tr>" . $rowspecalt . translate("uws:total-inv") . '</th><td class="spec">' . $totalInventory;
	echo "</td></tr>";
?>
	</table>

				</div>	<!--content-->		
				<div class="footer">
				<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div><!--footer-->
			</div><!--post-->
		
	</div><!--primarycontent-->
		
		<div id="secondarycontent">

			<!-- Displaying lists links -->
<?php include "lists.php" ?>
			<!-- Displaying action links -->
<?php include "actions.php" ?>

			<!-- secondary content end -->

		</div>
				<div id="servicelist">
				<div class="header">
					<h3><?php echo translate("uws:my_srv_del") ?></h3>
				</div>
				<div class="content">
 <table class="nicetablelarge" cellspacing="0">
   <tr>
        <th scope="col" abbr="Date"><?php echo translate("uws:date") ?></th>
        <th scope="col" abbr="Service delivered"><?php echo translate("uws:service") ?></th>
        <th scope="col" abbr="Description"><?php echo translate("uws:desc") ?></th>
        <th scope="col" abbr="Time"><?php echo translate("uws:time") ?></th>
        <th scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
        <th scope="col" abbr="Link"><?php echo translate("uws:link") ?></th>
   </tr>


<?php
   $sql = ("SELECT * from service where contributor = '$username'");
   $query = mysql_query($sql);
   $cnt=0;
   $tdnorm = '<td class="spec">';
   $tdalt  = '<td class="specalt">';
   $td = $tdnorm;
   while ($result = mysql_fetch_array($query)) {
	if ($cnt%2 == 0) {
		$td = $tdnorm;
	} else {
		$td = $tdalt;
	}
        echo "<tr>";
                $date = $result['date'];
                echo $td . date('Y M d H:i:s',$date) . "</td>";
                $service = $result['service'];
                echo $td . utf8_decode($service) . "</td>";
                $description = $result['description'];
                echo $td . utf8_decode($description) . "</td>";
                $lifetime = $result['lifetime'];
                echo $td . $lifetime . "</td>";
                $factor = $result['factor'];
                echo $td . $factor . "</td>";
                $link = $result['link'];
                echo $td . utf8_decode($link) . "</td>";
        echo "</tr>";
	$cnt++;
   }
?>
  </table>
				</div><!-- content-->			
				<div class="footer">
					<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div><!--footer-->
			</div><!--servicelist-->
	
	
		<div id="footer">
		
			&copy; UWS. </a>.
		
		</div>

	</div>

</div>

</body>
</html>
