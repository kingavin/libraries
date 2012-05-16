<?php
class Class_Server
{
	const API_KEY = '21FguiogaLL9y923t715hi4guo32iofgdsz8ohj0phgyUIFMUubNUh78rF';
	
	protected static $_config = null;
	protected static $_enviroment = 'production';
	protected static $_orgCode = null;
	
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
	
	public static function getOrgCode()
	{
		if(is_null(self::$_orgCode)) {
			$pathPieces = explode('/', $_SERVER["REQUEST_URI"]);
			if(strpos($_SERVER["REQUEST_URI"], 'http:') !== false) {
				self::$_orgCode = $pathPieces[3];
			} else {
				self::$_orgCode = $pathPieces[1];
			}
		}
		return self::$_orgCode;
	}
	
	public static function getMongoServer()
	{
		if(self::$_enviroment == 'production') {
			return 'mongodb://craftgavin:whothirstformagic?@127.0.0.1';
		} else {
			return '127.0.0.1';
		}
	}
}