<?php
class Class_Session_UserV2 extends App_Session_SsoUser
{
	private static $_instance = null;

	private static $_md5salt = '^;1[djw#1&';
	private static $_md5salt2 = 'cH)b3u(7689)(*';

	private $_isLogin = null;
	private $_hasPrivilege = false;
	/**
	 * @return Class_Session_User
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public static function getLiv($userData, $userId, $startTimeStamp)
	{
		return md5($userData.self::$_md5salt.$userId.self::$_md5salt2.$startTimeStamp);
	}
	
	public function hasSSOToken()
	{
		if(isset($_COOKIE['st']) && $_COOKIE['st'] != '') {
			return true;
		}
		return false;
	}
	
	public function getSSOToken()
	{
		if(isset($_COOKIE['st']) && $_COOKIE['st'] != '') {
			return $_COOKIE['st'];
		} else {
			$token = md5(time());
			setcookie('st', $token, time()+$this->_expTime, '/');
			return $token;
		}
	}
	
	public function login($xml)
	{
		if($xml instanceof SimpleXMLElement) {
			$user = $xml;
		}

		if(is_null($user)) {
			return false;
		}
		$userId = $user->attributes()->userId;
		$startTimeStamp = time();
		$userDataArr = array();
		foreach ($user->children() as $tag => $val) {
	    	$userDataArr[$tag] = (string)$val;
	    }
	    $userData = Zend_Json::encode($userDataArr);
		$liv = self::getLiv($userData, $userId, $startTimeStamp);
		
		$this->_updateCookie(array(
    		'userId' => $userId,
        	'startTimeStamp' => $startTimeStamp,
        	'userData' => $userData,
        	'liv' => $liv
		));
		$this->_isLogin = true;
		return true;
	}
	
	public function logout()
	{
		setcookie('userId', '', 1, '/');
		setcookie('startTimeStamp', '', 1, '/');
		setcookie('userData', '', 1, '/');
		setcookie('liv', '', 1, '/');
		$this->_isLogin = false;
	}
	
	public function isLogin()
	{
		if($this->_isLogin == null) {
			if(isset($_COOKIE['userId']) && $_COOKIE['userId'] != '') {
				$livToken = self::getLiv($_COOKIE['userData'], $_COOKIE['userId'], $_COOKIE['startTimeStamp']);
				if($livToken == $_COOKIE['liv']) {
					$this->_isLogin = true;
				} else {
					$this->_isLogin = false;
					$this->logout();
				}
			} else {
				$this->_isLogin = false;
			}
		}
		return $this->_isLogin;
	}
	
	public function hasPrivilege()
	{
		if(!$this->isLogin()) {
			return false;
		}
		
		$zcf = Zend_Controller_Front::getInstance();
		$routeName = $zcf->getRouter()->getCurrentRouteName();
		
		if($routeName == 'info') {
			return true;
		} else if($routeName == 'default' || $routeName == 'rest') {
			$siteId = Class_Server::getSiteId();
			//$siteDoc = Class_RemoteServerV2::getSiteDoc($siteId);
// 			if(is_null($siteDoc)) {
// 				return false;
// 			}
			if($this->getUserData('userType') != 'designer') {
				$siteIds = $this->getUserData('siteIds');
				$siteIdsArr = explode(",", $siteIds);
				if(in_array($siteId, $siteIdsArr)) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		}
		return false;
	}
	
	public function getUserId()
	{
		if($this->isLogin()) {
			return $_COOKIE['userId'];
		}
		return 'nobody';
	}
	
	public function getUserData($key = null)
	{
		if($this->isLogin()) {
			$userData = Zend_Json::decode($_COOKIE['userData']);
			if(is_null($key)) {
				return $userData;
			}
			return $userData[$key];
		}
		return null;
	}
	
	public function getHomeLocation()
	{
		return "/info/";
	}
	
//	static public function setOrgCode($orgCode)
//	{
//		$_SESSION['presetOrgCode'] = $orgCode;
//	}
	
//	public function getOrgCode()
//	{
//		if($this->getUserData('userType') == 'designer') {
//			$presetOrgCode = $_SESSION['presetOrgCode'];
//			if(is_null($presetOrgCode)) {
//				throw new Exception('org code is empty or it is not saved in the session');
//			} else {
//				return $presetOrgCode;
//			}
//		} else {
//			return $this->getUserData('orgCode');
//		}
//	}
	
	public function _updateCookie($cookies)
	{
		foreach($cookies as $k => $v) {
    		setcookie($k, $v, time()+$this->_expTime, '/');
    	}
	}
}
