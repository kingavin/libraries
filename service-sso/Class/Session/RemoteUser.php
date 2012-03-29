<?php
class Class_Session_RemoteUser
{
	private function __construct(){}
	private function __clone(){}
	private static $_instance = null;
	
	private static $_md5salt = 'fie&4Jgoaaq1d#$@(lj21';
	private static $_md5salt2 = '6234GY69)+3jo108';
	
	private $_isLogin = null;
	
    /**
     * @return Class_Session_RemoteUser
     */
    public static function getInstance()
    {
    	if(is_null(self::$_instance)) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    
    public static function encryptPassword($p)
    {
    	return md5($p.self::$_md5salt);
    }
    
    public function login($post)
    {
    	$loginName = $post['loginName'];
    	$password = $post['password'];
    	
    	$ru = App_Factory::_m('RemoteUser');
    	$ruDoc = $ru->addFilter('loginName', $loginName)
//    		->addFilter('password', md5($password.self::$_md5salt))
    		->addFilter('password', $password)
    		->fetchOne();
    		
		if(!is_null($ruDoc)) {
			$userId = $ruDoc->getId();
			$orgCode = $ruDoc->orgCode;
			$loginName = $ruDoc->loginName;
			$orgName = $ruDoc->orgName;
	    	$startTimeStamp = time();
	    	$userData = Zend_Json::encode(array(
	        	'orgCode' => $orgCode,
	    		'orgName' => $orgName,
	        	'loginName' => $loginName
	        ));
	        $this->_updateCookie(array(
	    		'userId' => $userId,
	        	'startTimeStamp' => $startTimeStamp,
	        	'userData' => $userData,
	        	'liv' => md5($userData.self::$_md5salt.$userId.self::$_md5salt2.$startTimeStamp)
	        ));
	        $this->_isLogin = true;
	        return true;
		} else {
			return false;
		}
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
    			$livToken = md5($_COOKIE['userData'].self::$_md5salt.$_COOKIE['userId'].self::$_md5salt2.$_COOKIE['startTimeStamp']);
    			if($livToken == $_COOKIE['liv']) {
    				$this->_isLogin = true;
    			} else {
    				$this->_isLogin = false;
    			}
    		} else {
    			$this->_isLogin = false;
    		}
    	}
    	return $this->_isLogin;
    }
    
    public function getUserId()
    {
    	return $_COOKIE['userId'];
    }
    
    public function getUserData()
    {
    	return $_COOKIE['userData'];
    }
    
    public function _updateCookie($cookies)
    {
    	foreach($cookies as $k => $v) {
    		setcookie($k, $v, time()+60*60*24*7, '/');
    	}
    }
}