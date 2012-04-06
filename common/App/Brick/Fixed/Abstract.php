<?php
abstract class App_Brick_Fixed_Abstract implements App_Brick_Interface
{
	abstract protected function _getExtName();
	
	public function path()
	{
		return BASE_PATH.'/extension/common/'.$this->_getExtName();
	}

	public function render()
	{
		$this->view = new App_Brick_Fixed_TwigView();
		$this->view->setScriptPath($this->path());
		
		$this->prepare();
		return $this->view->render('view.tpl');
	}
}