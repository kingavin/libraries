<?php
class Class_Mongo_Storage_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
			"siteId",
			"imageFileCount",
			"otherFileCount",
			"totalCapacity",
			"usedCapacity"
	);
	
	public function checkCapacity()
	{
		return ($this->usedCapacity >= $this->totalCapacity) ? false : true;
	}
	
	public function getStorageInfo()
	{
//		$storageCo = App_Factory::_m('Storage');
//		$storageDoc = $storageCo->addFilter('orgCode',$orgcode)->fetchOne();
//		if(is_null($storageDoc)) {
//			$storageDoc = $storageCo->create();
//			$arrstorage = array(
//					'orgCode' => $orgcode,
//					'imageFileCount' => 0,
//					'avFileCount' => 0,
//					'otherFileCount' => 0,
//					'totalCapacity' => 1048576,
//					'usedCapacity' => 189440
//			);
//			$storageDoc->setFromArray($arrstorage);
//			$storageDoc->save();
//		}
		$infoArr[] = round($this->usedCapacity/1024,2);
		$infoArr[] = round($this->totalCapacity/1024,2);
		return $infoArr;
	}
	
	public function addFile($file)
	{
		if($file->isImage){
			$arredit['imageFileCount'] = $this->imageFileCount + 1;
			$fileSize = ceil($file->size/1024) + 5;
		} else {
			$arredit['otherFileCount'] = $this->otherFileCount + 1;
			$fileSize = ceil($file->size/1024);
		}
		$arredit['usedCapacity'] = $this->usedCapacity + $fileSize;
		$this->setFromArray($arredit);
		$this->save();
	}
	
	public function removeFile($file)
	{
		if($file->isImage){
			$arredit['imageFileCount'] = $this->imageFileCount - 1;
			$fileSize = ceil($file->size/1024) + 5;
		}else{
			$arredit['otherFileCount'] = $this->otherFileCount - 1;
			$fileSize = ceil($file->size/1024);
		}
		$arredit['usedCapacity'] = $this->usedCapacity - $fileSize;
		$this->setFromArray($arredit);
		$this->save();
	}
	
	public function recalculateCapacity($files, $siteId)
	{
		$recalculate['sizeCount'] = 0;
		$recalculate['imageFileCount'] = 0;
		$recalculate['otherFileCount'] = 0;
		foreach ($files as $fileinfomation){
			$recalculate['sizeCount']+= ceil($fileinfomation->size/1024);
			if($fileinfomation->isImage){
				$recalculate['imageFileCount']++;
			}else{
				$recalculate['otherFileCount']++;
			}
		}
		$recalculate['sizeCount']+= 189440;
		$arrstorage = array(
				'siteId' => $siteId,
				'imageFileCount' => $recalculate['imageFileCount'],
				'avFileCount' => 0,
				'otherFileCount' => $recalculate['otherFileCount'],
				'totalCapacity' => 1048576,
				'usedCapacity' => $recalculate['sizeCount']
		);
		$this->setFromArray($arrstorage);
		$this->save();
		return $this;
	}
}