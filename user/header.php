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
this is the header file for the application. contains neccessary loader files as well as styling information. More details on this can be found in the documentation.
*/
function subheader($title=null, $css=null, $js=null) {
	global $webtitle, $website, $sesslife, $userid, $_setting, $first_name, $last_name, $username, $is_admin;
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title><?php if(!empty($title)) { echo $title." - "; } ?><?php echo $webtitle; ?></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="Description" content="<?php echo $_setting['description']; ?>" />
<meta name="Keywords" content="<?php echo $_setting['keywords']; ?>" />
<meta name="Author" content="StitchApps" />
<link href="<?php echo $website; ?>/css/bootstrap.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?php echo $website; ?>/css/bootstrap-responsive.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?php echo $website; ?>/css/style.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="<?php echo $website; ?>/images/favicon.ico">
<?php
/*
additional css files for specific pages
*/
echo $css;
?>
<script type="text/javascript" src="<?php echo $website."/".JS_DIRECTORY; ?>/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $website."/".JS_DIRECTORY; ?>/bootstrap.min.js"></script>
<?php
/*
additional js files for specific pages
*/
echo $js;
?>
</head>
<body>
<?php if($_setting['enable_facebook'] == 1) { ?>
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
	FB.init({
		appId      : '<?php echo $_setting['facebook_api']; ?>',
		channelUrl : '//<?php echo $website; ?>/channel.html',
		status     : true,
		cookie     : true,
		xfbml      : true
	});
};

(function(d) {
	var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	d.getElementsByTagName('head')[0].appendChild(js);
}(document));
</script>
<?php } ?>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="brand" href="<?php echo $website; ?>"><img src="<?php echo $website; ?>/images/am_logo.png" /></a>
<?php if($is_admin == 1) { ?>
	<div class="btn-group pull-left">
		<button class="btn btn-danger"><?php echo _("Admin"); ?></button>
		<button class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
		<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li><a href="<?php echo $website."/".ADMIN_DIRECTORY; ?>/statistics"><i class="icon-signal"></i> <?php echo _("Statistics"); ?></a></li>
			<li><a href="<?php echo $website."/".ADMIN_DIRECTORY; ?>/users"><i class="icon-user"></i> <?php echo _("Users"); ?></a></li>
			<li><a href="<?php echo $website."/".ADMIN_DIRECTORY; ?>/settings"><i class="icon-wrench"></i> <?php echo _("Settings"); ?></a></li>
			<li><a href="<?php echo $website."/".ADMIN_DIRECTORY; ?>/access"><i class="icon-list-alt"></i> <?php echo _("Access"); ?></a></li>
			<li><a href="<?php echo $website."/".ADMIN_DIRECTORY; ?>/sql-logs"><i class="icon-warning-sign"></i> <?php echo _("Logs"); ?></a></li>
		</ul>
	</div>
<?php } ?>
			<div class="nav-collapse collapse">
				<ul class="nav pull-right">
<?php if($sesslife == false) { ?>
					<li><a href="<?php echo $website."/".USER_DIRECTORY; ?>/login"><?php echo _("Login"); ?></a></li>
					<li><a href="<?php echo $website."/".USER_DIRECTORY; ?>/register"><?php echo _("Register"); ?></a></li>
<?php } else {
/*
displaying gravatar photo over here if email is associated with a gravatar account.
*/
$default = $website."/images/anonuser_50px.gif";
$gravatar = new Gravatar($username, $default);
$gravatar->size = 50;
?>
					<img src="<?php echo $gravatar->getSrc(); ?>" class="profile-photo">
					<li><a href="<?php echo $website; ?>/profile/<?php echo $userid; ?>"><?php echo $first_name." ".$last_name; ?></a></li>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo _("Account"); ?>&nbsp;<b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo $website."/".USER_DIRECTORY; ?>/account"><?php echo _("Info"); ?></a></li>
							<li><a href="<?php echo $website."/".USER_DIRECTORY; ?>/changepassword"><?php echo _("Change Password"); ?></a></li>
							<li><a href="<?php echo $website."/".USER_DIRECTORY; ?>/editprofile"><?php echo _("Edit Profile"); ?></a></li>
							<li class="divider"></li>
							<li>
						<?php if(!empty($_SESSION["code"])) { ?>
								<a href="https://www.facebook.com/logout.php?next=<?php echo urlencode($website."/".USER_DIRECTORY."/logout"); ?>&access_token=<?php echo $_SESSION["access_token"]; ?>"><?php echo _("Logout"); ?></a>
						<?php } else { ?>
							<a href="<?php echo $website."/".USER_DIRECTORY; ?>/logout"><?php echo _("Logout"); ?></a>
						<?php } ?>
							</li>
						</ul>
					</li>
<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="container">
<?php } ?>