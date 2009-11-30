<?php
/*
 * UWS - Universal Wealth System
 * viewTransaction.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * This file displays a detailed information view on 
 * single transactions. Depending on the transaction type,
 * it will retrieve the according information from the
 * correspondent database table.
 */
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
<title>Universal Wealth System UWS - <?php echo translate("uws:view") ?> </title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />

</head>

<body>

<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

<?php 

	include "header.php";
		
	$username 	= $_SESSION['uname'];

	$ta_id		= $_GET['taID'];
	$type_code	= $_GET['taType'];
	//$member_id	= $_GET['userID'];
	
	$member_id		= 0;
  	$timestamp		= 0;
  	$transaction_id = 0;
  	$factor			= 0;
  	$balance		= 0;
  	$desc			= "";
  	$link			= "";
		
	//select the correspondent transaction, from the $POST ta_id variable
	$sql = "SELECT * from transactions where journal_id='" . $ta_id . "'";
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) 
   	{
   		$timestamp 		= $result['tstamp'];
   		$transaction_id	= $result['transaction_id'];
   		$member_id		= $result['member_id'];
        $balance		= $result['balance'];
        $factor			= $result['factor'];
   		$desc			= $result['description'];
        $link			= $result['link'];
   	}

   		//select the appropriate transaction type
   		$sql  	= "SELECT type_desc FROM transaction_type WHERE type_code='$type_code'";
   		$query	= mysql_query($sql);
   		$result = mysql_fetch_row($query);
   		$type_desc = $result[0];
?>
	
<?php

