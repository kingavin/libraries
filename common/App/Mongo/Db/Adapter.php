<?php
class App_Mongo_Db_Adapter
{
	protected $_dbName = null;
	protected $_dbHost = null;
	
	protected $_mongo = null;
	protected $_db = null;
	
	public function __construct($dbName, $dbHost = '127.0.0.1')
	{
		$this->_dbName = $dbName;
		$this->_dbHost = $dbHost;
	}	
	
	public function getMongo()
	{
		$this->_connect();
		return $this->_mongo;
	}
	
	public function getDb()
	{
		$this->_connect();
		return $this->_db;
	}
	
	public function getDbName()
	{
		return $this->_dbName;
	}
	
	public function getCollection($collectionName)
	{
		$this->_connect();
		return $this->_db->$collectionName;
	}
	
	public function isConnected()
    {
        return ((bool) ($this->_db instanceof Mongo));
    }
    
	protected function _connect()
    {
        if ($this->_db) {
            return;
        }
        
        $m = new Mongo($this->_dbHost, array('persist' => 'x'));
        $this->_mongo = $m;
        $this->_db = $m->selectDb($this->_dbName);
        return;
    }
}