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
functions for the admin section of the application.
*/
function admin_add_user() {
	global $err, $website;
	
	/*
	displaying anonymous photo over here.
	*/
?>
	<div class="page-header no-border">
		<h1><img class="profilephoto thumbnail" src="<?php echo $website."/images/anonuser_50px.gif"; ?>" />&nbsp;&nbsp;<?php echo _("Add User"); ?></h1>
	</div>
<?php
echo "<ul class=\"breadcrumb\">
<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
<li><a href=\"{$website}/".ADMIN_DIRECTORY."/users\">"._("Users")."</a> <span class=\"divider\">/</span></li>
<li class=\"active\">"._("Add User")."</li>
</ul>";
?>
	<div class="tabs-left">
		<ul class="nav nav-tabs" id="usermanage">
			<li class="active"><a href="#general" data-toggle="tab"><i class="icon-cog"></i> <?php echo _("General"); ?></a></li>
			<li><a href="#profile" data-toggle="tab"><i class="icon-user"></i> <?php echo _("Profile"); ?></a></li>
		</ul>

	<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/add-user">
	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<fieldset>
				<legend><?php echo _("General"); ?></legend>
				<?php echo $err; ?>
				<div class="control-group">
					<label class="control-label" for="first_name"><?php echo _("First Name"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="first_name" name="first_name">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="last_name"><?php echo _("Last Name"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="last_name" name="last_name">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email"><?php echo _("Email"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="email" name="email">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="pass"><?php echo _("Password"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="pass" name="pass">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="verified"><?php echo _("Verified"); ?></label>
					<div class="controls">
						<select name="verified" id="verified">
							<option value="1"><?php echo _("Yes"); ?></option>
							<option value="0"><?php echo _("No"); ?></option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="tab-pane" id="profile">
			<fieldset>
				<legend><?php echo _("Profile"); ?></legend>
				<?php echo $err; ?>
				<div class="control-group">
					<label class="control-label" for="bio"><?php echo _("Bio"); ?></label>
					<div class="controls">
						<textarea class="input-xxlarge" id="bio" name="bio" rows="8"></textarea>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" name="adduser" value="<?php echo _("Add User"); ?>">
		</div>
	</div>
	</form>
	</div>
<?php
}

function admin_edit_user($id) {
	global $db, $err, $log, $website;

	$current_url = current_url();

	try {
		$sql = "SELECT * FROM `members` WHERE `id` = :id";
		$sql_do = $db->prepare($sql);
		$sql_do->bindParam(':id', $id, PDO::PARAM_INT);
		$sql_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $sql_do->fetch(PDO::FETCH_ASSOC);
			$verified = intval($f['verified']);
			$email_user = cleanInput($f['email']);
			$first_name = cleanInput($f['first_name']);
			$last_name = cleanInput($f['last_name']);
			$banned = intval($f['banned']);
			$joined_on = cleanInput($f['join']);
			$last_access = cleanInput($f['access']);
			$user_bio = cleanInput($f['bio']);
			
			/*
			displaying gravatar photo over here if email is associated with a gravatar account.
			*/
			$default = $website."/images/anonuser_50px.gif";
			$gravatar = new Gravatar($email_user, $default);
			$gravatar->size = 50;
?>
	<div class="page-header no-border">
		<h1><img class="profilephoto thumbnail" src="<?php echo $gravatar->getSrc(); ?>" />&nbsp;&nbsp;<?php echo $first_name." ".$last_name; ?></h1>
	</div>
<?php
echo "<ul class=\"breadcrumb\">
<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
<li><a href=\"{$website}/".ADMIN_DIRECTORY."/users\">"._("Users")."</a> <span class=\"divider\">/</span></li>
<li class=\"active\">{$first_name} {$last_name}</li>
</ul>";
?>
	<div class="tabs-left">
		<ul class="nav nav-tabs" id="usermanage">
			<li class="active"><a href="#general" data-toggle="tab"><i class="icon-cog"></i> <?php echo _("General"); ?></a></li>
			<li><a href="#profile" data-toggle="tab"><i class="icon-user"></i> <?php echo _("Profile"); ?></a></li>
			<li><a href="#logs" data-toggle="tab"><i class="icon-list-alt"></i> <?php echo _("Access Logs"); ?></a></li>
		</ul>

	<form class="form-horizontal" method="POST" action="<?php echo $current_url; ?>">
	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<fieldset>
				<legend><?php echo _("General"); ?></legend>
				<?php echo $err; ?>
				<div class="control-group">
					<label class="control-label" for="first_name"><?php echo _("First Name"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="first_name" name="first_name" value="<?php echo $first_name; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="last_name"><?php echo _("Last Name"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="last_name" name="last_name" value="<?php echo $last_name; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email"><?php echo _("Email"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="email" name="email" value="<?php echo $email_user; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="pass"><?php echo _("Password"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="pass" name="pass" value="<?php echo $f['password']; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="verified"><?php echo _("Verified"); ?></label>
					<div class="controls">
						<select name="verified" id="verified">
							<option value="1"<?php if($verified == 1) { echo " selected=\"selected\""; } ?>><?php echo _("Yes"); ?></option>
							<option value="0"<?php if($verified == 0) { echo " selected=\"selected\""; } ?>><?php echo _("No"); ?></option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="join"><?php echo _("Joined On"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="join" name="join" value="<?php echo $joined_on; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="access"><?php echo _("Last Access"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="access" name="access" value="<?php echo $last_access; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="ban"><?php echo _("Banned"); ?></label>
					<div class="controls">
						<select name="ban" id="ban">
							<option value="1"<?php if($banned == 1) { echo " selected=\"selected\""; } ?>><?php echo _("Yes"); ?></option>
							<option value="0"<?php if($banned == 0) { echo " selected=\"selected\""; } ?>><?php echo _("No"); ?></option>
						</select>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="tab-pane" id="profile">
			<fieldset>
				<legend><?php echo _("Profile"); ?></legend>
				<?php echo $err; ?>
				<div class="control-group">
					<label class="control-label" for="bio"><?php echo _("Bio"); ?></label>
					<div class="controls">
						<textarea class="input-xxlarge" id="bio" name="bio" rows="8"><?php echo $user_bio; ?></textarea>
					</div>
				</div>
			</fieldset>
		</div>
		<div class="tab-pane" id="logs">
		<fieldset>
			<legend><?php echo _("Access Logs"); ?></legend>
<?php
		try {
			$q = "SELECT * FROM `access` WHERE `userid` = :userid ORDER BY `id` DESC";
			$q_do = $db->prepare($q);
			$q_do->bindParam(':userid', $id, PDO::PARAM_INT);
			$q_do->execute();
			$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			$log->logError($e." - ".basename(__FILE__));
		}
		
		if(!empty($number)) {
			echo "<table class=\"table table-condensed\">
			<thead>
			<tr>
			<th>"._("IP Address")."</th>
			<th>"._("Country")."</th>
			<th>"._("Access")."</th>
			</tr>
			</thead>
			<tbody>";

			/*
			initializing the ip2country class for converting ip address to country.
			*/
			$ip2country = new ip2country($db);

			/*
			displaying the information in a while loop.
			*/
			while($row = $q_do->fetch (PDO::FETCH_ASSOC)) {
				$ip = cleanInput($row['ip_address']);
				$date = cleanInput($row['datetime']);
				/*
				fetching country name for the specific ip address
				*/
				$country = $ip2country->get_country_name($ip);
				
				echo "<tr>";
				echo "<td>{$ip}</td>";
				echo "<td>{$country}</td>";
				echo "<td><abbr class=\"micro\" title=\"{$date}\"></abbr></td>";
				echo "</tr>";
			}

			echo "</tbody>
			</table>";
		} else {
			echo "<div class=\"alert\"><strong>"._("No Access Records.")."</strong><br/>"._("There are no access records for this user in the database.")."</div>";
		}
?>
		</fieldset>
		</div>
		<div class="form-actions">
			<input type="submit" class="btn btn-primary" name="edituser" value="<?php echo _("Update User"); ?>">
		</div>
	</div>
	</form>
	</div>
<?php } else {
			echo "<div class=\"alert alert-error\"><strong>"._("Not Found.")."</strong><br/>"._("User does not exist in the database. There are no records matching the user ID specified.")."</div>";
		}
}

function admin_settings() {
	global $db, $website, $username, $log;
	global $err_general, $err_captcha, $err_analytics, $err_facebook, $err_email, $err_css, $err_misc;

	$current_url = current_url();

	try {
		$q = "SELECT * FROM `settings`";
		$q_do = $db->prepare($q);
		$q_do->execute();
		$check = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($check)) {
			while($r = $q_do->fetch(PDO::FETCH_ASSOC)) {
				$option = $r['option_name'];
				$_setting[$option] = $r['option_value'];
			}
		} else {
			die("<div class=\"alert alert-error\"><strong>"._("Application Error.")."</strong><br/>"._("There seems to be some error with the application. Application settings seem to be corrupt.")."<br/><small>"._("Please contact ")."<strong>"._("support@stitchapps.com")."</strong>"._(" for assistance.")."</small></div>");
		}
?>
<div class="tabs-left">
	<ul class="nav nav-tabs" id="settings">
		<li class="active"><a href="#general" data-toggle="tab"><i class="icon-cog"></i> <?php echo _("General"); ?></a></li>
		<li><a href="#captcha" data-toggle="tab"><i class="icon-check"></i> <?php echo _("Captcha"); ?></a></li>
		<li><a href="#analytics" data-toggle="tab"><i class="icon-signal"></i> <?php echo _("Analytics"); ?></a></li>
		<li><a href="#facebook" data-toggle="tab"><i class="icon-hand-up"></i> <?php echo _("Facebook"); ?></a></li>
		<li class="dropdown">
			<a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-edit"></i> <?php echo _("CSS Editor"); ?>&nbsp;<b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="#css-bootstrap" data-toggle="tab">bootstrap.css</a></li>
				<li><a href="#css-bootstrap-responsive" data-toggle="tab">bootstrap-responsive.css</a></li>
				<li><a href="#css-style" data-toggle="tab">style.css</a></li>
			</ul>
		</li>
		<li class="dropdown">
			<a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-envelope"></i> <?php echo _("Email"); ?>&nbsp;<b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="#email-newuser" data-toggle="tab"><?php echo _("User Welcome"); ?></a></li>
				<li><a href="#email-forgot" data-toggle="tab"><?php echo _("Forgot Password"); ?></a></li>
				<li><a href="#email-verify" data-toggle="tab"><?php echo _("Verify Email"); ?></a></li>
				<li><a href="#email-newpass" data-toggle="tab"><?php echo _("New Password"); ?></a></li>
			</ul>
		</li>
		<li><a href="#misc" data-toggle="tab"><i class="icon-fire"></i> <?php echo _("Miscellaneous"); ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="general">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/general">
			<fieldset>
				<legend><?php echo _("General Settings"); ?></legend>
				<?php echo $err_general; ?>
				<div class="control-group">
					<label class="control-label" for="email"><?php echo _("Email"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge disabled" id="email" name="email" value="<?php echo $username; ?>" disabled>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="website"><?php echo _("Website"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="website" name="website" value="<?php echo $_setting['website']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="title"><?php echo _("Title"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="title" name="title" value="<?php echo $_setting['title']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="description"><?php echo _("Description"); ?></label>
					<div class="controls">
						<textarea class="input-xlarge" id="description" name="description" rows="5"><?php echo $_setting['description']; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="keywords"><?php echo _("Keywords"); ?></label>
					<div class="controls">
						<textarea class="input-xlarge" id="keywords" name="keywords" rows="5"><?php echo $_setting['keywords']; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="sending_email"><?php echo _("Sending Email"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="sending_email" name="sending_email" value="<?php echo $_setting['sending_email']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="user_verification"><?php echo _("User Verification"); ?></label>
					<div class="controls">
						<select name="user_verification">
							<option value="1"<?php if($_setting['user_verification'] == 1) { echo " selected=\"selected\""; } ?>><?php echo _("Yes"); ?></option>
							<option value="0"<?php if($_setting['user_verification'] == 0) { echo " selected=\"selected\""; } ?>><?php echo _("No"); ?></option>
						</select>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="general">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="captcha">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/captcha">
			<fieldset>
				<legend><?php echo _("Captcha Settings"); ?></legend>
				<?php echo $err_captcha; ?>
				<div class="control-group">
					<label class="control-label" for="recaptcha_enabled"><?php echo _("Enable reCaptcha"); ?></label>
					<div class="controls">
						<select name="recaptcha_enabled">
							<option value="0"<?php if($_setting['inbuilt_captcha'] == 0) { echo " selected=\"selected\""; } ?>><?php echo _("Yes"); ?></option>
							<option value="1"<?php if($_setting['inbuilt_captcha'] == 1) { echo " selected=\"selected\""; } ?>><?php echo _("No"); ?></option>
						</select>
						<span class="help-block"><small><?php echo _("Please note that if you do not enable reCAPTCHA option above, the application will use it's inbuilt CAPTCHA system for spam protection.")."<br/>"._("This is necessary in order to keep the system free from any sort of spam."); ?></small></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="recaptcha_public"><?php echo _("reCaptcha Public Key"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="recaptcha_public" name="recaptcha_public" value="<?php echo $_setting['recaptcha_public']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="recaptcha_private"><?php echo _("reCaptcha Private Key"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="recaptcha_private" name="recaptcha_private" value="<?php echo $_setting['recaptcha_private']; ?>">
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="captcha">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="analytics">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/analytics">
			<fieldset>
				<legend><?php echo _("Analytics Settings"); ?></legend>
				<?php echo $err_analytics; ?>
				<div class="control-group">
					<label class="control-label" for="analytics_enabled"><?php echo _("Enable Analytics"); ?></label>
					<div class="controls">
						<select name="analytics_enabled">
							<option value="1"<?php if($_setting['analytics_enabled'] == 1) { echo " selected=\"selected\""; } ?>><?php echo _("Yes"); ?></option>
							<option value="0"<?php if($_setting['analytics_enabled'] == 0) { echo " selected=\"selected\""; } ?>><?php echo _("No"); ?></option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="analytics_code"><?php echo _("Analytics Code"); ?></label>
					<div class="controls">
						<textarea class="input-xlarge" id="analytics_code" name="analytics_code" rows="5"><?php echo $_setting['analytics_code']; ?></textarea>
					</div>
				</div><br/>
				<h6><?php echo _("Google Tracking Settings"); ?></h6><br/>
				<div class="control-group">
					<label class="control-label" for="google_id"><?php echo _("Google Username"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="google_id" name="google_id" value="<?php echo $_setting['google_id']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="google_password"><?php echo _("Google Password"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="google_password" name="google_password" value="<?php echo $_setting['google_password']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="site_id"><?php echo _("Site ID"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="site_id" name="site_id" value="<?php echo $_setting['site_id']; ?>">
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="analytics">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="facebook">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/facebook">
			<fieldset>
				<legend><?php echo _("Facebook Settings"); ?></legend>
				<?php echo $err_facebook; ?>
				<div class="control-group">
					<label class="control-label" for="enable_facebook"><?php echo _("Enable Facebook"); ?></label>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" id="enable_facebook" name="enable_facebook" value="1"<?php if($_setting['enable_facebook'] == 1) { echo " checked"; } ?>>
						</label>
						<span class="help-block"><small><?php echo _("If you uncheck the above option, current users who logged in via Facebook will no longer be able to login via Facebook option.")."<br/>"._("It is recommended to have Facebook connect enabled to increase signups."); ?></small></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="facebook_api"><?php echo _("Facebook API Key"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="facebook_api" name="facebook_api" value="<?php echo $_setting['facebook_api']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="facebook_secret"><?php echo _("Facebook Secret Key"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="facebook_secret" name="facebook_secret" value="<?php echo $_setting['facebook_secret']; ?>">
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="facebook">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="css-bootstrap">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/css-bootstrap">
			<fieldset>
				<legend><?php echo _("CSS Editor"); ?> <small>(bootstrap.css)</small></legend>
				<?php echo $err_css; ?>
				<div class="control-group">
					<div class="controls">
						<textarea class="span7" id="css-bootstrap" name="css-bootstrap" rows="18">
<?php
						$template_file = "../css/bootstrap.css";
						$file_handle = fopen($template_file, "rb");
						echo fread($file_handle, filesize($template_file));
						fclose($file_handle);
?>
						</textarea>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="css-bootstrap">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="css-bootstrap-responsive">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/css-bootstrap-responsive">
			<fieldset>
				<legend><?php echo _("CSS Editor"); ?> <small>(bootstrap-responsive.css)</small></legend>
				<?php echo $err_css; ?>
				<div class="control-group">
					<div class="controls">
						<textarea class="span7" id="css-responsive" name="css-responsive" rows="18">
<?php
						$template_file = "../css/bootstrap-responsive.css";
						$file_handle = fopen($template_file, "rb");
						echo fread($file_handle, filesize($template_file));
						fclose($file_handle);
?>
						</textarea>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="css-responsive">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="css-style">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/css-style">
			<fieldset>
				<legend><?php echo _("CSS Editor"); ?> <small>(style.css)</small></legend>
				<?php echo $err_css; ?>
				<div class="control-group">
					<div class="controls">
						<textarea class="span7" id="css-style" name="css-style" rows="18">
<?php
						$template_file = "../css/style.css";
						$file_handle = fopen($template_file, "rb");
						echo fread($file_handle, filesize($template_file));
						fclose($file_handle);
?>
						</textarea>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="css-style">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="email-newuser">
<?php
	$slug = "new_user";
	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			$subject[$slug] = cleanInput($f['subject']);
			$message[$slug] = $f['message'];
		}
?>
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/email-newuser">
			<fieldset>
				<legend><?php echo _("Email Settings"); ?> <small><?php echo _("(New User)"); ?></small></legend>
				<?php echo $err_email; ?>
				<div class="control-group">
					<label class="control-label" for="subject-newuser"><?php echo _("Subject"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="subject-newuser" name="subject-newuser" value="<?php echo $subject[$slug]; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email-newuseremail"><?php echo _("Message"); ?></label>
					<div class="controls">
						<textarea class="span7" id="email-newuseremail" name="email-newuseremail" rows="14"><?php echo $message[$slug]; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<pre>{webtitle} - For website title<br/>{website} - For website address</pre>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="email-newuser">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="email-forgot">
<?php
	$slug = "forgot_pass";
	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			$subject[$slug] = cleanInput($f['subject']);
			$message[$slug] = $f['message'];
		}
?>
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/email-forgot">
			<fieldset>
				<legend><?php echo _("Email Settings"); ?> <small><?php echo _("(Forgot Password)"); ?></small></legend>
				<?php echo $err_email; ?>
				<div class="control-group">
					<label class="control-label" for="subject-forgotpass"><?php echo _("Subject"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="subject-forgotpass" name="subject-forgotpass" value="<?php echo $subject[$slug]; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email-forgotpass"><?php echo _("Message"); ?></label>
					<div class="controls">
						<textarea class="span7" id="email-forgotpass" name="email-forgotpass" rows="14"><?php echo $message[$slug]; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<pre>{webtitle} - For website title<br/>{website} - For website address<br/>{email} - User's email address<br/>{reset_key} - Password reset key<br/>USER_DIRECTORY - Name of USER directory on your server</pre>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="email-forgotpass">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="email-verify">
<?php
	$slug = "verify_email";
	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			$subject[$slug] = cleanInput($f['subject']);
			$message[$slug] = $f['message'];
		}
?>
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/email-verify">
			<fieldset>
				<legend><?php echo _("Email Settings"); ?> <small><?php echo _("(Verify User)"); ?></small></legend>
				<?php echo $err_email; ?>
				<div class="control-group">
					<label class="control-label" for="subject-verifyemail"><?php echo _("Subject"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="subject-verifyemail" name="subject-verifyemail" value="<?php echo $subject[$slug]; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email-verifyemail"><?php echo _("Message"); ?></label>
					<div class="controls">
						<textarea class="span7" id="email-verifyemail" name="email-verifyemail" rows="14"><?php echo $message[$slug]; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<pre>{webtitle} - For website title<br/>{website} - For website address<br/>{key} - Email verification key<br/>USER_DIRECTORY - Name of USER directory on your server</pre>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="email-verify">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="email-newpass">
<?php
	$slug = "new_pass";
	try {
		$q = "SELECT `subject`, `message` FROM `emails` WHERE `slug` = :slug";
		$q_do = $db->prepare($q);
		$q_do->bindParam(':slug', $slug, PDO::PARAM_STR);
		$q_do->execute();
		$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
	}

		if(!empty($number)) {
			$f = $q_do->fetch (PDO::FETCH_ASSOC);
			$subject[$slug] = cleanInput($f['subject']);
			$message[$slug] = $f['message'];
		}
?>
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/email-newpass">
			<fieldset>
				<legend><?php echo _("Email Settings"); ?> <small><?php echo _("(New Password)"); ?></small></legend>
				<?php echo $err_email; ?>
				<div class="control-group">
					<label class="control-label" for="subject-newpass"><?php echo _("Subject"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="subject-newpass" name="subject-newpass" value="<?php echo $subject[$slug]; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="email-newpassword"><?php echo _("Message"); ?></label>
					<div class="controls">
						<textarea class="span7" id="email-newpassword" name="email-newpassword" rows="14"><?php echo $message[$slug]; ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<pre>{webtitle} - For website title<br/>{website} - For website address<br/>{email} - User's email address<br/>{new_password} - New password generated by the system</pre>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="email-newpass">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
		<div class="tab-pane" id="misc">
		<form class="form-horizontal" method="POST" action="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings#/misc">
			<fieldset>
				<legend><?php echo _("Miscellaneous Settings"); ?></legend>
				<?php echo $err_misc; ?>
				<div class="control-group">
					<label class="control-label" for="enable_access"><?php echo _("Enable Access Logs"); ?></label>
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" id="enable_access" name="enable_access" value="1"<?php if($_setting['enable_access'] == 1) { echo " checked"; } ?>>
						</label>
						<span class="help-block"><small><?php echo _("Check the option above if you want to enable access logs for the users.")."<br/>"._("Enabling user access logs will maintain user records when they signin to the website along with their IP Address."); ?></small></span>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="max_invalid_login"><?php echo _("Maximum Invalid Logins"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="max_invalid_login" name="max_invalid_login" value="<?php echo $_setting['max_invalid_login']; ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="session_timeout"><?php echo _("Session Timeout"); ?></label>
					<div class="controls">
						<input type="text" class="input-xlarge" id="session_timeout" name="session_timeout" value="<?php echo $_setting['session_timeout']; ?>">
						<span class="help-block"><small><?php echo _("(Enter the value in seconds.)"); ?></small></span>
					</div>
				</div>
				<div class="form-actions">
					<input type="hidden" name="section" value="misc">
					<input type="submit" class="btn btn-primary" name="updatesettings" value="<?php echo _("Update Settings"); ?>">
				</div>
			</fieldset>
		</form>
		</div>
	</div>
</div>
<?php
}

function deleteUser($id) {
	global $db, $log;

	try {
		$delete_user = "DELETE FROM `members` WHERE `id` = :id";
		$delete_user_do = $db->prepare($delete_user);
		$delete_user_do->bindParam(':id', $id, PDO::PARAM_INT);
		$delete_user_do->execute();
		return true;
	} catch(PDOException $e) {
		$log->logError($e." - ".basename(__FILE__));
		return false;
	}
}
?>