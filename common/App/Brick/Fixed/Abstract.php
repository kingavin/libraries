<?php
abstract class App_Brick_Fixed_Abstract implements App_Brick_Interface
{
	abstract protected function _getExtName();
	
	public function path()
	{
		$path = str_replace('_', '/', $this->_getExtName());
		return '/brick/'.$path;
	}

	public function render()
	{
		$this->view = new App_Brick_Fixed_TwigView();
		$this->view->setScriptPath(CONTAINER_PATH.'/extension'.$this->path());
		
		$this->prepare();
		return $this->view->render('view.tpl');
	}
}