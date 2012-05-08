<?php
class App_Brick_Controller
{
	static private $_instance;
	
    protected $_flexiBrickList = array();
    protected $_brickNameList = array();
    protected $_extensionParams = array();
    protected $_jsList = array();
    protected $_cssList = array();
    protected $_cache = null;
    
    private function __construct()
    {
//    	$frontendOptions = array(
//	       'lifetime' => 7200
//	    );
//	    $backendOptions = array(
//	        'cache_dir' => CACHE_PATH
//	    );
//	    $this->_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }
    
    private function __clone() {}
    
    /**
     * @return App_Brick
     * Enter description here ...
     */
    static public function getInstance()
    {
    	if(!self::$_instance) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    
    static public function createFixedBrick($extName)
    {
    	$className = $extName;
	    
        $folderPath = str_replace('_', '/', $className);
        $fileNameArr = explode('_', $className);
        $fileName = $fileNameArr[count($fileNameArr) - 1];
	    
        if(is_file(CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php')) {
            require_once CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php';
        } else {
            throw new App_Brick_Exception('Brick file: '.CONTAINER_PATH.'/extension/brick/'.$folderPath.'/'.$fileName.'.php'.' not exist for '.$className);
        }
	    $fixedBrick = new $className();
	    return $fixedBrick;
    }
    
//    public function createSolidBrick($brick, Zend_Controller_Request_Abstract $request)
//    {
//    	if($brick instanceof Class_Model_Brick_Row) {
//    		
//    	} else if(is_string($brick)) {
//    		$brickTb = App_Factory::_('Brick');
//			$brickRow = $brickTb->createRow();
//			$brickRow->setFromArray(array('extName' => $brick));
//			$solidBrick = $brickRow->createSolidBrick($request);
//		    return $solidBrick;
//    	}
//    }
    
    public function registerBrick(
        Class_Model_Brick_Row $brick,
        Zend_Controller_Request_Abstract $request)
    {
        $solidBrick = $brick->createSolidBrick($request);
		$this->_flexiBrickList[] = $solidBrick;
        return true;
    }
    
    public function getBrickList($spriteName = null)
    {
    	if(is_null($spriteName)) {
        	return $this->_flexiBrickList;
    	} else {
    		$solidBrickList = $this->_flexiBrickList;
	    	$returnBricks = array();
			foreach($solidBrickList as $solidBrick) {
				if($solidBrick->getSpriteName() == $spriteName) {
					$returnBricks[] = $solidBrick;
				}
			}
			return $returnBricks;
    	}
    }
    
    public function getJsList()
    {
        return $this->_jsList;
    }
    
    public function getCssList()
    {
        return $this->_cssList;
    }
    
    public function renderBrick($brickId)
    {
    	$solidBrickList = $this->_flexiBrickList;
    	
    	$brickHTML = "brick-id:".$brickId;
    	foreach($solidBrickList as $solidBrick) {
    		if($solidBrick->getBrickId() == $brickId) {
    			$brickHTML = $solidBrick->render();
    			break;
    		}
    	}
    	return $brickHTML;
    }
    
	public function render($position)
	{
	    if(!is_null($position)) {
	        if(array_key_exists($position, $this->_flexiBrickList)) {
    	        $solidBrickList = $this->_flexiBrickList[$position];
    	        $HTML = "";
    	        foreach($solidBrickList as $solidBrick) {
    	            $HTML.= $solidBrick->render();
    	        }
    	        return $HTML;
	        }
	    } else {
	        throw new Class_Brick_Exception('position required for brick rendering');
	    }
	}
	
	public function renderAll()
	{
		$solidBrickList = $this->_flexiBrickList;
		$HTML_ARR = array();
		foreach($solidBrickList as $solidBrick) {
			if(array_key_exists($solidBrick->getSpriteName(), $HTML_ARR)) {
				$BrickHTML = $HTML_ARR[$solidBrick->getSpriteName()];
			} else {
				$BrickHTML = "";
			}
			/**
			 * @todo redesign the cache mech
			 */
//			if($solidBrick->getCacheId() !== null && !Class_Session_Admin::isLogin()) {
//				$cacheId = $solidBrick->getCacheId();
//				if(!$this->_cache->test($cacheId)) {
//					$BrickHTML.= $solidBrick->render();
//					$this->_cache->save($BrickHTML, $cacheId, array('brick'));
//				} else {
//					$BrickHTML.= $this->_cache->load($cacheId);
//				}
//			} else {
				$BrickHTML.= $solidBrick->render();
//			}
			$HTML_ARR[$solidBrick->getSpriteName()] = $BrickHTML;
		}
		return $HTML_ARR;
	}
	
	public function renderPosition()
	{
		$tb = new Zend_Db_Table('layout_stage');
		$spriteRowset = $tb->fetchAll($tb->select()->where('layoutId = 1')->order('sort ASC'));
		$HTML_ARR = array();
		
		foreach($spriteRowset as $row) {
			$HTML = "";
			$spriteName = $row->spriteName;
			if(array_key_exists($spriteName, $this->_flexiBrickList)) {
				$HTML.= $this->_render($spriteName);
			}
			$HTML_ARR[$spriteName] = $HTML;
		}
		return $HTML_ARR;
	}
	
	protected function _render($spriteName)
	{
		if(is_null($spriteName)) {
			throw new Class_Brick_Exception('position required for brick rendering');
		}
		$solidBrickList = $this->_flexiBrickList[$spriteName];
		$HTML = "";
		foreach($solidBrickList as $solidBrick) {
			$BrickHTML = "";
			if($solidBrick->getCacheId() !== null && !Class_Session_Admin::isLogin()) {
				$cacheId = $solidBrick->getCacheId();
				if(!$this->_cache->test($cacheId)) {
					$BrickHTML = $solidBrick->render();
					$this->_cache->save($BrickHTML, $cacheId, array('brick'));
				} else {
					$BrickHTML = $this->_cache->load($cacheId);
				}
			} else {
				$BrickHTML = $solidBrick->render();
			}
			$HTML.= $BrickHTML;
		}
		return $HTML;
	}
}