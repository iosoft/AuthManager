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
sessions class for the application. this class stores the normal PHP sessions in the database rather than in the files which is not a secure method.
*/
class dbSession {
	public function dbSession($db, $gc_maxlifetime = "", $gc_probability = "", $gc_divisor = "", $securityCode = "eF@0#u^*sZD9!S$%") {
		if($gc_maxlifetime != "" && is_integer($gc_maxlifetime)) {
			@ini_set('session.gc_maxlifetime', $gc_maxlifetime);
		}

		if($gc_probability != "" && is_integer($gc_probability)) {
			@ini_set('session.gc_probability', $gc_probability);
		}

		if($gc_divisor != "" && is_integer($gc_divisor)) {
			@ini_set('session.gc_divisor', $gc_divisor);
		}

		$this->db              = $db;
		$this->sessionLifetime = ini_get("session.gc_maxlifetime");
		$this->securityCode    = $securityCode;
		
		session_set_save_handler(array(
			&$this,
			'open'
		), array(
			&$this,
			'close'
		), array(
			&$this,
			'read'
		), array(
			&$this,
			'write'
		), array(
			&$this,
			'destroy'
		), array(
			&$this,
			'gc'
		));
		register_shutdown_function('session_write_close');
		session_start();
	}

	public function stop() {
		$this->regenerate_id();
		session_unset();
		session_destroy();		
	}
	
	public function regenerate_id() {
		$oldSessionID = session_id();
		session_regenerate_id();
		$this->destroy($oldSessionID);
	}
	
	public function get_users_online() {
		$this->gc($this->sessionLifetime);
		$query  = "SELECT COUNT(`session_id`) as count FROM `sessions`";
		$result = $this->db->query($query)->fetch(PDO::FETCH_ASSOC);
		
		return intval($result["count"]);
	}
	
	public function open($save_path, $session_name) {
		return true;
	}
	
	public function close() {
		return true;
	}
	
	public function read($session_id) {
		$user_agent = md5($_SERVER["HTTP_USER_AGENT"] . $this->securityCode);
		$query_time = time();
		
		try {
			$query    = "SELECT `session_data` FROM `sessions` WHERE `session_id` = :session_id AND `http_user_agent` = :user_agent AND `session_expire` > :time LIMIT 1";
			$query_do = $this->db->prepare($query);
			$query_do->bindParam(':session_id', $session_id, PDO::PARAM_STR);
			$query_do->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
			$query_do->bindParam(':time', $query_time, PDO::PARAM_INT);
			$query_do->execute();
			$number = $this->db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			/* echo $e->getMessage(); */
		}
		
		if(!empty($number)) {
			$fields = $query_do->fetch(PDO::FETCH_ASSOC);
			return $fields["session_data"];
		}
		
		return "";
	}
	
	public function write($session_id, $session_data) {
		$user_agent     = md5($_SERVER["HTTP_USER_AGENT"] . $this->securityCode);
		$session_expire = time() + $this->sessionLifetime;
		
		try {
			$write_session    = "INSERT INTO `sessions` (`session_id`, `http_user_agent`, `session_data`, `session_expire`) VALUE(:session_id, :user_agent, :session_data, :session_expire) ON DUPLICATE KEY UPDATE session_data = :session_data, session_expire = :session_expire";
			$write_session_do = $this->db->prepare($write_session);
			$write_session_do->bindParam(':session_id', $session_id, PDO::PARAM_STR);
			$write_session_do->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
			$write_session_do->bindParam(':session_data', $session_data, PDO::PARAM_LOB);
			$write_session_do->bindParam(':session_expire', $session_expire, PDO::PARAM_INT);
			$confirm = $write_session_do->execute();
		} catch(PDOException $e) {
			/* echo $e->getMessage(); */
		}
		
		if(!empty($confirm)) {
			return true;
		} else {
			return "";
		}
		
		return false;
	}

	public function destroy($session_id) {
		try {
			$delete_session    = "DELETE FROM `sessions` WHERE `session_id` = :session_id";
			$delete_session_do = $this->db->prepare($delete_session);
			$delete_session_do->bindParam(':session_id', $session_id, PDO::PARAM_STR);
			$delete_session_do->execute();
		} catch(PDOException $e) {
			/* echo $e->getMessage(); */
		}
		
		if($delete_session_do->rowCount()) {
			return true;
		}
		
		return false;
	}
	
	public function gc($maxlifetime) {
		$query_time = time() - $maxlifetime;

		try {
			$gc    = "DELETE FROM `sessions` WHERE `session_expire` < :query_time";
			$gc_do = $this->db->prepare($gc);
			$gc_do->bindParam(':query_time', $query_time, PDO::PARAM_INT);
			$gc_do->execute();
		} catch(PDOException $e) {
			/* echo $e->getMessage(); */
		}
	}
}

?>