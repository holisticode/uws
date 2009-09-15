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
<title>Universal Wealth System UWS - <?php echo translate("uws:bid") ?> </title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />

<script type="text/javascript">

		var last_factor = 0;
        
        function validate_form() {
        	var price		= parseInt(document.forms['bid'].elements["price"].value);
        	var balance		= parseInt(document.forms['bid'].elements["balance"].value);		        	
        	var max_bid		= parseInt(document.forms['bid'].elements["my_share_physical"].value);
        	var bid 		= parseInt(document.forms['bid'].elements["my_bid_amount"].value);
        	if (bid == 0 || price == 0 || isNaN(bid) || isNaN(price))
        	{
        		var invalid= "<?php echo translate('uws:invalid_value')?>";
        		alert(invalid);
        		return false;
        	}
        	if (price > balance){
        		var invalid= "<?php echo translate('uws:price_above_balance')?>";
        		alert();
        		return false;
        	}
        	if (bid > max_bid)
        	{
        		var bid_too_high= "<?php echo translate('uws:bid_too_high')?>";
        		alert(bid_too_high);
        		alert("bid/max_bid: " + bid + "/" + max_bid);
        		return false;
        	}
        	else {
        		 	//check here if the factor has been changed
        			//if not, buy directly, otherwise an auction starts
        			//this means that the bid must be proportional to
        			//the physical amount which is one's part
        			//(= the factor remains the same)
        		
        		return true;
        	}
        }
        
        function update_fields() {
        	calc_fair_price();
        	//calc_factor();
        	
        }
        function calc_factor2() {
        	if (last_factor == 0)
        	{
        		var old_factor  = document.forms['bid'].elements["old_factor"].value;
        		last_factor		= old_factor;
        	}
        	var same_factor_price_per_unit = document.forms['bid'].elements["same_factor_price_per_unit"].value;
        	var new_price	= document.forms['bid'].elements["price"].value;
        	var reference	= document.forms['bid'].elements["my_share_physical"].value;
        	// var new_factor	= (new_price / (reference / 100)) / 100;
        	var new_factor	= (last_factor * reference) / new_price;
        	
        	document.forms['bid'].elements["my_factor"].value = new_factor; 
        }
        
        function calc_factor() {
        	var same_factor_price_per_unit = document.forms['bid'].elements["same_factor_price_per_unit"].value;
        	var new_price	= document.forms['bid'].elements["price"].value;
        	var bid_amount	= document.forms['bid'].elements["my_bid_amount"].value;
        	var new_price_per_unit = bid_amount / new_price;
        	
        	var new_factor	= same_factor_price_per_unit / new_price_per_unit;
        	
        	document.forms['bid'].elements["my_factor"].value = new_factor; 
        }
        
        function calc_fair_price() {
        	var bid 		= document.forms['bid'].elements["my_bid_amount"].value;
        	var total_cost 	= document.forms['bid'].elements["total_cost"].value;
        	var total_units = document.forms['bid'].elements["physical"].value;
        	var unit_price	= total_cost / total_units;
        	var bid_price	= bid * unit_price;
        	
        	document.forms['bid'].elements["price"].value = bid_price;
        }


</script>
</head>

<body>

<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

<?php 

	include "header.php";
		
	$member_id 	= $_SESSION['member_id'];

	$asset_id		= $_GET['unitID'];
	$inventory		= 0;
	$balance		= 0;
	$myshare		= 0;
	$my_share_price	= 0;
	
	//TODO: if unitID not set, first offer to choose unit
	$sql = "SELECT unit,inventory,physical,factor from assetlist where asset_id='" . $unitID . "'";
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) 
   	{
   		$inventory 	= $result['inventory'];
   		$unit		= $result['asset'];
   		$physical	= $result['physical'];
   		$factor		= $result['last_factor'];
   	}
   	
   	$sql = "SELECT balance from members where member_id='" . $member_id . "'";
   	$query = mysql_query($sql);
   	while ($result = mysql_fetch_array($query)) 
   	{
   		$balance = $result['balance'];
   	}
   	   	
   	$sql   				= "SELECT total_services from totals";
   	$query 				= mysql_query($sql);
   	$total_services		= mysql_fetch_row($query);
   	
   	$sql   				= "SELECT total_inventory from totals";
   	$query 				= mysql_query($sql);
   	$total_inventory	= mysql_fetch_row($query);
   	
   	$total_cost 		= $inventory * $total_services[0] / $total_inventory[0];
   	$ratio				= $total_cost / $balance;
   	$my_share_physical	= $physical / $ratio;
   	$my_share_inventory = $inventory / $ratio;
   	
   	if ($my_share_physical > $physical)
   	{
   		$my_share_physical = $physical;
   		$my_share_inventory = $inventory;
   	}
   	
   	$price = ($total_cost / $inventory) * $my_share_inventory;
   	$same_factor_price_per_unit = $physical / $total_cost;
