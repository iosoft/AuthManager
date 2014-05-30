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

/* extra js file to be included for the show password option (jquery). */
$js = "<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.showpassword.js\"></script>
<script type=\"text/javascript\">
$(function() {
	$('#new_password').showPassword('#showpass');
});
</script>";
subheader(_("Change Password"), null, $js);

if($sesslife == true) {
	echo "<div class=\"page-header\"><h1>"._("Change Password")."</h1></div>";
	if(isset($_POST["changepassword"])) {
		$current_password = cleanInput($_POST["current_password"]);
		$new_password = cleanInput($_POST["new_password"]);

		if(!empty($current_password) && !empty($new_password)) {
			/* changing the current password to the encrypted format. */
			$current_password = generate_encrypted_password($current_password);
				if($current_password == $userpass) {
					$new_password = generate_encrypted_password($new_password);
					
					try {
						$q = "UPDATE `members` SET `password` = :new_password WHERE `id` = :userid";
						$q_do = $db->prepare($q);
						$q_do->bindParam(':new_password', $new_password, PDO::PARAM_STR);
						$q_do->bindParam(':userid', $userid, PDO::PARAM_INT);
						$confirm_do = $q_do->execute();
					} catch(PDOException $e) {
						$log->logError($e." - ".basename(__FILE__));
					}

					if(!empty($confirm_do)) {
						echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/logout\">";
					} else {
						$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Unable to process.")."</strong><br/>"._("We are unable to process your request at this time. Please try again later.")."</div>";
					}
				} else {
					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Password mismatch.")."</strong><br/>"._("Your current password does not match with the one stored with us.")."</div>";
				}
		} else {
			$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
		}
	}

	am_showChangePassword();

} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("footer.php");
?>