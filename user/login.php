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

	/* include required files as per the admin option of inbuilt captcha enabled or not. */
	if($inbuilt_captcha == 1) {
		include("../".MODS_DIRECTORY."/captcha/php-captcha.inc.php");
	} else {
		include("../".MODS_DIRECTORY."/recaptchalib.php");
	}

include("header.php");
subheader(_("Login"));

	if(isset($_GET["r"])) {
		$r = cleanInput($_GET["r"]);
	}

		if($r == "verify") {
			if($sesslife == false) {
				showcaptcha();
			} else {
				echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
			}
		} elseif($r == "reg") {
			if($sesslife == false) {
				am_showLogin();
			} else {
				echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
			}
		} else {
			if($sesslife == false) {
				/* show the login box if the session is false. */
				am_showLogin();
			} else {
				/* error message is shown if the user visits this page even after logging in. */
				echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
			}
		}

include("footer.php");
?>