?>
					<h3><?php echo translate("uws:bid_asset") . ": " . $unit ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div>
				</div>
				<div class="content">


	<form name="bid" id="story" action="doConsume.php" method="post" enctype="multipart/form-data">
<table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" class="text"><?php echo translate("uws:inventory")?>:</td>
          <td width="10">&nbsp;</td>
          <td><span class="text">
          		<input name="inventory" type="text" id="inventory" value="<?php echo $inventory?>" size="40" readonly/>
              </span>
          </td>
        </tr>
         <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
        </tr>
        <tr>
          <td width="150" class="text"><?php echo translate("uws:physical")?>:</td>
          <td width="5">&nbsp;</td>
          <td><span class="text"><input name="physical" type="text" id="physical" value="<?php echo $physical ?>" size="40" readonly/>
          </span></td>
        </tr>
         <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        <tr>
          <td width="150" class="text"><?php echo translate("uws:factor") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="factor" type="text" id="factor" value="<?php echo $factor ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>        
        <tr>
          <td width="150" class="text"><?php echo translate("uws:total_cost") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="total_cost" type="text" id="total_cost" value="<?php echo $total_cost ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        <tr>
          <td width="150" class="text"><?php echo translate("uws:my_share_physical") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="my_share_physical" type="text" id="my_share_physical" value="<?php echo $my_share_physical ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
         <tr>
          <td width="150" class="text"><?php echo translate("uws:my_share_inventory") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="my_share_inventory" type="text" id="my_share_inventory" value="<?php echo $my_share_inventory ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        <tr bgcolor=" #666666">
          <td width="150" height="1"></td>
          <td height="5"></td>
          <td></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>          
        </tr>
        
        <tr>
          <td width="150" class="text"><?php echo translate("uws:balance") ?>:</td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="balance" type="text" id="balance" value="<?php echo $balance?>" size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        
        <tr>
          <td width="150" class="text"><?php echo translate("uws:my_bid_amount") ?></td>
          <td width="10">&nbsp;</td>
          <td>
          	<span class="text">
          		<input name="my_bid_amount" type="text" id="my_bid_amount" value="<?php echo $my_share_physical ?>" onchange=update_fields() />
          	</span>
          </td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        
        <tr>
          <td width="150" class="text"><?php echo translate("uws:price") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="price" type="text" id="price" value="<?php echo $price ?>"  size="40" readonly /> <!--onchange=calc_factor() -->
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        <tr>
          <td width="150" class="text"><?php echo translate("uws:my_factor") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="my_factor" type="text" id="my_factor" value="<?php echo $factor ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        
        <tr>
          <td colspan="3"><div align="left" class="text">
            <input type="submit" name="place_bid" id="place_bid" value="<?php echo translate("uws:submit_bid") ?>" onclick="return validate_form()" />
            <br />
            <br />
<!--           
 <input type="button" name="button2" id="button2" value="<?php echo translate("uws:save_record") ?>" onclick="javascript:saveStory()" />
-->
            <br />
            <br />
          </div></td>
        </tr>
      </table>
	  <input type="hidden" name="asset_id" id="asset_id" value="<?php echo $asset_id ?>" />
      <input type="hidden" name="unit" id="unit" value="<?php echo $unit ?>" />
      <input type="hidden" name="old_factor" id="unit" value="<?php echo $factor ?>" />
      <input type="hidden" name="same_factor_price_per_unit" id="same_factor_price_per_unit" value="<?php echo $same_factor_price_per_unit ?>" />
	
  </form>
    
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
