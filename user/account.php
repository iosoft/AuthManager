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
subheader(_("Account"));

if($sesslife == true) {
	echo "<div class=\"page-header\"><h1>"._("Account")."</h1></div>";
	echo "<div class=\"row\">";
	echo "<div class=\"span6\">";

		try {
			$q = "SELECT `email`, `key`, `join`, `access` FROM `members` WHERE `id` = :userid";
			$q_do = $db->prepare($q);
			$q_do->bindParam(':userid', $userid, PDO::PARAM_INT);
			$q_do->execute();
			$q_num = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			$log->logError($e." - ".basename(__FILE__));
		}

		if(!empty($q_num)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			$a_email = cleanInput($f['email']);
			$a_key = cleanInput($f['key']);
			$a_join = cleanInput($f['join']);
			$a_access = cleanInput($f['access']);
?>
			<table class="table table-striped table-bordered table-condensed">
			<tr><td class="first"><?php echo _("Name"); ?></td><td><?php echo $first_name." ".$last_name; ?></td></tr>
			<tr><td class="first"><?php echo _("Email"); ?></td><td><?php echo $a_email; ?></td></tr>
			<tr><td class="first"><?php echo _("Password"); ?></td><td><a href="<?php echo $website."/".USER_DIRECTORY; ?>/changepassword"><strong><?php echo _("Click here to change your password"); ?></strong></a></td></tr>
			<tr><td class="first"><?php echo _("Key"); ?></td><td><?php echo $a_key; ?></td></tr>
			<tr><td class="first"><?php echo _("Joined"); ?></td><td><?php echo $a_join; ?></td></tr>
			<tr><td class="first"><?php echo _("Last Access"); ?></td><td><?php echo $a_access; ?></td></tr>
			</table>
<?php
		} else {
			echo "<div class=\"alert alert-error\"><strong>"._("Unable to retrieve.")."</strong><br/>"._("Oops! We are unable to retrieve your account information at the moment. Please try again later.")."</div>";
		}
		
		echo "</div>";
		
		/*
		info text and link for closing account.
		*/
		echo "<div class=\"span5 offset1\">";
		echo "<h4>"._("Close Account")."</h4>";
		echo "<p>"._("If you wish to close your account permanently, then you can do so by clicking the button below.")."</p><br/>";
		echo "<a href=\"{$website}/".USER_DIRECTORY."/closeaccount\" class=\"btn btn-danger\">"._("Close your account")."</a>";
		echo "</div></div><br/>";
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("footer.php");
?>