?>
					<h3><?php echo translate("uws:detail_view") . ": " . translate($type_desc) ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div>
				</div>
				<div class="content">
			<table class="nicetable" cellspacing="0">
			
	<?php
	switch ($type_code) {
		//get the detail infos depending on the transaction type
   		case $SERVICE_TYPE:
   			$sql 	= "SELECT * FROM service WHERE journal_id='$transaction_id'";
			$query 	= mysql_query($sql);
			$receiver_id = 0;
			$service_id  = 0;
			$lifetime	 = 0;
			while ($result = mysql_fetch_array($query))
			{
				$receiver_id = $result['receiver_id'];
				$service_id  = $result['service_id'];
				$lifetime	 = $result['lifetime'];
			}
			$member		= get_member_name_from_id($member_id);
			$receiver 	= get_member_name_from_id($receiver_id);
			$service  	= get_service_name_from_id($service_id);
			$service_units = $lifetime * $factor;
			?>
				<tr>
			 	<th scope="col" abbr="Date"><?php echo translate("uws:date") ?></th>
		        <th scope="col" abbr="Service"><?php echo translate("uws:service") ?></th>
		        <th scope="col" abbr="Description"><?php echo translate("uws:desc") ?></th>
			    <th scope="col" abbr="Who"><?php echo translate("uws:user") ?></th>
			   	</tr>
			<?php
			$td  = "<td align=\"center\">";
			$tde = "</td>";
			
			echo $td . date('Y M d H:i:s',$timestamp) . $tde;
			echo $td . $service . $tde;
			echo $td . $desc . $tde;
			echo $td . $member . $tde;
			
?>
		</tr></table>
		<br><br>
		<table class="nicetable" cellspacing="0">
		<tr>
			<th scope="col" abbr="Receiver"><?php echo translate("uws:receiver") ?></th>			   
			<th scope="col" abbr="Lifetime"><?php echo translate("uws:time") ?></th>
			<th scope="col" abbr="Delivered"><?php echo translate("uws:service_units") ?></th>
			<th scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
			<th scope="col" abbr="Link"><?php echo translate("uws:link") ?></th>
		</tr>
		<?php
			echo $td . $receiver . $tde;
			echo $td . $lifetime . $tde;
			echo $td . $service_units . $tde;
			echo $td . $factor . $tde;
			echo $td . $link . $tde;
			
			break;
		case $INVENTORIZE_TYPE:
			$sql 	= "SELECT * FROM inventorize WHERE journal_id='$transaction_id'";
			$query 	= mysql_query($sql);
			
			$asset_id 			= 0;
			$is_donation  		= 0;
			$amount_physical	= 0;
			$amount_inventory	= 0;
			while ($result = mysql_fetch_array($query))
			{
				$asset_id 			= $result['asset_id'];
				$is_donation  		= $result['is_donation'];
				$amount_physical	= $result['amount_physical'];
				$amount_inventory	= $result['amount_inventory'];
			}
			
			$asset 	= get_asset_name_from_id($asset_id);
			?>
				<tr>
			 	<th scope="col" abbr="Date"><?php echo translate("uws:date") ?></th>
		        <th scope="col" abbr="Asset"><?php echo translate("uws:asset") ?></th>
		        <th scope="col" abbr="Description"><?php echo translate("uws:desc") ?></th>
			    <th scope="col" abbr="Who"><?php echo translate("uws:user") ?></th>
			    </tr>
			<?php
			$td  = "<td align=\"center\">";
			$tde = "</td>";
			
			echo $td . date('Y M d H:i:s',$timestamp) . $tde;
			echo $td . $asset . $tde;
			echo $td . $desc . $tde;
			echo $td . $member_id . $tde;
			
	?>
	</tr></table>
		<br><br>
		<table class="nicetable" cellspacing="0">
		<tr>
			<th scope="col" abbr="Donation"><?php echo translate("uws:donation") ?></th>			   
			<th scope="col" abbr="Physical"><?php echo translate("uws:physical") ?></th>
			<th scope="col" abbr="Inventory"><?php echo translate("uws:inventory") ?></th>
			<th scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
			<th scope="col" abbr="Link"><?php echo translate("uws:link") ?></th>
		</tr>
		<?php
			$donation = "uws:no";
			if ($is_donation){
				$donation = "uws:yes";
			}
			echo $td . translate($donation) . $tde;
			echo $td . $amount_physical . $tde;
			echo $td . $amount_inventory . $tde;
			echo $td . $factor . $tde;
			echo $td . $link . $tde;
			
			break;
		case $CONSUME_TYPE:
			$sql 	= "SELECT * FROM consume WHERE journal_id='$transaction_id'";
			$query 	= mysql_query($sql);
			
			$asset_id = 0;
			$amount  = 0;
			$price	 = 0;
			while ($result = mysql_fetch_array($query))
			{
				$asset_id	 = $result['asset_id'];
				$amount		 = $result['amount'];
				$price	 	 = $result['price'];
			}
			
			$member = get_member_name_from_id($member_id);
			$asset  = get_asset_name_from_id($asset_id);
			?>
				<tr>
			 	<th scope="col" abbr="Date"><?php echo translate("uws:date") ?></th>
		        <th scope="col" abbr="Asset"><?php echo translate("uws:asset") ?></th>
		        <th scope="col" abbr="Description"><?php echo translate("uws:desc") ?></th>
			    <th scope="col" abbr="Who"><?php echo translate("uws:user") ?></th>
			    </tr>
			<?php
			$td  = "<td align=\"center\">";
			$tde = "</td>";
			
			echo $td . date('Y M d H:i:s',$timestamp) . $tde;
			echo $td . $asset . $tde;
			echo $td . $desc . $tde;
			echo $td . $member . $tde;
		?>
		</tr></table>
		<br><br>
		<table class="nicetable" cellspacing="0">
			<tr>	
		  	<th scope="col" abbr="Amount"><?php echo translate("uws:amount") ?></th>			   
			<th scope="col" abbr="Price"><?php echo translate("uws:price") ?></th>
			<th scope="col" abbr="Factor"><?php echo translate("uws:factor") ?></th>
			<th scope="col" abbr="Link"><?php echo translate("uws:link") ?></th>
			</tr>
		<?php
			echo $td . $amount . $tde;
			echo $td . $price . $tde;
			echo $td . $factor . $tde;
			echo $td . $link . $tde;
			break;				
	}

			?>
	</tr>
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
