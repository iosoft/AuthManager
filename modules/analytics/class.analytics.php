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
google analytics class for analyzing website traffic from the application backend.
*/
class analytics {
	private $_sUser;
    private $_sPass;
    private $_sAuth;
    private $_sProfileId;
    private $_sStartDate;
    private $_sEndDate;
    private $_bUseCache;
    private $_iCacheAge;

    public function __construct($sUser, $sPass){
        $this->_sUser = $sUser;
        $this->_sPass = $sPass;     
        $this->_bUseCache = false;
        $this->auth();
    }

    private function auth() {
        if(isset($_SESSION['auth'])) {
            $this->_sAuth = $_SESSION['auth'];
            return;
        }

        $aPost = array ('accountType'   => 'GOOGLE', 
                        'Email'         => $this->_sUser,
                        'Passwd'        => $this->_sPass,
                        'service'       => 'analytics',
                        'source'        => 'SWIS-Webbeheer-4.0');
        $sResponse = $this->getUrl('https://www.google.com/accounts/ClientLogin', $aPost);
    
        $_SESSION['auth'] = '';
        if(strpos($sResponse, "\n") !== false) {
            $aResponse = explode("\n", $sResponse);
            foreach($aResponse as $sResponse) {
                if(substr($sResponse, 0, 4) == 'Auth') {
                    $_SESSION['auth'] = trim(substr($sResponse, 5));
                }
            }
        }
        if($_SESSION['auth'] == '') {
            unset($_SESSION['auth']);
            throw new Exception('Retrieving Auth hash failed!');
        }
        $this->_sAuth = $_SESSION['auth']; 
    }

    public function useCache($bCaching = true, $iCacheAge = 300) {
        $this->_bUseCache = $bCaching;
        $this->_iCacheAge = $iCacheAge;
        if($bCaching && !isset($_SESSION['cache'])) {
            $_SESSION['cache'] = array();     
        }
    }

    private function getXml($sUrl) {
        return $this->getUrl($sUrl, array(), array('Authorization: GoogleLogin auth=' . $this->_sAuth));
    }

    public function setProfileById($sProfileId) {
		$this->_sProfileId = $sProfileId;
    }

    public function setProfileByName($sAccountName) {
        if(isset($_SESSION['profile'])) {
			$this->_sProfileId = $_SESSION['profile'];
            return;
        }

        $this->_sProfileId = '';
        $sXml = $this->getXml('https://www.google.com/analytics/feeds/accounts/default');
        $aAccounts = $this->parseAccountList($sXml);
            
        foreach($aAccounts as $aAccount) {
            if(isset($aAccount['accountName']) && $aAccount['accountName'] == $sAccountName) {
                if(isset($aAccount['tableId'])) {
                    $this->_sProfileId =  $aAccount['tableId'];
                }
            }    
        }
        if($this->_sProfileId == '') {
			throw new Exception('No profile ID found!');
        }

        $_SESSION['profile'] = $this->_sProfileId;
    }

    public function getProfileList() {
		$sXml = $this->getXml('https://www.google.com/analytics/feeds/accounts/default');
        $aAccounts = $this->parseAccountList($sXml);
        $aReturn = array();
        foreach($aAccounts as $aAccount) {
            $aReturn[$aAccount['tableId']] =  $aAccount['title'];
        }       
        return $aReturn;
    }

    private function getCache($sKey) {
        if($this->_bUseCache === false) {
            return false;
        }

        if(!isset($_SESSION['cache'][$this->_sProfileId])) {
            $_SESSION['cache'][$this->_sProfileId] = array();
        }  
        if(isset($_SESSION['cache'][$this->_sProfileId][$sKey])) {
			if(time() - $_SESSION['cache'][$this->_sProfileId][$sKey]['time'] < $this->_iCacheAge) {
				return $_SESSION['cache'][$this->_sProfileId][$sKey]['data'];
            }
        }
        return false;
    }

