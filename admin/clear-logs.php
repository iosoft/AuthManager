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
include("../".USER_DIRECTORY."/header.php");
subheader(_("Clear SQL Logs"));

if($sesslife == true) {
	if($is_admin == 1) {
			echo "<div class=\"page-header\"><h1>"._("Clear SQL Logs")."</h1></div>";
				if(isset($_POST["clearlogs"])) {
					/*
					getting the date to make sure that the current date's log file is not deleted.
					*/
					$log_date = date("Y-m-d");
					
					/*
					deleting all sql logs present on the system.
					*/
					foreach(new DirectoryIterator("../".MODS_DIRECTORY."/logs") as $fileInfo) {
						if(!$fileInfo->isDot() && $fileInfo->getFilename() != "log_{$log_date}.txt") {
							unlink("../".MODS_DIRECTORY."/logs/".$fileInfo->getFilename());
						}
					}
					echo "<div class=\"alert alert-success\"><strong>"._("Logs Deleted.")."</strong><br/>"._("All application logs have been deleted from the system.")."</div>";
				} else {
					echo "<br/><p>"._("You are about to delete all application SQL logs from the system. Please note that this process is irreversible and logs once deleted cannot be recovered. Please be sure before clicking the button below to proceed further.")."</p><br/>";
					echo "<form method=\"POST\" action=\"{$website}/".ADMIN_DIRECTORY."/clear-logs\">
					<p><input type=\"submit\" name=\"clearlogs\" value=\""._("Clear all SQL logs")."\" class=\"btn btn-danger\">&nbsp;&nbsp;<input type=\"button\" value=\"Cancel\" class=\"btn\" onclick=\"window.location.href='{$website}/".ADMIN_DIRECTORY."/settings'\"></p>";
					echo "</form><br/>";
		}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>