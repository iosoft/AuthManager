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
include("../".MODS_DIRECTORY."/analytics/class.analytics.php");

/*
including the charts class for displaying user activity for the past week.
*/
include("../".MODS_DIRECTORY."/charts/FusionCharts_Gen.php");
include("../".USER_DIRECTORY."/header.php");
$js = "<script language=\"javascript\" src=\"{$website}/".JS_DIRECTORY."/FusionCharts.js\"></script>";
subheader(_("Statistics"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		echo "<div class=\"page-header\"><h1>"._("Statistics")."</h1></div>";
		echo "<h6>"._("Options")." : <a href=\"{$website}/".ADMIN_DIRECTORY."/settings#/analytics\">"._("Analytics Settings")."</a></h6><br/>";
		echo "<ul class=\"breadcrumb\">
		<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
		<li class=\"active\">"._("Statistics")."</li>
		</ul>";

		if(!empty($_setting['google_id']) && !empty($_setting['google_password']) && !empty($_setting['site_id'])) {
			echo "<div class=\"alert\">"._("Google ID")." : <strong>{$_setting['google_id']}</strong>&nbsp;&nbsp;&nbsp;"._("Site ID")." : <strong>{$_setting['site_id']}</strong></div>";
			$analytics = new analytics($_setting['google_id'], $_setting['google_password']);
			$analytics->setProfileById("ga:".$_setting['site_id']);
			$analytics->setMonth(date("n"), date("Y"));
			$month = date("M");

			$visitors = $analytics->getVisitors();
			$total = count($visitors);
			$basic = 0;
			$count = "01";

				while($count < $total + 1) {
					$arrData[$basic][0] = $count;
					$arrData[$basic][1] = $visitors[$count];
	
					$count++;
					$basic++;

						if($count < 10) {
							$count = "0".$count;
						}
				}

			$FC = new FusionCharts("Column3D","800","300");

			/*
			set Relative Path of chart swf file.
			*/
			$FC->setSwfPath("../".MODS_DIRECTORY."/charts/");

			/* store chart attributes in a variable. */
			$strParam = "caption={$month} "._("Statistics").";formatNumberScale=0;decimalPrecision=0";

			/* set chart attributes. */
			$FC->setChartParams($strParam);
			$FC->addChartDataFromArray($arrData);

			/* displaying the traffic chart by fetching the data directly from the analytics server. */
			echo "<div>".$FC->renderChart()."</div>";
		} else {
			echo "<div class=\"alert alert-error\"><strong>"._("Settings not configured.")."</strong><br/>"._("It seems that your Google analytics settings are not configured properly. Please configure them properly.")."</div>";
		}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>