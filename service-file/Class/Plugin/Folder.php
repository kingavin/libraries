<?php
class Class_Plugin_Folder extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$csu = Class_Session_User::getInstance();
		if($csu->isLogin()) {
			echo $csu->getUserId();
			die();
		}
	}
}