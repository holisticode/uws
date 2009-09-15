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
<title>Universal Wealth System UWS - User home</title>
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
	
	$member_id = $_SESSION['member_id'];
	$sql = ("SELECT balance from members where member_id = '$member_id'");
   	$query = mysql_query($sql);
   	$userbalance = 0;
   	while ($result = mysql_fetch_array($query)) 
   	{
       		$userbalance = $result['balance'];
   	}
   	$totalServices = 0;
   	$sql = ("SELECT total_services from totals");
   	$query = mysql_query($sql);
   	$result = mysql_fetch_row($query);
    $totalServices = $result[0];
   	

   	$totalInventory = 0;
   	$sql = ("SELECT total_inventory from totals");
   	$query = mysql_query($sql);
   	$result = mysql_fetch_row($query);
    $totalInventory = $result[0];
   	

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
					<h3><?php echo translate("uws:my_account") ?></h3>
				</div>
				<div class="content">
 <table class="nicetablelarge" cellspacing="0">
   <tr>
        <th scope="col" abbr="Date"><?php echo translate("uws:date") ?></th>        
        <th scope="col" abbr="Type"><?php echo translate("uws:type") ?></th>
        <th scope="col" abbr="Action"><?php echo translate("uws:transaction") ?></th>
        <th scope="col" abbr="Description"><?php echo translate("uws:desc") ?></th>
        <th scope="col" abbr="Balance"><?php echo translate("uws:balance") ?></th>
        <th scope="col" abbr="View"><?php echo translate("uws:view") ?></th>
   </tr>


<?php
	
	$sql = "SELECT * FROM transactions WHERE member_id = '$member_id'";
	//TODO: also check the service list for receiver entries for member_id!!!   	
   	$sql = "SELECT * FROM transaction_type";
   	$query = mysql_query($sql);
   	$ta_types = array();
   	
   	$cnt=0;
   	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td 	= $tdnorm;
   	$tde 	= "</td>";
   	
   	while ($result = mysql_fetch_array($query))
   	{
   		$key   = $result['type_code'];
   		$value = $result['type_desc'];
   		$ta_types[$key] = $value;
   	}   	
   	
   	$sql = "SELECT * FROM transactions WHERE member_id = '$member_id'";
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query))
   	{
		if ($cnt%2 == 0) {
			$td = $tdnorm;
		} else {
			$td = $tdalt;
		}
		$type_code 	= $result['transaction_type'];
		$ta_type	= $ta_types[$type_code];
		$transaction_id = $result['transaction_id'];
		$transaction= get_transaction($type_code,$transaction_id);
		$ta_id 		= $result['journal_id'];		
	    echo "<tr>";
        $date 		= $result['tstamp'];
        echo $td . date('Y M d H:i:s',$date) . $tde;
        //echo "ta_type: ".$ta_type;
        echo $td . translate($ta_type) . $tde;
        echo $td . $transaction . $tde;
        $desc = $result['description'];
        echo $td . $desc . $tde;
        $balance = $result ['balance'];
        echo $td . $balance . $tde; 
        echo $td;
        echo '<a href="viewTransaction.php?taID=' . $ta_id .
			 '&taType='.$type_code .'&detID='. $transaction_id .
			 '&userID='.$member_id. 
				'"><img src="/images/bid.png" border="0" alt="' .
				translate("uws:view") . '"></a>'; 
		echo $tde;      
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
