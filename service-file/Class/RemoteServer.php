<?php
class Class_RemoteServer
{
	public static function getSiteDoc($siteId)
	{
		$siteCo = App_Factory::_m('Site');
		$siteDoc = $siteCo->addFilter('siteId', $siteId)->fetchOne();
		
		if(is_null($siteDoc)) {
			$ch = curl_init("http://account.enorange.cn/rest/remote-site/".$siteId);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$returnedStr = curl_exec($ch);
			
			$returnedObj = Zend_Json::decode($returnedStr);
			
			if($returnedObj['result'] == 'success') {
				$siteDoc = $siteCo->create();
				$siteDoc->setFromArray($returnedObj['data']);
				$siteDoc->siteId = $siteId;
				$siteDoc->save();
			}
		}
		if(!is_null($siteDoc)) {
			Class_Server::setOrgCode($siteDoc->orgCode);
			Class_Server::setDesignerOrgCode($siteDoc->designerOrgCode);
		}	
		return $siteDoc;
	}
}