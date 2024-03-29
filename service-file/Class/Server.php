<?php
class Class_Server
{
	const API_KEY = 'gioqnfieowhczt7vt87qhitonqfn8eaw9y8s90a6fnvuzioguifeb';
	
	protected static $_config = null;
	protected static $_enviroment = 'production';
	protected static $_libVersion = 'v1';
	protected static $_miscFolder = null;
	
	protected static $_siteId = null;
	protected static $_orgCode = null;
	protected static $_designerOrgCode = null;
	
	public static function config($env, $miscFolder = 'test')
	{
		self::$_enviroment = $env;
		self::$_miscFolder = $miscFolder;
	}
	
	public static function getSiteUrl()
	{
		if(self::$_enviroment == 'production') {
			return "http://file.enorange.com";
		} else {
			return "http://file.eo.test";
		}
	}
	
	public static function getEnv()
	{
		return self::$_enviroment;
	}
	
	public static function setMiscFolder($mf)
	{
		if(self:: $_enviroment == 'production') {
			self::$_miscFolder = $mf;
		} else {
			self::$_miscFolder = 'local-'.$mf;
		}
	}
	
	public static function getMiscFolder()
	{
		return self::$_miscFolder;
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
			$url = "http://st.onlinefu.com/file";
		} else {
			$url = "http://lib.eo.test/file";
		}
		return $url;
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
	
	public static function getSiteId()
	{
		if(is_null(self::$_siteId)) {
			$request = Zend_Controller_Front::getInstance()->getRequest();
			
			if($request->isXmlHttpRequest()) {
				self::$_siteId = $request->getHeader('X-Site-Id');
			} else {
				self::$_siteId = $request->getParam('siteId');
			}
		}
		return self::$_siteId;
	}
	
	public static function setOrgCode($orgCode)
	{
		self::$_orgCode = $orgCode;
	}
	
	public static function getOrgCode()
	{
		return self::$_orgCode;
	}
	
	public static function setDesignerOrgCode($doc)
	{
		self::$_designerOrgCode = $doc;
	}
	
	public static function getDesignerOrgCode()
	{
		return self::$_designerOrgCode;
	}
	
	protected static function getConfig()
	{
		if(self::$_config == null) {
			self::$_config = new Zend_Config_Ini(APP_PATH.'/configs/server.ini', 'localhost');
		}
		return self::$_config;
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