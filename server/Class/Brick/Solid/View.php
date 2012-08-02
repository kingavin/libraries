<?php
class Class_Brick_Solid_View extends Zend_View_Abstract
{
	protected $_params;
	protected $_globalParams;
	
	public function __construct($config = array(), $params = NULL)
	{
		$this->_params = $params;
		parent::__construct($config);
	}
	
	public function getParam($key, $defaultValue = NULL)
    {
    	$params = $this->_params;
    	if(isset($params->$key)) {
    		$temp = $params->$key;
    		if($params->$key == 'global' && isset($this->_globalParams->$key)) {
    			$temp = $this->_globalParams->$key;
    		}
    		return $temp;
    	}
    	return $defaultValue;
    }
	
    protected function _run()
    {
        include func_get_arg(0);
    }
}