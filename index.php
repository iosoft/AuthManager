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

$css = "<style>
body { background: url(images/grid-mask.png) 0 40px repeat-x; }
</style>";
subheader(_("Home"), $css);
?>
<div class="row">
<div class="span6">
<div class="hero-unit">
	<h1>User authentication & management.</h1>
	<h2>Easy user management and a comprehensive admin panel. All, for you <3</h2>
	<p>
		<a href="http://www.stitchapps.com/products/auth-manager" class="btn btn-danger btn-large">Details on <strong>StitchApps</strong></a>
	</p>
</div>
</div>

<div class="span6">
	<h6 class="home-margin">Protect content in just few lines.</h6><br/>
	<pre>if($sesslife == true) {
	
	/* content for logged in users over here. */

} else {

	/* redirect user to the login page or you can also
	show content for the anonymous users over here. */

}</pre>
</div>
</div>
<?php
include(USER_DIRECTORY."/footer.php");
?>