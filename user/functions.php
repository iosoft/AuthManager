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

/*
all the functions for the script are included in this file. do not modify this file until you know very well what you are doing.
*/
if(!function_exists("gettext")) {
	function _($value) {
		return $value;
	}
}

function current_url() {
	return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function cleanInput($input) {
	$search = array(
	'@<script[^>]*?>.*?</script>@si',   /* strip out javascript */
	'@<[\/\!]*?[^<>]*?>@si',            /* strip out HTML tags */
	'@<style[^>]*?>.*?</style>@siU',    /* strip style tags properly */
	'@<![\s\S]*?--[ \t\n\r]*>@'         /* strip multi-line comments */
	);

	$output = preg_replace($search, '', $input);
	return $output;
}

/*
core functions for different processes.
*/
function isValidEmail($email) {
	if(preg_match("/^(\w+(([.-]\w+)|(\w.\w+))*)\@(\w+([.-]\w+)*\.\w+$)/",$email)) {
		return true;
	} else {
		return false;
	}
}

function createRandomPassword() {
	$chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = "";
	while ($i <= 7) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

function getGuid() {
	$strGuid = md5(uniqid(rand(), 1));
	return $strGuid;
}

/*
two salts are used to securely encrypt the password. It encrypts the password in such a way that decryption of the password is not possible. Moreover, a user password reset is used in case the user forgets its password. There is no way to recover the password. It is advised that you should not change the salt once your site is live.
*/
define('SALT_ONE', 'some_random_123_collection_&$^%_of_stuff');
define('SALT_TWO', 'another_random_%*!_collection_ANbu_of_stuff');
function generate_encrypted_password($str) {
	$new_pword = '';

		if(defined('SALT_ONE')):
			$new_pword .= md5(SALT_ONE);
		endif;

		$new_pword .= md5($str);

		if(defined('SALT_TWO')):
			$new_pword .= md5(SALT_TWO);
		endif;

	return substr($new_pword, strlen($str), 40);
}

/*
functions for the email templates. Different templates such as new user email, forget password etc are included over here.
*/
function newuser_email($email) {
	global $db, $log, $webtitle, $website, $sending_email;
	$slug = "new_user";

	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			
			$subject = cleanInput($f['subject']);
			$subject = str_replace("{webtitle}", $webtitle, $subject);

			$message = $f['message'];
			$search = array("{webtitle}", "{website}");
			$replace = array($webtitle, $website);
			$message = str_replace($search, $replace, $message);
		}

	$headers = "From: ".$sending_email. "\r\n";
	$headers .= "Reply-To: ".$sending_email. "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	/* $_lang for email templates */
	/* email template for the "New user welcome" function. */

	$_lang['email_new_user_welcome'] = $message;
	$mailsent = mail($email, $subject, $_lang['email_new_user_welcome'], $headers);
}

function verification_email($email, $key) {
	global $db, $log, $webtitle, $website, $sending_email;
	$slug = "verify_email";

	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);

			$subject = cleanInput($f['subject']);
			$subject = str_replace("{webtitle}", $webtitle, $subject);

			$message = $f['message'];
			$search = array("{webtitle}", "{website}", "{key}", "USER_DIRECTORY");
			$replace = array($webtitle, $website, $key, USER_DIRECTORY);
			$message = str_replace($search, $replace, $message);
		}

	$headers = "From: ".$sending_email. "\r\n";
	$headers .= "Reply-To: ".$sending_email. "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	/* $_lang for email templates */
	/* email template for the "User verification" function. */

	$_lang['email_user_verification'] = $message;
	$mailsent = mail($email, $subject, $_lang['email_user_verification'], $headers);
}

function forgotpass_email($email, $reset_key) {
	global $db, $log, $webtitle, $website, $sending_email;
	$slug = "forgot_pass";

	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			
			$subject = cleanInput($f['subject']);
			$subject = str_replace("{webtitle}", $webtitle, $subject);

			$message = $f['message'];
			$search = array("{email}", "{webtitle}", "{website}", "{reset_key}", "USER_DIRECTORY");
			$replace = array($email, $webtitle, $website, $reset_key, USER_DIRECTORY);
			$message = str_replace($search, $replace, $message);
		}

	$headers = "From: ".$sending_email. "\r\n";
	$headers .= "Reply-To: ".$sending_email. "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	/* $_lang for email templates */
	/* email template for the "Forgot Password" function. */

	$_lang['email_forgot_password'] = $message;
	$mailsent = mail($email, $subject, $_lang['email_forgot_password'], $headers);
}

