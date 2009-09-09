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
<title>Universal Wealth System UWS - Admin</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />

</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
		
					<h3><?php echo translate("uws:key_data",$lang) ?></h3>
					<div class="date"><?php echo date('d F Y') ?></div> <!-- div date -->
	
				</div><!-- div header -->
				
				<div class="content">
				<table class="nicetable" cellspacing="0">
	<tr>
       <th scope="col" abbr="Indicator"><?php echo translate("uws:indicator",$lang) ?></th>
       <th scope="col" abbr="Value"><?php echo translate("uws:value",$lang) ?></th>
   	</tr>
	<tr>
<?php
	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
	
	$sql   	= ("SELECT total_services from uwstotals;");
   	$query 	= mysql_query($sql);
   	$srvsum	= mysql_fetch_row($query);
   	
   	$sql 	= ("SELECT total_inventory from uwstotals;");
   	$query 	= mysql_query($sql);
   	$invsum	= mysql_fetch_row($query);
   	
   	echo $tdnorm . translate("uws:lg", $lang);
   	echo '</td>' . $tdnorm . $srvsum[0] . "</td></tr>";
   	echo "<tr>";
	echo $tdalt  . translate("uws:ig",$lang) . '</td>' . $tdalt . $invsum[0] . "</td></tr>";
?>
	</table><br><br>
				</div><!-- content -->
				
				<div class="footer">
					<ul>
						<li class="printerfriendly"><a href="#">Printer Friendly</a></li>
						<li class="readmore"><a href="#">Read more</a></li>
					</ul>
				</div>
				-->
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
					<h3><?php echo translate("uws:all_ds",$lang) ?></h3>
				</div>
				<div class="content">	
	<table class="nicetablelarge" cellspacing="0">
	<tr>

       <th scope="col" abbr="Date"><?php echo translate("uws:date",$lang) ?></th>
       <th scope="col" abbr="Typ"><?php echo translate("uws:type",$lang) ?></th>
       <th scope="col" abbr="KID"><?php echo translate("uws:kid",$lang) ?></th>
       <th scope="col" abbr="TID"><?php echo translate("uws:tid",$lang) ?></th>
       <th scope="col" abbr="RA"><?php echo translate("uws:ra",$lang) ?></th>
       <th scope="col" abbr="Faktor"><?php echo translate("uws:factor",$lang) ?></th>
       <th scope="col" abbr="link"><?php echo translate("uws:link",$lang) ?></th>
   	</tr>

<?php
	include "config.php";
	session_start();

	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td = $tdnorm;

   	$sql = ("SELECT * from uwsservice");
   	$query = mysql_query($sql);

   	while ($result = mysql_fetch_array($query)) 
   	{
		if ($cnt%2 == 0) {
			$td = $tdnorm;
		} else {
			$td = $tdalt;
		}
        echo "<tr>";
        $date = $result['date'];
        echo $td . date('Y M d H:i:s',$date) . "</td>";        
        $type = "L";
        echo $td . $type . "</td>";
        $contributor = $result['contributor'];
        echo $td . utf8_decode($contributor) . "</td>";
        $uwsservice = $result['uwsservice'];
        echo $td . utf8_decode($uwsservice) . "</td>";       
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
  </table><br><br>
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
