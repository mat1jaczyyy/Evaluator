d<?php
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
	
	$required_login = True;
	
	$puno_ime = "isus krist na raznju";
	if (array_key_exists('TOKEN', $_COOKIE)) {
		if ($_COOKIE['TOKEN'] == "administrator") {
			header("Location: /administrator/");
		}
		$users_file = file_get_contents("db/users.db");
		$users_file = str_replace("\r", "", $users_file);
		$users_array = explode("\n", $users_file);
		
		$users = [];
		foreach ($users_array as $user) {
			$asdfghjkl = explode(": ", $user);
			array_push($users, $asdfghjkl);
			if ($asdfghjkl[0] == $_COOKIE['TOKEN']) {
				$puno_ime = $asdfghjkl[2];
			}
		}
		
		$broken = False;
		foreach ($users as $user) {
			if ($_COOKIE['TOKEN'] == $user[0]) {
				$broken = True;
				$required_login = False;
				break;
			}
		}
	}
	
	$contest_file = file_get_contents("db\contest.db");
	$contest_file = str_replace("\r", "", $contest_file);
	$contest_array = explode("\n", $contest_file);
	
	$contest_data = [];
	foreach ($contest_array as $entry) {
		$colondot = strpos($entry, ": ");
		$contest_data[substr($entry, 0, $colondot)] = str_replace("ยง", "<br><br>", substr($entry, $colondot + 2));
	}
?>
<!--

	EVALUATOR BETA 0.2
	
	Dominik Matijaca 2017

-->

