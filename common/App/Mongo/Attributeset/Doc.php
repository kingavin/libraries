<?php
class App_Mongo_Attributeset_Doc extends App_Mongo_Db_Document
{
	protected $_attributeDocs = null;
	
	public function getAttributeDoc($id)
	{
		foreach($this->_attributeDocs as $ad) {
			if($ad->getId() == $id) {
				return $ad;
			}
		}
	}
	
	public function getElementList($zfFormElement = true)
	{
		if(is_null($this->_attributeDocs)) {
			$this->_attributeDocs = App_Factory::_am('Attribute')
				->addFilter('attributesetId', $this->getId())
				->fetchDoc();
		}
		$attributeDocs = $this->_attributeDocs;
		
		if(!$zfFormElement) {
//			return $this->attributeList;
		} else {
			$elList = array();
			
			foreach($attributeDocs as $aDoc) {
				$el = null;
				$options = $aDoc->options;
				
				$optionsArr = array();
				foreach($options as $op) {
					$optionsArr[$op['label']] = $op['label'];
				}
				switch($aDoc->type) {
					case 'text':
						$el = new Zend_Form_Element_Text('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label
						));
						break;
					case 'radio':
						$el = new Zend_Form_Element_Radio('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'multiOptions' => $optionsArr
						));
						break;
					case 'select':
						$el = new Zend_Form_Element_Select('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'multiOptions' => $optionsArr
						));
						break;
					case 'multicheckbox':
						$el = new Zend_Form_Element_MultiCheckbox('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'multiOptions' => $optionsArr
						));
						break;
					case 'textarea':
						$el = new Zend_Form_Element_Textarea('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label
						));
						break;
					case 'button':
						$el = new Zend_Form_Element_Button('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label
						));
						break;
				}
				$elList[] = $el;
			}
			
			return $elList;
		}
	}
}