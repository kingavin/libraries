<?php
class Class_Mongo_File_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'file';
	protected $_documentClass = 'Class_Mongo_File_Doc';
	
	public function copyFile(Class_Mongo_File_Doc $doc, $toSiteId, $toOrgCode)
	{
		$urlname = $doc->urlname;
		$fromSiteId = $doc->siteId;
		
		$toData = $this->getCollection()->findOne(array(
			'urlname' => $urlname,
			'siteId' => $toSiteId
		), array('size'));
		
		if(!is_null($toData)) {
			$toDoc = $this->create($toData, false);
		} else {
			$toDoc = $this->create($doc->toArray(), true);
			$toDoc->size = -1;
			$toDoc->orgCode = $toOrgCode;
			$toDoc->siteId = $toSiteId;
			if($toDoc->groupId != 'system') {
				$toDoc->groupId = 'ungrouped';
			}
		}
		if($doc->size != $toDoc->size) {
			$uploadUnixTime = time();
			$toDoc->size = $doc->size;
			$toDoc->uploadTime = date('Y-m-d H:i:s', $uploadUnixTime);
			$toDoc->uploadUnixTime = $uploadUnixTime;
			$toDoc->save();
			
			try {
				$service = Class_Api_Oss_Instance::getInstance();
				
				$fromObj = $fromSiteId.'/'.$urlname;
				$resp = $service->copyObject($fromObj, $toSiteId.'/'.$urlname);
				if($doc->isImage) {
					$thumbObj = $fromSiteId.'/_thumb/'.$urlname;
					$resp = $service->copyObject($thumbObj, $toSiteId.'/_thumb/'.$urlname);
				}
				return true;
			} catch (Exception $e) {
				$toDoc->delete();
				return 'file copy error: '.$fromObj.' to '.$toSiteId.'/'.$urlname;
			}
		}
		return 'file copy escaped: '.$urlname;
	}
}