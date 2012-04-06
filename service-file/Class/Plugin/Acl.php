<?php
class Class_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if($request->getModuleName() == 'admin' || $request->getModuleName() == 'rest') {
			$csu = Class_Session_User::getInstance();
			
			if(!$csu->isLogin()) {
				$sso = new App_SSO();
				if($csu->hasSSOToken()) {
					$st = $csu->getSSOToken();
					$response = $sso->auth($st);
					 
					$responseCode = $response[0];
					$xmlBody = $response[1];
					
					$xml = new SimpleXMLElement($xmlBody);
					switch($responseCode) {
						case '200':
							$csu->login($xml);
							header("Location: ".Class_Server::getSiteUrl().'/admin');
							break;
						case '403':
							//token not exist or expired, try to request with a new token
							$ssoToken = $csu->getSSOToken();
							$ssoLoginUrl = $sso->getLoginUrl('service-file', Class_Server::getSiteUrl().'/admin', $ssoToken, Class_Server::API_KEY);
							header("Location: ".$ssoLoginUrl);
							break;
						default:
							echo "error while getting identity from server!";
							exit(1);
					}
				} else {
					$ssoToken = $csu->getSSOToken();
					$ssoLoginUrl = $sso->getLoginUrl('service-file', Class_Server::getSiteUrl().'/admin', $ssoToken, Class_Server::API_KEY);
					header("Location: ".$ssoLoginUrl);
				}
			} else {
				$folder = $csu->getUserId();
				Class_Server::setMiscFolder($folder);
				
				$roleId = $csu->getRoleId();
				$moduleName = $request->getModuleName();
				$controllerName = $request->getControllerName();
				$actionName = $request->getActionName();
	
				$acl = Class_Acl::getInstance();
				if(!$acl->isAllowed($roleId, $moduleName.'-'.$controllerName, $actionName)) {
					if($roleId == 'nobody') {
						$request->setControllerName('anonymous');
						$request->setActionName('index');
					} else {
						$request->setControllerName('anonymous');
						$request->setActionName('no-privilege');
					}
				}
			}
		}
	}
}