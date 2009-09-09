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
<title>Universal Wealth System UWS</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<div id="header">
			<h1><span>UWS</span><sup>1.0</sup></h1>
			<h2>Free yourself.</h2>
		</div>

		<div id="splash"></div>

		<?php include "menu.php" ?>

		<div id="primarycontent">
		
			<!-- primary content start -->
		
			<div class="post">
				<div class="header">
					<h3>List of usables in UWS</h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">
	<table class="nicetable" cellspacing="0">
	<tr>

       <th scope="col" abbr="Usable">Usable</th>
   	</tr>

<?php
   	$sql = ("SELECT * from uwsconsume");
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
                $unit= $result['uws-unit'];
		echo $td . $unit . "</td>";
		echo $td . $inventory . "</td>";
        	echo "</tr>";
	$cnt++;
   }
?>
  </table>
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
<?php include "lists.php" ?>
		<!-- Displaying action links -->
<?php include "actions.php" ?>

		<!-- secondary content end -->

		</div>
	
		<div id="footer">
		
			&copy; UWS. </a>.
		
		</div>

	</div>

</div>

</body>
</html>