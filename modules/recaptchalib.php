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
captcha class for the online captcha service provided by reCAPTCHA.
*/
define("RECAPTCHA_API_SERVER", "http://api.recaptcha.net");
define("RECAPTCHA_API_SECURE_SERVER", "https://api-secure.recaptcha.net");
define("RECAPTCHA_VERIFY_SERVER", "api-verify.recaptcha.net");

function _recaptcha_qsencode($data) {
	$req = "";
	foreach ($data as $key => $value)
		$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
	
	// Cut the last '&'
	$req = substr($req, 0, strlen($req) - 1);
	return $req;	
}

function _recaptcha_http_post($host, $path, $data, $port = 80) {
	$req = _recaptcha_qsencode($data);
	
	$http_request = "POST $path HTTP/1.0\r\n";
	$http_request .= "Host: $host\r\n";
	$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
	$http_request .= "Content-Length: " . strlen($req) . "\r\n";
	$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
	$http_request .= "\r\n";
	$http_request .= $req;
	
	$response = '';
	if(false == ($fs = @fsockopen($host, $port, $errno, $errstr, 10))) {
		die('Could not open socket');
	}
	
	fwrite($fs, $http_request);
	
	while (!feof($fs))
		$response .= fgets($fs, 1160); // One TCP-IP packet
	fclose($fs);
	$response = explode("\r\n\r\n", $response, 2);
	
	return $response;
}

function recaptcha_get_html($pubkey, $error = null, $use_ssl = false) {
	if($pubkey == null || $pubkey == '') {
		die("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
	}
	
	if($use_ssl) {
		$server = RECAPTCHA_API_SECURE_SERVER;
	} else {
		$server = RECAPTCHA_API_SERVER;
	}

	$errorpart = "";
	if($error) {
		$errorpart = "&amp;error=" . $error;
	}
	return '<script type="text/javascript">
	var RecaptchaOptions = {
		theme : \'white\'
	};
	</script>
	<script type="text/javascript" src="' . $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>
	
	<noscript>
  		<iframe src="' . $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
  		<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
  		<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
	</noscript>';
}

class ReCaptchaResponse {
	var $is_valid;
	var $error;
}

function recaptcha_check_answer($privkey, $remoteip, $challenge, $response, $extra_params = array()) {
	if($privkey == null || $privkey == '') {
		die("To use reCAPTCHA you must get an API key from <a href='http://recaptcha.net/api/getkey'>http://recaptcha.net/api/getkey</a>");
	}
	
	if($remoteip == null || $remoteip == '') {
		die("For security reasons, you must pass the remote ip to reCAPTCHA");
	}
	
	if($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
		$recaptcha_response           = new ReCaptchaResponse();
		$recaptcha_response->is_valid = false;
		$recaptcha_response->error    = 'incorrect-captcha-sol';
		return $recaptcha_response;
	}
	
	$response = _recaptcha_http_post(RECAPTCHA_VERIFY_SERVER, "/verify", array(
		'privatekey' => $privkey,
		'remoteip' => $remoteip,
		'challenge' => $challenge,
		'response' => $response
	) + $extra_params);
	
	$answers            = explode("\n", $response[1]);
	$recaptcha_response = new ReCaptchaResponse();
	
	if(trim($answers[0]) == 'true') {
		$recaptcha_response->is_valid = true;
	} else {
		$recaptcha_response->is_valid = false;
		$recaptcha_response->error    = $answers[1];
	}
	return $recaptcha_response;	
}

function recaptcha_get_signup_url($domain = null, $appname = null) {
	return "http://recaptcha.net/api/getkey?" . _recaptcha_qsencode(array(
		'domain' => $domain,
		'app' => $appname
	));
}

function _recaptcha_aes_pad($val) {
	$block_size = 16;
	$numpad     = $block_size - (strlen($val) % $block_size);
	return str_pad($val, strlen($val) + $numpad, chr($numpad));	
}

function _recaptcha_aes_encrypt($val, $ky) {
	if(!function_exists("mcrypt_encrypt")) {
		die("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
	}
	$mode = MCRYPT_MODE_CBC;
	$enc  = MCRYPT_RIJNDAEL_128;
	$val  = _recaptcha_aes_pad($val);
	return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}

function _recaptcha_mailhide_urlbase64($x) {
	return strtr(base64_encode($x), '+/', '-_');
}

function recaptcha_mailhide_url($pubkey, $privkey, $email) {
	if($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
		die("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " . "you can do so at <a href='http://mailhide.recaptcha.net/apikey'>http://mailhide.recaptcha.net/apikey</a>");
	}

	$ky        = pack('H*', $privkey);
	$cryptmail = _recaptcha_aes_encrypt($email, $ky);
	
	return "http://mailhide.recaptcha.net/d?k=" . $pubkey . "&c=" . _recaptcha_mailhide_urlbase64($cryptmail);
}

function _recaptcha_mailhide_email_parts($email) {
	$arr = preg_split("/@/", $email);
	
	if (strlen($arr[0]) <= 4) {
		$arr[0] = substr($arr[0], 0, 1);
	} elseif(strlen($arr[0]) <= 6) {
		$arr[0] = substr($arr[0], 0, 3);
	} else {
		$arr[0] = substr($arr[0], 0, 4);
	}
	return $arr;
}

function recaptcha_mailhide_html($pubkey, $privkey, $email) {
	$emailparts = _recaptcha_mailhide_email_parts($email);
	$url        = recaptcha_mailhide_url($pubkey, $privkey, $email);
	
	return htmlentities($emailparts[0]) . "<a href='" . htmlentities($url) . "' onclick=\"window.open('" . htmlentities($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities($emailparts[1]);
}
?>