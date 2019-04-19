<?php
	$notif_new = "";
	
	var_dump($_POST);
	
	$break = false;
	for ($k = 1; !($break); $k++) {
		if (array_key_exists("$k", $_POST)) {
			if (!(empty($_POST["$k"]))) {
				$_POST["$k"] = str_replace("\r", "", $_POST["$k"]);
				$_POST["$k"] = str_replace("\n", "<br><br>", $_POST["$k"]);
				$notif_new .= "$k: " . $_POST["$k"] . "\n";
			}
		} else {
			$break = true;
		}
	}
	
	if (!(empty($_POST["new"]))) {
		$_POST["new"] = str_replace("\r", "", $_POST["new"]);
		$_POST["new"] = str_replace("\n", "<br><br>", $_POST["new"]);
		$notif_new .= ($k - 1) . ": " . $_POST["new"];
	} else {
		$notif_new = trim($users_new, "\n");
	}
	
	$handle = fopen("../db/notif.db", "w");
	if ($handle) {
		fwrite($handle, $notif_new);
		fclose($handle);
	}
	
	header("Location: /administrator/?page=notif");
?>