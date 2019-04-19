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
	
	if (count(glob("db/")) == 0) {
		header("Location: administrator/");
	}
	
	if ($_FILES['code']['error'] == 0) {
		if (!(file_exists("code/" . $_COOKIE['TOKEN'] . "/"))) {
			mkdir("code/" . $_COOKIE['TOKEN']);
		}
		
		$filename = "code/" . $_COOKIE['TOKEN'] . "/" . $_POST['task'] . "-" . time();
		
		move_uploaded_file($_FILES['code']['tmp_name'], $filename);
		
		$handle = fopen("db/queue.db", "a");
		if ($handle) {
			if (file_get_contents("db/queue.db") != "") {
				fwrite($handle, "\n");
			}
			fwrite($handle, $filename);
			fclose($handle);
		}
	}
	
	header("Location: /?page=eval");
?>