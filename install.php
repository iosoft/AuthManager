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
error_reporting(0);
ini_set('display_errors', '0');
?>
<!DOCTYPE html>
<html>
<head>
<title>AuthManager 3.0 - Setup Wizard</title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="Author" content="StitchApps" />
<link href="css/bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-responsive.css" media="screen" rel="stylesheet" type="text/css" />
<link href="css/style.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="images/favicon.ico">
</head>
<body>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="brand" href="#"><img src="images/am_logo.png" /></a>
		</div>
	</div>
</div>
<div class="container">
<?php
if(isset($_POST["install"])) {
	$host = $_POST["host"];
	$dbuser = $_POST["dbuser"];
	$dbpass = $_POST["dbpass"];
	$dbname = $_POST["dbname"];
		if(isset($_POST["dbcreate"])) {
			$dbcreate = intval($_POST["dbcreate"]);
		} else {
			$dbcreate = 0;
		}

	$adminemail = $_POST["adminemail"];
	$adminpass = $_POST["adminpass"];
	$websitepath = $_POST["websitepath"];

		if(!empty($host) && !empty($dbuser) && !empty($websitepath) && !empty($dbname) && !empty($adminemail) && !empty($adminpass)) {
			/*
			we remove the end slash over here even if it has been entered by the user.
			*/
			if(substr($websitepath, strlen($websitepath) - 1) == "/") {
				$websitepath = substr($websitepath,0, strlen($websitepath) - 1);
			}

			/*
			now verifying the admin email just to ensure that it is correct.
			*/
			if(isValidEmail($adminemail)) {
				$c = mysql_connect($host, $dbuser, $dbpass);
				
				if($c) {
					/*
					creating database over here if the user has checked the create database checkbox.
					*/
					if($dbcreate == 1) {
						$created = mysql_query("CREATE DATABASE `{$dbname}`;") or die(mysql_error());
					}

					$s = mysql_select_db($dbname);
						if($s) {
							/*
							installing queries from the database file to the database. each query is seperated by the ";" delimiter.
							*/
							$file = fopen("install/database.sql", "r");
							$read = fread($file, filesize("install/database.sql"));
							$read = explode(";", $read);
								foreach($read AS $query) {
									mysql_query($query);
								}

							unset($query);
							fclose($file);

							$update_s1 = mysql_query("UPDATE `settings` SET `option_value` = '{$websitepath}' WHERE `option_name` = 'website'") or die(mysql_error());
							$update_s2 = mysql_query("UPDATE `settings` SET `option_value` = '{$adminemail}' WHERE `option_name` = 'admin_email'") or die(mysql_error());
							$update_s3 = mysql_query("UPDATE `settings` SET `option_value` = '{$adminemail}' WHERE `option_name` = 'sending_email'") or die(mysql_error());
							
							$join = date("Y-m-d H:i:s");

							/*
							getGuid() function generates a random unique 32 character unique key.
							*/
							$key = getGuid();

							/*
							encrypting the password using the required format.
							*/
							$adminpass = generate_encrypted_password($adminpass);

							/*
							create first user with the admin rights.
							*/
							$create_admin = "INSERT INTO `members`(`first_name`, `last_name`, `password`, `email`, `key`, `verified`, `join`, `is_admin`) VALUE('Site', 'Admin', '{$adminpass}', '{$adminemail}', '{$key}', 1, '{$join}', 1)";
							$execute_query = mysql_query($create_admin) or die(mysql_error());
								if($execute_query) {
?>
									<div class="page-header">
										<h1>Final Step</h1>
									</div>

									<div class="alert alert-success"><strong>Installation Completed.</strong><br/>You have just finished installing <i>AuthManager</i> on your server. Please complete the final step before you can start using the app.</div>
									<p>Open file <strong>'user/database.php'</strong> and copy the text from below and replace it with the text already there:<br/>
									<pre>$db_server = "<?php echo $host; ?>";<br/>$db_user = "<?php echo $dbuser; ?>";<br/>$db_password = "<?php echo $dbpass; ?>";<br/>$db_name = "<?php echo $dbname; ?>";</pre>
									<p>Also, delete the <strong>'install.php'</strong> file for security reasons. <strong>That's it!</strong> enjoy the product</p><br/>
<?php
								} else {
									$err = "<div class=\"alert alert-error\"><strong>Oops!</strong><br/>There was an error installing the product on your server. Please contact support@stitchapps.com for further assistance.</div>";
									showform();
								}
						} else {
							$err = "<div class=\"alert alert-error\"><strong>Oops!</strong><br/>Database does not exist. Tick the <i>Create Database</i> checkbox to let us create one for you.</div>";
							showform();
						}
				} else {
					$err = "<div class=\"alert alert-error\"><strong>Unable to Connect.</strong><br/>".mysql_error()."</div>";
					showform();
				}
			} else {
				$err = "<div class=\"alert alert-error\"><strong>Invalid Email.</strong><br/>Please enter a valid email address.</div>";
				showform();
			}
		} else {
			$err = "<div class=\"alert alert-error\"><strong>Empty Fields</strong><br/>Please fill in all the fields to continue.</div>";
			showform();
		}
} else {
	showform();
}

