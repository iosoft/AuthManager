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

$js = "<script src=\"{$website}/".JS_DIRECTORY."/jquery.hashchange.js\"></script>
<script src=\"{$website}/".JS_DIRECTORY."/admin.base.js\"></script>";
subheader(_("Add User"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		if(isset($_POST["adduser"])) {
			/*
			input sanitization class to filter the user input and show errors if they contain malicious entry.
			*/
			$first_name = cleanInput($_POST["first_name"]);
			$last_name = cleanInput($_POST["last_name"]);
			$password = cleanInput($_POST["pass"]);
			$email = cleanInput($_POST["email"]);
			$bio = cleanInput($_POST["bio"]);
			$verified = intval($_POST["verified"]);
			
			$join = date("Y-m-d H:i:s");
			/*
			getGuid() function generates a random unique 32 character unique key.
			*/
			$key = getGuid();

			/*
			if email and password are not empty. If they are show an error and display the form.
			*/
			if(!empty($email) && !empty($password) && !empty($first_name) && !empty($last_name)) {
				if(isValidEmail($email)) {
					try {
						$query = "SELECT * FROM `members` WHERE `email` = :email";
						$check_query = $db->prepare($query);
						$check_query->bindParam(':email', $email, PDO::PARAM_STR);
						$check_query->execute();
						$n = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
					} catch(PDOException $e) {
						$log->logError($e." - ".basename(__FILE__));
					}

						if(!$n) {
							/*
							here the user password gets encrypted using a secure algorithm.
							*/
							$password = generate_encrypted_password($password);

							try {
								$create_user_query = "INSERT INTO `members`(`first_name`, `last_name`, `password` ,`email` , `bio`, `key` ,`verified` ,`join`) VALUE(:first_name, :last_name, :password, :email, :bio, :key, :verified, :join)";
								$create_user = $db->prepare($create_user_query);
								$create_user->bindParam(':first_name', $first_name, PDO::PARAM_STR);
								$create_user->bindParam(':last_name', $last_name, PDO::PARAM_STR);
								$create_user->bindParam(':password', $password, PDO::PARAM_STR);
								$create_user->bindParam(':email', $email, PDO::PARAM_STR);
								$create_user->bindParam(':bio', $bio, PDO::PARAM_STR);
								$create_user->bindParam(':key', $key, PDO::PARAM_STR);
								$create_user->bindParam(':verified', $verified, PDO::PARAM_INT);
								$create_user->bindParam(':join', $join, PDO::PARAM_STR);
								$create_user->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err = "<div class=\"alert alert-success\"><strong>"._("User Added.")."</strong><br/>"._("A new user has been added successfully.")."</div>";
						} else {
							$err = "<div class=\"alert alert-error\"><strong>"._("Email already exists.")."</strong><br/>"._("This email is already registered with us.")."</div>";
						}
				} else {
					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Invalid Email.")."</strong><br/>"._("Please enter a valid email address in the email field.")."</div>";
				}
			} else {
				$err = "<div class=\"alert alert-error\"><strong>"._("Empty Fields.")."</strong><br/>"._("Please fill in all the fields.")."</div>";
			}
		}

		/*
		displaying the user info edit page to the admin.
		*/
		admin_add_user();
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>