function newpass_email($email, $new_password) {
	global $db, $log, $webtitle, $website, $sending_email;
	$slug = "new_pass";

	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);

			$subject = cleanInput($f['subject']);
			$subject = str_replace("{webtitle}", $webtitle, $subject);

			$message = $f['message'];
			$search = array("{email}", "{webtitle}", "{website}", "{new_password}");
			$replace = array($email, $webtitle, $website, $new_password);
			$message = str_replace($search, $replace, $message);
		}

	$headers = "From: ".$sending_email. "\r\n";
	$headers .= "Reply-To: ".$sending_email. "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	/* $_lang for email templates */
	/* email template for the "New Password" function. */

	$_lang['email_new_password'] = $message;
	$mailsent = mail($email, $subject, $_lang['email_new_password'], $headers);
}

function contact_admin_email($email, $message) {
	global $webtitle, $website, $_setting;

	$subject = "Message - {$webtitle}";
	$headers = "From: ".$email. "\r\n";
	$headers .= "Reply-To:" .$email. "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";

	$_lang['email_contact_admin'] = "Hi,<br/><br/>A message has been sent via {$webtitle} contact form using {$email} as email.<br/><br/>
	Please find the message below:<br/><br/>
	{$message}<br/><br/>
	--<br/>
	Thanks<br/>
	{$webtitle} Staff<br/>
	<a href=\"{$website}/\">{$website}</a>";

	$mailsent = mail($_setting['admin_email'], $subject, $_lang['email_contact_admin'], $headers);
		if($mailsent) {
			return true;
		} else {
			return false;
		}
}

/*
functions for displaying various screens such as login, register etc.
*/
function am_showLogin() {
	global $_setting, $website, $user_verification;
?>
<div class="page-header">
	<h1><?php echo _("Login"); ?></h1>
</div>
<div class="row">
<div class="span6">
<?php echo $_SESSION["error"]; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/process/reg">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="email"><?php echo _("Email"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="email" name="username" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="password"><?php echo _("Password"); ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge" id="password" name="password" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="autologin"><?php echo _("Remember Me"); ?></label>
		<div class="controls">
			<label class="checkbox">
				<input type="checkbox" id="autologin" name="autologin" value="1"> <?php echo _("Don't check this option on public computers."); ?>
			</label>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<small><a href="<?php echo $website."/".USER_DIRECTORY; ?>/forgot"><?php echo _("Forgot Password?"); ?></a></small>
<?php
/* if user verification is disabled by admin then hide this resend verification link from the users. */
if($user_verification == 1) {
	echo "<br/><small><a href=\"{$website}/".USER_DIRECTORY."/resend\">". _("Resend Confirmation Email"). "</a></small>";
}
?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" name="login"><?php echo _("Login"); ?></button>
		</div>
	</div>
</fieldset>
</form>
</div>

<?php if($_setting['enable_facebook'] == 1) { ?>
<div class="span4 offset1">
	<h3><?php echo _("Go Social"); ?></h3><br/>
	<p><?php echo _("Select from the following social media to login to the website using your account."); ?></p><br/>
	<a href="https://www.facebook.com/dialog/oauth?client_id=<?php echo $_setting['facebook_api']; ?>&redirect_uri=<?php echo urlencode($website."/"); ?>&scope=email"><img src="<?php echo $website; ?>/images/fb_connect.png" /></a>
</div><br/>
<?php } ?>	
</div>

<?php
}

function showcaptcha() {
	global $_setting, $website, $inbuilt_captcha;
?>
<div class="page-header">
	<h1><?php echo _("Verify Login"); ?></h1>
</div>
<div class="row">
<div class="span6">
<?php echo $_SESSION["error"]; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/process/verify">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="email"><?php echo _("Email"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="email" name="username" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="password"><?php echo _("Password"); ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge" id="password" name="password" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="autologin"><?php echo _("Remember Me"); ?></label>
		<div class="controls">
			<label class="checkbox">
				<input type="checkbox" id="autologin" name="autologin" value="1"> <?php echo _("Don't check this option on public computers."); ?>
			</label>
		</div>
	</div>
<?php
/*
use the captcha as per the admin choice. Two captcha options are included. one is the inbuilt captcha and other is using the reCAPTCHA online captcha service.
*/
	echo "<div class=\"control-group\">
		<label class=\"control-label\" for=\"autologin\">"._("Are you Human?")."</label>
		<div class=\"controls\">";
			if($inbuilt_captcha != 1) {
				echo recaptcha_get_html($_setting['recaptcha_public']);
			} else {
				echo "<img src=\"{$website}/".MODS_DIRECTORY."/captcha/visual-captcha\" width=\"200\" height=\"60\" alt=\"Visual CAPTCHA\" /><br/><input type=\"text\" name=\"user_code\" id=\"code\" autocomplete=\"off\" />";
			}
		echo "</div>
	</div>";
?>
	<div class="control-group">
		<div class="controls">
			<small><a href="<?php echo $website."/".USER_DIRECTORY; ?>/forgot"><?php echo _("Forgot Password?"); ?></a></small>
<?php
/*
if user verification is disabled by admin then hide this resend verification link from the users.
*/
if($user_verification == 1) {
	echo "<small><a href=\"{$website}/".USER_DIRECTORY."/resend\">". _("Resend Confirmation Email"). "</a></small>";
}
?>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn btn-primary" name="login" value="<?php echo _("Login"); ?>">
		</div>
	</div>
</fieldset>
</form>
</div>
</div>

<?php
}

