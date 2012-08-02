<?php
class Class_Server
{
	const API_KEY = '';
	
	protected static $_enviroment = 'production';
	
	public static function config()
	{
		self::$_enviroment = APP_ENV;
	}
	
	public static function getEnv()
	{
		return self::$_enviroment;
	}
	
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
			$url = "http://st.onlinefu.com/account";
		} else {
			$url = "http://lib.eo.test/account";
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
}