<?php
class Class_Server
{
	const API_KEY = '21FguiogaLL9y923t715hi4guo32iofgdsz8ohj0phgyUIFMUubNUh78rF';
	
	protected static $_config = null;
	protected static $_enviroment = 'production';
	
	public static function config()
	{
		self::$_enviroment = APP_ENV;
	}
	
	public static function getExtUrl()
	{
		if(self::$_enviroment == 'development') {
			return "http://lib.eo.test/ext";
		} else {
			return "http://tempst.enorange.com";
		}
	}
	
	public static function getLibUrl()
	{
		if(self::$_enviroment == 'development') {
			return "http://lib.eo.test/form";
		} else {
			return "http://tempst.enorange.com";
		}
	}
}