function am_showForgot() {
	global $_setting, $website, $webtitle, $inbuilt_captcha, $err;
?>
<div class="page-header">
	<h1><?php echo _("Forgot Password"); ?></h1>
</div>
<div class="row">
<div class="span6">
<?php echo $err; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/forgot">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="email"><?php echo _("Email"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="email" name="email" autocomplete="off">
			<span class="help-block"><small>(<?php echo _("Please enter your email address over here."); ?>)</small></span>
		</div>
	</div>
<?php
/*
use the captcha as per the admin choice. two captcha options are included. one is the inbuilt captcha and other is using the reCAPTCHA online captcha service.
*/
	echo "<div class=\"control-group\">
		<label class=\"control-label\" for=\"autologin\">"._("Are you Human?")."</label>
		<div class=\"controls\">";
			if($inbuilt_captcha != 1) {
				echo recaptcha_get_html($_setting['recaptcha_public']);
			} else {
				echo "<img src=\"{$website}/".MODS_DIRECTORY."/captcha/visual-captcha\" width=\"200\" height=\"60\" alt=\"Visual CAPTCHA\" /><br/><input type=\"text\" class=\"input-xlarge\" name=\"user_code\" id=\"code\" autocomplete=\"off\" />";
			}
		echo "</div>
	</div>";
?>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn btn-primary" name="forgot" value="<?php echo _("Reset Password"); ?>">
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<small><?php echo _("Already on")." ".$webtitle; ?>? <a href="<?php echo $website."/".USER_DIRECTORY; ?>/login"><?php echo _("Login Here"); ?></a></small>
		</div>
	</div>
</fieldset>
</form>
</div>
</div>

<?php
}

function am_showResend() {
	global $_setting, $website, $webtitle, $inbuilt_captcha, $err;
?>
<div class="page-header">
	<h1><?php echo _("Resend confirmation email"); ?></h1>
</div>
<div class="row">
<div class="span6">
<?php echo $err; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/resend">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="email"><?php echo _("Email"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="email" name="email" autocomplete="off">
			<span class="help-block"><small>(<?php echo _("Please enter your email address over here."); ?>)</small></span>
		</div>
	</div>
<?php
/*
use the captcha as per the admin choice. two captcha options are included. one is the inbuilt captcha and other is using the reCAPTCHA online captcha service.
*/
	echo "<div class=\"control-group\">
		<label class=\"control-label\" for=\"autologin\">"._("Are you Human?")."</label>
		<div class=\"controls\">";
			if($inbuilt_captcha != 1) {
				echo recaptcha_get_html($_setting['recaptcha_public']);
			} else {
				echo "<img src=\"{$website}/".MODS_DIRECTORY."/captcha/visual-captcha\" width=\"200\" height=\"60\" alt=\"Visual CAPTCHA\" /><br/><input type=\"text\" class=\"input-xlarge\" name=\"user_code\" id=\"code\" autocomplete=\"off\" />";
			}
		echo "</div>
	</div>";
?>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn btn-primary" name="resend" value="<?php echo _("Resend confirmation email"); ?>">
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<small><?php echo _("Already on")." ".$webtitle; ?>? <a href="<?php echo $website."/".USER_DIRECTORY; ?>/login"><?php echo _("Login Here"); ?></a></small>
		</div>
	</div>
</fieldset>
</form>
</div>
</div>

<?php
}

