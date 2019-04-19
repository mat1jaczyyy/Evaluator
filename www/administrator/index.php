<?php
	$pass = false;
	if (count(glob("../db")) == 0) {
		mkdir("../db/");
		fopen("../db/contest.db", "w");
		fopen("../db/users.db", "w");
		fopen("../db/messages.db", "w");
		fopen("../db/notif.db", "w");
		fopen("../db/queue.db", "w");
		fopen("../db/evaluator.log", "w");
		
		setcookie('TOKEN', "administrator");
		$pass = true;
	}
	
	if (array_key_exists('TOKEN', $_COOKIE) or $pass) {
		if ($_COOKIE['TOKEN'] == "administrator" or $pass) {
			?>
				<html>
					<head>
						<title>
							Evaluator - Administrator
						</title>
						<link rel="stylesheet" href="/stylesheet/administrator.css">
						<meta charset="UTF-8" />
					</head>
					<body>
						<div class="wrapper">
							<div class="header">
								<table>
									<tr>
										<td onclick="location.href = '/administrator/';">
											<img src="/image/evaluator.png" style="max-height: 60px; max-width: 100%;">
										</td>
										<td onclick="location.href = '/administrator/?page=contest';">
											Osnovni podatci
										</td>
										<td onclick="location.href = '/administrator/?page=notif';">
											Obavijesti
										</td>
										<td onclick="location.href = '/administrator/?page=users';">
											Korisnici
										</td>
										<td onclick="location.href = '/administrator/?page=msg';">
											Poruke
										</td>
										<td onclick="location.href = '/logout.php';">
											Odjava
										</td>
									</tr>
								</table>
							</div>
							<div class="content">
								<?php
									if (array_key_exists('page', $_GET)) {
										switch ($_GET['page']) {
											case "contest":
												$contest_file = file_get_contents("../db/contest.db");
												$contest_file = str_replace("\r", "", $contest_file);
												$contest_array = explode("\n", $contest_file);
												
												$contest_data = [];
												foreach ($contest_array as $entry) {
													$colondot = strpos($entry, ": ");
													$contest_data[substr($entry, 0, $colondot)] = str_replace("§", "<br><br>", substr($entry, $colondot + 2));
												}
												
												if (array_key_exists('name', $contest_data) == false) {
													$contest_data['name'] = "";
												}
												
												?>
													<form method="post" action="/administrator/update_contest.php" class="center" enctype="multipart/form-data">
														<p>
															Naziv Evaluatora:
															<br><br>
															<input type="text" name="name" value="<?php echo $contest_data['name']; ?>">
														</p>
														<hr width=250px>
														<p>
															Slika sponzora:
															<br><br>
															<input type="file" name="sponsor">
															<br><br>
															<img src="../image/sponsor.png">
														</p>
														<p>
															<input type="submit" value="Spremi">
														</p>
													</form>
												<?php
												
												break;
											
											case "notif":
												$notif_file = file_get_contents("../db/notif.db");
												
												?>
													<form method="post" action="update_notif.php">
														<?php
															if ($notif_file != "") {
																$notif_array = explode("\n", $notif_file);
																
																$notif_data = [];
																foreach ($notif_array as $entry) {
																	$colondot = strpos($entry, ": ");
																	$notif_data[substr($entry, 0, $colondot)] = str_replace("<br><br>", "\r\n", substr($entry, $colondot + 2));
																}
																
																foreach ($notif_data as $index => $notif) {
																	?>
																		<p class="center">
																			Obavijest #<?php echo $index; ?> <a href="/administrator/remove_notif.php?id=<?php echo $index; ?>" class="remove">x</a>
																			<br>
																			<textarea name="<?php echo $index; ?>" rows=6 readonly><?php echo $notif; ?></textarea>
																		</p>
																	<?php
																}
																?>
																	<hr>
																<?php
															}
															
															?>
																<p class="center">
																	<textarea name="new" rows=6 placeholder="Nova obavijest"></textarea>
																</p>
																<p class="center">
																	<input type="submit" value="Spremi">
																</p>
															<?php
														?>
													</form>
												<?php
												
												break;
											
											case "users":
												$users_file = file_get_contents("../db/users.db");
												$users_file = str_replace("\r", "", $users_file);
												$users_array = explode("\n", $users_file);
												
												$users = [];
												foreach ($users_array as $user) {
													array_push($users, explode(": ", $user));
												}
												
												?>
													<form method="get" action="/administrator/update_users.php">
														<p>
															<table class="users">
																<tr>
																	<th>
																		Korisničko ime
																	</th>
																	<th>
																		Lozinka
																	</th>
																	<th>
																		Puno ime
																	</th>
																</tr>
																<?php
																	if ($users != [['']]) {
																		foreach ($users as $index => $user) {
																			?>
																				<tr>
																					<td>
																						<input type="text" name="username-<?php echo $index; ?>" value="<?php echo $user[0]; ?>">
																					</td>
																					<td>
																						<input type="text" name="password-<?php echo $index; ?>" value="<?php echo $user[1]; ?>">
																					</td>
																					<td>
																						<input type="text" name="name-<?php echo $index; ?>" value="<?php echo $user[2]; ?>">
																					</td>
																				</tr>
																			<?php
																		}
																	}
																?>
																<tr>
																	<td>
																		<input type="text" name="username-new" placeholder="novi_korisnik">
																	</td>
																	<td>
																		<input type="text" name="password-new" placeholder="lozinka">
																	</td>
																	<td>
																		<input type="text" name="name-new" placeholder="Novi Korisnik">
																	</td>
																</tr>
															</table>
														</p>
														<p class="center">
															<input type="submit" value="Spremi">
														</p>
													</form>
												<?php
												
												break;
											
											case "msg":
												$message_file = file_get_contents("../db/messages.db");
												if ($message_file == "") {
													?>
														<p class="center">
															Nema poruka.
														</p>
													<?php
												} else {
													$message_file = str_replace("\r", "", $message_file);
													$message_array = explode("\n", $message_file);
													
													$message_data = [];
													$answers = [];
													foreach ($message_array as $entry) {
														$split = explode(": ", $entry);
														if ($split[0] == "answer") {
															$answers[$split[1]][$split[2]] = str_replace("<br><br>", "\r\n", $split[3]);
														} else {
															$message_data[$split[0]][$split[1]] = str_replace("<br><br>", "\r\n", $split[2]);
														}
													}
													
													$users_file = file_get_contents("../db/users.db");
													$users_file = str_replace("\r", "", $users_file);
													$users_array = explode("\n", $users_file);
													
													$users = [];
													foreach ($users_array as $user) {
														array_push($users, explode(": ", $user));
													}
													
													?>
														<p>
															<table class="msg">
																<tr>
																	<td class="center">
																		<p>
																			Neodgovorene poruke
																		</p>
																		<?php
																			foreach ($message_data as $user => $messages) {
																				$username = "undefined";
																				foreach ($users as $search) {
																					if ($search[0] == $user) {
																						$username = $search[2];
																						break;
																					}
																				}
																				foreach ($messages as $id => $message) {
																					if (!(array_key_exists($user, $answers) and array_key_exists($id, $answers[$user]))) {
																						?>
																							<form method="post" action="/administrator/answer_message.php?user=<?php echo $user; ?>&id=<?php echo $id ?>">
																								<hr>
																								<p class="center">
																									Poruka #<?php echo $id; ?> od <?php echo $username; ?>
																									<br>
																									<textarea rows=6 readonly><?php echo $message; ?></textarea>
																									<br>
																									Odgovor:
																									<br>
																									<textarea name="answer" rows=6 placeholder="Odgovor..."></textarea>
																									<br>
																									<input type="submit" value="Odgovori">
																								</p>
																							</form>
																						<?php
																					}
																				}
																			}
																		?>
																	</td>
																	<td class="center">
																		<p>
																			Odgovorene poruke
																		</p>
																		<?php
																			foreach ($message_data as $user => $messages) {
																				$username = "undefined";
																				foreach ($users as $search) {
																					if ($search[0] == $user) {
																						$username = $search[2];
																						break;
																					}
																				}
																				foreach ($messages as $id => $message) {
																					if (array_key_exists($user, $answers) and array_key_exists($id, $answers[$user])) {
																						?>
																							<hr>
																							<p class="center">
																								Poruka #<?php echo $id; ?> od <?php echo $username; ?>
																								<br>
																								<textarea rows=6 readonly><?php echo $message; ?></textarea>
																								<br>
																								Odgovor:
																								<br>
																								<textarea rows=6 readonly><?php echo $answers[$user][$id]; ?></textarea>
																							</p>
																						<?php
																					}
																				}
																			}
																		?>
																	</td>
																</tr>
															</table>
														</p>
													<?php
												}
												
												break;
										}
									}
								?>
							</div>
							<div class="spacer">
								<br>
							</div>
							<div class="footer">
								<p class="center">
									Izradio 2016 Dominik Matijaca, III. gimnazija Split
								</p>
							</div>
						</div>
					</body>
				</html>
			<?php
		} else {
			header("Location: /");
		}
	} else {
		header("Location: /");
	}
?>