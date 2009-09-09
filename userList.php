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


<script language="javascript">AC_FL_RunContent = 0;</script>
<script language="javascript"> DetectFlashVer = 0; </script>
<script src="charts/AC_RunActiveContent.js" language="javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
var requiredMajorVersion = 9;
var requiredMinorVersion = 0;
var requiredRevision = 45;
-->
</script>
<BODY bgcolor="#FFFFFF">

<noscript>
	<P>This content requires JavaScript.</P>
</noscript>









</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:reg_users") ?></h3>
					<div class="date"><?php echo date('d F Y') ?></div> <!-- div date -->
	
				</div><!-- div header -->
				<div class="content">
	<table class="nicetable" cellspacing="0">
	<tr>

       <th scope="col" abbr="User"><?php echo translate("uws:user") ?></th>
       <th scope="col" abbr="Services"><?php echo translate("uws:services") ?></th>
       <th scope="col" abbr="%"><?php echo translate("uws:srv_share") ?></th>
   	</tr>

<?php
	$tdnorm = '<td class="spec">';
   	$tdalt  = '<td class="specalt">';
   	$td = $tdnorm;
   	
   	$users = array();
   	$perc_chart = array();
   	$total_balance = 0;
   	$total_perc    = 0;

   	$sql = ("SELECT contributor,balance from uwscontributors");
   	$query = mysql_query($sql);

   	while ($result = mysql_fetch_array($query)) 
   	{   		   		
   		$user = $result['contributor'];   	   		
        $balance = $result['balance'];
        $users[$user] = $balance;
        
        $total_balance += $balance;        
   	}

   	$i=0;
   	
   //	while ($user = current($users))
    //reset($users);
   	foreach ($users as $user => $balance)
   	{
		if ($i%2 != 0) 
		{
			$td = $tdnorm;
		} else 
		{
			$td = $tdalt;
		}
        echo "<tr>";
        //$user = key($users);
        //$balance = $users[$user];
        $perc = 0;         
		echo $td . "<a href=\"user.php?user=$user\">$user"  . "</td>";
		echo $td . $balance . "</td>";
		if ($total_balance != 0)
		{
			$perc = ($balance/$total_balance) * 100;
			$perc = round($perc,3);
			$perc_chart[$user] = $perc;
		}
		
		$total_perc += $perc;
		if ($perc == 0){
			$perc = "< 0.001";
		}
		echo $td . $perc . "</td>";
		
        echo "</tr>";
        $i++;
        //next($users);	
   }
   
   create_chart_xml($perc_chart);
   
   echo "<tr>";
   echo "<th scope=\"col\" abbr=\"\">" . translate("uws:total") ."</th>";
   echo "<th scope=\"col\" abbr=\"Total\">" . $total_balance ."</th>";
   echo "<th scope=\"col\" abbr=\"\">" . $total_perc . "</th>";
   echo "</tr>"; 
   
   function create_chart_xml($perc_chart)
   {
   	$fh = fopen("users.xml", "w");
   	fwrite($fh,"<chart>\n");
   	fwrite($fh,"\t<chart_data>\n");
   	fwrite($fh,"\t\t<row>\n");
   	fwrite($fh,"\t\t\t<null/>\n");
   	
   	$userlist = array_keys($perc_chart);
   	foreach($userlist as $username)
   	{
   		fwrite($fh,"\t\t\t<string>" . $username . "</string>\n");
   	}
   	
   	fwrite($fh,"\t\t</row>\n");
   	fwrite($fh,"\t\t<row>\n");   	
   	fwrite($fh,"\t\t\t<string>% of Total Services</string>\n");
   	
   	$uservalues = array_values($perc_chart);
   	foreach($uservalues as $perc)
   	{	
   		fwrite($fh,"\t\t\t<number>" . $perc . "</number>\n");
   		next($perc_chart);
   	}
   	fwrite($fh,"\t\t</row>\n");
   		
   	fwrite($fh,"\t</chart_data>\n");
    fwrite($fh,"\t<chart_type>Pie");
    fwrite($fh,"</chart_type>\n");
    fwrite($fh,"</chart>\n");
    fclose($fh);
   }
?>
  </table><br><br>
				</div>
				<div class="content"> <!--chart -->
				<script src="perc_chart.js" type="text/javascript"></script>
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
