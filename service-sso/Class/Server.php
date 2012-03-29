<?php
class Class_Server
{
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
	
	protected static function getConfig()
	{
		if(self::$_config == null) {
			self::$_config = new Zend_Config_Ini(BASE_PATH.'/configs/pm/server.ini', self::$_enviroment);
		}
		return self::$_config;
	}
}