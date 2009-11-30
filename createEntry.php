<?php
/*
 * UWS - Universal Wealth System
 * createEntry.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * When a user has performed a service, be it for the community or for a
 * specific user, a click on the correspondent action in the menu 
 * ("Create new service delivery") will lead to this file.
 * 
 * The user enters the details of the service delivery, like the type of
 * service (which needs to be created in createService.php first if not existing),
 * the description, the amount of time in minutes, and the factor. Updating
 * will calculate the service units according to the factor. The save button
 * is active only after updating. Saving is done in saveEntry.php
 * 
 * If the user does not select a receiver for the service, the service is regarded
 * as a community service and the user only gets credited his account. If a receiver
 * gets selected, the amount gets credited on the user's account and debited on the
 * receiver's account.
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
<title>Universal Wealth System UWS - <?php echo translate("uws:create_entry") ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
<script type="text/javascript">
        function saveStory() {
        		//clicking on save will submit the form to saveEntry.php
                document.story.action = "saveEntry.php";
                document.story.submit();
        }


</script>
</head>

<body>

<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:create_entry") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div>
				</div>
				<div class="content">
<?php
	//echo "isset: " .isset($_SESSION['uname']);
	$username 	= $_SESSION['uname'];
	$user_1		= "";
	$submit    		  = "disabled";
	
	//enable the submit button only if the form has been updated
	if (isset($_POST['update']))
	{
		$user_1 	= $_POST['user_1'];	
		$submit = "";
	}
	else {
		//the form has not been updated, thus no receiver selected yet
		$user_1		= $username;
	}
	
	$factor_1 		= $_POST['factor_1'];
	
	$user_2			= $_POST['user_2'];
	$contribution 	= $_POST['contribution'];
	$work_1 		= $_POST['work_1'];
	$desc 			= $_POST['desc'];
	$weighted_perf  = $factor_1 * $work_1;	
	
	$selected_receiver = null;
	$selected_service  = null;
	
	//the form has been updated and a receiver has been selected in 
	//the dropdown list
	if (isset($_POST['user_2']))
	{
		$selected_receiver = $_POST['user_2'];
	}
	
	//the form has been updated and a service has been selected in the
	//the dropdown list
	if (isset($_POST['contribution']))
	{
		$selected_service = $_POST['contribution'];
	}
	
	//get all services in the system and add them to an array
	$sql = "SELECT service FROM servicelist";
	$query = mysql_query($sql);
	$services_list = array();
	while ($result = mysql_fetch_array($query)) {
		array_push($services_list, current($result));
	}
	
	//get all members in the system and add them to an array
	$sql = "SELECT name FROM members";
	$query = mysql_query($sql);
	$user_list = array();
	while ($result = mysql_fetch_array($query)) {
		array_push($user_list, current($result));
	}
	//add an empty entry to the members dropdown list,
	//as there a service delivery might not need a member
	//(community service)
	array_unshift($user_list, "");
	
	
?>
	<form name="story" id="story" action="<?php echo $_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50" class="text"><?php echo translate("uws:user") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text"><input name="user_1" type="text" id="user_1" value="<?php echo $user_1?>" size="30" />
          </span></td>
        </tr>
        <tr>
          <td width="50">&nbsp;</td>
          <td width="5">&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:receiver") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text">
          <select name="user_2" size="1">
             <?php
             //fill in the members dropdown list from an array filled further up
             foreach ($user_list as $user) {             	
             	echo "<option";
             	//the form has been updated and a receiver had previously been selected.
             	if (! strcmp($user,$selected_receiver)) {
             		echo " selected>";
             	}
             	else {
             		echo ">";
             	} 
             	echo $user ."</option";
             }
             ?>
             </select>
        <!--  <input name="user_2" type="text" id="user_2" value="<?php echo $user_2?>" size="30" /> -->
          </span></td>
        </tr>
        <tr>
          <td width="50">&nbsp;</td>
          <td width="5">&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:service") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text">
             <!-- <input name="contribution" type="text" id="contribution" value="<?php echo $contribution?>" size="30" /> -->
             <select name="contribution" size="1">
             <?php
             //fill in the services dropdown list from an array further up
             foreach ($services_list as $service) {
             	echo "<option";
             	//the form had been updated and a service had previously been selected
             	if (! strcmp($service,$selected_service)) {
             		echo " selected>";
             	}
             	else {
             		echo ">";
             	} 
             	echo $service ."</option";
             }
             ?>
             </select>
          </span></td>
        </tr>
        <tr>
          <td width="50">&nbsp;</td>
          <td width="5">&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:desc") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text"><input name="desc" type="text" id="desc" value="<?php echo $desc ?>" size="30" />
          </span></td>
        </tr>
      </table>
      <p>&nbsp;</p>
      <table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <tr>
          <td width="65" class="text"><?php echo translate("uws:factor") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text">
            <input name="factor_1" type="text" id="factor_1" size="8" maxlength="8" value="<?php echo number_format($factor_1, 6, '.', '\'') ?>" />
          </span></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:lifetime") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text">
            <input name="work_1" type="text" id="work_1" size="8" maxlength="8" value="<?php echo $work_1 ?>" />
          </span></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:service_units") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text"><?php echo ($weighted_perf) ?></span></td>
        </tr>
        <tr>
          <td width="50" height="5"></td>
          <td height="5"></td>
          <td></td>
          <td height="5"></td>
        </tr>
        <tr bgcolor=" #666666">
          <td width="50" height="1"></td>
          <td height="5"></td>
          <td></td>
          <td height="1"></td>
        </tr>
        <tr>
          <td width="50" height="5"></td>
          <td height="5"></td>
          <td></td>
          <td height="5"></td>
        </tr>
        <tr>
          <td colspan="4"><div align="left" class="text">
            <input type="submit" name="update" id="update" value="<?php echo translate("uws:calc_srv_units") ?>" />
            <br />
            <br />
            <input type="button" name="button2" id="button2" value="<?php echo translate("uws:save_record") ?>" onclick="javascript:saveStory()" <?php echo $submit ?> />
            <br />
            <br />
          </div></td>
          <td width="80">&nbsp;</td>
        </tr>
      </table>
      <input type="hidden" name="weighted_perf" id="weighted_perf" value="<?php echo $weighted_perf ?>" />
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
