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
include("init.php");
include(USER_DIRECTORY."/header.php");

$page_title = _("Error");
$_loader = false;

	if(isset($_GET["id"])) {
		$id = intval($_GET["id"]);
			if(!empty($id)) {
				try {
					$profile_query = "SELECT * FROM `members` WHERE `id` = :id";
					$profile_query_check = $db->prepare($profile_query);
					$profile_query_check->bindParam(':id', $id, PDO::PARAM_INT);
					$profile_query_check->execute();
					$ac = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
				} catch(PDOException $e) {
					$log->logError($e." - ".basename(__FILE__));
				}

					if(!empty($ac)) {
						$_loader = true;
						$fetch = $profile_query_check->fetch (PDO::FETCH_ASSOC);
						$user_fn = cleanInput($fetch['first_name']);
						$user_ln = cleanInput($fetch['last_name']);
						$page_title = $user_fn." ".$user_ln._("'s profile");
						$email_user = cleanInput($fetch['email']);
						$user_bio = cleanInput($fetch['bio']);
							if(empty($user_bio)) {
								$user_bio = "<h6>"._("The user has not entered any information in this section. You can check back some time later to see any changes made over here.")."</h6>";
							} else {
								$user_bio = nl2br($user_bio);
							}
						$user_join = cleanInput($fetch['join']);
						$user_access = cleanInput($fetch['access']);
						$user_fb = cleanInput($fetch['fb_id']);
							if(!empty($user_fb)) {
								$fb_link = "<a href=\"http://www.facebook.com/profile.php?id={$user_fb}\">"._("Facebook Profile")."</a>";
							} else {
								$fb_link = null;
							}
					}
			}
	}
subheader($page_title);

if($_loader == true) {
	/*
	displaying gravatar photo over here if email is associated with a gravatar account.
	*/
	$default = $website."/images/anonuser_50px.gif";
	$gravatar = new Gravatar($email_user, $default);
	$gravatar->size = 50;
?>
<div class="page-header no-border">
	<h1><img class="profilephoto thumbnail" src="<?php echo $gravatar->getSrc(); ?>" />&nbsp;&nbsp;<?php echo $user_fn." ".$user_ln; ?></h1>
</div>

<div class="row">
	<div class="span6">
		<div class="page-header no-border">
			<h3><?php echo _("About Me"); ?></h3>
		</div>
		<p><?php echo $user_bio; ?></p><br/>
	</div>
	<div class="span6">
		<div class="page-header no-border">
			<h3><?php echo _("Info"); ?></h3>
		</div>
		<table class="table table-bordered table-striped table-condensed">
			<tr><td class="first"><?php echo _("Name"); ?></td><td><?php echo $user_fn." ".$user_ln; ?></td></tr>
			<tr><td class="first"><?php echo _("Email"); ?></td><td><?php echo $email_user; ?></td></tr>
			<tr><td class="first"><?php echo _("Joined On"); ?></td><td><?php echo $user_join; ?></td></tr>
			<tr><td class="first"><?php echo _("Last Access"); ?></td><td><?php echo $user_access; ?></td></tr>
			<tr><td class="first"><?php echo _("Facebook"); ?></td><td><?php echo $fb_link; ?></td></tr>
		</table>
	</div>
</div><br/><br/>
<?php } else { ?>
<div class="page-header no-border">
	<h1><img class="profilephoto thumbnail" src="<?php echo $website."/images/anonuser_50px.gif"; ?>" />&nbsp;&nbsp;<?php echo _("Add User"); ?></h1>
</div>
<?php
}

include(USER_DIRECTORY."/footer.php")
?>