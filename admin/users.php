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
include("../".USER_DIRECTORY."/header.php");

$js = "<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/jquery.timeago.js\"></script>
<script type=\"text/javascript\">
$(function() {
	$(\".micro\").timeago();
});
</script>
<script type=\"text/javascript\" src=\"{$website}/".JS_DIRECTORY."/admin.base.js\"></script>";
subheader(_("Manage Users"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		/*
		$err holds the most recent error for the photos page and displays it on the page.
		*/
		$err = null;

		/*
		for user actions i.e move to trash, delete forever and generate code etc.
		*/
		if(isset($_POST["content_selector_2"])) {
			$sel = cleanInput($_POST["content_selector_2"]);

			if($sel == "xx") {
				/* this is for deleting the photos forever from the server. */
				if(isset($_POST["users"])) {
					$users = $_POST["users"];
						while(list($index, $id) = each($users)) {
							deleteUser($id);
						}

					$err = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("User(s) removed.")."</strong><br/>"._("Your selected users have been removed.")."</div>";
				} else {
					$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Select User(s).")."</strong><br/>"._("You must select atleast one user to apply this action.")."</div>";
				}
			} else {
				/*
				if none of the above is selected.
				*/
				$err = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Select an option.")."</strong><br/>"._("You must select an option to do anything.")."</div>";
			}
		}

		/*
		few variables necessary for the photos page.
		*/
		$current_address = current_url();

		if(isset($_GET["p"])) {
			$page = intval($_GET["p"]);
		} else {
			$page = 1;
		}

		$max_show = 10;

		try {
			$q = "SELECT * FROM `members` ORDER BY `id` DESC";
			$q_do = $db->prepare($q);
			$q_do->execute();
			$number = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			$log->logError($e." - ".basename(__FILE__));
		}

		echo "<div class=\"page-header\"><h1>"._("Manage Users")."</h1></div>";
		echo "<h6>"._("Options")." : <a href=\"{$website}/".ADMIN_DIRECTORY."/add-user\">"._("Add User")."</a></h6><br/>";
		echo "<ul class=\"breadcrumb\">
		<li><a href=\"{$website}/".ADMIN_DIRECTORY."/settings\">"._("Home")."</a> <span class=\"divider\">/</span></li>
		<li class=\"active\">"._("Users")."</li>
		</ul>";

		/*
		this is the photo management panel offering different options.
		*/
		echo "<form class=\"well form-inline\" method=\"POST\" action=\"{$current_address}\" name=\"modify-form\">
		<div>&nbsp;<input type=\"checkbox\" name=\"checkall\" onclick=\"checkUncheckAll(this);\" />&nbsp;&nbsp;<select name=\"content_selector_2\"><option value=\"select\">-- "._("select")." --</option>";
		echo "<option value=\"xx\">&rarr; "._("Delete users")."</option></select>&nbsp;&nbsp;<input type=\"submit\" name=\"modifyphotos\" class=\"btn\" value=\""._("Apply")."\" /></div><br/>{$err}";

		if(!empty($number)) {
			$p = new pagination;
			$p->items($number);
			$p->limit($max_show);
			$p->currentPage($page);
			$p->parameterName("p");
			$p->urlFriendly();
			$p->target("{$website}/".ADMIN_DIRECTORY."/users/%");

			$from2 = $page * $max_show;
				if($from2 > $number) {
					$diff = $number % $max_show;
					$from2 = $number;
					$from1 = $from2 - $diff;
				} else {
					$from1 = $from2 - $max_show;
				}

			echo "<table class=\"table table-condensed\">";
			echo "<thead><tr><th></th>
			<th>"._("ID")."</th>
			<th>"._("Name")."</th>
			<th>"._("Verified")."</th>
			<th>"._("Email")."</th>
			<th>"._("Facebook ID")."</th>
			<th>"._("Last Access")."</th>
			</tr></thead>";
			echo "<tbody>";

			/*
			loading all the tags data in arrays.
			*/
			while($row = $q_do->fetch (PDO::FETCH_ASSOC)) {
				$idarr[] = intval($row['id']);
				$farr[] = cleanInput($row['first_name']);
				$larr[] = cleanInput($row['last_name']);
				$earr[] = cleanInput($row['email']);
				$varr[] = intval($row['verified']);
				$barr[] = intval($row['banned']);
				$aarr[] = cleanInput($row['access']);
				$fbarr[] = intval($row['fb_id']);
			}

			for($i=$from1; $i < $from2; $i++) {
				$id = $idarr[$i];
				$fn = $farr[$i];
				$ln = $larr[$i];
				$email_user = $earr[$i];
				$is_verified = $varr[$i];
				$banned = $barr[$i];
				$access_date = $aarr[$i];
				$fb_user = $fbarr[$i];

				if($banned == 1) {
					$class = "<span class=\"label label-important\">"._("Banned")."</span>";
				} else {
					$class = null;
				}

				/*
				assigning words to the verified status.
				*/
				switch($is_verified) {
					case 1:
						$is_verified = _("Yes");
						break;
					case 0:
						$is_verified = _("No");
						break;
					default:
						$is_verified = _("No");
						break;
				}
				
				/*
				displaying gravatar photo over here if email is associated with a gravatar account.
				*/
				$default = $website."/images/anonuser_50px.gif";
				$gravatar = new Gravatar($email_user, $default);
				$gravatar->size = 25;
?>
				<tr id="td<?php echo $id; ?>">
				<td><input type="checkbox" name="users[]" value="<?php echo $id; ?>" onclick="highlight(this);"></td>
				<td><?php echo $id; ?></td>
				<td><img class="adminusers thumbnail" src="<?php echo $gravatar->getSrc(); ?>" /><a href="<?php echo $website."/".ADMIN_DIRECTORY."/manageuser/".$id; ?>"><?php echo $fn." ".$ln; ?></a> &nbsp;<?php echo $class; ?></td>
				<td><?php echo $is_verified; ?></td>
				<td><?php echo $email_user; ?></td>
				<td><?php echo $fb_user; ?></td>
				<td><abbr class="micro" title="<?php echo $access_date; ?>"></abbr></td>
				</tr>
<?php
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
			echo "<div class=\"alert alert-error\"><strong>"._("No users found.")."</strong><br/>"._("There are no registered users till now.")."</div>";
		}

		echo "</form>";  
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>