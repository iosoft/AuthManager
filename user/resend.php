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

include("header.php");
	/*
	show this page only if the user verification is enabled by the admin. Otherwise, show the page not found error.
	*/
	if($user_verification == 1) {
		subheader(_("Resend confirmation email"));
	} else {
		subheader(_("Error"));
	}

if($user_verification == 1) {
	if($sesslife == false) {
		if(isset($_POST["resend"])) {
			$email = cleanInput($_POST["email"]);
				if(!empty($email)) {
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
							$q = "SELECT * FROM `members` WHERE `email` = :email";
							$q_do = $db->prepare($q);
							$q_do->bindParam(':email', $email, PDO::PARAM_STR);
							$q_do->execute();
							$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
						} catch(PDOException $e) {
							$log->logError($e." - ".basename(__FILE__));
						}

							if(!empty($number)) {
								$f = $q_do->fetch (PDO::FETCH_ASSOC);
								$uid = intval($f['id']);
								$verified = intval($f['verified']);
								$key = cleanInput($f['key']);

									if(empty($verified)) {
										if(empty($key)) {
											/*
											getGuid() function generates a random unique 32 character unique key.
											*/
											$key = getGuid();

											try {
												$update = "UPDATE `members` SET `key` = :key WHERE `id` = :uid LIMIT 1";
												$update_do = $db->prepare($update);
												$update_do->bindParam(':key', $key, PDO::PARAM_STR);
												$update_do->bindParam(':uid', $uid, PDO::PARAM_STR);
												$update_do->execute();
											} catch(PDOException $e) {
												$log->logError($e." - ".basename(__FILE__));
											}

											verification_email($email, $key);
											$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Activation email sent.")."</strong><br/>"._("You will receive the activation email shortly.")."</div>";
											am_showResend();
										} else {
											verification_email($email, $key);
											$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Activation email sent.")."</strong><br/>"._("You will receive the activation email shortly.")."</div>";
											am_showResend();
										}
									} else {
										$err = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Account Active.")."</strong><br/>"._("You already have an active account with us. There is no need for activation.")."</div>";
										am_showResend();
									}
							} else {
								$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Email not found.")."</strong><br/>"._("It seems that this email is not registered with us.")."</div>";
								am_showResend();
							}
					} else {
						$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Verification.")."</strong><br/>"._("Please fill in the verification field correctly.")."</div>";
						am_showResend();
					}
				} else {
					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
					am_showResend();
				}
		} else {
			am_showResend();
		}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
	}
} else {
	echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
}

include("footer.php");
?>