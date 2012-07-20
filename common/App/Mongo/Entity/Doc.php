<?php
class App_Mongo_Entity_Doc extends App_Mongo_Db_Document
{
	protected $_attributesetDoc = null;
	
	public function getAttributeDetail($code, $field = 'value')
	{
		if(isset($this->attributeDetail[$code])) {
			return $this->attributeDetail[$code][$field];
		}
		return null;
	}
	
	public function getAttributesetDoc()
	{
		if(is_null($this->_attributesetDoc)) {
			$attributesetCo = App_Factory::_am('Attributeset');
			$this->_attributesetDoc = $attributesetCo->find($this->attributesetId);
		}
		return $this->_attributesetDoc;
	}
	
	public function setFromArray($array)
	{
		$attributeArr = array();
		foreach($array as $key => $val) {
			if(substr($key, 0, 10) == 'attribute_') {
				$attrId = substr($key, 10);
				$attributeArr[$attrId] = $val;
			}
		}
		$this->attributeValue = $attributeArr;
		
		$attributeDetail = array();
		$attributesetDoc = $this->getAttributesetDoc();
		if(!is_null($attributesetDoc)) {
			$attributeDocArr = $attributesetDoc->getAttributeDoc();
			foreach($attributeDocArr as $ad) {
				$value = $attributeArr[$ad->getId()];
				$attributeDetail[$ad->code] = array(
					'label' => $ad->label,
					'value' => $value
				);
			}
		}
		$this->attributeDetail = $attributeDetail;
		
		return parent::setFromArray($array);
	}
	
	public function getData()
	{
		$data = $this->_data;
		if(!is_array($this->attributeValue)) {
			return $this->_data;
		}
		foreach($this->attributeValue as $key => $val) {
			$data['attribute_'.$key] = $val;
		}
		return $data;
	}
	
	public function toArray($withId = false)
	{
		if($withId) {
			$data = $this->getData();
			$data['id'] = $this->getId();
			return $data;
		} else {
			return $this->getData();
		}
	}
}