<?php
class App_Mongo_Attributeset_Doc extends App_Mongo_Db_Document
{
	public function getElementList($zfFormElement = true)
	{
		if(!$zfFormElement) {
			return $this->attributeList;
		} else {
			$elementArr = array();
			
			foreach($this->attibuteList as $key => $val) {
				$el = null;
				switch($val->type) {
					case '';
						$el = new Zend_Form_Element_Text($key, array(
							'label' => '文本输入'
						));
						break;
				}
				$elementArr[$key] = $el;
			}
			
			return $elementArr;
		}
	}
}