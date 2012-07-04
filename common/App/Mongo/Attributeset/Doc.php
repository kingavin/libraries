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
				->sort('sort', 1)
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
							'label' => $aDoc->label,
							'Description' => $aDoc->description
						));
						break;
					case 'radio':
						$el = new Zend_Form_Element_Radio('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'multiOptions' => $optionsArr,
							'Description' => $aDoc->description
						));
						break;
					case 'select':
						$el = new Zend_Form_Element_Select('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'multiOptions' => $optionsArr,
							'Description' => $aDoc->description
						));
						break;
					case 'multicheckbox':
						$el = new Zend_Form_Element_MultiCheckbox('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'multiOptions' => $optionsArr,
							'Description' => $aDoc->description
						));
						break;
					case 'textarea':
						$el = new Zend_Form_Element_Textarea('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'Description' => $aDoc->description
						));
						break;
					case 'button':
						$el = new Zend_Form_Element_Button('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'Description' => $aDoc->description
						));
						break;
					case 'label':
						$el = new App_Form_Element_Note('attr_'.$aDoc->getId(), array(
							'label' => $aDoc->label,
							'Description' => $aDoc->description
						));
						break;
				}
				foreach($aDoc->$proving as $k => $v) {
					$el->addValidator(new Validator('fefac'));
				}
				
				if(!is_null($aDoc->className)) {
					$el->class = $aDoc->className;
				}
				$labelDecorator = new App_Form_Decorator_Label();
				$el->setDecorators(array(
			        array('ViewHelper'),
			        array('Errors'),
			        array('Description', array('tag' => 'p', 'class' => 'description')),
			        array('HtmlTag', array('tag' => 'dd', 'class' => $aDoc->className)),
			        $labelDecorator
			    ));
				
				$elList[] = $el;
			}
			
			return $elList;
		}
	}
}