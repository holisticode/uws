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
<div id="outer" width="100%">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:bid_list") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">
				
	<table class="nicetable" cellspacing="0">
	<tr>       
       <th class="small" scope="col" abbr="Date"><?php echo translate("uws:date") ?></th>
       <th class="small" scope="col" abbr="User"><?php echo translate("uws:user") ?></th>       
       <th class="small" scope="col" abbr="Unit"><?php echo translate("uws:unit") ?></th>
       <th class="small" scope="col" abbr="Amount"><?php echo translate("uws:bid_amount") ?></th>
       <th class="small" scope="col" abbr="Price"><?php echo translate("uws:price") ?></th>
       <th class="small" scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
       <th class="small" scope="col" abbr="Action"></th>
   	</tr>

<?php	
   	$sql = "SELECT * from bid";
   	$query = mysql_query($sql);
   	$cnt=0;
   	$tdnorm = '<td class="specsmall">';
   	$tdalt  = '<td class="specaltsmall">';
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
    		$date		= $result['tstamp'];
    		$user		= $result['member_id'];
            $asset_id	= $result['asset_id'];
            $amount		= $result['amount'];
            $price		= $result['price'];
            $factor		= $result['factor'];
            
			echo $td . date('Y M d H:i:s',$date) . "</td>";
			echo $td . get_member_name_from_id($user) . "</td>";
    		echo $td . get_asset_name_from_id($unit) . "</td>";
    		echo $td . round($amount,3) . "</td>";
    		echo $td . round($price,3) . "</td>";
    		echo $td . round($factor,3) . "</td>";
    		
    		echo $td;
    		$unitID = $result['bidID'];
    		echo '<a href="bidDetail.php?bidID=' . $unitID . 
				'"><img src="/images/bid.png" border="0" alt="' .
				translate("uws-bid") . '"></a>';
			echo "</td>";
			
    	echo "</tr>";
	$cnt++;
   }
   
//   echo "<tr>";
//   echo "<th scope=\"col\" abbr=\"\">Total</th>";
//   echo "<th scope=\"col\" abbr=\"\">" . $invsum[0] . "</th>";
//   echo "<th></th>";
//   echo "<th></th>";
//   echo "<th></th>";
//   echo "</tr>"; 
   
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
