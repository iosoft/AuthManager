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
this is to stop the session before any headers are sent by the script.
*/
if($sesslife == true) {
	$session->stop();

		/*
		unset the cookie for the particular user whether it is set or not.
		*/
		if(isset($_COOKIE["pauser"]) && isset($_COOKIE["papass"])) {
			$time = time();
			setcookie("pauser", "", $time - 3600*24*10, "/");
			setcookie("papass", "", $time - 3600*24*10, "/");
		}

		/*
		remove the session variables for the facebook authentication.
		*/
		if(isset($_SESSION["code"])) {
			$_SESSION["code"] = null;
			$_SESSION["access_token"] = null;
		}
} else {
	echo "<meta http-equiv=\"Refresh\" content=\"0;url={$website}/\" />";
}
/*
ends the session over here and then send the headers from below.
*/

include("header.php");
subheader(_("Logout"));

echo "<center><div class=\"row\"><img src=\"{$website}/images/working.gif\" /><br/><br/>";
echo "</div></center>";
echo "<meta http-equiv=\"refresh\" content=\"1;url={$website}/\" />";

include("footer.php");
?>