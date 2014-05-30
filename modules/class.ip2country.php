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
class to convert ip address to country and more.
*/
class ip2country {
	private $ip_num = 0;
	private $ip = "";
	private $country_code = "";
	private $country_name = "";

	function ip2country($db) {
		$this->db = $db;
		$this->set_ip();
	}

	public function get_ip_num() {
		return $this->ip_num;
	}

	public function set_ip($newip = "") {
		if($newip == "")
		$newip = $this->get_client_ip();

		$this->ip = $newip;
		$this->calculate_ip_num();
		$this->country_code = "";
		$this->country_name = "";
	}

	public function calculate_ip_num() {
		if($this->ip == "")
		$this->ip=$this->get_client_ip();
		$this->ip_num=sprintf("%u", ip2long($this->ip));
	}

	public function get_country_code($ip_addr = "") {
		if($ip_addr != "" && $ip_addr != $this->ip)
		$this->set_ip($ip_addr);

		if($ip_addr == "") {
			if($this->ip != $this->get_client_ip())
			$this->set_ip();
		}

		if($this->country_code != "")
		return $this->country_code;

		try {
			$sq = "SELECT `country_code`, `country_name` FROM `ips` WHERE :ip_num BETWEEN begin_ip_num AND end_ip_num";
			$sq_do = $this->db->prepare($sq);
			$sq_do->bindParam(':ip_num', $this->ip_num, PDO::PARAM_STR);
			$sq_do->execute();
			$number = $this->db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $e) {
			/* echo $e->getMessage(); */
		}

		if(empty($number)) {
			return "";
		} else {
			$row = $sq_do->fetch (PDO::FETCH_ASSOC);
			$this->country_name = $row['country_name'];
			$this->country_code = $row['country_code'];
			return $row['country_code'];
		}
	}

	public function get_country_name($ip_addr = "") {
		$this->get_country_code($ip_addr);
		return $this->country_name;
	}

	public function get_client_ip() {
		$v = "";
		$v = (!empty($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR'] :((!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR']: @getenv('REMOTE_ADDR'));
		if(isset($_SERVER['HTTP_CLIENT_IP']))
		$v = $_SERVER['HTTP_CLIENT_IP'];
		return htmlspecialchars($v,ENT_QUOTES);
	}
}
?>