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

/*
show this page only if the user verification is enabled by the admin. Otherwise, show the page not found error.
*/
if($user_verification == 1) {
	subheader(_("Email verification"));
} else {
	subheader(_("Error"));
}

if($user_verification == 1) {
	if($sesslife == false) {
		if(isset($_GET["k"])) {
			$key = cleanInput($_GET["k"]);
				if(!empty($key)) {
					try {
						$q = "SELECT * FROM `members` WHERE `key` = :key ORDER BY `id` DESC LIMIT 1";
						$q_do = $db->prepare($q);
						$q_do->bindParam(':key', $key, PDO::PARAM_STR);
						$q_do->execute();
						$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
					} catch(PDOException $e) {
						$log->logError($e." - ".basename(__FILE__));
					}

						if(!empty($number)) {
							$f = $q_do->fetch (PDO::FETCH_ASSOC);
							$verify = intval($f['verified']);

							if(empty($verify)) {
								try {
									$update = "UPDATE `members` SET `verified` = 1 WHERE `key` = :key LIMIT 1";
									$update_do = $db->prepare($update);
									$update_do->bindParam(':key', $key, PDO::PARAM_STR);
									$update_do->execute();
								} catch(PDOException $e) {
									$log->logError($e." - ".basename(__FILE__));
								}

								echo "<div class=\"alert alert-success\"><strong>"._("Email verified.")."</strong><br/>"._("Your email has been verified successfully. You can now login and access your account.")."</div>";
							} else {
								echo "<div class=\"alert alert-error\"><strong>"._("Account active.")."</strong><br/>"._("The account associated with this email is already active. There is no need for activation.")."</div>";
							}
						} else {
							echo "<div class=\"alert alert-error\"><strong>"._("Verification is not valid.")."</strong><br/>"._("The email verification seems to be invalid. Please make sure that you are following the right activation link sent to your email.")."</div>";
						}
				} else {
					echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
				}
		} else {
			echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
		} 
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Active Session.")."</strong><br/>"._("You are already logged in to the website. This page cannot be accessed by a logged in user")."</div>";
	}
} else {
	echo "<div class=\"alert alert-error\"><strong>"._("Oops! Invalid Link.")."</strong><br/>"._("The page you are trying to access does not exist. Please make sure you are following the right link.")."</div>";
}

include("footer.php");
?>