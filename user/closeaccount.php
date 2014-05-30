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
subheader(_("Close Account"));

if($sesslife == true) {
	echo "<div class=\"page-header\"><h1>"._("Close Account")."</h1></div>";
		if(isset($_POST["closeaccount"])) {
			/*
			deleting the user access information from the application.
			*/
			try {
				$q = "DELETE FROM `access` WHERE `userid` = :userid";
				$q_do = $db->prepare($q);
				$q_do->bindParam(':userid', $userid, PDO::PARAM_INT);
				$q_do->execute();
			} catch(PDOException $e) {
				$log->logError($e." - ".basename(__FILE__));
			}

			/*
			deleting the user account from the application.
			*/
			try {
				$q = "DELETE FROM `members` WHERE `id` = :userid";
				$q_do = $db->prepare($q);
				$q_do->bindParam(':userid', $userid, PDO::PARAM_INT);
				$q_do->execute();
			} catch(PDOException $e) {
				$log->logError($e." - ".basename(__FILE__));
			}

			$session->stop();
			echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}\" />";
		} else {
			echo "<br/><p>"._("You are about to close your account. Closing your account will delete your profile data, and any other data associated with your account.");
			echo _(" This process is completely irreversible and you will have to register your account again on <strong>{$webtitle}</strong> to enjoy our services. Please confirm your action by clicking on the button below.")."</p><br/>";
			echo "<form method=\"POST\" action=\"{$website}/".USER_DIRECTORY."/closeaccount\">
			<p><input type=\"submit\" name=\"closeaccount\" value=\""._("Permanently close my account")."\" class=\"btn btn-danger\" />&nbsp;&nbsp;<input type=\"button\" value=\"Cancel\" class=\"btn\" onclick=\"window.location.href='{$website}/'\" /></p>";
			echo "</form><br/>";
		}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("footer.php");
?>