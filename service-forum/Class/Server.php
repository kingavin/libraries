<?php
class Class_Server
{
	protected static $_serverId = null;
	protected static $_config = null;
	protected static $_enviroment = 'production';
	
	public static function config($env)
	{
		self::$_enviroment = $env;
	}
	
	protected static function getConfig()
	{
		if(self::$_config == null) {
			self::$_config = new Zend_Config_Ini(BASE_PATH.'/configs/forum/server.ini', self::$_enviroment);
		}
		return self::$_config;
	}
	
	public static function getSUId()
	{
		$config = self::getConfig();
		return $config->server->id;
	}
	
	public static function getEnv()
	{
		return self::$_enviroment;
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
	
	public static function domain($type)
	{
		$config = self::getConfig();
		switch($type) {
			case 'file':
				return $config->domain->fileService;
				
		}
		return null;
	}
}