<?php
class App_Plugin_LayoutSwitch extends Zend_Controller_Plugin_Abstract
{
	protected $_layout;
	protected $_activeModuleArr = array();
	
	public function __construct($layout, Array $activeModuleArr)
	{
		$this->_layout = $layout;
		$this->_activeModuleArr = $activeModuleArr;
	}
	
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	if(in_array($request->getModuleName(), $this->_activeModuleArr) && ($request->isXmlHttpRequest())) {
    		$this->_layout->setLayout('template-lightbox');
    	}
    	if($request->getParam('layout') == 'disable') {
    		$this->_layout->disableLayout();
    	}
    }
}