<?php
abstract class Class_Brick_Solid_Abstract implements Class_Brick_Interface
{
    protected $_request = null;
    protected $_extName = null;
    protected $_brick = null;
    protected $_params = null;
    protected $_globalParams = null;
    protected $_scriptName = 'view.phtml';
    protected $_disableRender = false;
    protected $_gearLinks = array();
    
    protected $_useTwig = false;
    
    public function __construct($extName, Zend_Controller_Request_Abstract $request)
    {
    	$this->_request = $request;
    	$this->_extName = $extName;
        
        $this->_init();
    }
    
    public function _init(){}
    
    public function getExtName()
    {
    	return $this->_extName;
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
    
    public function setParam($key, $value)
    {
    	$this->_params->$key = $value;
    	return true;
    }
    
    public function setParams($src, $type = 'array')
    {
    	if(!empty($src)) {
	    	if($type == 'json') {
	    		$src = Zend_Json_Decoder::decode($src);
	    	}
	    	foreach($src as $key => $value) {
	    		if(!empty($value)) {
	    			$this->_params->$key = $value;
	    		}
	    	}
    	}
    }
    
    public function path()
    {
        $path = str_replace('_', '/', $this->_extName);
        return '/brick/'.$path;
    }
    
    public function twigPath()
    {
        return '/'.$this->_extName;
    }
    
    public function render($type = null)
    {
    	if($this->_disableRender === true) {
	        return "<div class='no-render'></div>";
    	} else {
	    	$this->view = new Class_Brick_Solid_TwigView();
			$this->view->setScriptPath(CONTAINER_PATH.'/extension'.$this->path());
			$this->view->assign($this->_params);
			if(is_dir(TEMPLATE_PATH.$this->twigPath())) {
				$this->view->addScriptPath(TEMPLATE_PATH.$this->twigPath());
			}
			$this->prepare();
			
			if($this->_disableRender === true) {
				return "<div class='no-render'></div>";
			} else if($this->_disableRender == 'no-resource') {
				return "<div class='no-resource'>NO RESOURCE</div>";
			} else {
				$this->view->setExtName($this->_extName)
					->setClassSuffix($this->_cssSuffix);
				
				$this->view->brickName = $this->_brick->brickName;
				$this->view->brickId = $this->_brick->brickId;
				$this->view->displayBrickName = $this->_brick->displayBrickName;
				
				$this->_prepareGearLinks();
				$this->view->setGearLinks($this->_gearLinks);
				try {
					return $this->view->render($this->_brick->tplName);
				} catch(Exception $e) {
					
				}
			}
    	}
    }
    
    public function configTplOptions($form)
    {
    	$tplArray = array();
    	
    	$systemFolder = CONTAINER_PATH.'/extension'.$this->path();
    	$handle = opendir($systemFolder);
    	while($file = readdir($handle)) {
    		if(strpos($file, '.tpl')) {
    			$tplArray[$file] = $file;
    		}
    	}
    	$userFolder = TEMPLATE_PATH.$this->twigPath();
    	$userTplArray = array();
		if(is_dir($userFolder)) {
			$handle = opendir($userFolder);
	    	while($file = readdir($handle)) {
	    		if(strpos($file, '.tpl')) {
	    			$userTplArray[$file] = $file;
	    		}
	    	}
		}
		if(count($userTplArray) > 0) {
			$tplArray = array('system' => $tplArray, 'user' => $userTplArray);
		}
    	
    	$form->tplName->setMultiOptions($tplArray);
    	return $form;
    }
    
    public function getTplArray()
    {
    	$sysTplArray = array();
    	$userTplArray = array();
    	
    	$systemFolder = CONTAINER_PATH.'/extension'.$this->path();
    	$handle = opendir($systemFolder);
    	while($file = readdir($handle)) {
    		if(strpos($file, '.tpl')) {
    			$sysTplArray[$file] = $file;
    		}
    	}
    	$userFolder = TEMPLATE_PATH.$this->twigPath();
    	if(is_dir($userFolder)) {
			$handle = opendir($userFolder);
	    	while($file = readdir($handle)) {
	    		if(strpos($file, '.tpl')) {
	    			$userTplArray[$file] = $file;
	    		}
	    	}
		}
		$tplArray = array('system' => $sysTplArray, 'user' => $userTplArray);
    	return $tplArray;
    }
    
    public function configParam($form)
    {
    	return $form;
    }
    
    public function getGlobalForm()
    {
    	return new Zend_Form();
    }
    
    /**
     * ready gear links
     * 
     * empty function for inheritance
     */
    protected function _prepareGearLinks()
    {
    	return array();
    }
    
    /**
     * append new gear link
     * 
     * @param string $lable
     * @param string $href
     */
    protected function _addGearLink($lable, $href)
    {
		$link = array('label' => $lable, 'href' => $href);
    	array_push($this->_gearLinks, $link);
    	return $this;
    }
    
    public function getCacheId()
    {
    	return null;
    }
}