    private function setCache($sKey, $mData) {
        if($this->_bUseCache === false) {
			return false;
        }

        if(!isset($_SESSION['cache'][$this->_sProfileId])) {
			$_SESSION['cache'][$this->_sProfileId] = array();
        }
        $_SESSION['cache'][$this->_sProfileId][$sKey] = array('time'  => time(), 'data'  => $mData);
    }

    public function getData($aProperties = array()) {
        $aParams = array();
        foreach($aProperties as $sKey => $sProperty) {
			$aParams[] = $sKey . '=' . $sProperty;
        }
        
        $sUrl = 'https://www.google.com/analytics/feeds/data?ids=' . $this->_sProfileId . 
                                                        '&start-date=' . $this->_sStartDate . 
                                                        '&end-date=' . $this->_sEndDate . '&' . 
                                                        implode('&', $aParams);
        $aCache = $this->getCache($sUrl);
        if($aCache !== false) {
			return $aCache;
        }

        $sXml = $this->getXml($sUrl);
        $aResult = array();
        $oDoc = new DOMDocument();
        $oDoc->loadXML($sXml);
        $oEntries = $oDoc->getElementsByTagName('entry');
        foreach($oEntries as $oEntry) {
            $oTitle = $oEntry->getElementsByTagName('title');
            $sTitle = $oTitle->item(0)->nodeValue;
            $oMetric = $oEntry->getElementsByTagName('metric');

            if(strpos($sTitle, ' | ') !== false && strpos($aProperties['dimensions'], ',') !== false) {
                $aDimensions = explode(',', $aProperties['dimensions']);
                $aDimensions[] = '|';
                $aDimensions[] = '=';
                $sTitle = preg_replace('/\s\s+/', ' ', trim(str_replace($aDimensions, '', $sTitle)));                
            }

			$sTitle = str_replace($aProperties['dimensions'] . '=', '', $sTitle);
            $aResult[$sTitle] = $oMetric->item(0)->getAttribute('value');
        }

        $this->setCache($sUrl, $aResult);
        return $aResult;
	}

    private function parseAccountList($sXml) {
        $oDoc = new DOMDocument();
        $oDoc->loadXML($sXml);
        $oEntries = $oDoc->getElementsByTagName('entry');
        $i = 0;
        $aProfiles = array();
        foreach($oEntries as $oEntry) {
            $aProfiles[$i] = array();
            $oTitle = $oEntry->getElementsByTagName('title');
            $aProfiles[$i]["title"] = $oTitle->item(0)->nodeValue;
            $oEntryId = $oEntry->getElementsByTagName('id');
            $aProfiles[$i]["entryid"] = $oEntryId->item(0)->nodeValue;
            $oProperties = $oEntry->getElementsByTagName('property');
            foreach($oProperties as $oProperty) {
                if(strcmp($oProperty->getAttribute('name'), 'ga:accountId') == 0) {
					$aProfiles[$i]["accountId"] = $oProperty->getAttribute('value');
                }    
                if(strcmp($oProperty->getAttribute('name'), 'ga:accountName') == 0) {
					$aProfiles[$i]["accountName"] = $oProperty->getAttribute('value');
                }
                if(strcmp($oProperty->getAttribute('name'), 'ga:profileId') == 0) {
					$aProfiles[$i]["profileId"] = $oProperty->getAttribute('value');
                }
                if(strcmp($oProperty->getAttribute('name'), 'ga:webPropertyId') == 0) {
					$aProfiles[$i]["webPropertyId"] = $oProperty->getAttribute('value');
                }
            }

            $oTableId = $oEntry->getElementsByTagName('tableId');
            $aProfiles[$i]["tableId"] = $oTableId->item(0)->nodeValue;
            $i++;
        }
        return $aProfiles;
    }

