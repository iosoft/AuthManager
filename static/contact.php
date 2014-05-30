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
include("../init.php");	/*	Include required files as per the admin option of inbuilt captcha enabled or not.	*/	if($inbuilt_captcha == 1) {		include("../".MODS_DIRECTORY."/captcha/php-captcha.inc.php");	} else {		include("../".MODS_DIRECTORY."/recaptchalib.php");	}include("../".USER_DIRECTORY."/header.php"); subheader(_("Contact Us"));
if(isset($_POST["contact"])) {	/*	making the error variable null before executing the form submission.	*/	$err = null;
	$email = cleanInput($_POST["email"]);
	$usermessage = cleanInput($_POST["message"]);		if($inbuilt_captcha == 1) {			if(PhpCaptcha::Validate($_POST["user_code"])) {				$is_captcha_true = true;			} else {				$is_captcha_true = false;			}		} else {			$resp = recaptcha_check_answer ($_setting['recaptcha_private'], $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);				if($resp->is_valid) {					$is_captcha_true = true;				} else {					$is_captcha_true = false;				}		}
			if(!empty($email) && !empty($usermessage)) {				if($is_captcha_true == true) {				$send_mail = contact_admin_email($email, $usermessage);					if($send_mail) {						$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Message Sent.")."</strong><br/>"._("You message has been sent successfully. We will revert back to you soon.")."</div>";
					} else {						$err = "<div class=\"alert\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Oops!")."</strong><br/>"._("Message sending failed. Please try again after some time.")."</div>";					}
				} else {					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Verification.")."</strong><br/>"._("Please fill in the verification field correctly.")."</div>";				}
			} else {				$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";			}
}admin_contact();include("../".USER_DIRECTORY."/footer.php");?>