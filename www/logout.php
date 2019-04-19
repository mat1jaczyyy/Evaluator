<?php
	$log = $_SERVER["REMOTE_ADDR"] . " accessed " . $_SERVER["REQUEST_URI"] . " {\n\t" . '$_COOKIE["TOKEN"] => ';
	if (array_key_exists('TOKEN', $_COOKIE)) {
		$log .= $_COOKIE["TOKEN"];
	} else {
		$log .= "empty";
	}
	$log .= "\n\tTime: " . date("d.m.Y. H:i:s") . "\n}\n\n";
	
	$handle = fopen("db/evaluator.log", "a");
	if ($handle) {
		fwrite($handle, $log);
		fclose($handle);
	}
	
	setcookie("TOKEN", "", 0);
	header("Location: /");
?>