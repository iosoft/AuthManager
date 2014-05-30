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
include("functions.php");
include("../".USER_DIRECTORY."/header.php");

$js = "<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.timeago.js\"></script>
<script type=\"text/javascript\">
$(function() {
	$(\".micro\").timeago();
});
</script>";
subheader(_("SQL Logs"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		echo "<div class=\"page-header\"><h1>"._("SQL Logs")."</h1></div>";
		echo "<h6>"._("Options")." : <a href=\"{$website}/".ADMIN_DIRECTORY."/clear-logs\">"._("Clear SQL Logs")."</a></h6><br/>";
		echo "<ul class=\"breadcrumb\">
		<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
		<li class=\"active\">"._("SQL Logs")."</li>
		</ul>";

		echo "<table class=\"table table-condensed\">";
		echo "<thead><tr>
		<th>"._("ID")."</th>
		<th>"._("Filename")."</th>
		<th>"._("Size")."</th>
		<th>"._("Date")."</th>
		</tr></thead>";
		echo "<tbody>";

		/*
		setting $id to 1 to displaying numbering in the table.
		*/
		$id = 1;

		foreach(new DirectoryIterator("../".MODS_DIRECTORY."/logs") as $file) {
			if($file->isFile() === true) {
				$filename = htmlentities($file->getBasename());
				$replace = array("log_", ".txt");
				$date = str_replace($replace, "", $filename);
				$size = ceil($file->getSize() / 1000);

				echo "<tr>";
				echo "<td>".$id++."</td>";
				echo "<td><a href=\"{$website}/".ADMIN_DIRECTORY."/log/{$filename}\">".$filename."</a></td>";
				echo "<td>".$size." KB</td>";
				echo "<td><abbr class=\"micro\" title=\"".$date."\"></abbr></td>";
				echo "</tr>";
			}
		}

		echo "</tbody>";
		echo "</table>";
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>