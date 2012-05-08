<?php
abstract class App_Session_SsoUser
{
	protected $_expTime = 3600;	
	
	protected function __construct(){}
	protected function __clone(){}
	
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
	
	protected function _updateCookie($cookies)
	{
		foreach($cookies as $k => $v) {
    		setcookie($k, $v, time()+$this->_expTime, '/');
    	}
	}
	
	abstract public function hasPrivilege();
	
	abstract public function login($data);
	
	abstract public function logout();
}