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
class for fetching user gravatar pic associated with the email address.
*/
class Gravatar {
    const GRAVATAR_URL = "http://www.gravatar.com/avatar.php";
    private $GRAVATAR_RATING = array("G", "PG", "R", "X");
    protected $properties = array(
        "gravatar_id"    => null,
        "default"        => null,
        "size"            => 80,
        "rating"        => null,
        "border"        => null,
    );
    protected $email = "";
    protected $extra = "";
    public function __construct($email = null, $default = null) {
        $this->setEmail($email);
        $this->setDefault($default);
    }

    public function setEmail($email) {
        if ($this->isValidEmail($email)) {
            $this->email = $email;
            $this->properties['gravatar_id'] = md5(strtolower($this->email));
            return true;
        }
        return false;
    }
	
	public function setDefault($default) {
        $this->properties['default'] = $default;
    }

    public function setRating($rating) {
		if(in_array($rating, $this->GRAVATAR_RATING)) {
			$this->properties['rating'] = $rating;
            return true;
        }
        return false;
    }

    public function setSize($size) {
        $size = (int) $size;
        if($size <= 0)
            $size = null;
        $this->properties['size'] = $size;
    }

    public function setExtra($extra) {
        $this->extra = $extra;
    }

    public function isValidEmail($email) {
        return preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email);
    }

    public function __get($var) {
		return @$this->properties[$var];
	}

    public function __set($var, $value) {
		switch($var) {
			case "email":    return $this->setEmail($value);
            case "rating":    return $this->setRating($value);
            case "default":    return $this->setDefault($value);
            case "size":    return $this->setSize($value);

            case "gravatar_id": return;
        }
        return @$this->properties[$var] = $value;
    }

    public function __isset($var) {
		return isset($this->properties[$var]);
	}

    public function __unset($var) {
		return @$this->properties[$var] == null;
	}

    public function getSrc() {
		$url = self::GRAVATAR_URL ."?";
        $first = true;
        foreach($this->properties as $key => $value) {
            if(isset($value)) {
                if(!$first)
                    $url .= "&";
                $url .= $key."=".urlencode($value);
                $first = false;
            }
        }
        return $url;    
    }
	
	public function toHTML() {
        return '<img src="'. $this->getSrc() .'"'
                .(!isset($this->size) ? "" : ' width="'.$this->size.'" height="'.$this->size.'"')
                .$this->extra
                .' />';    
    }

    public function __toString() {
		return $this->toHTML();
	}
}
?>