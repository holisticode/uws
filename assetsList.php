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
<title>Universal Wealth System UWS - <?php echo translate("uws:bid_list") ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:assets_list") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">
	<table class="nicetable" cellspacing="0">
	<tr>

       <th scope="col" abbr="Asset"><?php echo translate("uws:asset") ?></th>
       <th scope="col" abbr="Value"><?php echo translate("uws:value") ?></th>       
       <th scope="col" abbr="Physical"><?php echo translate("uws:physical") ?></th>
       <th scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
       <th scope="col" abbr="Action"></th>
   	</tr>

<?php	
   	$sql = "SELECT * from assetlist";
   	$query = mysql_query($sql);
   	$cnt=0;
   	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td = $tdnorm;
   	while ($result = mysql_fetch_array($query)) 
   	{
		if ($cnt%2 == 0) 
		{
			$td = $tdnorm;
		} else {
			$td = $tdalt;
		}
    	echo "<tr>";
            $unit		= $result['asset'];
            $value		= $result['inventory'];
            $factor		= $result['last_factor'];
            $physical	= $result['physical'];
			echo $td . $unit . "</td>";
			echo $td . $value . "</td>";
    		echo $td . $physical . "</td>";
    		echo $td . $factor . "</td>";
    		
    		echo $td;
    		$asset_id = $result['asset_id'];
    		echo '<a href="bidAsset.php?unitID=' . $asset_id . 
				'"><img src="/images/bid.png" border="0" alt="' .
				translate("uws-bid") . '"></a>';
			echo "</td>";
    	echo "</tr>";
	$cnt++;
   }
   
   $sql   	= "SELECT total_inventory from totals";
   $query 	= mysql_query($sql);
   $invsum	= mysql_fetch_row($query);
   
   echo "<tr>";
   echo "<th scope=\"col\" abbr=\"\">Total</th>";
   echo "<th scope=\"col\" abbr=\"\">" . $invsum[0] . "</th>";
   echo "<th></th>";
   echo "<th></th>";
   echo "<th></th>";
   echo "</tr>"; 
   
?>


  </table><br><br>
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
