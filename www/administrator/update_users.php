<?php
	$users_new = "";
	
	$break = false;
	for ($k = 0; !($break); $k++) {
		if (array_key_exists("username-$k", $_GET) and array_key_exists("password-$k", $_GET) and array_key_exists("name-$k", $_GET)) {
			if (!(empty($_GET["username-$k"])) and !(empty($_GET["password-$k"])) and !(empty($_GET["name-$k"]))) {
				$users_new .= $_GET["username-$k"] . ": " . $_GET["password-$k"] . ": " . $_GET["name-$k"] . "\n";
			}
		} else {
			$break = true;
		}
	}
	
	if (!(empty($_GET["username-new"])) and !(empty($_GET["password-new"])) and !(empty($_GET["name-new"]))) {
		$users_new .= $_GET["username-new"] . ": " . $_GET["password-new"] . ": " . $_GET["name-new"];
	} else {
		$users_new = trim($users_new, "\n");
	}
	
	$handle = fopen("../db/users.db", "w");
	if ($handle) {
		fwrite($handle, $users_new);
		fclose($handle);
	}
	
	header("Location: /administrator/?page=users");
?>