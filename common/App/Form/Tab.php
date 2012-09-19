<?php
class App_Form_Tab extends Zend_Form
{
	public function setTabs($tabs)
	{
		foreach($tabs as $key => $elementArr) {
			$this->addDisplayGroup($elementArr, $key);
		}
	}

	public function __toString()
	{
		$this->setDisplayGroupDecorators(array(
            'FormElements',
			array(array('DL' => 'HtmlTag'), array('tag' => 'dl', 'class' => 'admin-zendform')),
			array(array('LI' => 'HtmlTag'), array('tag' => 'li', 'class' => 'content-item'))
		));
		$this->setDecorators(array(
			'FormElements'
		));
		$this->setMethod('post');
		return parent::__toString();
	}
}