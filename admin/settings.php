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
/*
including filewriter class to write the changes made to CSS files from the settings panel.
*/
include("../".MODS_DIRECTORY."/class.filewriter.php");
include("../".USER_DIRECTORY."/header.php");

$js = "<script src=\"{$website}/".JS_DIRECTORY."/jquery.hashchange.js\"></script>
<script src=\"{$website}/".JS_DIRECTORY."/admin.base.js\"></script>";
subheader(_("Site Settings"), null, $js);

if($sesslife == true) {
	if($is_admin == 1) {
		/*
		processing the updated values if the form is submitted, else show the respective settings page to the admin.
		*/
		if(isset($_POST["updatesettings"])) {
			if(isset($_POST["section"])) {
				$_section = cleaninput($_POST["section"]);
					if(!empty($_section)) {
						if($_section == "general") {
							$web = cleanInput($_POST["website"]);
							$webtitle = cleanInput($_POST["title"]);
							$description = cleanInput($_POST["description"]);
							$keywords = cleanInput($_POST["keywords"]);
							$sending_email = cleanInput($_POST["sending_email"]);
							$user_verification = intval($_POST["user_verification"]);
								if(!empty($web) && !empty($webtitle)) {
									/*
									remove the "/" from the website address.
									*/
									if(substr($web, strlen($web) - 1) == "/")
										$web = substr($web,0, strlen($web) - 1);
										
									/*
									updating the website option in the database.
									*/
									try {
										$q = "UPDATE `settings` SET `option_value` = :website WHERE `option_name` = 'website'";
										$q_do = $db->prepare($q);
										$q_do->bindParam(':website', $web, PDO::PARAM_STR);
										$q_do->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}
									
									/*
									updating the website title option in the database.
									*/
									try {
										$q = "UPDATE `settings` SET `option_value` = :webtitle WHERE `option_name` = 'title'";
										$q_do = $db->prepare($q);
										$q_do->bindParam(':webtitle', $webtitle, PDO::PARAM_STR);
										$q_do->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}
									
									/*
									updating the description option in the database.
									*/
									try {
										$q = "UPDATE `settings` SET `option_value` = :description WHERE `option_name` = 'description'";
										$q_do = $db->prepare($q);
										$q_do->bindParam(':description', $description, PDO::PARAM_STR);
										$q_do->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}

									/*
									updating the keywords option in the database.
									*/
									try {
										$q = "UPDATE `settings` SET `option_value` = :keywords WHERE `option_name` = 'keywords'";
										$q_do = $db->prepare($q);
										$q_do->bindParam(':keywords', $keywords, PDO::PARAM_STR);
										$q_do->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}

									/*
									updating the sending email option in the database.
									*/
									try {
										$q = "UPDATE `settings` SET `option_value` = :sending_email WHERE `option_name` = 'sending_email'";
										$q_do = $db->prepare($q);
										$q_do->bindParam(':sending_email', $sending_email, PDO::PARAM_STR);
										$q_do->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}
									
									/*
									updating the user verification option in the database.
									*/
									try {
										$q = "UPDATE `settings` SET `option_value` = :user_verification WHERE `option_name` = 'user_verification'";
										$q_do = $db->prepare($q);
										$q_do->bindParam(':user_verification', $user_verification, PDO::PARAM_INT);
										$q_do->execute();
									} catch(PDOException $e) {
										$log->logError($e." - ".basename(__FILE__));
									}
									
									$err_general = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Settings Updated.")."</strong><br/>"._("Changes have been made successfully.")."</div>";
								} else {
									$err_general = "<div class=\"alert alert-error\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Empty Fields.")."</strong><br/>"._("Please make sure that Website & Webtitle fields are not empty.")."</div>";
								}
						} elseif($_section == "captcha") {
							$recaptcha_enabled = intval($_POST["recaptcha_enabled"]);
							$recaptcha_public = cleanInput($_POST["recaptcha_public"]);
							$recaptcha_private = cleanInput($_POST["recaptcha_private"]);

							/*
							updating the inbuilt captcha option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :recaptcha_enabled WHERE `option_name` = 'inbuilt_captcha'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':recaptcha_enabled', $recaptcha_enabled, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the recaptcha public option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :recaptcha_public WHERE `option_name` = 'recaptcha_public'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':recaptcha_public', $recaptcha_public, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the recaptcha private option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :recaptcha_private WHERE `option_name` = 	'recaptcha_private'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':recaptcha_private', $recaptcha_private, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_captcha = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Settings Updated.")."</strong><br/>"._("Changes have been made successfully.")."</div>";
						} elseif($_section == "analytics") {
							$analytics_enabled = intval($_POST["analytics_enabled"]);
							$analytics_code = $_POST["analytics_code"];
							$google_id = cleanInput($_POST["google_id"]);
							$google_password = cleanInput($_POST["google_password"]);
							$site_id = intval($_POST["site_id"]);

							/*
							updating the enable analytics option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :analytics_enabled WHERE `option_name` = 'analytics_enabled'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':analytics_enabled', $analytics_enabled, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the analytics code option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :analytics_code WHERE `option_name` = 'analytics_code'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':analytics_code', $analytics_code, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the google id option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :google_id WHERE `option_name` = 'google_id'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':google_id', $google_id, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							/*
							updating the google password option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :google_password WHERE `option_name` = 'google_password'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':google_password', $google_password, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							/*
							updating the site id option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :site_id WHERE `option_name` = 'site_id'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':site_id', $site_id, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_analytics = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Settings Updated.")."</strong><br/>"._("Changes have been made successfully.")."</div>";
						} elseif($_section == "facebook") {
							if(isset($_POST["enable_facebook"])) {
								$enable_facebook = intval($_POST["enable_facebook"]);
							} else {
								$enable_facebook = 0;
							}

							$facebook_api = cleanInput($_POST["facebook_api"]);
							$facebook_secret = cleanInput($_POST["facebook_secret"]);

							/*
							updating the enable facebook option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :enable_facebook WHERE `option_name` = 'enable_facebook'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':enable_facebook', $enable_facebook, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the facebook api option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :facebook_api WHERE `option_name` = 'facebook_api'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':facebook_api', $facebook_api, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the facebook secret option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :facebook_secret WHERE `option_name` = 'facebook_secret'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':facebook_secret', $facebook_secret, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_facebook = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Settings Updated.")."</strong><br/>"._("Changes have been made successfully.")."</div>";
						} elseif($_section == "css-bootstrap") {
							$css_file = $_POST["css-bootstrap"];
							$file = new filewriter;
							/*
							write to a new file
							*/
							$file->write("../css/bootstrap.css", $css_file);
							$err_css = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("CSS Updated.")."</strong><br/>"._("Changes made to the CSS file have been saved.")."</div>";
						} elseif($_section == "css-responsive") {
							$css_file = $_POST["css-responsive"];
							$file = new filewriter;
							/*
							write to a new file
							*/
							$file->write("../css/bootstrap-responsive.css", $css_file);
							$err_css = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("CSS Updated.")."</strong><br/>"._("Changes made to the CSS file have been saved.")."</div>";
						} elseif($_section == "css-style") {
							$css_file = $_POST["css-style"];
							$file = new filewriter;
							/*
							write to a new file
							*/
							$file->write("../css/style.css", $css_file);
							$err_css = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("CSS Updated.")."</strong><br/>"._("Changes made to the CSS file have been saved.")."</div>";
						} elseif($_section == "email-newuser") {
							$subject_newuser = $_POST["subject-newuser"];
							$message_newuser = $_POST["email-newuseremail"];

							/*
							updating the email template for "new user" in the database.
							*/
							try {
								$q = "UPDATE `emails` SET `subject` = :subject, `message` = :message WHERE `slug` = 'new_user'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':subject', $subject_newuser, PDO::PARAM_STR);
								$q_do->bindParam(':message', $message_newuser, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_email = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Template Updated.")."</strong><br/>"._("Changes made to the email template have been saved.")."</div>";
						} elseif($_section == "email-forgot") {
							$subject_forgotpass = $_POST["subject-forgotpass"];
							$message_forgotpass = $_POST["email-forgotpass"];

							/*
							updating the email template for "new user" in the database.
							*/
							try {
								$q = "UPDATE `emails` SET `subject` = :subject, `message` = :message WHERE `slug` = 'forgot_pass'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':subject', $subject_forgotpass, PDO::PARAM_STR);
								$q_do->bindParam(':message', $message_forgotpass, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_email = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Template Updated.")."</strong><br/>"._("Changes made to the email template have been saved.")."</div>";
						} elseif($_section == "email-verify") {
							$subject_verifyemail = $_POST["subject-verifyemail"];
							$message_verifyemail = $_POST["email-verifyemail"];

							/*
							updating the email template for "new user" in the database.
							*/
							try {
								$q = "UPDATE `emails` SET `subject` = :subject, `message` = :message WHERE `slug` = 'verify_email'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':subject', $subject_verifyemail, PDO::PARAM_STR);
								$q_do->bindParam(':message', $message_verifyemail, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_email = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Template Updated.")."</strong><br/>"._("Changes made to the email template have been saved.")."</div>";
						} elseif($_section == "email-newpass") {
							$subject_newpass = $_POST["subject-newpass"];
							$message_newpass = $_POST["email-newpassword"];

							/*
							updating the email template for "new user" in the database.
							*/
							try {
								$q = "UPDATE `emails` SET `subject` = :subject, `message` = :message WHERE `slug` = 'new_pass'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':subject', $subject_newpass, PDO::PARAM_STR);
								$q_do->bindParam(':message', $message_newpass, PDO::PARAM_STR);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_email = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Template Updated.")."</strong><br/>"._("Changes made to the email template have been saved.")."</div>";
						} elseif($_section == "misc") {
							if(isset($_POST["enable_access"])) {
								$enable_access = intval($_POST["enable_access"]);
							} else {
								$enable_access = 0;
							}

							$max_invalid_login = cleanInput($_POST["max_invalid_login"]);
							$session_timeout = cleanInput($_POST["session_timeout"]);

							/*
							updating the enable access option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :enable_access WHERE `option_name` = 'enable_access'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':enable_access', $enable_access, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the maximum invalid logins option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :max_invalid_login WHERE `option_name` = 'max_invalid_login'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':max_invalid_login', $max_invalid_login, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}

							/*
							updating the session timeout option in the database.
							*/
							try {
								$q = "UPDATE `settings` SET `option_value` = :session_timeout WHERE `option_name` = 'session_timeout'";
								$q_do = $db->prepare($q);
								$q_do->bindParam(':session_timeout', $session_timeout, PDO::PARAM_INT);
								$q_do->execute();
							} catch(PDOException $e) {
								$log->logError($e." - ".basename(__FILE__));
							}
							
							$err_misc = "<div class=\"alert alert-success\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><strong>"._("Settings Updated.")."</strong><br/>"._("Changes have been made successfully.")."</div>";
						}
					} else {
						/* some error processing your request. */
					}
			} else {
				/* some error processing your request. */
			}
		}

		/*
		displaying the admin settings screen to the user.
		*/
		admin_settings();
	} else {
		echo "<div class=\"alert alert-error\"><strong>"._("Unauthorized Access.")."</strong><br/>"._("You are not authorized to access this page.")."</div>";
	}
} else {
	echo "<meta http-equiv=\"refresh\" content=\"0;url={$website}/".USER_DIRECTORY."/login\" />";
}

include("../".USER_DIRECTORY."/footer.php");
?>