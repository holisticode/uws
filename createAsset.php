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
<title>Universal Wealth System UWS - <?php echo translate("uws:create_asset") ?></title>
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
					<h3><?php echo translate("uws:create_asset") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div>
				</div>
				<div class="content">
<?php
	//echo "isset: " .isset($_SESSION['uname']);	
	
	$asset_list = array();
	$asset = "";
	$desc 	 = "";
	
	if (isset($_POST['save_asset']))
	{
		$asset 	= $_POST['asset'];	
		$desc 	= $_POST['desc'];
		$factor = $_POST['factor'];
		
		$sql = "SELECT asset FROM assetlist";
		$query = mysql_query($sql);
		//$services_list = array();
		while ($result = mysql_fetch_array($query)) 
		{
			array_push($asset_list, current($result));
		}
	}
		
	
?>
	<form name="asset" id="asset" action="<?php echo $_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <?php
        if (in_array($asset, $asset_list))
		{
			echo $asset . translate("uws:existing");	
		} 
		else {
			if (isset($_POST['save_asset']))
			{
				$time = time();
				$sql = "INSERT INTO assetlist VALUES ('','$time','$asset','0','0','$factor','$desc')";
				$query = mysql_query($sql);
				if ($query) 
				{
					echo translate("uws:asset_insert_ok");
				} else
				{
					echo translate("uws:asset_insert_not_ok")." ". mysql_error();
				}
				
			}
		}
        ?>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:asset") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text">
             <input name="asset" type="text" id="asset" value="<?php echo $asset?>" size="30" /> 
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
        <tr>
          <td width="50">&nbsp;</td>
          <td width="5">&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td width="50" class="text"><?php echo translate("uws:factor") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text"><input name="factor" type="text" id="factor" value="<?php echo $factor ?>" size="30" />
          </span></td>
        </tr>
    
      <p>&nbsp;</p>
        <tr>
          <td colspan="4"><div align="left" class="text">
            <input type="submit" name="save_asset" id="save_asset" value="<?php echo translate("uws:save") ?>" />
            <br />    
          </div></td>
          <td width="80">&nbsp;</td>
        </tr>
      </table>
  </form><br /><br />
    
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
