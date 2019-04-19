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
	
	if ($_POST['username'] == "administrator" and $_POST['password'] == "9VV5aS3W") {
		setcookie('TOKEN', $_POST['username']);
		header("Location: /");
	} else {
		$users_file = file_get_contents("db/users.db");
		$users_file = str_replace("\r", "", $users_file);
		$users_array = explode("\n", $users_file);
		
		$users = [];
		foreach ($users_array as $user) {
			array_push($users, explode(": ", $user));
		}
		
		$in = False;
		foreach ($users as $user) {
			if ($user[0] == $_POST['username'] && $user[1] == $_POST['password']) {
				setcookie('TOKEN', $_POST['username']);
				$in = True;
				header("Location: /");
				break;
			}
		}
		
		if ($in == False) {
			header("Location: /?msg=0");
		}
	}
?>