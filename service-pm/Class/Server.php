<?php
class Class_Server
{
	protected static $_siteId = null;
	
	protected static $_config = null;
	protected static $_enviroment = 'production-server';
	protected static $_libVersion = 'v1';
	protected static $_siteFolder = null;
	
	public static function config($env, $libVersion, $siteId, $siteFolder = null)
	{
		self::$_enviroment = $env;
		self::$_libVersion = $libVersion;
		self::$_siteId = $siteId;
		self::$_siteFolder = $siteFolder;
	}
	
	public static function setSiteId($id)
	{
		self::$_siteId = $id;
	}
	
	public static function getSiteId()
	{
		if(is_null(self::$_siteId)) {
			throw new Exception('not able to detect site id');
		}
		return self::$_siteId;
	}
	
	public static function getSUId()
	{
		return self::getServerId().'-'.self::getSiteId();
	}
	
	public static function getServerId()
	{
		$config = self::getConfig();
		return $config->server->id;
	}
	
	public static function getSiteFolder()
	{
		return self::$_siteFolder;
	}
	
	public static function getEnv()
	{
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
		$url = "http://";
		$url.= self::name('lib');
		$url.= '/cms/'.self::$_libVersion;
		return $url;
	}
	
	public static function miscUrl()
	{
		$url = "http://";
		$url.= self::name('misc');
		if(!is_null(self::$_siteFolder)) {
			$url.= '/'.self::$_siteFolder;
		}
		return $url;
	}
	
	public static function name($type = null)
	{
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
		if(self::$_config == null) {
			self::$_config = new Zend_Config_Ini(BASE_PATH.'/configs/pm/server.ini', 'localhost');
		}
		return self::$_config;
	}
}