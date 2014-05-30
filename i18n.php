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
this is the translation file for the application. the main use of this file is to read the translations in the .mo files as per 
the language selected.
*/
if(isset($_GET["lang"])) {
	$lang = cleanInput($_GET["lang"]);
		if(!empty($lang)) {
			/*
			destroying any previous cookie set.
			*/
			setcookie("am_lang", "", time() - 3600, "/");

			/*
			setting a new cookie for the new selection.
			*/
			setcookie("am_lang", $lang, time() + 3600*24*365, "/");
			$_SESSION["lang"] = $lang;
		}
} elseif(isset($_COOKIE["am_lang"])) {
	$lang = cleanInput($_COOKIE["am_lang"]);
	$_SESSION["lang"] = $lang;
} else {
	$_SESSION["lang"] = "en_US";
}

putenv("LANG=".$_SESSION["lang"]);
setlocale(LC_ALL, $_SESSION["lang"]);

$domain = "lang";
bindtextdomain($domain, dirname( __FILE__ )."/languages");
textdomain($domain);
bind_textdomain_codeset($domain, 'UTF-8');
?>