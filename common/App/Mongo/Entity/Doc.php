<?php
class App_Mongo_Entity_Doc extends App_Mongo_Db_Document
{
	protected $_attributesetDoc = null;
	
	public function setAttributesetDoc($ac)
	{
		$this->_attributesetDoc = $ac;
	}
	
	public function save($safe = true)
	{
		if(!is_null($this->_attributesetDoc)) {
			$entityValue = array();
			foreach($this->_data as $key => $value) {
				if(strpos($key, 'attr_') === 0) {
					$attrId = substr($key, 5);
					$attrDoc = $this->_attributesetDoc->getAttributeDoc($attrId);
					$entityValue[$attrDoc->label] = $value;
				}
			}
			$this->entityValue = $entityValue;
		}
		return parent::save($save);
	}
}