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
<title>Universal Wealth System UWS</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:error_title",$lang) ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">
	<?php 
		$msg = $_GET['error'];
		echo translate("uws:error") ."<br> ". $msg . "<br><br>" ?>
<!--
	<table class="nicetable" cellspacing="0">
	<tr>

       <th scope="col" abbr="Asset">Asset</th>
       <th scope="col" abbr="Value">Value</th>
   	</tr>
<?php
/*
	include "config.php";
	session_start();

   	$sql = ("SELECT unit,inventory from units");
   	$query = mysql_query($sql);
   	$cnt=0;
   	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td = $tdnorm;
   	while ($result = mysql_fetch_array($query)) {
		if ($cnt%2 == 0) {
			$td = $tdnorm;
		} else {
			$td = $tdalt;
		}
        	echo "<tr>";
                $unit= $result['unit'];
                $value= $result['inventory'];
		echo $td . $unit . "</td>";
		echo $td . $inventory . "</td>";
        	echo "</tr>";
	$cnt++;
   }
*/
?>
  </table>
-->
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