function am_showRegister() {
	global $_setting, $website, $webtitle, $inbuilt_captcha, $err;
?>
<div class="page-header">
	<h1><?php echo _("Register"); ?></h1>
</div>
<div class="row">
<div class="span6">
<?php echo $err; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/register">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="first_name"><?php echo _("First Name"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="first_name" name="first_name" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="last_name"><?php echo _("Last Name"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="last_name" name="last_name" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="email"><?php echo _("Email"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="email" name="email" autocomplete="off">
			<span class="help-block"><small><?php echo _("(This will be your username.)"); ?></small></span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="pass"><?php echo _("Password"); ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge" id="pass" name="pass" autocomplete="off">
			<span class="help-block">
				<label class="checkbox">
					<input type="checkbox" id="showpass"><small><?php echo _("Show Password"); ?></small>
				</label>
			</span>
		</div>
	</div>
<?php
/*
use the captcha as per the admin choice. two captcha options are included. one is the inbuilt captcha and other is using the reCAPTCHA online captcha service.
*/
	echo "<div class=\"control-group\">
		<label class=\"control-label\" for=\"autologin\">"._("Are you Human?")."</label>
		<div class=\"controls\">";
			if($inbuilt_captcha != 1) {
				echo recaptcha_get_html($_setting['recaptcha_public']);
			} else {
				echo "<img src=\"{$website}/".MODS_DIRECTORY."/captcha/visual-captcha\" width=\"200\" height=\"60\" alt=\"Visual CAPTCHA\" /><br/><input type=\"text\" class=\"input-xlarge\" name=\"user_code\" id=\"code\" autocomplete=\"off\" />";
			}
		echo "</div>
	</div>";
?>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn btn-primary" name="join" value="<?php echo _("Join Now"); ?>">
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<small><?php echo _("Already on")." ".$webtitle; ?>? <a href="<?php echo $website."/".USER_DIRECTORY; ?>/login"><?php echo _("Login Here"); ?></a></small>
		</div>
	</div>
</fieldset>
</form>
</div>

<div class="span4 offset1">
	<h3><?php echo _("Unrestricted Access"); ?></h3><br/>
	<p><?php echo _("Sign up to the website and get total access our services without any restrictions. Please make sure you fill in all the fields in the form to register."); ?></p>
</div>
</div>

<?php
}

function am_showChangePassword() {
	global $website, $err;
?>
<div class="row">
<div class="span6">
<?php echo $err; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/changepassword">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="current_password"><?php echo _("Current Password"); ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge" id="current_password" name="current_password" autocomplete="off">
			<span class="help-block">
				<small>
					(<?php echo _("Please enter your current password over here."); ?>)
				</small>
			</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="new_password"><?php echo _("New Password"); ?></label>
		<div class="controls">
			<input type="password" class="input-xlarge" id="new_password" name="new_password" autocomplete="off">
			<span class="help-block">
				<label class="checkbox">
					<input type="checkbox" id="showpass"><small><?php echo _("Show Password"); ?></small>
				</label>
			</span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn btn-primary" name="changepassword" value="<?php echo _("Save Changes"); ?>">
		</div>
	</div>
</fieldset>
</form>
</div>

<div class="span5 offset1">
	<h4><?php echo _("Info"); ?></h4>
	<p><?php echo _("Once your password is changed successfully, you will be logged out of the website and will be required to sign in again with your new password."); ?></p>
</div><br/>
</div>

<?php
}

function get_username($userid) {
	global $db, $log;

	try {
		$q = "SELECT `first_name`, `last_name` FROM `members` WHERE `id` = :userid";
		$query = $db->prepare($q);
		$query->bindParam(':userid', $userid, PDO::PARAM_INT);
		$query->execute();
		$n2 = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}
	
		if(!empty($n2)) {
			$f = $query->fetch (PDO::FETCH_ASSOC);
			$first_name = cleanInput($f['first_name']);
			$last_name = cleanInput($f['last_name']);
			$name = $first_name." ".$last_name;
		}
		
	return $name;
}

