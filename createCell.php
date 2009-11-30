<?php
/*
 * UWS - Universal Wealth System
 * createCell.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * UWS evolves around the concept of a cell. Everyone can start a new cell.
 * To start a new cell this file will be called. Especially when creating a new
 * site and no users are registered yet, registering will redirect to this page first.
 * 
 * The page just displays an entry field for the cell's name resp. identification.
 * For Willi's first UWS system, the cell is automatically created in the impord_db.php
 * on import of the original entries from www.ressort.info/de/uws.htm. 
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
<title>Universal Wealth System UWS - <?php echo translate("uws:create_cell") ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />

</head>

<body>

<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:create_cell") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div>
				</div>
				<div class="content">
<?php
	
	
	if (isset($_POST['save_cell']))
	{
		$cell_name 	= $_POST['cell_name'];
		
		$sql = "INSERT INTO network VALUES ('','$cell_name','')";
		$DEFAULT_CELL_ID = do_query($sql);
        $INITIALIZED = 1;
		echo translate("uws:cell_insert_ok");
	}
	else{
		echo "<p>". translate('uws:first_user')."\n<br></p></br>";
	}	
        ?>
        <form name="cell" id="cell" action="<?php echo $_SERVER['PHP_SELF']?>" method="post" enctype="multipart/form-data">
<table class="formtable" width="470" cellspacing="0" cellpadding="0">
        <tr>
          <td width="50" class="text"><?php echo translate("uws:cell") ?></td>
          <td width="5">&nbsp;</td>
          <td><span class="text">
             <input name="cell_name" type="text" id="cell_name" value="UWS" size="30" /> 
          </span></td>
        </tr>
      <p>&nbsp;</p>
        <tr>
          <td colspan="4"><div align="left" class="text">
            <input type="submit" name="save_cell" id="save_cell" value="<?php echo translate("uws:save") ?>" />
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
