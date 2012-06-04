<?php
class App_Server
{
	protected static $_config = null;
	protected static $_enviroment = 'production-server';
	protected static $_libType = 'cms';
	protected static $_libVersion = 'v1';
	protected static $_siteFolder = null;
	
	public static function ssoUrl()
	{
		return "http://sso.eo.test";
	}
	
	
	public static function config($env, $libType, $libVersion, $siteFolder = null)
	{
		throw new Exception('moved to Class_Server');
		
		self::$_enviroment = $val;
		self::$_libType = $libType;
		self::$_libVersion = $libVersion;
		self::$_siteFolder = $siteFolder;
	}
	
	public static function getSiteFolder()
	{
		throw new Exception('moved to Class_Server');
		
		return self::$_siteFolder;
	}
	
	public static function getEnv()
	{
		throw new Exception('moved to Class_Server');
		
		return self::$_enviroment;
	}
	
	public static function extUrl()
	{
		$url = "http://";
		$url.= self::name('ext').'/ext';
		return $url;
	}
	
	public static function libUrl()
	{
		throw new Exception('moved to Class_Server');
		
		$url = "http://";
		$url.= self::name('lib');
		$url.= '/'.self::$_libType.'/'.self::$_libVersion;
		return $url;
	}
	
	public static function miscUrl()
	{
		throw new Exception('moved to Class_Server');
		
		$url = "http://";
		$url.= self::name('misc');
		if(!is_null(self::$_siteFolder)) {
			$url.= '/'.self::$_siteFolder;
		}
		return $url;
	}
	
	public static function name($type = null)
	{
		throw new Exception('moved to Class_Server');
		
		$config = self::getConfig();
		$name = null;
		switch($type) {
			case 'ext':
				$name = $config->ext->name;
				break;
			case 'lib':
				$name = $config->lib->name;
				break;
			case 'misc':
				$name = $config->misc->name;
				break;
			default:
				throw new Exception('server type '.$type.' is not defined');
		}
		return $name;
	}
	
	protected static function getConfig()
	{
		throw new Exception('moved to Class_Server');
		
		if(self::$_config == null) {
			self::$_config = new Zend_Config_Ini(APP_PATH.'/configs/server.ini', 'localhost');
		}
		return self::$_config;
	}
}