function showform() {
	global $err;

	if(empty($_POST["host"])) {
		$host = "DB_SERVER";
	} else {
		$host = $_POST["host"];
	}

	if(empty($_POST["dbuser"])) {
		$dbuser = "DB_USER";
	} else {
		$dbuser = $_POST["dbuser"];
	}

	if(!empty($_POST["dbpass"])) {
		$dbpass = $_POST["dbpass"];
	} else {
		$dbpass = null;
	}

	if(empty($_POST["dbname"])) {
		$dbname = "DB_NAME";
	} else {
		$dbname = $_POST["dbname"];
	}

	if(!empty($_POST["adminemail"])) {
		$adminemail = $_POST["adminemail"];
	} else {
		$adminemail = null;
	}

	if(!empty($_POST["adminpass"])) {
		$adminpass = $_POST["adminpass"];
	} else {
		$adminpass = null;
	}

	if(empty($_POST["websitepath"])) {
		$websitepath = "http://".$_SERVER["HTTP_HOST"];
	} else {
		$websitepath = $_POST["websitepath"];
	}

	/*
	checking support for gettext which is important for the translation purpose.
	*/
	if(!function_exists("gettext")) {
		$err_gt = "<div class=\"alert alert-warning\"><strong>No Gettext Support.</strong><br/>Your PHP installation is without the support for <i>Gettext</i> which is necessary for the translation system to work. Please contact your hosting provider to enable support for <i>Gettext</i> in PHP.</div>";
	} else {
		$err_gt = null;
	}
	
	if(class_exists("PDO")) {
?>

		<div class="page-header">
			<h1>Setup Wizard</h1>
		</div><br/>
		
		<div class="row">
		<div class="span6">
		<?php echo $err_gt; ?>
		<?php echo $err; ?>
		<form class="form-horizontal" method="POST" action="install.php">
		<fieldset>
			<div class="control-group">
				<label class="control-label" for="host">Database Host</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="host" name="host" value="<?php echo $host; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbuser">Database User</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="dbuser" name="dbuser" value="<?php echo $dbuser; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbpass">Database Password</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="dbpass" name="dbpass" value="<?php echo $dbpass; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbname">Database Name</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="dbname" name="dbname" value="<?php echo $dbname; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="dbcreate">Create Database</label>
				<div class="controls">
					<label class="checkbox"><input type="checkbox" id="dbcreate" name="dbcreate" value="1"></label>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="websitepath">Website Path</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="websitepath" name="websitepath" value="<?php echo $websitepath; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="adminemail">Admin Email</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="adminemail" name="adminemail" value="<?php echo $adminemail; ?>">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="adminpass">Admin Password</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="adminpass" name="adminpass" value="<?php echo $adminpass; ?>">
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-success" name="install">Let's Begin</button>
				</div>
			</div>
		</fieldset>
		</form>
		</div>
		
		<div class="span4 offset2">
			<h3>AuthManager</h3>
			<p>Beautifully crafted premium open source user authentication and management web application. Power packed with awesome features and a great user interface. No reason why you won't love it.</p><br/>
			
			<div class="alert alert-info"><strong>Support Information</strong><br/><br/>
			<label>Email &nbsp;&nbsp;support@stitchapps.com</label>
			<label>Mobile &nbsp;+91 9871084893</label>
			</div>
		</div><br/>
		</div>
<?php
	} else {
		echo "<div class=\"alert alert-error\"><strong>Critical Error: PDO</strong><br/>Your PHP installation does not have support for PDO. <i>AuthManager</i> is based on PDO and it is necessary to get the app working. Please contact your hosting provider to enable support for PHP PDO.</div>";
	}
}

function isValidEmail($email) {
	if(preg_match("/^(\w+(([.-]\w+)|(\w.\w+))*)\@(\w+([.-]\w+)*\.\w+$)/", $email)) {
		return true;
	} else {
		return false;
	}
}

/*
two salts are used to securely encrypt the password. It encrypts the password in such a way that decryption of the password is
not possible. Moreover, a user password reset is used in case the user forgets its password. There is no way to recover the 
password. It is advised that you should not change the salt once your site is live.
*/
function generate_encrypted_password($str) {
	define('SALT_ONE', 'some_random_123_collection_&$^%_of_stuff');
	define('SALT_TWO', 'another_random_%*!_collection_ANbu_of_stuff');
	$new_pword = "";

		if(defined('SALT_ONE')):
			$new_pword .= md5(SALT_ONE);
		endif;

		$new_pword .= md5($str);

		if(defined('SALT_TWO')):
			$new_pword .= md5(SALT_TWO);
		endif;

	return substr($new_pword, strlen($str), 40);
}

function getGuid() {
	$strGuid = md5(uniqid(rand(), 1));
	return $strGuid;
}
?>
<div class="footer-container">
	<div class="pull-left">
		<span>Copyright &copy; <strong>StitchApps</strong> <?php echo date("Y"); ?>
			<a href="http://www.stitchapps.com/develop/post-request">Get Project Quote</a>
			<a href="http://www.stitchapps.com/support/open-ticket">Request Support</a>
			<a href="http://www.stitchapps.com/contact">Contact Us</a>
		</span>
	</div>
</div>
</div>
</body>
</html>