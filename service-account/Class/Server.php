<?php
class Class_Server
{
	const API_KEY = 'nfieawfueau86572hhuiGYU615hf678tRcewq7uh43qffugUIGIfefwg';
	
//	protected static $_config = null;
	protected static $_enviroment = 'production';
//	protected static $_libVersion = 'v1';
//	protected static $_miscFolder = null;
	
	public static function config()
	{
		self::$_enviroment = APP_ENV;
//		self::$_miscFolder = $miscFolder;
	}
	
	public static function getEnv()
	{
		return self::$_enviroment;
	}
	
//	public static function getMiscFolder()
//	{
//		return self::$_miscFolder;
//	}
	
	public static function extUrl()
	{
		if(self::$_enviroment == 'production') {
			$url = "http://st.onlinefu.com/ext";
		} else {
			$url = "http://lib.eo.test/ext";
		}
		return $url;
	}
	
	public static function libUrl()
	{
		if(self::$_enviroment == 'production') {
			$url = "http://st.onlinefu.com/file";
		} else {
			$url = "http://lib.eo.test/file";
		}
		return $url;
	}
	
	public static function getMongoServer()
	{
		if(self::$_enviroment == 'production') {
			return 'mongodb://craftgavin:whothirstformagic?@127.0.0.1';
		} else {
			return 'mongodb://craftgavin:whothirstformagic?@58.51.194.8';
		}
	}
	
	protected static function getConfig()
	{
//		if(self::$_config == null) {
//			self::$_config = new Zend_Config_Ini(BASE_PATH.'/configs/sso/server.ini', self::$_enviroment);
//		}
//		return self::$_config;
	}
}