<?php
class Class_Plugin_BrickRegister extends Zend_Controller_Plugin_Abstract
{
    private $_registed = false;
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		$bricks = $request->getParam('bricks');


		$type = $request->getModuleName();
		
		switch($type) {
			case 'user':
				$locale = Zend_Registry::get('Locale');
				$translate = new Zend_Translate(
					array('adapter' => 'gettext', 'content' => CONTAINER_PATH.'/languages/user/zh_CN.mo', 'locale' => 'zh_CN')
				);
				$translate->addTranslation(
					array('content' => CONTAINER_PATH.'/languages/user/en_US.mo', 'locale' => 'en_US')
				);
				$translate->setLocale($locale);
				Zend_Registry::set('Zend_Translate', $translate);
			break;
		}



		
		if($this->_registed != true && $bricks != 'disabled') {
            $type = $request->getModuleName();
            $controllerName = $this->getRequest()->getControllerName();
			$actionName = $this->getRequest()->getActionName();
				
            if($type == 'admin' || $type == 'forbidden') {
            	
            } else {
            	$layoutFront = Class_Layout_Front::getInstance();
            	$layoutRow = $layoutFront->getLayoutRow();
				
				if(is_null($layoutRow)) {
					throw new Exception("layout settings not found with given layoutName");
				}
				
                $layoutId = $layoutRow->id;
	            $brickTb = Class_Base::_tb('Brick');
	            if($layoutFront->isDisplayHead() == 1) {
					$selector = $brickTb->select(false)
						->from(array('b' => 'brick'), array('*', 'isnull' => new Zend_Db_Expr('`b`.`sort` IS NULL')))
						->where('layoutId = ? or layoutId = 0', $layoutId)
						->where('active = ?', 1)
						->order('isnull ASC')
						->order('sort ASC');
	            } else {
	            	$selector = $brickTb->select(false)
						->from(array('b' => 'brick'), array('*', 'isnull' => new Zend_Db_Expr('`b`.`sort` IS NULL')))
						->where('layoutId = ?', $layoutId)
						->where('active = ?', 1)
						->order('isnull ASC')
						->order('sort ASC');
	            }
	            $brickRowset = $brickTb->fetchAll($selector);
	            $cbc = Class_Brick_Controller::getInstance();
	            foreach($brickRowset as $brick) {
	                $cbc->registerBrick($brick, $request);
	            }
	            
	            if($request->getModuleName() == 'default' || $request->getModuleName() == '') {
	            	$request->setControllerName('index');
            		$request->setActionName('index');
	            }
            }
            
            $this->_registed = true;
        }
    }
}
