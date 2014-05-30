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

$js = "<script src=\"{$website}/".JS_DIRECTORY."/jquery.hashchange.js\"></script>
<script src=\"{$website}/".JS_DIRECTORY."/admin.base.js\"></script>
<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.timeago.js\"></script>
<script type=\"text/javascript\">
$(function() {
	$(\".micro\").timeago();
});
</script>";

subheader(_("Edit Profile"), null, $js);

if($sesslife == true) {
	if(isset($_POST["editprofile"])) {
		$first_name = cleanInput($_POST["first_name"]);
		$last_name = cleanInput($_POST["last_name"]);
		$bio = cleanInput($_POST["bio"]);

		try {
			$update_user = "UPDATE `members` SET `first_name` = :first_name, `last_name` = :last_name, `bio` = :bio WHERE `id` = :userid";
			$update_user_do = $db->prepare($update_user);
			$update_user_do->bindParam(':first_name', $first_name, PDO::PARAM_STR);
			$update_user_do->bindParam(':last_name', $last_name, PDO::PARAM_STR);
			$update_user_do->bindParam(':bio', $bio, PDO::PARAM_STR);
			$update_user_do->bindParam(':userid', $userid, PDO::PARAM_INT);
			$update_user_do->execute();
		} catch(PDOException $e) {
			$log->logError($e." - ".basename(__FILE__));
		}

		$err = "<div class=\"alert alert-success\"><strong>"._("Profile Updated.")."</strong><br/>"._("Your profile has been updated successfully.")."</div>";
	}

	/*
	displaying the user info edit profile page.
	*/
	edit_profile();
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>