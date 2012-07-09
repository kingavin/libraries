<?php
class Class_Mongo_Storage_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'storage';
	protected $_documentClass = 'Class_Mongo_Storage_Doc';
	
//	public function index($orgcode)
//	{
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
//		$arrtrue[] = round($storageDoc->usedCapacity/1024,2);
//		$arrtrue[] = round($storageDoc->totalCapacity/1024,2);
//		return $arrtrue;
//	}
//	
//	public function checkCapacity($orgcode)
//	{
//		$storageCo = App_Factory::_m('Storage');
//		$storageDoc = $storageCo->addFilter('orgCode',$orgcode)->fetchOne();
//		return ($storageDoc->usedCapacity>=$storageDoc->totalCapacity)?false:true;
//	}
	
//	public function editCapacity($orgcode,$isFileType,$fileSize,$operating)
//	{
//		$storageCo = App_Factory::_m('Storage');
//		$storageDoc = $storageCo->addFilter('orgCode',$orgcode)->fetchOne();
//		$arredit = array();
//		if($operating == 'create'){
//			if($isFileType == 'image'){
//				$arredit['imageFileCount'] = $storageDoc->imageFileCount + 1;
//				$fileSize = ceil($fileSize/1024) + 5;
//			} else {
//				$arredit['otherFileCount'] = $storageDoc->otherFileCount + 1;
//				$fileSize = ceil($fileSize/1024);
//			}
//			$arredit['usedCapacity'] = $storageDoc->usedCapacity + $fileSize;
//		} else {
//			if($isFileType){
//				$arredit['imageFileCount'] = $storageDoc->imageFileCount - 1;
//				$fileSize = ceil($fileSize/1024) + 5;
//			}else{
//				$arredit['otherFileCount'] = $storageDoc->otherFileCount - 1;
//				$fileSize = ceil($fileSize/1024);
//			}
//			$arredit['usedCapacity'] = $storageDoc->usedCapacity - $fileSize;
//		}
//		$storageDoc->setFromArray($arredit);
//		$storageDoc->save();
//	}
	
// 	public function recalculateCapacity($file,$orgCode)
// 	{
// 		$recalculate['sizeCount'] = 0;
// 		$recalculate['imageFileCount'] = 0;
// 		$recalculate['otherFileCount'] = 0;
// 		foreach ($file as $num => $file){
// 			$recalculate['sizeCount']+= ceil($file->size/1024);
// 			if($file->isImage){
// 				$recalculate['imageFileCount']++;
// 			}else{
// 				$recalculate['otherFileCount']++;
// 			}
// 		}
// 		$recalculate['sizeCount']+= 189440;
// 		$storageDoc = $this->create();
// 		$arrstorage = array(
// 				'orgCode' => $orgCode,
// 				'imageFileCount' => $recalculate['imageFileCount'],
// 				'avFileCount' => 0,
// 				'otherFileCount' => $recalculate['otherFileCount'],
// 				'totalCapacity' => 1048576,
// 				'usedCapacity' => $recalculate['sizeCount']
// 		);
// 		$storageDoc->setFromArray($arrstorage);
// 		$storageDoc->save();
// 		return $storageDoc;
// 	}
}