function admin_contact() {
	global $website, $_setting, $inbuilt_captcha, $err;
?>
<div class="page-header">
	<h1><?php echo _("Contact Us"); ?></h1>
</div>
<div class="row">
<div class="span6">
<?php echo $err; ?>
<form class="form-horizontal" method="POST" action="<?php echo $website."/".STATIC_DIRECTORY; ?>/contact">
<fieldset>
	<div class="control-group">
		<label class="control-label" for="email"><?php echo _("Email"); ?></label>
		<div class="controls">
			<input type="text" class="input-xlarge" id="email" name="email" autocomplete="off">
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="message"><?php echo _("Message"); ?></label>
		<div class="controls">
			<textarea class="span4" id="message" name="message" rows="10"></textarea>
		</div>
	</div>
<?php
/*
use the captcha as per the admin choice. two captcha options are included. one is the inbuilt captcha and other is using the reCAPTCHA online captcha service.
*/
	echo "<div class=\"control-group\">
		<label class=\"control-label\" for=\"autologin\">"._("Are you Human?")."</label>
		<div class=\"controls\">";
			if($inbuilt_captcha != 1) {
				echo recaptcha_get_html($_setting['recaptcha_public']);
			} else {
				echo "<img src=\"{$website}/".MODS_DIRECTORY."/captcha/visual-captcha\" width=\"200\" height=\"60\" alt=\"Visual CAPTCHA\" /><br/><input type=\"text\" class=\"input-xlarge\" name=\"user_code\" id=\"code\" autocomplete=\"off\" />";
			}
		echo "</div>
	</div>";
?>
	<div class="control-group">
		<div class="controls">
			<input type="submit" class="btn btn-primary" name="contact" value="<?php echo _("Send Message"); ?>">
		</div>
	</div>
</fieldset>
</form>
</div>

<div class="span3 offset3">
<h4><?php echo _("Reach Us"); ?></h4>
<p><?php echo _("You can also reach us via following means:"); ?></p>
<small><?php echo _("support@stitchapps.com")."<br/>"._("Mobile: +91 9871084893"); ?></small>
</div>
</div>

<?php
}

function edit_profile() {
	global $db, $err, $log, $website, $userid;

	try {
		$sql = "SELECT * FROM `members` WHERE `id` = :userid";
		$sql_do = $db->prepare($sql);
		$sql_do->bindParam(':userid', $userid, PDO::PARAM_INT);
		$sql_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $sql_do->fetch(PDO::FETCH_ASSOC);
			$email_user = cleanInput($f['email']);
			$first_name = cleanInput($f['first_name']);
			$last_name = cleanInput($f['last_name']);
			$joined_on = cleanInput($f['join']);
			$last_access = cleanInput($f['access']);
			$user_bio = cleanInput($f['bio']);
			
			/*
			displaying gravatar photo over here if email is associated with a gravatar account.
			*/
			$default = $website."/images/anonuser_50px.gif";
			$gravatar = new Gravatar($email_user, $default);
			$gravatar->size = 50;
?>
	<div class="page-header no-border">
		<h1><img class="profilephoto thumbnail" src="<?php echo $gravatar->getSrc(); ?>" />&nbsp;&nbsp;<?php echo $first_name." ".$last_name; ?></h1>
	</div>

	<div class="tabs-left">
		<ul class="nav nav-tabs" id="usermanage">
			<li class="active"><a href="#general" data-toggle="tab"><i class="icon-cog"></i> <?php echo _("General"); ?></a></li>
			<li><a href="#profile" data-toggle="tab"><i class="icon-user"></i> <?php echo _("Profile"); ?></a></li>
		</ul>

	<form class="form-horizontal" method="POST" action="<?php echo $website."/".USER_DIRECTORY; ?>/editprofile">
	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<fieldset>
				<legend><?php echo _("General"); ?></legend>
				<?php echo $err; ?>
				<div class="control-group">
					<label class="control-label" for="first_name"><?php echo _("First Name"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="first_name" name="first_name" value="<?php echo $first_name; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="last_name"><?php echo _("Last Name"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="last_name" name="last_name" value="<?php echo $last_name; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email"><?php echo _("Email"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="email" name="email" value="<?php echo $email_user; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="pass"><?php echo _("Password"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="pass" name="pass" value="<?php echo $f['password']; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="join"><?php echo _("Joined On"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="join" name="join" value="<?php echo $joined_on; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="access"><?php echo _("Last Access"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="access" name="access" value="<?php echo $last_access; ?>" disabled>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="tab-pane" id="profile">
			<fieldset>
				<legend><?php echo _("Profile"); ?></legend>
				<?php echo $err; ?>
				<div class="control-group">
					<label class="control-label" for="bio"><?php echo _("Bio"); ?></label>
					<div class="controls">
						<textarea class="input-xxlarge" id="bio" name="bio" rows="8"><?php echo $user_bio; ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" name="editprofile" value="<?php echo _("Update Profile"); ?>">
		</div>
	</form>
	</div>
<?php } else {
			echo "<div class=\"alert alert-error\"><strong>"._("Oops!")."</strong><br/>"._("We are unable to find the user in our system. You can still try again later.")."</div>";
		}
}
?>