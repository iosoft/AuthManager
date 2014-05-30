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
?>
<div class="footer-container">
<?php
/*
set the $_SESSION['error'] to null so that the same error does not repeat again and again.
*/
$_SESSION["error"] = null;

/*
closing the database handle once the script has finished processing. this is done at the end of the footer so as to make sure that nothing gets left over.
*/
$db = null;

/*
getting the page generation time for the page in seconds.
*/
$processing_time = $timer->get();

/*
enabling language only if the gettext support is enabled.
*/
if($_gettext == true) {
	$left_or_right = "pull-right";
?>
<div class="pull-left">
	<ul>
		<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Language</a>&nbsp;<b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="?lang=en_US">English</a></li>
				<li><a href="?lang=es_ES">Spanish</a></li>
				<li><a href="?lang=el_GR">Greek</a></li>
			</ul>
		</li>
	</ul>
</div>
<?php
} else {
	$left_or_right = "pull-left";
}
?>

<div class="<?php echo $left_or_right; ?>">
<span><?php echo _("Copyright"); ?> &copy; <strong><?php echo $webtitle; ?></strong> <?php echo date("Y"); ?>
<a href="<?php echo $website."/".STATIC_DIRECTORY; ?>/terms"><?php echo _("Terms of Service"); ?></a>
<a href="<?php echo $website."/".STATIC_DIRECTORY; ?>/contact"><?php echo _("Contact Us"); ?></a>
</span>
<span class="benchmark">
<?php echo _("Page generated in")." ".$processing_time." "._("seconds"); ?>
</span>
</div>

</div>
</div>
<?php
/*
analytics code for tracking of application traffic. if analytics is enabled, only then it will display the code.
*/
echo $analytics_code;
?>
</body>
</html>