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
database values for the application.
*/
$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "am-test";

/*
connecting to the database using PHP PDO. all the database connectivity in the application is handled using PDO only for injection proof queries.
*/
try {
	$db = new PDO("mysql:host={$db_server};dbname={$db_name}", $db_user, $db_password);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	/* displaying the error page over here. */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Oops!</title>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
<style>
*{margin:0;padding:0}html,code{font:15px/22px arial,sans-serif}html{background:#1a1a1a;color:#fefefe;padding:15px}body{margin:7% auto 0;max-width:390px;min-height:180px;padding:30px 0 15px}p{margin:0 0 10px;overflow:hidden}ins{font-size:13px;color:#efefef;text-decoration:none}a{text-decoration:none;color:#fff;}a img{border:0}@media screen and (max-width:772px){body{background:none;margin-top:0;max-width:none;padding-right:0}}
</style>
</head>
<body>
<h1>Oops!</h1><br/>
<p>Something has gone wrong because of which you are seeing this page. Please try again after some time.</p><br/>
<ins>We have received an report about this issue. We are working on to resolve this at the earliest possible.</ins>
</body>
</html>
<?php
	exit;
}
?>