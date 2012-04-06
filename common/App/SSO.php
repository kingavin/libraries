<?php
class App_SSO
{
	public function auth($st)
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
	
	public function getLoginUrl($consumer, $returnUrl, $token, $apiKey)
	{
		$timeStamp = time();
		$sig = md5($consumer.$returnUrl.$timeStamp.$token.$apiKey);
		
		$c = urlencode($consumer);
		$r = urlencode($returnUrl);
		$t = urlencode($token);
		
		return 'http://sso.enorange.com/sso/login?consumer='.$c.'&ret='.$r.'&timeStamp='.$timeStamp.'&token='.$t.'&sig='.$sig;
	}
}