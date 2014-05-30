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

/*
assuming the session is false and the userid is set to 0. we also set the $is_admin to 0 for security purposes.
*/
$userid = 0;
$is_admin = 0;
$sesslife = false;

/*
checking the $_session variables to check whether they exist or not. this will make the application more faster for the users.
*/
if(empty($_SESSION["code"])) {
	if(isset($_REQUEST["code"])) {
		$_SESSION["code"] = $_REQUEST["code"];
	}

	$_SESSION["access_token"] = null;
}

if(!empty($_SESSION["code"])) {
	$access_date = date("Y-m-d H:i:s");
	if(empty($_SESSION["access_token"])) {
		$token_url = "https://graph.facebook.com/oauth/access_token?client_id=".$_setting['facebook_api']."&redirect_uri=".urlencode($website."/")."&client_secret=".$_setting['facebook_secret']."&code=".$_SESSION["code"];

		$response = file_get_contents($token_url);
		$cookie = null;
		parse_str($response, $cookie);

		$graph_url = "https://graph.facebook.com/me?access_token=".$cookie['access_token'];$user = json_decode(file_get_contents($graph_url));

		$temp_fb_id = $user->id;
		$_SESSION["temp_fb_id"] = $temp_fb_id;

		$fb_email = $user->email;
		$_SESSION["fb_email"] = $fb_email;

		$_SESSION["access_token"] = $cookie['access_token'];
	} else {
		$temp_fb_id = $_SESSION["temp_fb_id"];
		$fb_email = $_SESSION["fb_email"];
	}

	if(!empty($fb_email)) {
		/* query the database and check whether the user information exists or not and if not then add the user information to the database with facebook ID */
		try {
			$fb_query = "SELECT * FROM `members` WHERE `email` = :fb_email";
			$fb_query_do = $db->prepare($fb_query);
			$fb_query_do->bindParam(':fb_email', $fb_email, PDO::PARAM_STR);
			$fb_query_do->execute();
			$fb_query_num = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			/* catch and log errors over here. */
		}

		if(!empty($fb_query_num)) {
			$sesslife = true;
			$fb_fetch = $fb_query_do->fetch (PDO::FETCH_ASSOC);

			$userid = intval($fb_fetch['id']);
			$username = cleanInput($fb_fetch['email']);
			$facebook_pid = $temp_fb_id;
			$first_name = cleanInput($fb_fetch['first_name']);
			$last_name = cleanInput($fb_fetch['last_name']);
			$is_admin = intval($fb_fetch['is_admin']);
			
			try {
				$fb_update = "UPDATE `members` SET `access` = :access_date, `fb_id` = :temp_fb_id WHERE `id` = :userid";
				$fb_update_do = $db->prepare($fb_update);
				$fb_update_do->bindParam(':access_date', $access_date, PDO::PARAM_STR);
				$fb_update_do->bindParam(':temp_fb_id', $temp_fb_id, PDO::PARAM_INT);
				$fb_update_do->bindParam(':userid', $userid, PDO::PARAM_INT);
				$fb_update_do->execute();
			} catch(PDOException $e) {
				/* catch and log errors over here. */
			}
		} else {
	  		/* creating a random key for the user */
			$temp_key = getGuid();

			$temp_password = createRandomPassword();
			$temp_password = generate_encrypted_password($temp_password);
			
			/* fetching the user's first and last name from their facebook profile. */
			$first_name = $user->first_name;
			$last_name = $user->last_name;

			try {
				$fb_insert = "INSERT INTO `members`(`first_name`, `last_name`, `password`, `email`, `key`, `verified`, `join`, `access`, `fb_id`) VALUE(:first_name, :last_name, :temp_password, :fb_email, :temp_key, 1, :access_date, :access_date, :temp_fb_id)";
				$fb_insert_do = $db->prepare($fb_insert);
				$fb_insert_do->bindParam(':first_name', $first_name, PDO::PARAM_STR);
				$fb_insert_do->bindParam(':last_name', $last_name, PDO::PARAM_STR);
				$fb_insert_do->bindParam(':temp_password', $temp_password, PDO::PARAM_STR);
				$fb_insert_do->bindParam(':fb_email', $fb_email, PDO::PARAM_STR);
				$fb_insert_do->bindParam(':temp_key', $temp_key, PDO::PARAM_STR);
				$fb_insert_do->bindParam(':access_date', $access_date, PDO::PARAM_STR);
				$fb_insert_do->bindParam(':temp_fb_id', $temp_fb_id, PDO::PARAM_INT);
				$fb_insert_do->execute();
				$last_insert = $db->lastInsertId();
			} catch(PDOException $e) {
				/* catch and log errors over here. */
			}

			$userid = $last_insert;
			$username = cleanInput($fb_email);
			$facebook_pid = intval($temp_fb_id);

			/* if the query is successful, then only make the sesslife true for the user. */
			$sesslife = true;
		}
	}
} elseif(isset($_COOKIE["pauser"]) && isset($_COOKIE["papass"])) {
	$username = $_COOKIE["pauser"];
	$password = $_COOKIE["papass"];

	try {
		$user_details = "SELECT * FROM `members` WHERE `email` = :username AND `password` = :password";
		$user_details_do = $db->prepare($user_details);
		$user_details_do->bindParam(':username', $username, PDO::PARAM_STR);
		$user_details_do->bindParam(':password', $password, PDO::PARAM_STR);
		$user_details_do->execute();
		$n1 = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {		
		/* to display error messages over here.
		echo $e->getMessage(); */
	}

		if(empty($n1)) {
			$session->stop();
		} else {
			$sesslife = true;
			$r = $user_details_do->fetch (PDO::FETCH_ASSOC);

			$userid = intval($r['id']);
			$username = cleanInput($r['email']);
			$userpass = $r['password'];
			$first_name = cleanInput($r['first_name']);
			$last_name = cleanInput($r['last_name']);
			$is_admin = intval($r['is_admin']);
		}
} elseif(isset($_SESSION["user"]) && isset($_SESSION["pass"])) {
	$username = $_SESSION["user"];
	$password = $_SESSION["pass"];

    try {
		$user_details = "SELECT * FROM `members` WHERE `email` = :username AND `password` = :password";
		$user_details_do = $db->prepare($user_details);
		$user_details_do->bindParam(':username', $username, PDO::PARAM_STR);
		$user_details_do->bindParam(':password', $password, PDO::PARAM_STR);
		$user_details_do->execute();
		$n1 = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {		
		/* catch and log errors over here. */
	}

		if(empty($n1)) {
			$session->stop();
		} else {
			$sesslife = true;
			$r = $user_details_do->fetch (PDO::FETCH_ASSOC);
			
			$userid = intval($r['id']);
			$username = cleanInput($r['email']);
			$userpass = $r['password'];
			$first_name = cleanInput($r['first_name']);
			$last_name = cleanInput($r['last_name']);
			$is_admin = intval($r['is_admin']);
		}
}
?>