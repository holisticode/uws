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
	
	$sql   	= ("SELECT total_services from totals;");
   	$query 	= mysql_query($sql);
   	$srvsum	= mysql_fetch_row($query);
   	
   	$sql 	= ("SELECT total_inventory from totals;");
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
       <th scope="col" abbr="view"></th>
   	</tr>

<?php
	include "config.php";
	session_start();

	$sql = "SELECT * FROM transaction_type";
   	$query = mysql_query($sql);
   	$ta_types = array();
   	
	while ($result = mysql_fetch_array($query))
   	{
   		$key   = $result['type_code'];
   		$value = $result['type_desc'];
   		$ta_types[$key] = $value;
   	}   	

	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td = $tdnorm;

   	$sql = "SELECT * from transactions";
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
        echo $td . get_transaction_code($type_code) . $tde;
        echo $td . get_member_name_from_id($result['member_id']) . $tde;
        echo $td . $transaction . $tde;
        echo $td . get_ra($type_code, $ta_id) . $tde;
        echo $td . $result['factor'] . $tde;
        echo $td . $result['link'] . $tde;
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
   
   
function get_transaction_code($type_code)
{
	$code = "";
	switch ($type_code) {
		case 1: 
			$code = "L";
			break;
		case 2:
			$code = "I";
			break;
		case 3:
			$code = "K";
			break;
	}
	return $code;
}

function get_ra($type_code, $ta_id)
{
	$ra = "";
	$sql = "";
	switch ($type_code) {
		case 1:
			$sql 	= "SELECT lifetime FROM service WHERE transaction_id='$ta_id'";
			break;
		case 2:
			$sql 	= "SELECT amount_physical FROM inventorize WHERE transaction_id='$ta_id'";
			break;
		case 3:
			$sql 	= "SELECT amount FROM consume WHERE transaction_id='$ta_id'";
			break;
	}
	$query 	= mysql_query($sql);
	$result = mysql_fetch_row($query);
	$ra = $result[0];
	return $ra;
}
   
//function get_member_name_from_id($member_id) 
//{
//	global $found_members;	
//	
//	if (array_key_exists($member_id, $found_members))
//	{
//		return $found_members [$member_id];
//	}
//	$sql 	= "SELECT name FROM members WHERE member_id='$member_id'";
//	$query 	= mysql_query($sql);
//	$set 	= mysql_fetch_row($query);
//	$member = $set[0];
//	
//	$found_members[$member_id] = $member;
//	return $member;
//}
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
