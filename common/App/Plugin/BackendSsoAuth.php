<?php
class App_Plugin_BackendSsoAuth extends Zend_Controller_Plugin_Abstract
{
	const CMS = 'cms';
	const PM = 'pm';
	const SERVICE_FILE = 'service-file';
	
	protected $_assu;
	protected $_serviceType;
	protected $_apiKey;
	
	public function __construct(App_Session_SsoUser $assu, $serviceType, $apiKey)
	{
		$this->_assu = $assu;
		$this->_serviceType = $serviceType;
		$this->_apiKey = $apiKey;
	}
	
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$csu = $this->_assu;
		if($request->getModuleName() == 'admin' || $request->getModuleName() == 'rest') {
			if(!$csu->isLogin()) {
				if($csu->hasSSOToken()) {
					$st = $csu->getSSOToken();
					$response = $this->_auth($st);
					$responseCode = $response[0];
					$xmlBody = $response[1];
					
					switch($responseCode) {
						case '200':
							$xml = new SimpleXMLElement($xmlBody);
							$csu->login($xml);
							$retUrl = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
							header("Location: ".$retUrl);
							break;
						case '403':
							//token not exist or expired, try to request with a new token
							$ssoToken = $csu->getSSOToken();
							$retUrl = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
							$ssoLoginUrl = $this->_getLoginUrl($this->_serviceType, $retUrl, $ssoToken);
							header("Location: ".$ssoLoginUrl);
							break;
						default:
							echo "error while getting identity from server!";
							exit(1);
					}
				} else {
					$ssoToken = $csu->getSSOToken();
					$retUrl = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
					$ssoLoginUrl = $this->_getLoginUrl($this->_serviceType, $retUrl, $ssoToken);
					header("Location: ".$ssoLoginUrl);
				}
			}
		}
	}
	
	protected function _auth($st)
	{
		$curl = curl_init('http://sso.enorange.com/sso/info/format/xml');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, array('token' => $st));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		$body = curl_exec($curl);
		$ret = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if (curl_errno($curl) != 0) {
			throw new Exception("SSO failure: HTTP request to server failed. " . curl_error($curl));
		}
		
		return array($ret, $body);
	}
	
	public function _getLoginUrl($consumer, $returnUrl, $token)
	{
		$apiKey = $this->_apiKey;
		$timeStamp = time();
		$sig = md5($consumer.$returnUrl.$timeStamp.$token.$apiKey);
		
		$c = urlencode($consumer);
		$r = urlencode($returnUrl);
		$t = urlencode($token);
		
		return 'http://sso.enorange.com/sso/login?consumer='.$c.'&ret='.$r.'&timeStamp='.$timeStamp.'&token='.$t.'&sig='.$sig;
	}
}