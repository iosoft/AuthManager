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
/* extra js file to be included for the show password option (jquery). */
$js = "<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.showpassword.js\"></script>
<script type=\"text/javascript\">
$(function() {
$('#pass').showPassword('#showpass');
});
</script>";
subheader(_("Register"), null, $js);

if($sesslife == false) {
	/*
	this is to ensure that the user session is false. If it is true, throw an error.
	*/
	if(isset($_POST["join"])) {
		/*
		input sanitization class to filter the user input and show errors if they contain malicious entry.
		*/
		$first_name = cleanInput($_POST["first_name"]);
		$last_name = cleanInput($_POST["last_name"]);
		$email = cleanInput($_POST["email"]);
		$password = cleanInput($_POST["pass"]);

		/*
		if email and password are not empty. If they are show an error and display the form.
		*/
		if(!empty($email) && !empty($password) && !empty($first_name) && !empty($last_name)) {
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
					if(isValidEmail($email)) {
						try {
							$query = "SELECT * FROM `members` WHERE `email` = :email";
							$check_query = $db->prepare($query);
							$check_query->bindParam(':email', $email, PDO::PARAM_STR);
							$check_query->execute();
							$n = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
						} catch(PDOException $e) {
							$log->logError($e." - ".basename(__FILE__));
						}

						if(!$n) {
							/*
							getGuid() function generates a random unique 32 character unique key.
							*/
							$key = getGuid();

								if($user_verification == 1) {
									$verified = 0;
								} else {
									$verified = 1;
								}

							$join = date("Y-m-d H:i:s");

							/*
							Here the user password gets encrypted using a secure algorithm.
							*/
							$password = generate_encrypted_password($password);

							try {
								$create_user_query = "INSERT INTO `members`(`first_name`, `last_name`, `password` ,`email` ,`key` ,`verified` ,`join`) VALUE(:first_name, :last_name, :password, :email, :key, :verified, :join)";
								$create_user = $db->prepare($create_user_query);
								$create_user->bindParam(':first_name', $first_name, PDO::PARAM_STR);
								$create_user->bindParam(':last_name', $last_name, PDO::PARAM_STR);
								$create_user->bindParam(':password', $password, PDO::PARAM_STR);
								$create_user->bindParam(':email', $email, PDO::PARAM_STR);
								$create_user->bindParam(':key', $key, PDO::PARAM_STR);
								$create_user->bindParam(':verified', $verified, PDO::PARAM_INT);
								$create_user->bindParam(':join', $join, PDO::PARAM_STR);
								$create_user->execute();

									if($user_verification == 1) {
										verification_email($email, $key);
										$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Congrats!")."</strong><br/>"._("You have been registered successfully on the website.")."<br/>"._("You will receive an activation email shortly. Please verify your email address to access our services.")."</div>";
									} else {
										newuser_email($email);
										$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Congrats!")."</strong><br/>"._("You have been registered successfully on the website.")."</div>";
									}
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							am_showRegister();
						} else {
							$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Email already exists.")."</strong><br/>"._("This email is already registered with us.")."</div>";
							am_showRegister();
						}
					} else {
						$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Email.")."</strong><br/>"._("Please enter a valid email address in the email field.")."</div>";
						am_showRegister();
					}
				} else {
					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Verification.")."</strong><br/>"._("Please fill in the verification field correctly.")."</div>";
					am_showRegister();
				}
		} else {
			$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
			am_showRegister();
		}
	} else {
		am_showRegister();
	}
} else {
	echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
}

include("footer.php");
?>