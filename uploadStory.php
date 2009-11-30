<?php
/*
 * UWS - Universal Wealth System
 * uploadStory.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * Displays a form where the user can browse to the UNP standardized chat protocol
 * in order to be uploaded and its values calculated (the form uploads to doUpload.php)
 */
	session_start();
	//include "globals.php";
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
<title>Universal Wealth System UWS - Chat upload</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="stylesheet" type="text/css" href="default.css" />
</head>
<body>
<div id="outer">

	<div id="upbg"></div>

	<div id="inner">

		<?php include "header.php" ?>
					<h3><?php echo translate("uws:chat_upload") ?></h3>
					<div class="date">
<?php
	echo date('d F Y') ?></div> <!-- div date -->
				</div><!-- div header -->
				<div class="content">
		
<form action="doUpload.php" method="post" enctype="multipart/form-data">
  <label>
      <span class="text"><?php echo translate("uws:story_upload") ?><br />
      </span><br />
  <input name="toProcess" type="file" id="toProcess" size="45" />
  </label>
  <br />
  <br />
  
  <label>  </label>
  <label><br />
  </label>
      
      <p>
        <label>
        <input type="submit" value="<?php echo translate("uws:upload") ?>" />
        </label>
      </p>
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
