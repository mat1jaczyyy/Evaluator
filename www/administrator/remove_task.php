<?php
	$contest_file = file_get_contents("../db/contest.db");
	$contest_file = str_replace("\r", "", $contest_file);
	$contest_array = explode("\n", $contest_file);
	
	$contest_data = [];
	foreach ($contest_array as $entry) {
		$colondot = strpos($entry, ": ");
		$contest_data[substr($entry, 0, $colondot)] = substr($entry, $colondot + 2);
	}
	
	$tasks = 0;
	foreach ($contest_data as $key => $value) {
		if (explode("-", $key)[0] == "task") {
			$tasks += 1;
		}
	}
	
	$k = $_GET["id"];
	unset($contest_data["task-$k"]);
	unset($contest_data["text-$k"]);
	unset($contest_data["in-$k"]);
	unset($contest_data["out-$k"]);
	unset($contest_data["bod-$k"]);
	unset($contest_data["pts-$k"]);
	
	if ($tasks > $k) {
		foreach ($contest_data as $key => $value) {
			$_key = explode("-", $key);
			if (count($_key) > 1) {
				$contest_data[$_key[0] . "-" . (intval($_key[1]) - 1)] = $value;
			}
		}
		
		unset($contest_data["task-$tasks"]);
		unset($contest_data["text-$tasks"]);
		unset($contest_data["in-$tasks"]);
		unset($contest_data["out-$tasks"]);
		unset($contest_data["bod-$tasks"]);
		unset($contest_data["pts-$tasks"]);
	}
	
	$contest_new = "";
	foreach ($contest_data as $key => $value) {
		$contest_new .= "$key: $value";
		$contest_new .= "\n";
	}
	
	$contest_new = trim($contest_new, "\n");
	
	$handle = fopen("../db/contest.db", "w");
	if ($handle) {
		fwrite($handle, $contest_new);
		fclose($handle);
	}
	
	header("Location: /administrator/?page=task");
?>