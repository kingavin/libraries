<?php
class App_Mongo_Cart_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'cart';
	protected $_documentClass = 'App_Mongo_Cart_Doc';
	
	public function _init()
	{
		$this->_dbAdapter = new App_Mongo_Db_Adapter('server_center');
	}
}