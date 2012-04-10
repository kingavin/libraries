<?php
class Class_Session_User extends App_Session_SsoUser
{
	private static $_md5salt = 'lp[9Ho567b1&';
	private static $_md5salt2 = 's32*gnBUIOfg';

	protected static $_instance = null;
	private $_isLogin = null;
	
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
	
	public function getUserId()
	{
		if($this->isLogin()) {
			return $_COOKIE['userId'];
		}
		return 'nobody';
	}
	
	public function getUserData($key)
	{
		if($this->isLogin()) {
			$userData = Zend_Json::decode($_COOKIE['userData']);
			return $userData[$key];
		}
		return null;
	}
}