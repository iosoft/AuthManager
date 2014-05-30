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
class for generating application's own captcha to fight spam.
*/
define('CAPTCHA_SESSION_ID', 'php_captcha');
define('CAPTCHA_WIDTH', 200);
define('CAPTCHA_HEIGHT', 50);
define('CAPTCHA_NUM_CHARS', 5);
define('CAPTCHA_NUM_LINES', 70);
define('CAPTCHA_CHAR_SHADOW', false);
define('CAPTCHA_OWNER_TEXT', '');
define('CAPTCHA_CHAR_SET', '');
define('CAPTCHA_CASE_INSENSITIVE', true);
define('CAPTCHA_BACKGROUND_IMAGES', '');
define('CAPTCHA_MIN_FONT_SIZE', 16);
define('CAPTCHA_MAX_FONT_SIZE', 25);
define('CAPTCHA_USE_COLOUR', false);
define('CAPTCHA_FILE_TYPE', 'jpeg');
define('CAPTCHA_FLITE_PATH', '/usr/bin/flite');
define('CAPTCHA_AUDIO_PATH', '/tmp/');

class PhpCaptcha {
	var $oImage;
	var $aFonts;
	var $iWidth;
	var $iHeight;
	var $iNumChars;
	var $iNumLines;
	var $iSpacing;
	var $bCharShadow;
	var $sOwnerText;
	var $aCharSet;
	var $bCaseInsensitive;
	var $vBackgroundImages;
	var $iMinFontSize;
	var $iMaxFontSize;
	var $bUseColour;
	var $sFileType;
	var $sCode = '';
	
	function PhpCaptcha($aFonts, $iWidth = CAPTCHA_WIDTH, $iHeight = CAPTCHA_HEIGHT) {
		$this->aFonts = $aFonts;
		$this->SetNumChars(CAPTCHA_NUM_CHARS);
        $this->SetNumLines(CAPTCHA_NUM_LINES);
        $this->DisplayShadow(CAPTCHA_CHAR_SHADOW);
        $this->SetOwnerText(CAPTCHA_OWNER_TEXT);
        $this->SetCharSet(CAPTCHA_CHAR_SET);
        $this->CaseInsensitive(CAPTCHA_CASE_INSENSITIVE);
        $this->SetBackgroundImages(CAPTCHA_BACKGROUND_IMAGES);
        $this->SetMinFontSize(CAPTCHA_MIN_FONT_SIZE);
        $this->SetMaxFontSize(CAPTCHA_MAX_FONT_SIZE);
        $this->UseColour(CAPTCHA_USE_COLOUR);
        $this->SetFileType(CAPTCHA_FILE_TYPE);   
        $this->SetWidth($iWidth);
        $this->SetHeight($iHeight);
	}
	
	function CalculateSpacing() {
         $this->iSpacing = (int)($this->iWidth / $this->iNumChars);
	}
	
	function SetWidth($iWidth) {
		$this->iWidth = $iWidth;
		if ($this->iWidth > 500) $this->iWidth = 500; // to prevent perfomance impact
		$this->CalculateSpacing();
	}
	
	function SetHeight($iHeight) {
		$this->iHeight = $iHeight;
		if ($this->iHeight > 200) $this->iHeight = 200; // to prevent performance impact
	}
	
	function SetNumChars($iNumChars) {
		$this->iNumChars = $iNumChars;
		$this->CalculateSpacing();
	}
	
	function SetNumLines($iNumLines) {
		$this->iNumLines = $iNumLines;
	}
	
	function DisplayShadow($bCharShadow) {
		$this->bCharShadow = $bCharShadow;
	}
	
	function SetOwnerText($sOwnerText) {
		$this->sOwnerText = $sOwnerText;
	}
	
