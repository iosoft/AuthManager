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
include("../".MODS_DIRECTORY."/class.ip2country.php");
include("../".USER_DIRECTORY."/header.php");

$js = "<script src=\"{$website}/".JS_DIRECTORY."/jquery.hashchange.js\"></script>
<script src=\"{$website}/".JS_DIRECTORY."/admin.base.js\"></script>
<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.timeago.js\"></script>
<script type=\"text/javascript\">
$(function() {
	$(\".micro\").timeago();
});
</script>";

subheader(_("Manage User"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		if(isset($_GET["id"])) {
			$id = intval($_GET["id"]);
				if(!empty($id)) {
					if(isset($_POST["edituser"])) {
						$ban = intval($_POST["ban"]);
						$verified = intval($_POST["verified"]);
						$first_name = cleanInput($_POST["first_name"]);
						$last_name = cleanInput($_POST["last_name"]);
						$bio = cleanInput($_POST["bio"]);

							try {
								$update_user = "UPDATE `members` SET `first_name` = :first_name, `last_name` = :last_name, `bio` = :bio, `verified` = :verified, `banned` = :ban WHERE `id` = :id";
								$update_user_do = $db->prepare($update_user);
								$update_user_do->bindParam(':first_name', $first_name, PDO::PARAM_STR);
								$update_user_do->bindParam(':last_name', $last_name, PDO::PARAM_STR);
								$update_user_do->bindParam(':bio', $bio, PDO::PARAM_STR);
								$update_user_do->bindParam(':verified', $verified, PDO::PARAM_INT);
								$update_user_do->bindParam(':ban', $ban, PDO::PARAM_INT);
								$update_user_do->bindParam(':id', $id, PDO::PARAM_INT);
								$update_user_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

						$err = "<div class=\"alert alert-success\"><strong>"._("User Updated.")."</strong><br/>"._("User information has been updated successfully.")."</div>";
					}
					
					/*
					displaying the user info edit page to the admin.
					*/
					admin_edit_user($id);
				} else {
					echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
				}
		} else {
			echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
		}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>