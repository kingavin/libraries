<?php
abstract class Class_Brick_Solid_Abstract
{
    protected $_className = null;
    protected $_params = array();
    
    public function setClassName($className)
    {
    	$this->_className = $className;
    }
    
    public function path()
    {
        $path = str_replace('_', '/', $this->_className);
        return '/brick/'.$path;
    }
    
    public function render()
    {
    	$this->view = new Zend_View(array('scriptPath' => CONTAINER_PATH.'/extension'.$this->path()));
    	$this->prepare();
    	
		return $this->view->render('view.phtml');
    }
    
    abstract function prepare();
}