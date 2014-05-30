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
include("../".MODS_DIRECTORY."/class.ip2country.php");
include("../".USER_DIRECTORY."/header.php");

$js = "<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.timeago.js\"></script>
<script type=\"text/javascript\">
jQuery(document).ready(function($) {
	$(\".micro\").timeago();
});
</script>";
subheader(_("Access Logs"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		/*
		$err holds the most recent error for the photos page and displays it on the page.
		*/
		$err = null;

		if(isset($_GET["p"])) {
			$page = intval($_GET["p"]);
		} else {
			$page = 1;
		}

		$max_show = 15;

		try {
			$q = "SELECT * FROM `access` ORDER BY `id` DESC";
			$q_do = $db->prepare($q);
			$q_do->execute();
			$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			$log->logError($e." - ".basename(__FILE__));
		}

		echo "<div class=\"page-header\"><h1>"._("Access Logs")."</h1></div>";
		echo "<h6>"._("Options")." : <a href=\"{$website}/".ADMIN_DIRECTORY."/settings#/misc\">"._("Access Settings")."</a></h6><br/>";
		echo "<ul class=\"breadcrumb\">
		<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
		<li class=\"active\">"._("Access Logs")."</li>
		</ul>";

		if(!empty($number)) {
			/*
			initializing the ip2country class for converting ip address to country.
			*/
			$ip2country = new ip2country($db);

			$p = new pagination;
			$p->items($number);
			$p->limit($max_show);
			$p->currentPage($page);
			$p->parameterName("p");
			$p->urlFriendly();
			$p->target("{$website}/".ADMIN_DIRECTORY."/access/%");

			$from2 = $page * $max_show;
				if($from2 > $number) {
					$diff = $number % $max_show;
					$from2 = $number;
					$from1 = $from2 - $diff;
				} else {
					$from1 = $from2 - $max_show;
				}

			echo "<table class=\"table table-condensed\">";
			echo "<thead><tr>
			<th>"._("ID")."</th>
			<th>"._("IP Address")."</th>
			<th>"._("Username")."</th>
			<th>"._("Country")."</th>
			<th>"._("Access")."</th>
			</tr></thead>";

			/*
			loading all the tags data in arrays.
			*/
			while($row = $q_do->fetch (PDO::FETCH_ASSOC)) {
				$idarr[] = intval($row['id']);
				$iparr[] = cleanInput($row['ip_address']);
				$uarr[] = cleanInput($row['userid']);
				$darr[] = cleanInput($row['datetime']);
			}

			echo "<tbody>";
			for($i=$from1; $i < $from2; $i++) {
				$id = $idarr[$i];
				$ip = $iparr[$i];
				$uid = $uarr[$i];
				$date = $darr[$i];

				/*
				fetching the user's name from the database
				*/
				$name = get_username($uid);
				
				/*
				fetching country name for the specific ip address
				*/
				$country = $ip2country->get_country_name($ip);
				
				echo "<tr>";
				echo "<td>{$id}</td>";
				echo "<td>{$ip}</td>";
				echo "<td><a href=\"{$website}/".ADMIN_DIRECTORY."/manageuser/{$uid}#/logs\">{$name}</a></td>";
				echo "<td>{$country}</td>";
				echo "<td><abbr class=\"micro\" title=\"{$date}\"></abbr></td>";
				echo "</tr>";
			}
			echo "</tbody>";
			echo "</table>";

				/*
				displaying pagination below the table.
				*/
				if($number > $max_show) {
					$p->show();
				}			
		} else {
			echo "<div class=\"alert\"><strong>"._("No Access Records.")."</strong><br/>"._("There are no access records in the database.")."</div>";
			echo "<div class=\"alert alert-info\"><strong>"._("Suggestion")."</strong><br/>"._("Make sure you have enabled the option to log user access information in the settings panel.")."</div>";
		}
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>