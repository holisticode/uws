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
<title>Universal Wealth System UWS - <?php echo translate("uws:create_entry") ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
<script type="text/javascript">
        function saveStory() {
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
	
	if (isset($_POST['update']))
	{
		$user_1 	= $_POST['user_1'];	
	}
	else {
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
	
	if (isset($_POST['user_2']))
	{
		$selected_receiver = $_POST['user_2'];
	}
	if (isset($_POST['contribution']))
	{
		$selected_service = $_POST['contribution'];
	}
	
	
	$sql = "SELECT service FROM servicelist";
	$query = mysql_query($sql);
	$services_list = array();
	while ($result = mysql_fetch_array($query)) {
		array_push($services_list, current($result));
	}
	
	$sql = "SELECT name FROM members";
	$query = mysql_query($sql);
	$user_list = array();
	while ($result = mysql_fetch_array($query)) {
		array_push($user_list, current($result));
	}
	
	array_unshift($user_list, "");
	
	
?>
	<form name="story" id="story" action="createEntry.php" method="post" enctype="multipart/form-data">
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
             foreach ($user_list as $user) {             	
             	echo "<option";
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
             foreach ($services_list as $service) {
             	echo "<option";
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
            <input type="button" name="button2" id="button2" value="<?php echo translate("uws:save_record") ?>" onclick="javascript:saveStory()" />
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
