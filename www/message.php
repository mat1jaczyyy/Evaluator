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
	
	if ($_POST['msg'] != "") {
		if (strpos($_POST['msg'], ": ") === False) {
			$_POST['msg'] = str_replace("\r", "", $_POST['msg']);
			$_POST['msg'] = str_replace("\n", "<br><br>", $_POST['msg']);
			
			$message_file = file_get_contents("db/messages.db");
			if ($message_file == "") {
				$handle = fopen("db/messages.db", "w");
				if ($handle) {
					fwrite($handle, $_COOKIE['TOKEN'] . ": 1: " . $_POST['msg']);
					fclose($handle);
				}
				header("Location: /?msg=1");
				
			} else {
				$message_file = str_replace("\r", "", $message_file);
				$message_array = explode("\n", $message_file);
				
				$message_data = [];
				foreach ($message_array as $entry) {
					$split = explode(": ", $entry);
					if ($split[0] == $_COOKIE['TOKEN']) {
						$message_data[$split[1]] = $split[2];
					}
				}
				
				$id = count($message_data) + 1;
				
				$handle = fopen("db/messages.db", "a");
				if ($handle) {
					fwrite($handle, "\n" . $_COOKIE['TOKEN'] . ": $id: " . $_POST['msg']);
					fclose($handle);
				}
				
				header("Location: /?msg=1");
			}
		} else {
			header("Location: /?msg=3");
		}
	} else {
		header("Location: /?msg=2");
	}
?>