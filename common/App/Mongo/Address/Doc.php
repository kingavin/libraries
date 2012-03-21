<?php
class App_Mongo_Address_Doc extends App_Mongo_Db_Document
{
	protected function _preInsert()
	{
		$csu = Class_Session_User::getInstance();
		
		$userId = $csu->getUserId();
		$this->setProperty('userId', $userId);
	}
	
	protected function _preSave()
	{
		$this->setProperty('fullAddress', $this->_getFullAddress());
	}
	
	protected function _getFullAddress()
	{
		$fullAddress = "";
		if(empty($this->landLine)) {
			$fullAddress = $this->consignee.' '.$this->mobilePhone.' '.$this->addressDetail.','.$this->postcode;
		} else {
			$fullAddress = $this->consignee.' '.$this->mobilePhone.' - '.$this->landLine.' '.$this->addressDetail.','.$this->postcode;
		}
		return $fullAddress;
	}
}