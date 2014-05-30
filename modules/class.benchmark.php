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
benchmark class for the application. this class is used to show the page generation time taken by the server in seconds. useful for performance testing.
*/
class benchmark {
	var $start;
	var $pause_time;

	/*
	start the timer 
	*/
	function benchmark($start = 0) {
		if($start) {
			$this->start();
		}
	}

	/*
	start the timer
	*/
	function start() {
		$this->start = $this->get_time();
		$this->pause_time = 0;
	}

	/*
	pause the timer
	*/
	function pause() {
		$this->pause_time = $this->get_time();
	}

	/*
	unpause the timer
	*/
	function unpause() {
		$this->start += ($this->get_time() - $this->pause_time);
		$this->pause_time = 0;
	}

	/*
	get the current timer value
	*/
	function get($decimals = 8) {
		return round(($this->get_time() - $this->start),$decimals);
	}

	/*
	format the time in seconds
	*/
	function get_time() {
		list($usec,$sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
}
?>