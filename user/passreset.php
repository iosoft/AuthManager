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
include("header.php");
subheader(_("Password Reset"));

if($sesslife == false) {
	if(isset($_GET["k"])) {
		$key = cleanInput($_GET["k"]);
			if(!empty($key)) {
				if(isset($_GET["u"])) {
					$key_user = cleanInput($_GET["u"]);
						if(!empty($key_user)) {
							try {
								$check = "SELECT * FROM `members` WHERE `email` = :key_user AND `pass_reset_key` = :key";
								$check_do = $db->prepare($check);
								$check_do->bindParam(':key_user', $key_user, PDO::PARAM_STR);
								$check_do->bindParam(':key', $key, PDO::PARAM_STR);
								$check_do->execute();
								$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

								if(!empty($number)) {
									$pass_fetch = $check_do->fetch (PDO::FETCH_ASSOC);
									$expiry_time = intval($pass_fetch['expires_at']);
									$key_uid = intval($pass_fetch['id']);
									$time_now = time();
										if($expiry_time < $time_now) {
											echo "<div class=\"alert alert-error\"><strong>"._("Time Expired.")."</strong><br/>"._("You seem to have exceeded the timeframe to reset your password. Please generate your password reset link again.")."</div>";
										} else {
											$new_password = createRandomPassword();
											$encrypt_password = generate_encrypted_password($new_password);

											try {
												$check = "UPDATE `members` SET `password` = :encrypt_password, `pass_reset_key` = null, `expires_at` = null, `verified` = 1 WHERE `id` = :key_uid";
												$check_do = $db->prepare($check);
												$check_do->bindParam(':encrypt_password', $encrypt_password, PDO::PARAM_STR);
												$check_do->bindParam(':key_uid', $key_uid, PDO::PARAM_INT);
												$confirm_do = $check_do->execute();
											} catch(PDOException $e) {
												$log->logError($e." - ".basename(__FILE__));
											}

											if(!empty($confirm_do)) {
												newpass_email($key_user, $new_password);
												echo "<div class=\"alert alert-success\"><strong>"._("New Password Sent.")."</strong><br/>"._("An email containing your new password has been sent to your registered email address.")."</div>";
											} else {
												echo "<div class=\"alert\"><strong>"._("Unable to process.")."</strong><br/>"._("We are unable to process your request at this time. Please try again after some time.")."</div>";
											}
										}
								} else {
									echo "<div class=\"alert alert-error\"><strong>"._("Invalid Reset Link.")."</strong><br/>"._("This seems to be an invalid password reset link. Please check the link again or else try generating a new password reset request.")."</div>";
								}
						} else {
							echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
						}
				} else {
					echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
				}
			} else {
				echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
			}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
	}
} else {
	echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user.")."</div>";
}

include("footer.php");
?>