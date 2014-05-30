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
include("../../init.php");
require("php-captcha.inc.php");
$aFonts = array("fonts/AHGBold.ttf", "fonts/Vera.ttf", "fonts/VeraBd.ttf", "fonts/VeraBI.ttf", "fonts/VeraMoIt.ttf");
$oVisualCaptcha = new PhpCaptcha($aFonts, 200, 60);
$oVisualCaptcha->Create();
?>