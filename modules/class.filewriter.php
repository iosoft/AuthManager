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
class for writing to file on the system.
*/
define("WRITER_LOCK_FILES", TRUE);

class filewriter {
	public $files = array();

    function __construct() { }

	function write($file, $content) {
		$date = date("d-m-Y_his");
			if(!isset($files[$file])) {
				$files[$file] = fopen($file, "w");
					if(WRITER_LOCK_FILES) {
						flock($files[$file], LOCK_EX);
					}
			}
		fwrite($files[$file], $content);
	}

	function append($file, $content) {
		$date = date("d-m-Y_his");
			if(!isset($files[$file])) {
				/*
				make the file handle
				*/
				$files[$file] = fopen($file, "a");
					if(WRITER_LOCK_FILES) {
						flock($files[$file], LOCK_EX);
					}
			}
		fwrite($files[$file], $content);
	}

	function __destruct() {
		foreach($this->files as $file) {
			fclose($file);
		}

		if(WRITER_LOCK_FILES) {
			foreach($this->files as $file) {
				flock($file, LOCK_EX);
			}
		}
	}
}
?>