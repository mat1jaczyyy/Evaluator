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
	
	$k = $tasks + 1;
	$contest_data["task-$k"] = $_POST["name"];
	$contest_data["text-$k"] = str_replace("\n", "<br>", str_replace("\r", "", $_POST["text"]));
	$contest_data["in-$k"] = str_replace("\n", "<br>", str_replace("\r", "", $_POST["in"]));
	$contest_data["out-$k"] = str_replace("\n", "<br>", str_replace("\r", "", $_POST["out"]));
	$contest_data["pts-$k"] = str_replace("\n", "<br>", str_replace("\r", "", $_POST["pts"]));
		
	if ($_POST["bod"] != "") {
		$contest_data["bod-$k"] = str_replace("\n", "<br>", str_replace("\r", "", $_POST["bod"]));
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
	
	header("Location: /administrator/?page=task&task=$k");
?>