<html>
	<head>
		<title>
			Evaluator - <?php echo $contest_data['name']; ?>
		</title>
		<link rel="stylesheet" href="stylesheet/global.css">
		<meta charset="UTF-8" />
	</head>
	<body>
		<div class="wrapper">
			<div class="header center">
				<img src="image/evaluator.png" height="100px">
			</div>
			<div class="spacer">
				<br>
			</div>
			<div class="container">
				<?php
					if ($required_login) {
						?>
							<p class="center bold">
								Evaluator - <?php echo $contest_data['name']; ?>
							</p>
							<?php
								if (array_key_exists('msg', $_GET)) {
									?>
										<p class="text-red center">
											<?php
												switch ($_GET['msg']) {
													case "0":
														echo "Netocna prijava.";
														break;
												}
											?>
										</p>
									<?php
								}
							?>
							<form method="post" action="auth.php" class="center">
								<p>
									Korisničko ime:
									<br><br>
									<input type="text" name="username">
								</p>
								<p>
									Lozinka:
									<br><br>
									<input type="password" name="password">
								</p>
								<p>
									<input type="submit" value="Prijava">
								</p>
							</form>
						<?php
					} else {
						?>
							<p class="center bold">
								Evaluator - <?php echo $contest_data['name']; ?>
							</p>
							<table class="linkbar">
								<tr>
									<td>
										<a href="/" class="link">
											Naslovnica
										</a>
									</td>
									<td>
										/
									</td>
									<td>
										<a href="/?page=eval" class="link">
											Evaluacija
										</a>
									</td>
									<td>
										/
									</td>
									<td>
										<a href="/?page=result" class="link">
											Rezultati
										</a>
									</td>
								</tr>
							</table>
							<?php
								if (array_key_exists('page', $_GET)) {
									$__page = $_GET['page'];
								} else {
									$__page = "main";
								}
								
								switch ($__page) {
									case "main":
										?>
											<p class="center title bold">
												Zadatci
											</p>
											<p class="center title">
												<a href="/evaluator-zadatci.pdf" target=_blank>Preuzimanje zadataka</a>
											</p>
											<p class="center title bold">
												Obavijesti
											</p>
											<p class="text">
												Ovdje će se prikazati obavijesti koje objavljuje Administrator Evaluatora. Oni su vidljivi svim korisnicima.
											</p>
											<?php
												$notif_file = file_get_contents("db/notif.db");
												
												if ($notif_file != "") {
													$notif_file = str_replace("\r", "", $notif_file);
													$notif_array = explode("\n", $notif_file);
													
													$notif_data = [];
													foreach ($notif_array as $entry) {
														$colondot = strpos($entry, ": ");
														$notif_data[substr($entry, 0, $colondot)] = substr($entry, $colondot + 2);
													}
													
													foreach ($notif_data as $num => $notification) {
														?>
															<p class="notif-title">
																<?php
																	echo "Obavijest #$num";
																?>
															</p>
															<p class="text notif">
																<?php
																	echo $notification;
																?>
															</p>
															<p></p>
														<?php
													}
												}
											?>
											<hr>
											<p class="center title bold">
												Pošalji poruku Administratoru Evaluatora
											</p>
											<p class="text">
												Ukoliko imaš nekakvih poteškoća, ovdje možeš poslati upit Administratoru Evaluatora. 
												<br><br>
												Na ovoj stranici će se prikazati i odgovori na tvoje upite. Oni su vidljivi samo tebi.
											</p>
											<?php
												$message_file = file_get_contents("db/messages.db");
												$message_file = str_replace("\r", "", $message_file);
												$message_array = explode("\n", $message_file);
												
												$message_data = [];
												$answers = [];
												foreach ($message_array as $entry) {
													$split = explode(": ", $entry);
													if ($split[0] == $_COOKIE['TOKEN']) {
														$message_data[$split[1]] = $split[2];
													} elseif ($split[0] == "answer" and $split[1] == $_COOKIE['TOKEN']) {
														$answers[$split[2]] = $split[3];
													}
												}
												
												foreach ($message_data as $num => $message) {
													?>
														<p class="msg-title">
															<?php
																echo "Poruka #$num";
															?>
														</p>
														<p class="text msg">
															<?php
																echo $message;
															?>
														</p>
														<p></p>
														<?php
															if (array_key_exists($num, $answers)) {
																?>
																	<p class="ans-title">
																		<?php
																			echo "Odgovor na poruku #$num";
																		?>
																	</p>
																	<p class="text ans">
																		<?php
																			echo $answers[$num];
																		?>
																	</p>
																	<p></p>
																<?php
															}
														?>
													<?php
												}
											?>
											<form method="post" action="message.php" class="text">
												<p class="center">
													<textarea name="msg" style="width: 90%;" rows=4 placeholder="Unesi poruku..."></textarea>
												</p>
												<p class="right">
													<input type="submit" value="Pošalji">
												</p>
											</form>
										<?php
										break;
									
									case "eval":
										?>
											<p class="center title bold">
												Evaluacija
											</p>
											<form method="post" action="upload.php" enctype="multipart/form-data">
												<p class="text" style="text-align: center !important;" >
													Zadatak: <select name="task">
														<?php
															foreach ($contest_data as $key => $value) {
																if (substr($key, 0, 5) == "task-") {
																	?>
																		<option value="<?php echo substr($key, strpos($key, '-') + 1); ?>"><?php echo $value; ?></option>
																	<?php
																}
															}
														?>
													</select>
												</p>
												<p class="text" style="text-align: center !important;" >
													<input type="file" name="code">
												</p>
												<p class="text" style="text-align: center !important;" >
													<input type="submit" value="Evaluiraj">
												</p>
											</form>
											<hr>
											<p class="center title bold">
												Poslana rješenja
											</p>
											<?php
												for ($zadatak = 1; $zadatak <= (int)($contest_data['tasks']); $zadatak++) {
													if ($zadatak > 1) {
														?>
															<hr>
														<?php
													}
													?>
														<p class="center title">
															Zadatak <?php echo $contest_data["task-$zadatak"]; ?>
														</p>
													<?php
													if (count(glob("code/" . $_COOKIE['TOKEN'] . "/" . $zadatak . "*")) == 0) {
														?>
															<p class="center">
																Još niste predali ni jedno rješenje za ovaj zadatak.
															</p>
														<?php
													} else {
														?>
															<p>
																<table class="solutions">
																	<?php
																		$entireQueue = explode("\n", file_get_contents("db/queue.db"));
																		rsort($entireQueue);
																		foreach($entireQueue as $queueElement) {
																			$zzzzz = explode("/", $queueElement);
																			if ($zzzzz[1] == $_COOKIE['TOKEN'] && $zadatak == explode("-", $zzzzz[2])[0]) {
																				if (file_exists("$queueElement.result")) {
																					$resultFile = explode("\n", file_get_contents("$queueElement.result"));
																					
																					if ($resultFile[2] == "AC") {
																						$nTests = count(glob("in/" . $zadatak . ".*"));
																						?>
																							<tr>
																								<td class="sol">
																									<?php
																										echo date("Y-M-d H:i:s", explode("-", $queueElement)[1]);
																									?>
																									<span>
																										<a href="<?php echo $queueElement ?>" download>
																											preuzmi kod
																										</a>
																									</span>
																								</td>
																								<td style="background-color: #00FF00;">
																									<?php echo "$nTests/$nTests (" . $contest_data['pts-' . $zadatak] . ") " . $resultFile[2]; ?>
																								</td>
																							</tr>
																						<?php
																					} else {
																						?>
																							<tr>
																								<td class="sol">
																									<?php
																										echo date("Y-M-d H:i:s", explode("-", $queueElement)[1]);
																									?>
																									<span>
																										<?php
																											if (file_exists("$queueElement.err")) {
																												?>
																													<a href="<?php echo "/?page=error&file=" . explode("/", $queueElement)[2] ?>" style="color: #A00;">greška</a>
																												<?php
																											}
																										?>
																										<a href="<?php echo $queueElement ?>" download>
																											preuzmi kod
																										</a>
																									</span>
																								</td>
																								<?php
																									if ($resultFile[0] == "d") {
																										$color = "FF0000";
																										$nTests = count(glob("dummy_in/" . $zadatak . ".*"));
																										$solPts = 0;
																									} else {
																										$nTests = count(glob("in/" . $zadatak . ".*"));
																										$perc = ($resultFile[1] * 511) / $nTests;
																										$r_perc = 511 - $perc;
																										if ($r_perc > 255) {
																											$r_perc = 255;
																										}
																										$r = dechex($r_perc);
																										if (strlen($r) == 1) {
																											$r = "0$r";
																										}
																										
																										$g_perc = $perc;
																										if ($g_perc > 255) {
																											$g_perc = 255;
																										}
																										$g = dechex($g_perc);
																										if (strlen($g) == 1) {
																											$g = "0$g";
																										}
																										
																										$color = $r . $g . "00";																								
																										$solPts = ($resultFile[1] * $contest_data['pts-' . $zadatak]) / $nTests;
																									}
																								?>
																								<td style="background-color: #<?php echo $color; ?>;">
																									<?php 
																										if ($resultFile[0] == "d") {
																											echo "D " . $resultFile[1] . "/$nTests (0) " . $resultFile[2];
																										} else {
																											echo $resultFile[1] . "/$nTests ($solPts) ". $resultFile[2];
																										}
																									?>
																								</td>
																							</tr>
																						<?php
																					}
																				} else {
																					?>
																						<tr>
																							<td class="sol">
																								<?php
																									echo date("Y-M-d H:i:s", explode("-", $queueElement)[1]);
																								?>
																								<span>
																									<a href="<?php echo $queueElement ?>" download>
																										preuzmi kod
																									</a>
																								</span>
																							</td>
																							<td style="background-color: black; color: white;">
																								Evaluiranje...
																							</td>
																						</tr>
																					<?php
																				}
																			}
																		}
																	?>
																</table>
															</p>
														<?php
													}
												}
											?>
										<?php
										break;
										
									case "result":
										?>
											<p class="center title bold">
												Rezultati
											</p>
											<?php
											if ($contest_data['results'] == "0" && $_COOKIE['TOKEN'] != "dmatijaca") {
												?>
													<p class="center">
														Trenutno nemate dozvolu za pregledavanje rezultata.
													</p>
												<?php
											} else {
												if (count(glob("code/*")) == 0) {
													?>
														<p class="center">
															Jos nema predanih rješenja.
														</p>
													<?php
												} else {
													?>
														<p>
															<table class="results">
																<tr>
																	<th>
																		Korisnik
																	</th>
																	<?php
																		$tasks = 0;
																		foreach ($contest_data as $key => $value) {
																			if (explode("-", $key)[0] == "task") {
																				$tasks += 1;
																			}
																		}
																		
																		$maxPts = 0;
																		for ($k = 1; $k <= $tasks; $k++) {
																			?>
																				<th>
																					<?php
																						echo $contest_data['task-' . $k];
																						$maxPts += $contest_data["pts-$k"];
																					?>
																				</th>
																			<?php
																		}
																	?>
																	<th>
																		Ukupno
																	</th>
																</tr>
																<?php
																	foreach ($users as $usr) {
																		?>
																			<tr>
																				<td class="username">
																					<?php
																						echo $usr[2];
																					?>
																				</td>
																				<?php
																					$pts = 0;
																					for ($i = 1; $i <= $tasks; $i++) {
																						$resultFiles = glob("code/". $usr[0] . "/$i-*.result");
																						sort($resultFiles);
																						$tile = "
																							<td style=\"background: #000; color: #FFF;\">
																								N/A
																							</td>
																						";
																						$taskPts = 0;
																						foreach ($resultFiles as $resultFiile) {
																							$resultFile = explode("\n", file_get_contents($resultFiile));
																							if ($resultFile[2] == "AC") {
																								$nTests = count(glob("in/" . $i . ".*"));
																								$tile = "
																									<td style=\"background-color: #00FF00;\">
																										$nTests/$nTests (" . $contest_data["pts-$i"] . ") " . $resultFile[2] . "
																									</td>
																								";
																								$taskPts = $contest_data["pts-$i"];
																								break;
																							} else {
																								if ($resultFile[0] == "d") {
																									$color = "FF0000";
																									$nTests = count(glob("dummy_in/" . $i . ".*"));
																									$ptsX = "D " . $resultFile[1] . "/$nTests (0) " . $resultFile[2];
																									$solPts = 0;
																								} else {
																									$nTests = count(glob("in/" . $i . ".*"));
																									$perc = ($resultFile[1] * 511) / $nTests;
																									$r_perc = 511 - $perc;
																									if ($r_perc > 255) {
																										$r_perc = 255;
																									}
																									$r = dechex($r_perc);
																									if (strlen($r) == 1) {
																										$r = "0$r";
																									}
																									
																									$g_perc = $perc;
																									if ($g_perc > 255) {
																										$g_perc = 255;
																									}
																									$g = dechex($g_perc);
																									if (strlen($g) == 1) {
																										$g = "0$g";
																									}
																									
																									$color = $r . $g . "00";																								
																									$solPts = ($resultFile[1] * $contest_data["pts-$i"]) / $nTests;
																									$ptsX = $resultFile[1] . "/$nTests ($solPts) " . $resultFile[2];
																								}
																								if ($taskPts < $solPts) {
																									$tile = "
																										<td style=\"background-color: #$color;\">
																											$ptsX
																										</td>
																									";
																									$taskPts = $solPts;
																								}
																							}
																						}
																						echo $tile;
																						$pts += $taskPts;
																					}
																					
																					$perc = ($pts * 511) / $maxPts;
																					$r_perc = 511 - $perc;
																					if ($r_perc > 255) {
																						$r_perc = 255;
																					}
																					$r = dechex($r_perc);
																					if (strlen($r) == 1) {
																						$r = "0$r";
																					}
																					
																					$g_perc = $perc;
																					if ($g_perc > 255) {
																						$g_perc = 255;
																					}
																					$g = dechex($g_perc);
																					if (strlen($g) == 1) {
																						$g = "0$g";
																					}
																					
																					$ptscolor = $r . $g . "00";
																				?>
																				<td style="background-color: #<?php echo $ptscolor; ?>;">
																					<?php
																						echo $pts;
																					?>
																				</td>
																			</tr>
																		<?php
																	}	
																?>
															</table>
														</p>
													<?php
												}
											}
										break;
									
									case "error":
										?>
											<p class="center title bold">
												Greška
											</p>
											<p class="text">
												Tvoj kod za zadatak <a href="/?task=<?php echo explode("-", $_GET['file'])[0]; ?>"><?php echo $contest_data['task-' . explode("-", $_GET['file'])[0]]; ?></a> ispisao je grešku tijekom izvršavanja:
											</p>
											<pre class="text" style="color: #F00;"><?php
												echo file_get_contents("code/" . $_COOKIE['TOKEN'] . "/" . $_GET['file'] . ".err");
											?></pre>
											<p class="text">
												<a href="/?task=<?php echo explode("-", $_GET['file'])[0]; ?>">Natrag na zadatak</a>
											</p>
										<?php
										break;
									
									default:
										?>
											400 Bad Request.
										<?php
								}
							?>
						<?php
					}
				
					if ($puno_ime != "isus krist na raznju") {
						?>
							<p class="right">
								<br>
								<a href="logout.php" class="link">
									Odjava (<?php echo $puno_ime; ?>)
								</a>
							</p>
						<?php
					}
				?>
			</div>
			<div class="spacer">
				<br>
			</div>
			<div class="footer">
				<p class="center">
					Izradio 2016-2017 Dominik Matijaca, III. gimnazija Split
					<br><br>
					Evaluator Beta revizija <?php echo date("d.m.y", filemtime("index.php")); ?>
				</p>
			</div>
		</div>
	</body>
</html>