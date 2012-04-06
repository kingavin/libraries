<?php
class App_Mongo_Db_Adapter
{
	protected $_dbName = null;
	protected $_dbHost = null;
	
	protected $_connection = null;
	
	public function __construct($dbName, $dbHost = '127.0.0.1')
	{
		$this->_dbName = $dbName;
		$this->_dbHost = $dbHost;
	}
	
	public function getConnection()
	{
		$this->_connect();
		return $this->_connection;
	}
	
	public function getCollection($collectionName)
	{
		$this->_connect();
		return $this->_connection->$collectionName;
	}
	
	public function isConnected()
    {
        return ((bool) ($this->_connection instanceof Mongo));
    }
    
	protected function _connect()
    {
        if ($this->_connection) {
            return;
        }
        
        $m = new Mongo($this->_dbHost, array('persist' => 'x'));
        $this->_connection = $m->selectDb($this->_dbName);
        return;
    }
}