	function SetCharSet($vCharSet) {
		if(is_array($vCharSet)) {
			$this->aCharSet = $vCharSet;
		} else {
			if($vCharSet != '') {
				$aCharSet = explode(',', $vCharSet);
				$this->aCharSet = array();
					foreach($aCharSet as $sCurrentItem) {
						if(strlen($sCurrentItem) == 3) {
							$aRange = explode('-', $sCurrentItem);
								if(count($aRange) == 2 && $aRange[0] < $aRange[1]) {
									$aRange = range($aRange[0], $aRange[1]);
									$this->aCharSet = array_merge($this->aCharSet, $aRange);
								}
						} else {
							$this->aCharSet[] = $sCurrentItem;
						}
					}
            }
         }
	}
	
	function CaseInsensitive($bCaseInsensitive) {
		$this->bCaseInsensitive = $bCaseInsensitive;
	}
	
	function SetBackgroundImages($vBackgroundImages) {
		$this->vBackgroundImages = $vBackgroundImages;
	}
	
	function SetMinFontSize($iMinFontSize) {
		$this->iMinFontSize = $iMinFontSize;
	}
	
	function SetMaxFontSize($iMaxFontSize) {
		$this->iMaxFontSize = $iMaxFontSize;
	}
	
	function UseColour($bUseColour) {
		$this->bUseColour = $bUseColour;
	}
	
	function SetFileType($sFileType) {
		if(in_array($sFileType, array('gif', 'png', 'jpeg'))) {
			$this->sFileType = $sFileType;
		} else {
			$this->sFileType = 'jpeg';
		}
	}
	
	function DrawLines() {
		for($i = 0; $i < $this->iNumLines; $i++) {
			if($this->bUseColour) {
				$iLineColour = imagecolorallocate($this->oImage, rand(100, 250), rand(100, 250), rand(100, 250));
			} else {
				$iRandColour = rand(100, 250);
				$iLineColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
			}
            imageline($this->oImage, rand(0, $this->iWidth), rand(0, $this->iHeight), rand(0, $this->iWidth), rand(0, $this->iHeight), $iLineColour);
		}
	}
	
	function DrawOwnerText() {
		$iBlack = imagecolorallocate($this->oImage, 0, 0, 0);
		$iOwnerTextHeight = imagefontheight(2);
		$iLineHeight = $this->iHeight - $iOwnerTextHeight - 4;
		
		imageline($this->oImage, 0, $iLineHeight, $this->iWidth, $iLineHeight, $iBlack);
		imagestring($this->oImage, 2, 3, $this->iHeight - $iOwnerTextHeight - 3, $this->sOwnerText, $iBlack);
		$this->iHeight = $this->iHeight - $iOwnerTextHeight - 5;
	}
	
	function GenerateCode() {
		$this->sCode = '';
			for($i = 0; $i < $this->iNumChars; $i++) {
				if(count($this->aCharSet) > 0) {
					$this->sCode .= $this->aCharSet[array_rand($this->aCharSet)];
				} else {
					$this->sCode .= chr(rand(65, 90));
				}
			}
			
			if($this->bCaseInsensitive) {
				$_SESSION[CAPTCHA_SESSION_ID] = strtoupper($this->sCode);
			} else {
				$_SESSION[CAPTCHA_SESSION_ID] = $this->sCode;
			}
	}
	
