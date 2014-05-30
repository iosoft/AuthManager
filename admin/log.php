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
$_loader = false;

/* checking the filesystem to know whether the log file is present or not. */
if(isset($_GET["id"])) {
	$id = cleanInput($_GET["id"]);
		if(!empty($id)) {
			$log_file = "../".MODS_DIRECTORY."/logs/{$id}";
				if(file_exists($log_file)) {
					$page_title = _("Log - ")."{$id}";
					$_loader = true;
				} else {
					$page_title = _("Error");
				}
		} else {
			$page_title = _("Error");
		}
} else {
	$page_title = _("Error");
}

include("functions.php");
include("../".USER_DIRECTORY."/header.php");
subheader($page_title);

if($sesslife == true) {
	if($is_admin == 1) {
		if($_loader == true) {
			echo "<div class=\"page-header\"><h1>{$id}</h1></div>";
			echo "<ul class=\"breadcrumb\">
			<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
			<li><a href=\"{$website}/".ADMIN_DIRECTORY."/sql-logs\">"._("SQL Logs")."</a> <span class=\"divider\">/</span></li>
			<li class=\"active\">{$id}</li>
			</ul>";

			$file_handle = fopen($log_file, "rb");
				if($file_handle) {
					echo "<fieldset>";
					echo "<div class=\"control-group\">";
					echo "<div class=\"controls\">";
						if(filesize($log_file) > 0) {
							echo "<textarea class=\"span12\" rows=\"18\" disabled>";
							echo fread($file_handle, filesize($log_file));
							echo "</textarea>";
						} else {
							echo "<div class=\"alert alert-error\"><strong>"._("Log File Empty.")."</strong><br/>"._("The log file you are trying to view does not have any content. It seems to be empty.")."</div>";
						}
					echo "</div>";
					echo "</div>";
					echo "</fieldset>";
				} else {
				
				}
			fclose($file_handle);
		} else {
			echo "<div class=\"page-header\"><h1>"._("Error")."</h1></div>";
			echo "<div class=\"alert alert-error\"><strong>"._("Oops!")."</strong><br/>"._("The log file you are trying to view does not exist.")."</div>";
		}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>