    private function getUrl($sUrl, $aPost = array(), $aHeader = array()) {
        if(count($aPost) > 0) {
			$sMethod = 'POST'; 
            $sPost = http_build_query($aPost);    
            $aHeader[] = 'Content-type: application/x-www-form-urlencoded';
            $aHeader[] = 'Content-Length: ' . strlen($sPost);
            $sContent = $aPost;
        } else {
            $sMethod = 'GET';
            $sContent = null;
        }

        if(function_exists('curl_init')) {
            $rRequest = curl_init();
            curl_setopt($rRequest, CURLOPT_URL, $sUrl);
			curl_setopt($rRequest, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($rRequest, CURLOPT_RETURNTRANSFER, 1);
            
            if($sMethod == 'POST') {
                curl_setopt($rRequest, CURLOPT_POST, 1); 
                curl_setopt($rRequest, CURLOPT_POSTFIELDS, $aPost); 
            } else {
                curl_setopt($rRequest, CURLOPT_HTTPHEADER, $aHeader);
            }
            
            $sOutput = curl_exec($rRequest);
            if($sOutput === false) {
                throw new Exception('Curl error (' . curl_error($rRequest) . ')');    
            }
            
            $aInfo = curl_getinfo($rRequest);
            
            if($aInfo['http_code'] != 200) {
                if($aInfo['http_code'] == 400) {
                    throw new Exception('Bad request (' . $aInfo['http_code'] . ') url: ' . $sUrl);     
                }
                if($aInfo['http_code'] == 403) {
                    throw new Exception('Access denied (' . $aInfo['http_code'] . ') url: ' . $sUrl);     
                }
                throw new Exception('Not a valid response (' . $aInfo['http_code'] . ') url: ' . $sUrl);
            }
            
            curl_close($rRequest);
        } else {
            $aContext = array('http' => array('method' => $sMethod, 'header'=> implode("\r\n", $aHeader) . "\r\n", 'content' => $sContent));
            $rContext = stream_context_create($aContext);
            $sOutput = @file_get_contents($sUrl, 0, $rContext);
            if(strpos($http_response_header[0], '200') === false) {
				throw new Exception('Not a valid response (' . $http_response_header[0] . ') url: ' . $sUrl);
            }
        }
        return $sOutput;
    }

    public function setDateRange($sStartDate, $sEndDate) {
		$this->_sStartDate = $sStartDate;
		$this->_sEndDate   = $sEndDate;
    }

    public function setMonth($iMonth, $iYear) {
        $this->_sStartDate = date('Y-m-d', strtotime($iYear . '-' . $iMonth . '-01')); 
        $this->_sEndDate   = date('Y-m-d', strtotime($iYear . '-' . $iMonth . '-' . date('t', strtotime($iYear . '-' . $iMonth . '-01'))));
    }

    public function getVisitors() {
        return $this->getData(array('dimensions' => 'ga:day', 'metrics' => 'ga:visits', 'sort' => 'ga:day'));
    }

    public function getPageviews() {
        return $this->getData(array('dimensions' => 'ga:day', 'metrics' => 'ga:pageviews', 'sort' => 'ga:day'));
    }

    public function getVisitsPerHour() {
		return $this->getData(array('dimensions' => 'ga:hour', 'metrics' => 'ga:visits', 'sort' => 'ga:hour'));
    }

    public function getBrowsers() {        
        $aData = $this->getData(array('dimensions' => 'ga:browser,ga:browserVersion', 'metrics' => 'ga:visits', 'sort' => 'ga:visits'));
        arsort($aData);

        return $aData;
    }

    public function getOperatingSystem() {
		$aData = $this->getData(array('dimensions' => 'ga:operatingSystem', 'metrics' => 'ga:visits', 'sort' => 'ga:visits'));
        arsort($aData);

		return $aData; 
    }

    public function getScreenResolution() {
        $aData = $this->getData(array('dimensions' => 'ga:screenResolution', 'metrics' => 'ga:visits', 'sort' => 'ga:visits'));
        arsort($aData);

        return $aData; 
    }

    public function getReferrers() {
		$aData = $this->getData(array('dimensions' => 'ga:source', 'metrics' => 'ga:visits', 'sort' => 'ga:source'));
        arsort($aData);

		return $aData; 
    }

    public function getSearchWords() {
		$aData = $this->getData(array('dimensions' => 'ga:keyword', 'metrics' => 'ga:visits', 'sort' => 'ga:keyword'));
        arsort($aData);

        return $aData; 
    }
}
?>