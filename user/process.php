<?php
/*
	@! AuthManager v3.0
	@@ User authentication and management web application
-----------------------------------------------------------------------------	
	** author: StitchApps
	** website: http://www.stitchapps.com
	** email: support@stitchapps.com
	** phone support: +91 9871084893
-----------------------------------------------------------------------------
	@@package: am_authmanager3.0
*/
include("../init.php");

	/*
	include required files as per the admin option of inbuilt captcha enabled or not.
	*/
	if($inbuilt_captcha == 1) {
		include("../".MODS_DIRECTORY."/captcha/php-captcha.inc.php");
	} else {
		include("../".MODS_DIRECTORY."/recaptchalib.php");
	}

if(isset($_GET["r"])) {
	$r = cleanInput($_GET["r"]);

	/*
	time to be used for setcookie function which for the remember me feature.
	*/
	$time = time();
	
	/*
	ip address for logging the user access details	
	*/
	$ip_address = $_SERVER["REMOTE_ADDR"];
}

	if($r == "verify") {
		if(isset($_POST["login"])) {
			$username = cleanInput($_POST["username"]);
			$password = cleanInput($_POST["password"]);

			if(isset($_POST["autologin"])) {
				$autologin = intval($_POST["autologin"]);
			} else {
				$autologin = 0;
			}

			if(!empty($username) && !empty($password)) {
				/*
				encypting the password using the same alogrithm. this is the only way in which we can match the user passwords.
				*/

				$password = generate_encrypted_password($password);
				
				if($inbuilt_captcha == 1) {
					if(PhpCaptcha::Validate($_POST["user_code"])) {
						$is_captcha_true = true;
					} else {
						$is_captcha_true = false;
					}
				} else {
					$resp = recaptcha_check_answer ($_setting['recaptcha_private'], $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
					if($resp->is_valid) {
						$is_captcha_true = true;
					} else {
						$is_captcha_true = false;
					}
				}
				
				if($is_captcha_true == true) {
					try {
						$q = "SELECT * FROM `members` WHERE `email` = :username AND `password` = :password";
						$check_query = $db->prepare($q);
						$check_query->bindParam(':username', $username, PDO::PARAM_STR);
						$check_query->bindParam(':password', $password, PDO::PARAM_STR);
						$check_query->execute();
						$n2 = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
					} catch(PDOException $e) {
						$log->logError($e." - ".basename(__FILE__));
					}

							if(!$n2) {
								$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Credentials.")."</strong><br/>"._("The details entered by you do not match.")."</div>";
								header("Location: {$website}/".USER_DIRECTORY."/login/verify");
							} else {
								$f = $check_query->fetch (PDO::FETCH_ASSOC);
								$verified = intval($f['verified']);
								$banned = intval($f['banned']);
								$userid = intval($f['id']);

								if(empty($verified)) {
									$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Email not verified.")."</strong><br/>"._("You have not verified your email address yet. Please verify your email in order to access our services.")."</div>";
									header("Location: {$website}/".USER_DIRECTORY."/login/verify");
								} else {
									if($banned == 1) {
										$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Account Banned.")."</strong><br/>"._("Please contact support staff for further assistance.")."</div>";
										header("Location: {$website}/".USER_DIRECTORY."/login/verify");
									} else {
										if($autologin == 1) {
											setcookie("pauser", $username, $time + 3600*24*10);
											setcookie("papass", $password, $time + 3600*24*10);
										}

										$date = date("Y-m-d H:i:s");

										/*
										updating last access for the user once he is logged in to the website.
										*/
										try {
											$set_access = "UPDATE `members` SET `access` = :date WHERE `email` = :username";
											$s_a = $db->prepare($set_access);
											$s_a->bindParam(':date', $date, PDO::PARAM_STR);
											$s_a->bindParam(':username', $username, PDO::PARAM_STR);
											$s_a->execute();
										} catch(PDOException $e) {
											$log->logError($e." - ".basename(__FILE__));
										}

										/*
										inserting the user access details if the option is enabled in the backend.
										*/
										if($_setting['enable_access'] == 1) {
											try {
												$access_details = "INSERT INTO `access`(`ip_address`, `userid`, `datetime`) VALUE(:ip_address, :userid, :datetime)";
												$access_details_do = $db->prepare($access_details);
												$access_details_do->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
												$access_details_do->bindParam(':userid', $userid, PDO::PARAM_INT);
												$access_details_do->bindParam(':datetime', $date, PDO::PARAM_STR);
												$access_details_do->execute();
											} catch(PDOException $e) {
												$log->logError($e." - ".basename(__FILE__));
											}
										}

										/*
										reset the login attempts for the user after successful login to the website.
										*/
										try {
											$reset_attempts = "UPDATE `members` SET `login_attempt` = 0 WHERE `email` = :username LIMIT 1";
											$r_t = $db->prepare($reset_attempts);
											$r_t->bindParam(':username', $username, PDO::PARAM_STR);
											$r_t->execute();
										} catch(PDOException $e) {
											$log->logError($e." - ".basename(__FILE__));
										}	
										
										$_SESSION["user"] = $username;
										$_SESSION["pass"] = $password;
										
										echo "<center><br/><br/><img src=\"{$website}/images/working.gif\" /></center>";
										echo "<meta http-equiv=\"refresh\" content=\"1;url={$website}/\" />";
									}
								}
							}
					} else {
						$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Verification.")."</strong><br/>"._("Please fill in the verification field correctly.")."</div>";
						header("Location: {$website}/".USER_DIRECTORY."/login/verify");
					}
			} else {
				$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
				header("Location: {$website}/".USER_DIRECTORY."/login/verify");
			}
		}
	} elseif($r == "reg") {
		if(isset($_POST["login"])) {
			$username = cleanInput($_POST["username"]);
			$password = cleanInput($_POST["password"]);
		
			if(isset($_POST["autologin"])) {
				$autologin = intval($_POST["autologin"]);
			} else {
				$autologin = 0;
			}

			if(!empty($username) && !empty($password)) {
				/*
				encypting the password using the same alogrithm. This is the only way in which we can match the user passwords.
				*/

				$password = generate_encrypted_password($password);

				try {
					$a = "SELECT `login_attempt` FROM `members` WHERE `email` = :username";
					$account_check = $db->prepare($a);
					$account_check->bindParam(':username', $username, PDO::PARAM_STR);
					$account_check->execute();
					$ac = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
				} catch(PDOException $e) {
					$log->logError($e." - ".basename(__FILE__));
				}

					if(!empty($ac)) {
						$fetch = $account_check->fetch (PDO::FETCH_ASSOC);
						$login_attempt = intval($fetch['login_attempt']);

						if($login_attempt > $_setting['max_invalid_login']) {
							$_SESSION["error"] = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Attempts Exceeded.")."</strong><br/>"._("You have exceeded the limit of login attempts. Please verify that you are human.")."</div>";
							header("Location: {$website}/".USER_DIRECTORY."/login/verify");
						} else {
							try {
								$get_details = "SELECT * FROM `members` WHERE `email` = :username AND `password` = :password";
								$get_details_do = $db->prepare($get_details);
								$get_details_do->bindParam(':username', $username, PDO::PARAM_STR);
								$get_details_do->bindParam(':password', $password, PDO::PARAM_STR);
								$get_details_do->execute();
								$n2 = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

								if(empty($n2)) {
									$attempt = $login_attempt + 1;

									try {
										$update_query = "UPDATE `members` SET `login_attempt` = :attempt WHERE `email` = :username LIMIT 1";
										$update_login_attempts = $db->prepare($update_query);
										$update_login_attempts->bindParam(':attempt', $attempt, PDO::PARAM_INT);
										$update_login_attempts->bindParam(':username', $username, PDO::PARAM_STR);
										$update_login_attempts->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}

									$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Credentials.")."</strong><br/>"._("The details entered by you do not match.")."</div>";
									header("Location: {$website}/".USER_DIRECTORY."/login/reg");
								} else {
									$f = $get_details_do->fetch (PDO::FETCH_ASSOC);
									$verified = intval($f['verified']);
									$banned = intval($f['banned']);
									$userid = intval($f['id']);

									if(empty($verified)) {
										$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Email not verified.")."</strong><br/>"._("You have not verified your email address yet. Please verify your email in order to access our services.")."</div>";
										header("Location: {$website}/".USER_DIRECTORY."/login/reg");
									} else {
										if($banned == 1) {
											$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Account Banned.")."</strong><br/>"._("Please contact support staff for further assistance.")."</div>";
											header("Location: {$website}/".USER_DIRECTORY."/login/reg");
										} else {
											if($autologin == 1) {
												setcookie("pauser", $username, $time + 3600*24*10, "/");
												setcookie("papass", $password, $time + 3600*24*10, "/");
											}
											
											$date = date("Y-m-d H:i:s");
											
											/*
											updating last access for the user once he is logged in to the website.
											*/
											try {
												$set_access = "UPDATE `members` SET `access` = :date WHERE `email` = :username";
												$s_a = $db->prepare($set_access);
												$s_a->bindParam(':date', $date, PDO::PARAM_STR);
												$s_a->bindParam(':username', $username, PDO::PARAM_STR);
												$s_a->execute();
											} catch(PDOException $e) {
												$log->logError($e." - ".basename(__FILE__));
											}
											
											/*
											inserting the user access details if the option is enabled in the backend.
											*/
											if($_setting['enable_access'] == 1) {
												try {
													$access_details = "INSERT INTO `access`(`ip_address`, `userid`, `datetime`) VALUE(:ip_address, :userid, :datetime)";
													$access_details_do = $db->prepare($access_details);
													$access_details_do->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
													$access_details_do->bindParam(':userid', $userid, PDO::PARAM_INT);
													$access_details_do->bindParam(':datetime', $date, PDO::PARAM_STR);
													$access_details_do->execute();
												} catch(PDOException $e) {
													$log->logError($e." - ".basename(__FILE__));
												}
											}

											/*
											reset the login attempts for the user after successful login to the website.
											*/
											try {
												$reset_attempts = "UPDATE `members` SET `login_attempt` = 0 WHERE `email` = :username LIMIT 1";
												$r_t = $db->prepare($reset_attempts);
												$r_t->bindParam(':username', $username, PDO::PARAM_STR);
												$r_t->execute();
											} catch(PDOException $e) {
												$log->logError($e." - ".basename(__FILE__));
											}

											$_SESSION["user"] = $username;
											$_SESSION["pass"] = $password;

											echo "<center><br/><br/><img src=\"{$website}/images/working.gif\" /></center>";
											echo "<meta http-equiv=\"refresh\" content=\"1;url={$website}/\" />";
										}
									}
								}
						}
					} else {
						$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Email not found.")."</strong><br/>"._("It seems that this email is not registered with us.")."</div>";
						header("Location: {$website}/".USER_DIRECTORY."/login/reg");
					}
			} else {
				$_SESSION["error"] =  "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
				header("Location: {$website}/".USER_DIRECTORY."/login/reg");
			}
	}
} else {
	if($sesslife == false) {
		/*
		show the login box if the session is false.
		*/
		header("Location: {$website}/".USER_DIRECTORY."/login");
	} else {
		/*
		error message is shown if the user visits this page even after logging in.
		*/
		header("Location: {$website}/".USER_DIRECTORY."/login");
	}
}
?>