<?php
class Class_Server
{
	const API_KEY = 'nfieawfueau86572hhuiGYU615hf678tRcewq7uh43qffugUIGIfefwg';
	
	protected static $_config = null;
	protected static $_enviroment = 'production';
	protected static $_libVersion = 'v1';
	protected static $_miscFolder = null;
	
	public static function config($env, $miscFolder = 'test')
	{
		self::$_enviroment = $val;
		self::$_miscFolder = $miscFolder;
	}
	
	public static function getEnv()
	{
		return self::$_enviroment;
	}
	
	public static function getMiscFolder()
	{
		return self::$_miscFolder;
	}
	
	public static function extUrl()
	{
		$config = self::getConfig();
		return $config->url->ext;
	}
	
	public static function libUrl()
	{
		$config = self::getConfig();
		return $config->url->lib;
	}
	
	public static function miscUrl($appendMiscFolder = true)
	{
		$url = "http://";
		$url.= self::name('misc');
		if($appendMiscFolder == true) {
			$url.= '/'.self::$_miscFolder;
		}
		return $url;
	}
	
	public static function getMongoServer()
	{
		$config = self::getConfig();
		return $config->server->mongo;
	}
	
	protected static function getConfig()
	{
		if(self::$_config == null) {
			self::$_config = new Zend_Config_Ini(BASE_PATH.'/configs/sso/server.ini', self::$_enviroment);
		}
		return self::$_config;
	}
}