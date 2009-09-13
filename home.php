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
	
	//$username = $_SESSION['uname'];
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
        <th scope="col" abbr="Service"><?php echo translate("uws:service") ?></th>
        <th scope="col" abbr="Unit"><?php echo translate("uws:asset") ?></th>
        <th scope="col" abbr="Description"><?php echo translate("uws:desc") ?></th>
        <th scope="col" abbr="Time"><?php echo translate("uws:time") ?></th>
        <th scope="col" abbr="Amount"><?php echo translate("uws:amount") ?></th>
        <th scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
        <th scope="col" abbr="Price"><?php echo translate("uws:price") ?></th>
        <th scope="col" abbr="Balance"><?php echo translate("uws:balance") ?></th>
        <th scope="col" abbr="Link"><?php echo translate("uws:link") ?></th>
   </tr>


<?php
/*
	$service_entries = array();
	$inventorize_entries = array();
	$consume_entries = array();
	
	$sql = ("SELECT * from service where contributor = '$username'");
   	$query = mysql_query($sql);
   	
   	while ($result = mysql_fetch_array($query)) {
   		array_push($service_entries, $result);
   		//print_r($result);
   	}
   	//print_r($service_entries);
   	$sql = ("SELECT * from inventorize where contributor = '$username'");
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) {
   		//array_push($inventorize_entries, current($result));
   		array_push($inventorize_entries, $result);
   	}
   	print "<hr>";
   	//print_r($inventorize_entries);
   	$sql = ("SELECT * from consume where contributor = '$username'");
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) {
   		//array_push($consume_entries, current($result));
   		array_push($consume_entries, $result);
   		
   	}
   	
   	print "<hr>";
   	//print_r($consume_entries);   	  
   	 */
   	 
   	$sql = "select journalID,date,contributor,uwsservice,description,lifetime,factor,link," .
   				"null as uwsunit,null as amount,null as price from service where contributor='$username' union ".
   		   "select journalID,date,contributor,null,description,null,factor,link,uwsunit,amount,null ".
   		        "as invent from inventorize where contributor='$username'  union ".
   		   "select journalID,date,contributor,null,description,null,factor,link,uwsunit,amount,price ".
   		   		"as consume from consume where contributor='$username' order by date";
   	$query = mysql_query($sql);
   	
   	 
   	$cnt=0;
   	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td = $tdnorm;
   	
   	//$all_transactions = array_merge($service_entries, $inventorize_entries, $consume_entries);
   	//print_r($all_transactions);
  
	// Obtain a list of columns
	/*foreach ($all_transactions as $key => $row) {
    	$volume[$key]  = $row['volume'];
    	$edition[$key] = $row['edition'];
	}
	*/

	// Sort the data with volume descending, edition ascending
	// Add $data as the last parameter, to sort by the common key
	//$sorted_transactions = array_multisort($volume, SORT_DESC, $edition, SORT_ASC, $data);

   	
   	
   	
   	//foreach ($all_transactions as $entry) {
   	$balance = 0;
   	while ($result = mysql_fetch_array($query))
   	{
		if ($cnt%2 == 0) {
			$td = $tdnorm;
		} else {
			$td = $tdalt;
		}
		$ta_id = $result['journalID'];
		$sql = "SELECT balance FROM balance_history WHERE contributor='$username' AND ".
					"transaction_id = '$ta_id'";
		$query = mysql_query($sql);
		$balance = mysql_fetch_row($query);
	    echo "<tr>";
        $date = $result['date'];
        echo $td . date('Y M d H:i:s',$date) . "</td>";
        $uwsservice = $result['uwsservice'];
        echo $td . utf8_decode($uwsservice) . "</td>";
        $uwsunit = $result['uwsunit'];
        echo $td . utf8_decode($uwsunit) . "</td>";
        $description = $result['description'];
        echo $td . utf8_decode($description) . "</td>";
        $lifetime = $result['lifetime'];
        echo $td . $lifetime . "</td>";
        $amount = $result['amount'];
        echo $td . $amount . "</td>";
        $factor = $result['factor'];
        echo $td . $factor . "</td>";
        $price = $result['price'];
        echo $td . $price . "</td>";
        //$balance = $balance + ($lifetime * $factor);
        echo $td . $balance . "</td>";
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
