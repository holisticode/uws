<?php
class CssParser {
	
function parse() {
	//read css
	$userColors = array();
	$myCssLines = file("@.css");
	$userName   = '';

	for ($i=0; $i<count($myCssLines); $i++) {
		$cssLine = $myCssLines[$i];
		if (substr($cssLine, 0, 1) == ".") {
			$cssWords = explode("\t", $cssLine);
			$userName = substr($cssWords[0], 1, strlen($cssWords[0])-1);
			$userName = str_replace(" ", "", $userName);
			$colorDef = $cssWords[1];
			$pos = strpos($colorDef, "color:");
			$color = substr($colorDef, $pos+6, 7);

			$userColors[$userName] = $color;
			$userColors[strtolower($userName)] = $color;
		}
	}
	return $userColors;

} //function
} //class
?>
