<?php
class App_Controller_Action extends Zend_Controller_Action
{
	protected $_actionStatus = 'edit';
	
	protected function _setActionStatus($val)
	{
		$this->_actionStatus = $val;
	}
	
	protected function _getActionStatus()
	{
		return $this->_actionStatus;
	}
	
	protected function _getResource()
	{
		
	}
}