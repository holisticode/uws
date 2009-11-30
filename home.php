<?php
/*
 * UWS - Universal Wealth System
 * home.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * This file contains the home page, to which the user arrives when
 * he logs in. It displays user related information to his account:
 * balance, totals and all own transactions.
 * 
 * TODO: It uses cookies to check the login status. However, this mechanism is
 * currently not implemented in all other php files, which is a security 
 * leak.
 * TODO: The login mechanism should be re-worked and harmonized.
 * An existing framework/platform (drupal, etc.) could be used to integrate UWS,
 * offering user management, security, etc.
 */	
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
		//If cookie not set, check if the username is in the same session.
		//This is for compatibility with previous versions.
		//As noted, the login mechanism should be re-worked and harmonized.
			
		$username = $_SESSION['uname'];		
	}
	else 
	{
		//the user is not logged in or the session expired. Got the the index page
		//and require new login
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
	//Get the user id
	$member_id = $_SESSION['member_id'];
	//Get the user balance
	$sql = ("SELECT balance from members where member_id = '$member_id'");
   	$query = mysql_query($sql);
   	$userbalance = 0;
   	while ($result = mysql_fetch_array($query)) 
   	{
       	$userbalance = $result['balance'];
   	}
   	//Get the total services
   	$totalServices = 0;
   	$sql = ("SELECT total_services from totals");
   	$query = mysql_query($sql);
   	$result = mysql_fetch_row($query);
    $totalServices = $result[0];
   	
	//Get the total inventory
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
	//Display the user balance
	echo translate("uws:balance");
   	echo '</th><td class="spec"> ' . $userbalance . "</td></tr>";
   	echo "<tr>";
   	//Display the total services in the system
	echo $rowspecalt  . translate("uws:total-srv") . '</th><td class="spec">' . $totalServices . "</td>";
   	$share = 0;
   	if ($totalServices != 0) {
        	$share = (100/$totalServices)*$userbalance;
   	}
   	//Display the user's share of the total services in percent'
   	echo "<tr>" . $rowspec . translate("uws:share") . "</th>";
   	echo '<td class="spec">' . (" " . number_format($share,2));
   	echo " % " . "</td></tr>";
   	//Display the total inventory in the system
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
	//Now display the table with all the transactions for this account
	//In the current db design, first we need to retrieve all entries
	//for this user from the transaction table. Every transaction has a
	//journal_id as a counter and a transaction_id, which is the foreign
	//key into the appropriate table, depending on transaction type this
	//is the table service, inventorize or consume, where the details
	//of the transaction are stored, as every transaction type has different
	//fields.
	
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
   	
   	//get the type codes and its description from the database
   	while ($result = mysql_fetch_array($query))
   	{
   		$key   = $result['type_code'];
   		$value = $result['type_desc'];
   		$ta_types[$key] = $value;
   	}   	
   	
   	//get transaction_id and balance after each transaction from the service table
   	//and fill into an array for later use, as this info comes from the service table
   	//while the iteration further down for the GUI is over the transaction table result
   	$sql 	= "SELECT transaction_id,receiver_balance FROM service WHERE receiver_id='$member_id'";
   	$query 	= mysql_query($sql);
   	$rarray = array();
   	while($result = mysql_fetch_array($query))
   	{
   		$rarray[$result[0]] = $result[1];
   	}
   	
   	//get all transactions for this user from the transaction table
   	$list = "";
   	$sql = "SELECT * FROM transactions WHERE member_id = '$member_id'";
   	if (count($rarray) > 0) 
   	{   	
   		$list = implode(",",array_keys($rarray));
   		$sql  = $sql." OR journal_id IN($list)";
   	} 
   	//echo "SQL: ".$sql;
   	$query = mysql_query($sql);
   	
   	//for each transaction:
   	while ($result = mysql_fetch_array($query))
   	{
		if ($cnt%2 == 0) {
			$td = $tdnorm;
		} else {
			$td = $tdalt;
		}
		//get the type code
		$type_code 	= $result['transaction_type'];
		//get the description of the type
		$ta_type	= $ta_types[$type_code];
		//get the transaction_id to retrieve the details
		$transaction_id = $result['transaction_id'];
		//get the counter
		$ta_id 		= $result['journal_id'];
		//get the details
		$transaction= get_transaction($type_code,$ta_id);
				
	    echo "<tr>";
	    //get date and format it
        $date 		= $result['tstamp'];
        echo $td . date('Y M d H:i:s',$date) . $tde;
        //echo "ta_type: ".$ta_type;
        //translate the description of the transaction type (Service, Inventorization, Consumation)
        echo $td . translate($ta_type) . $tde;
        //display the transaction label
        echo $td . $transaction . $tde;
        //display its description
        $desc = $result['description'];
        echo $td . $desc . $tde;
        //get the balance after this transaction (stored in the db...)
        //further up an array has been built up with the mapping transaction id - balance
        $balance = $result ['balance'];
        if (array_key_exists($ta_id,$rarray))
        {        	
        	$balance = $rarray[$ta_id];
        }
        //display the balance
        echo $td . $balance . $tde; 
        echo $td;
        //display a clickable icon; clicking displays transaction details
        echo '<a href="viewTransaction.php?taID=' . $ta_id .
			 '&taType='.$type_code .'&detID='. $transaction_id .
			 '&userID='.$member_id. 
				'"><img src="images/bid.png" border="0" alt="' .
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
