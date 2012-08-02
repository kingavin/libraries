<?php
class Class_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$csa = Class_Session_Admin::getInstance();
		if(!$csa->isLogin()) {
			$request->setControllerName('index');
			$request->setActionName('login');
		}
	}
}