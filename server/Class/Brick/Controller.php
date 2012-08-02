<?php
class Class_Brick_Controller
{
	static private $_instance;
	
    protected $_solidBrickList = array();
    protected $_brickNameList = array();
    protected $_extensionParams = array();
    protected $_jsList = array();
    protected $_cssList = array();
    protected $_cache = null;
    
    private function __construct()
    {
    }
    
    private function __clone() {}
    
    /**
     * @return Class_Brick_Controller
     * Enter description here ...
     */
    static public function getInstance()
    {
    	if(!self::$_instance) {
    		self::$_instance = new Class_Brick_Controller();
    	}
    	return self::$_instance;
    }
    
    public function createSolidBrick($className)
    {
        $folderPath = str_replace('_', '/', $className);
        $fileNameArr = explode('_', $className);
        $fileName = $fileNameArr[count($fileNameArr) - 1];
        
        if(is_file(CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php')) {
            require_once CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php';
        } else {
            throw new Class_Brick_Exception('Brick file: '.CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php'.' not exist for '.$className);
        }
	    $solidBrick = new $className();
	    $solidBrick->setClassName($className);
		return $solidBrick;
    }
}