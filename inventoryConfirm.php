<?php
/*
 * UWS - Universal Wealth System
 * inventoryConfirm.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * When an inventorization takes place, a number of values get changed
 * in the database: the inventory totals (physical and inventory units),
 * but also the member's balance get changed with an amount implicitely
 * calculated according to the UWS formula in service units.
 * 
 * In order for the user to see what happens, a short summary of changed
 * values is shown. Currently, only if the inventorization is not a donation 
 * this summary is shown. 
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
<title>Universal Wealth System UWS - <?php echo translate("uws:confirm_inventory") ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:confirm_inventory") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">
				
<?php
	$asset 			= $_GET['asset'];
	$user 			= $_GET['user'];
	$donate			= $_GET['donate'];
	$physical		= $_GET['physical'];
	$inventory 		= $_GET['inventory'];
	$balance		= $_GET['balance'];
	$old_balance	= $_GET['old_balance'];
	$su				= $_GET['su'];

	echo translate("uws:confirm_text");
?>	
		
<table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <tr>
          <td width="150" class="text"><?php echo translate("uws:asset")?>:</td>
          <td width="10">&nbsp;</td>
          <td><span class="text">
          		<input name="asset" type="text" id="asset" value="<?php echo $asset?>" size="40" readonly/>
              </span>
          </td>
        </tr>
         <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
        </tr>
        <tr>
          <td width="150" class="text"><?php echo translate("uws:user") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="user" type="text" id="user" value="<?php echo $user ?>"  size="40" readonly/>
          </span></td>
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
          <td width="150" class="text"><?php echo translate("uws:inventory") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="inventory" type="text" id="inventory" value="<?php echo $inventory ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        <tr>
          <td width="150" class="text"><?php echo translate("uws:old_balance") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="old_balance" type="text" id="old_balance" value="<?php echo $old_balance ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
         <tr>
          <td width="150" class="text"><?php echo translate("uws:service_units") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="service_units" type="text" id="service_units" value="<?php echo $su ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        
        <tr>
          <td width="150" class="text"><?php echo translate("uws:balance") ?></td>
          <td width="10">&nbsp;</td>
          <td><span class="text"><input name="balance" type="text" id="balance" value="<?php echo $balance ?>"  size="40" readonly/>
          </span></td>
        </tr>
        <tr>
          <td width="150" height="5"></td>
          <td height="5"></td>
          <td></td>
          
        </tr>
        
       
      </table>
	  
	<br><br>
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
