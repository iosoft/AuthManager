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
defining directories for easy changes in future. You can change the directory names below but make sure to 
change the folder names before changing these values or else the script will stop functioning.
*/
define('USER_DIRECTORY', 'user');
define('JS_DIRECTORY', 'js');
define('MODS_DIRECTORY', 'modules');
define('LANG_DIRECTORY', 'languages');
define('STATIC_DIRECTORY', 'static');
define('ADMIN_DIRECTORY', 'admin');

/*
global settings for the website are fetched via this file. These settings are configurable from the admin panel.
*/
	try {
		$settings_query = $db->query("SELECT * FROM `settings` WHERE `autoload` = 'yes'");
	} catch(PDOException $e) {
		echo "<br/><p style=\"font-size:13px;font-family:arial;\"><strong>Error:</strong> ".$e."</p>";
		exit();
	}

	while($r = $settings_query->fetch (PDO::FETCH_ASSOC)) {
		$option = $r['option_name'];
		$_setting[$option] = $r['option_value'];
	}

	if(empty($_setting['website'])) {
		die("<br/><center><p style=\"font-size:13px;font-family:arial;\">Application <strong>error</strong> occured. Contact <strong>support@stitchapps.com</strong> for assistance.</p></center>");
	}

/*
fetch the analytics code only if it is enabled from the backend.
*/
if($_setting['analytics_enabled'] == 1) {
	$analytics_code = $_setting['analytics_code'];
} else {
	$analytics_code = null;
}

/*
if sending_email is not specified, then use the admin_email for sending mail to the users.
*/
if(empty($_setting['sending_email'])) {
	$_setting['sending_email'] = $_setting['admin_email'];
}

/*
defining two most commonly used variables in the application.
*/
$website = $_setting['website'];
$webtitle = $_setting['title'];
$inbuilt_captcha = $_setting['inbuilt_captcha'];
$user_verification = $_setting['user_verification'];
$sending_email = $_setting['sending_email'];
?>