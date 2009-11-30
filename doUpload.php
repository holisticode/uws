<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">


<?php
/*
 * UWS - Universal Wealth System
 * doUpload.php
 * GPL license
 * author: Fabio Barone
 * date: 30. Nov. 2009
 * 
 * When a chat protocol is being uploaded, this file is called
 */
 
    //function to sanitize filenames, as chat protocols may contain dodgy
    //characters
	function sanitize_filename($filename, $forceextension="")
	{
		/*
		1. Remove leading and trailing dots
		2. Remove dodgy characters from filename, including spaces and dots except last.
		3. Force extension if specified
		*/
	
		$defaultfilename = "none";
		$dodgychars = "[^0-9a-zA-z\/()_-]"; // allow only alphanumeric, underscore, parentheses and hyphen
	
		$filename = preg_replace("/^[.]*/","",$filename); // lose any leading dots
		$filename = preg_replace("/[.]*$/","",$filename); // lose any trailing dots
		$filename = $filename?$filename:$defaultfilename; // if filename is blank, provide default
	
		$lastdotpos=strrpos($filename, "."); // save last dot position
		$filename = preg_replace("/$dodgychars/","_",$filename); // replace dodgy characters
		$afterdot = "";
		if ($lastdotpos !== false) { // Split into name and extension, if any.
		$beforedot = substr($filename, 0, $lastdotpos);
		if ($lastdotpos < (strlen($filename) - 1))
			$afterdot = substr($filename, $lastdotpos + 1);
		}
		else // no extension
		$beforedot = $filename;
	
		if ($forceextension)
			$filename = $beforedot . "." . $forceextension;
		elseif ($afterdot)
			$filename = $beforedot . "." . $afterdot;
		else
		$filename = $beforedot;
	
		return $filename;
	}

	$target = "";
 	$filesdir = "files";
	
	//get the uploaded file
	$uploadFile = $_FILES['toProcess']['tmp_name'];
	$filename   = $_FILES['toProcess']['name'];
	
	if (is_uploaded_file($uploadFile)) 
	{
		//parse the file
		include_once "UNP_Parser.php";
		$htmlfile = file_get_contents($uploadFile);
		//print "Filename: " . $uploadFile . "\n<br>";		
		$myFile = sanitize_filename($filename);
		//print "myFile: " . $myFile . "\n<br>";
		$unp_parser = new UNP_Parser();
		$users = $unp_parser->parse($htmlfile);
		
		//the file has been parsed and the values calculated
		//to see the chat values, go to previewStory.php
		$target = "previewStory.php?storyLink=".$myFile."&".$users;
		
		//print $target;
	}	
		
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>UWS</title>
<link href="basic.css" rel="stylesheet" type="text/css" />

<script language="JavaScript">
<!-- Begin
	document.location.href = "<?php echo $target ?>";
	
	
 // End -->
</script>
</head>

<body>
</body>
</html>
