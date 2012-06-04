<?php
class App_Form_Decorator_Label extends Zend_Form_Decorator_Label
{
	public function render($content)
	{
		$element = $this->getElement();
		$view    = $element->getView();
		if (null === $view) {
			return $content;
		}

		$label     = $this->getLabel();
		$separator = $this->getSeparator();
		$placement = $this->getPlacement();
		$tag       = $this->getTag();
		$id        = $this->getId();
		$class     = $element->getAttrib('class');
		$options   = $this->getOptions();

		if (empty($label) && empty($tag)) {
			return $content;
		}

		if (empty($label)) {
			$label = '&#160;';
		}
        
		$decorator = new Zend_Form_Decorator_HtmlTag();
		if(!empty($class)) {
			$decorator->setOptions(array('tag' => '<dt>', 'class' => $class));
		} else {
			$decorator->setOptions(array('tag' => '<dt>'));
		}
		$label = $decorator->render($label);
		
		switch ($placement) {
			case self::APPEND:
				return $content . $separator . $label;
			case self::PREPEND:
				return $label . $separator . $content;
		}
	}
}