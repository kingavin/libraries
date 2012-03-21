<?php
class App_Cart
{
	private static $_carts = array();
	
	public static function factory($type = 'general')
	{
		if(!isset(self::$_carts['general'])) {
			$cart = new App_Cart_General();
			self::$_carts['general'] = $cart;
		}
		
		return self::$_carts['general'];
	}
	
	public static function getSessionId()
	{
		$sessionId = Zend_Session::getId();
		return $sessionId;
	}
}