	function DrawCharacters() {
		for($i = 0; $i < strlen($this->sCode); $i++) {
			$sCurrentFont = $this->aFonts[array_rand($this->aFonts)];
				if($this->bUseColour) {
					$iTextColour = imagecolorallocate($this->oImage, rand(0, 100), rand(0, 100), rand(0, 100));
						if($this->bCharShadow) {
							$iShadowColour = imagecolorallocate($this->oImage, rand(0, 100), rand(0, 100), rand(0, 100));
						}
				} else {
					$iRandColour = rand(0, 100);
					$iTextColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
						if($this->bCharShadow) {
							$iRandColour = rand(0, 100);
							$iShadowColour = imagecolorallocate($this->oImage, $iRandColour, $iRandColour, $iRandColour);
						}
				}
			$iFontSize = rand($this->iMinFontSize, $this->iMaxFontSize);
            $iAngle = rand(-30, 30);
            $aCharDetails = imageftbbox($iFontSize, $iAngle, $sCurrentFont, $this->sCode[$i], array());
            $iX = $this->iSpacing / 4 + $i * $this->iSpacing;
            $iCharHeight = $aCharDetails[2] - $aCharDetails[5];
            $iY = $this->iHeight / 2 + $iCharHeight / 4;
            imagefttext($this->oImage, $iFontSize, $iAngle, $iX, $iY, $iTextColour, $sCurrentFont, $this->sCode[$i], array());
				if($this->bCharShadow) {
					$iOffsetAngle = rand(-30, 30);
					$iRandOffsetX = rand(-5, 5);
					$iRandOffsetY = rand(-5, 5);
					imagefttext($this->oImage, $iFontSize, $iOffsetAngle, $iX + $iRandOffsetX, $iY + $iRandOffsetY, $iShadowColour, $sCurrentFont, $this->sCode[$i], array());
				}
		}
	}

	function WriteFile($sFilename) {
		if($sFilename == '') {
			header("Content-type: image/$this->sFileType");
		}
		
		switch ($this->sFileType) {
			case 'gif':
				$sFilename != '' ? imagegif($this->oImage, $sFilename) : imagegif($this->oImage);
				break;
			case 'png':
				$sFilename != '' ? imagepng($this->oImage, $sFilename) : imagepng($this->oImage);
				break;
			default:
				$sFilename != '' ? imagejpeg($this->oImage, $sFilename) : imagejpeg($this->oImage);
		}
	}
	
	function Create($sFilename = '') {
		if(!function_exists('imagecreate') || !function_exists("image$this->sFileType") || ($this->vBackgroundImages != '' && !function_exists('imagecreatetruecolor'))) {
			return false;
		}
		
		if(is_array($this->vBackgroundImages) || $this->vBackgroundImages != '') {
			$this->oImage = imagecreatetruecolor($this->iWidth, $this->iHeight);
				if(is_array($this->vBackgroundImages)) {
					$iRandImage = array_rand($this->vBackgroundImages);
					$oBackgroundImage = imagecreatefromjpeg($this->vBackgroundImages[$iRandImage]);
				} else {
					$oBackgroundImage = imagecreatefromjpeg($this->vBackgroundImages);
				}
				
			imagecopy($this->oImage, $oBackgroundImage, 0, 0, 0, 0, $this->iWidth, $this->iHeight);
            imagedestroy($oBackgroundImage);
		} else {
			$this->oImage = imagecreate($this->iWidth, $this->iHeight);
		}
		
		imagecolorallocate($this->oImage, 255, 255, 255);
		
		if($this->sOwnerText != '') {
			$this->DrawOwnerText();
		}
		
		if(!is_array($this->vBackgroundImages) && $this->vBackgroundImages == '') {
			$this->DrawLines();
		}
		
		$this->GenerateCode();
		$this->DrawCharacters();
		$this->WriteFile($sFilename);
		imagedestroy($this->oImage);
		return true;
	}
	
	function Validate($sUserCode, $bCaseInsensitive = true) {
		if($bCaseInsensitive) {
			$sUserCode = strtoupper($sUserCode);
		}
		
		if(!empty($_SESSION[CAPTCHA_SESSION_ID]) && $sUserCode == $_SESSION[CAPTCHA_SESSION_ID]) {
			unset($_SESSION[CAPTCHA_SESSION_ID]);
			return true;
		}
		
		return false;
	}
}

class PhpCaptchaColour extends PhpCaptcha {
	function PhpCaptchaColour($aFonts, $iWidth = CAPTCHA_WIDTH, $iHeight = CAPTCHA_HEIGHT) {
		parent::PhpCaptcha($aFonts, $iWidth, $iHeight);
		$this->UseColour(true);
	}
}
?>