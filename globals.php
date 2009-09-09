<?php
$defaultlang = "de";
//$_SESSION['language'] = $defaultlang;

if (! isset($_SESSION['language']) ) {
	$_SESSION['language'] = $defaultlang;
}

	
$messages = array();

function add_translation($lang, $array) {
	global $messages;

	if (! isset($messages[$lang]) ) {
		$messages[$lang] = $array;
	}
}

function translate($s) {
	$lang = $_SESSION['language'];
	global $messages; 

	if (isset($messages[$lang][$s]) )   {
		return $messages[$lang][$s];
	} else {
		error_log("UWS L10N error. Language: $lang, message: $s");
	}
} 
	
function include_all_once ($pattern) {
	foreach (glob($pattern) as $file) {
		$ok = include $file;
	}
}

include_all_once('langs/*.php');
?>
