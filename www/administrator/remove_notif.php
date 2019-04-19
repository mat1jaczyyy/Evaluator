<?php
	$notif_file = file_get_contents("../db/notif.db");
	$notif_array = explode("\n", $notif_file);
	$notif_data = [];
	
	foreach ($notif_array as $entry) {
		$colondot = strpos($entry, ": ");
		$notif_data[substr($entry, 0, $colondot)] = substr($entry, $colondot + 2);
	}
	
	unset($notif_data[$_GET['id']]);
	
	$notif_data_new = [];
	foreach ($notif_data as $index => $notif) {
		if ($index > (int)$_GET['id']) {
			$index += -1;
		}
		$notif_data_new[$index] = $notif;
	}
	
	$notif_new = "";
	foreach ($notif_data_new as $index => $notif) {
		$notif_new .= "$index: $notif\n";
	}
	$notif_new = trim($notif_new, "\n");
	
	$handle = fopen("../db/notif.db", "w");
	if ($handle) {
		fwrite($handle, $notif_new);
		fclose($handle);
	}
	
	header("Location: /administrator/?page=notif");
?>