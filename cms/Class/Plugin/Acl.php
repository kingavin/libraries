<?php
class Class_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if($request->getModuleName() == 'admin') {
        	$csa = Class_Session_Admin::getInstance();
            $roleId = $csa->getRoleId();
            
            $acl = Class_Acl::getInstance();
            $controllerName = $request->getControllerName();
            $actionName = $request->getActionName();
            
            if(!$acl->isAllowed($roleId, $controllerName, $actionName)) {
                if($roleId == 'nobody') {
                    $request->setControllerName('index');
                    $request->setActionName('login');
                } else {
                    $request->setControllerName('index');
                    $request->setActionName('no-privilege');
                }
            }
        } else {
        	$clf = Class_Layout_Front::getInstance();
        	$resource = $clf->getResource();
        	
        	if(is_null($resource)) {
        		throw new Class_Exception_Pagemissing();
        	}
        }
    }
}