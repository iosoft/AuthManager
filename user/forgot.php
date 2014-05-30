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
subheader(_("Forgot Password"));

if($sesslife == false) {
	if(isset($_POST["forgot"])) {
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
							$f = $q_do->fetch(PDO::FETCH_ASSOC);
							$uid = intval($f['id']);

							/*
							generate a random reset key for generating a random password.
							*/
							$reset_key = getGuid();
							$temp_time = time();
							$expires_at = $temp_time + 86400;
							
							try {
								$update_forgot = "UPDATE `members` SET `pass_reset_key` = :reset_key, `expires_at` = :expires_at WHERE `id` = :uid";
								$update_forgot_do = $db->prepare($update_forgot);
								$update_forgot_do->bindParam(':reset_key', $reset_key, PDO::PARAM_STR);
								$update_forgot_do->bindParam(':expires_at', $expires_at, PDO::PARAM_INT);
								$update_forgot_do->bindParam(':uid', $uid, PDO::PARAM_INT);
								$update_forgot_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							forgotpass_email($email, $reset_key);
							$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Password reset link sent.")."</strong><br/>"._("You will receive an email with password reset link shortly. Use the link to reset your password and start accessing our services.")."</div>";
						} else {
							$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Email not found.")."</strong><br/>"._("It seems that this email is not registered with us.")."</div>";
						}
				} else {
					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Verification.")."</strong><br/>"._("Please fill in the verification field correctly.")."</div>";
				}
			} else {
				$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
			}
	}

	am_showForgot();
} else {
	echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
}

include("footer.php");
?>