<?php	
	session_start();
	include "globals.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--

	terrafirma1.0 by nodethirtythree design
	http://www.nodethirtythree.com

-->
<html>
<head>


<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Universal Wealth System UWS</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
<script type="text/javascript">
	function saveStory() {
		document.story.action = "saveStory.php";
		document.story.submit();
	}
	


</script>

<style type="text/css">
<!--
.nav {
	position: fixed;
	width: 280px;
	z-index: 2;
}
body {
	margin-left: 20px;
	margin-top: 20px;
	margin-right: 20px;
	margin-bottom: 20px;
}
.scroll {
	position: absolute;
	left: 20px;
	top: 20px;
	right: 20px;
	bottom: 20px;
	z-index: 1;
}
-->
</style>
</head>

<?php
	$storyLink 	= $_GET['storyLink'];
	
	$users 			= array();
	$user_values 	= array();
	$current_user	= null;
	$factor_key		= "factor_";
	$time_key		= "time_";
	$user_key		= "user_";
	$weighted_key	= "weighted_";
	
	$default_factor	= 1.0;
	
	foreach($_GET as $key=>$value)
	{
		//print "KEY: ".$key." - VALUE: ".$value;
		if (strncmp($user_key,$key, 5) === 0)
		{
			//print "userkey<br>";
			$users[$value] 	= array();			
			$current_user	= $value;						
		}
		else if (strncmp($factor_key,$key,7) == 0)
		{
			//print "factorkey";
			$users[$current_user][$factor_key] = $value;
		}
		else if (strncmp($time_key,$key,5) === 0)
		{
			//print "timekey";
			$users[$current_user][$time_key] 	= $value;
			if (is_null($users[$current_user][$factor_key]))
			{			
				$users[$current_user][$factor_key] 	= $default_factor;
			}						 	
		}		
		else if (strncmp($weighted_key,$key,9) == 0)
		{
			//print "factorkey";
			$users[$current_user][$weighted_key] = $value;
		}
	}	
	//print_r($users);
?>

<body>

<div id="outer">

	<div id="upbg"></div>
	<div id="inner">
		<?php include "header.php" ?>
					<h3><?php echo translate("uws:chat_details") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div>
				</div>
				<div class="content">



	<form name="story" id="story" action="previewStory.php" method="get" enctype="multipart/form-data">
       <table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50" class="text"><?php echo translate("uws:service") ?>:</td>
          <td width="25">&nbsp;</td>
          <td><span class="text"></span><?php echo translate("uws:chat")?></td>          
        </tr>
        <tr>
          <td width="50">&nbsp;</td>
          <td width="25">&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:link")?></td>
          <td width="25">&nbsp;</td>
          <td><span class="text"><input name="storyLink" type="text" id="storyLink" value="<?php echo $storyLink ?>" size="40" />
          </span></td>       
        </tr>
      </table>
      <p>&nbsp;</p>
      <p>&nbsp;</p>

      <table class="formtable" width="470" cellspacing="0" cellpadding="0">
<?php
		$count = 1;
		foreach($users as $key=>$user)
		{
?>
        <tr>
        <!--
          <td width="20" bgcolor="<?php echo $color_1 ?>">&nbsp;</td>        
          <td width="5">&nbsp;</td>
        -->
          <td width="100"><span class="text"><?php echo $key ?></span></td>
          <input type="hidden" name="<?php echo $user_key.$count ?>" id="<?php echo $user_key.$count ?>" value="<?php echo $key ?>" />          
          <td width="120" class="text"><?php echo translate("uws:factor")?></td>
          <td width="40">            
            <input name="<?php echo $factor_key.$count ?>" type="text" id="<?php echo $factor_key.$count ?>" size="8" maxlength="8" value="<?php echo number_format($user[$factor_key], 6, '.', '\'') ?>" />
          </td>
        </tr>
        <tr>
        <!--
          <td width="20">&nbsp;</td>
          <td width="5">&nbsp;</td>
        -->
          <td width="100"></td>
          <td width="120" class="text"><?php echo translate("uws:lifetime")?></td>
          <td width="40"><span class="text"><?php echo number_format($user[$time_key], 6, '.', '\'') ?></span></td>
          <input name="<?php echo $time_key.$count ?>" type="hidden" id="<?php echo $time_key.$count ?>" size="8" maxlength="8" value="<?php echo $user[$time_key] ?>" />
        </tr>
        <tr>
        <!--
          <td width="20">&nbsp;</td>
          <td width="5">&nbsp;</td>
        -->
          <td width="100"></td>
          <td width="120" class="text"><?php echo translate("uws:service_units")?></td>
          <td width="40"><span class="text"><?php echo number_format($user[$time_key]*$user[$factor_key], 6, '.', '\'') ?></span></td>
          <input name="<?php echo $weighted_key.$count ?>" type="hidden" id="<?php echo $weighted_key.$counts ?>" size="8" maxlength="8" value="<?php echo ($user[$time_key])*($user[$factor_key]) ?>" />
        </tr>
<?php
		$count++;
		}
?>
		<tr bgcolor=" #666666">
          <td width="50" height="1"></td>
          <td height="5"></td>
          <td></td>
          <td height="1"></td>
        </tr>
        <tr>
        <!--
          <td width="20">&nbsp;</td>
          <td width="5">&nbsp;</td>
        -->
          <td colspan="4"><div align="left" class="text">
            <input type="submit" name="button" id="button" style="width:150px" value="<?php echo translate("uws:update_story")?>" />
            <br />
            <input type="button" name="button2" id="button2" style="width:150px" value="<?php echo translate("uws:save_record")?>" onclick="javascript:saveStory()" />
            <br />
            <br />
          </div></td>
          <td width="80">&nbsp;</td>
        </tr>
      </table>
      <input type="hidden" name="filename" id="filename" value="<?php echo $myFile ?>" />      
      <input type="hidden" name="entryDate" id="entryDate" value="<?php echo $entryDate ?>" />
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
