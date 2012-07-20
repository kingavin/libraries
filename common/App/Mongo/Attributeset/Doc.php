<?php
class App_Mongo_Attributeset_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'label',
		'type'
	);
	protected $_zfElements = null;
	protected $_attributeDocs = null;
	
	public function _loadAttributeDoc()
	{
		if(is_null($this->_attributeDocs)) {
			$this->_attributeDocs = App_Factory::_am('Attribute')
				->sort('sort', 1)
				->addFilter('attributesetId', $this->getId())
				->fetchDoc();
		}
		return;
	}
	
	public function getAttributeDoc($id = null)
	{
		$this->_loadAttributeDoc();
		if(!is_null($id)) {
			foreach($this->_attributeDocs as $ad) {
				if($ad->getId() == $id) {
					return $ad;
				}
			}
		} else {
			return $this->_attributeDocs;
		}
	}
	
	public function loadZfElements()
	{
		$this->_loadAttributeDoc();
		$attributeArr = $this->_attributeDocs;
		
		if(count($attributeArr) == 0) {
			$this->_zfElements = array();
			return;
		}
		
		foreach($attributeArr as $attr) {
			$attrId = $attr->getId();
			$type = $attr->type;
			$label = $attr->label;
			
			$element = null;
			$frontendModelName = null;
			if($frontendModelName != null) {
				$frontModel = Class_Core::_($frontendModelName, $this, $entity);
				$element = $frontModel->toElement();
			} else {
				$selectedValue = '';
				 
				switch($type) {
					case 'textarea':
						$element = new Zend_Form_Element_Textarea('attribute_'.$attrId, array(
	                        'label' => $label,
	                        'value' => $selectedValue
						));
						break;
					case 'text':
						$element = new Zend_Form_Element_Text('attribute_'.$attrId, array(
	                        'label' => $label,
	                        'value' => $selectedValue
						));
						break;
					case 'select':
						$element = new Zend_Form_Element_Select('attribute_'.$attrId, array(
	                        'label' => $label,
	                        'value' => $selectedValue
						));
						break;
					default:
						$element = null;
						break;
				}
				$this->_zfElements[] = $element;

			}
		}
	}
	
	public function getZfElements()
	{
		if($this->_zfElements == null) {
			$this->loadZfElements();
		}
		
        return $this->_zfElements;
	}
}