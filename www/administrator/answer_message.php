<?php
	var_dump($_GET);
	var_dump($_POST);
	
	if ($_POST['answer'] != "") {
		$_POST['answer'] = str_replace("\r", "", $_POST['answer']);
		$_POST['answer'] = str_replace("\n", "<br><br>", $_POST['answer']);

		$handle = fopen("../db/messages.db", "a");
		if ($handle) {
			fwrite($handle, "\nanswer: " . $_GET['user'] . ": ". $_GET['id'] . ": " . $_POST['answer']);
			fclose($handle);
		}
		
		header("Location: /administrator/?page=msg&msg=1");
	} else {
		header("Location: /administrator/?page=msg&msg=2");
	}
?>