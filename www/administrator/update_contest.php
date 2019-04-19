<?php
	$contest_file = file_get_contents("../db/contest.db");
	$contest_file = str_replace("\r", "", $contest_file);
	$contest_array = explode("\n", $contest_file);
	
	$contest_data = [];
	foreach ($contest_array as $entry) {
		$colondot = strpos($entry, ": ");
		$contest_data[substr($entry, 0, $colondot)] = str_replace("§", "<br><br>", substr($entry, $colondot + 2));
	}
	
	$contest_new = "";
	foreach ($contest_data as $key => $value) {
		if ($key == 'name') {
			$contest_new .= "name: " . $_POST['name'];
		} else {
			$contest_new .= "$key: $value";
		}
		$contest_new .= "\n";
	}
	
	$contest_new = trim($contest_new, "\n");
	
	$handle = fopen("../db/contest.db", "w");
	if ($handle) {
		fwrite($handle, $contest_new);
		fclose($handle);
	}

	if ($_FILES['sponsor']['tmp_name'] != '') {
		move_uploaded_file($_FILES['sponsor']['tmp_name'], "../image/sponsor.png");
	}
	
	header("Location: /administrator/?page=contest");
?>