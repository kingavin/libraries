<?php
abstract class App_Mongo_Db_Document
{
	protected $_id = null;
	protected $_data = array();
	protected $_cleanData = array();
	protected $_collection = null;
	protected $_field = array();
	
	protected $_operations = array();
	
	public function __construct($data = array(), array $config = array())
	{
		$this->_collection = $config['collection'];
		if (isset($config['isNew']) && $config['isNew'] === false) {
			$data = $this->_stripDataId($data);
            $this->_cleanData = $data;
            $this->_data = $data;
        } else {
        	$this->_data = $data;
        }
		$this->_init();
	}
	
	protected function _stripDataId($data)
	{
		if(array_key_exists('_id', $data)) {
			$this->_id = $data['_id'];
			unset($data['_id']);
		}
		return $data;
	}
	
	protected function _init()
	{
		
	}
	
	protected function _preInsert()
	{
		
	}
	
	protected function _postInsert()
	{
		
	}
	
	protected function _preUpdate()
	{
		
	}
	
	protected function _postUpdate()
	{
		
	}
	
	protected function _preSave()
	{
		
	}
	
	protected function _postSave()
	{
		
	}
	
	protected function _preDelete()
	{
		
	}
	
	protected function _postDelete()
	{
		
	}
	
	public function getDb()
	{
		
	}
	
	public function getCollection()
	{
		return $this->_collection;
	}
	
	public function getId()
	{
		//new object that does not have an id;
		if(is_null($this->_id)) {
			return null;
		}
		return $this->_id->{'$id'};
	}
	
	public function getProperty($property)
	{
		if (array_key_exists($property, $this->_data)) {
			return $this->_data[$property];
		}
		
		if (array_key_exists($property, $this->_cleanData)) {
			return $this->_cleanData[$property];
		}
		
		return null;
	}
	
	public function setProperty($property, $value)
	{
		if($property == 'id') {
			return false;
		}
		if (substr($property, 0, 1) == '_') {
			throw new Exception("Can not set private property '$property'");
		}
		
		if (is_null($value)) {
			$this->_data[$property] = null;
		}
		
		$this->_data[$property] = $value;
	}
	
	public function hasProperty($property)
	{
		if (array_key_exists($property, $this->_data)) {
			return !is_null($this->_data[$property]);
		}
		
		if (array_key_exists($property, $this->_cleanData)) {
			return !is_null($this->_cleanData[$property]);
		}
		
		return false;
	}
	
	public function setFromArray($array)
	{
		foreach($array as $key => $val) {
			if(in_array($key, $this->_field)) {
				$this->setProperty($key, $val);
			}
		}
		return $this;
	}
	
	public function isNewDocument()
	{
		return empty($this->_cleanData) && is_null($this->getId());
	}
	
	public function export()
	{
		$exportData = $this->_cleanData;
		
		foreach ($this->_data as $property => $value) {
			if($this->isNewDocument() && $property == 'id') {
				continue;
			}
			if (is_null($value)) {
				unset($exportData[$property]);
				continue;
			}
			
			$exportData[$property] = $value;
		}
		
		return $exportData;
	}
	
	public function getCriteria()
	{
		return array('_id' => $this->_id);
	}
	
	public function save($safe = true)
	{
		$isNew = $this->isNewDocument();
		if ($isNew)
			$this->_preInsert();
		else
			$this->_preUpdate();
		
		$this->_preSave();
		
		$exportData = $this->export();
		if ($isNew) {
			$result = $this->getCollection()->insert($exportData, array('safe' => $safe));
		} else {
			$this->processChanges($exportData);
			$operations = $this->getOperations(true);
			if (empty($operations)) {
				return true;
			}
			$result = $this->getCollection()->update($this->getCriteria(), $operations, array('safe' => $safe));
		}
		
		$this->purgeOperations(true);
		$data = $this->_stripDataId($exportData);
		$this->_data = $data;
		$this->_cleanData = $data;
		
		if ($this->$isNew)
			$this->_postInsert();
		else
			$this->_postUpdate();
		$this->_postSave();
		
		return $result;
	}
	
	public function processChanges(array $data = array())
	{
		foreach ($data as $property => $value) {
			if ($property === '_id') continue;
			
			if (!array_key_exists($property, $this->_cleanData)) {
				$this->addOperation('$set', $property, $value);
				continue;
			}
			
			$newValue = $value;
			$oldValue = $this->_cleanData[$property];
			
			if ($newValue !== $oldValue) {
				$this->addOperation('$set', $property, $value);
			}
		}
		
		foreach ($this->_cleanData as $property => $value) {
			if (array_key_exists($property, $data)) continue;
			
			$this->addOperation('$unset', $property, 1);
		}
	}
	
	public function getOperations()
	{
		return $this->_operations;
	}
	
	public function addOperation($operation, $property, $value = null)
	{
		if (!array_key_exists($operation, $this->_operations)) {
			$this->_operations[$operation] = array();
		}
		
		$this->_operations[$operation][$property] = $value;
	}
	
	public function purgeOperations($includingChildren = false)
	{
		$this->_operations = array();
	}
	
	public function delete($safe = true)
	{
		$mongoCollection = $this->getCollection();
		$this->_preDelete();
		
		$result = $mongoCollection->delete($this->getCriteria(), array('safe' => $safe));
		
		$this->_postDelete();
		return $result;
	}
	
//	public function setFromArray($array)
//	{
//		foreach($array as $property => $value) {
//			if($property == '_id' && $value instanceof MongoId) {
//				$this->_id = $value;
//			} else {
//				$this->setProperty($property, $value);
//			}
//		}
//		return $this;
//	}
	
	public function getData()
	{
		return $this->_data;
	}
	
	public function __get($property)
	{
		return $this->getProperty($property);
	}
	
	public function __set($property, $value)
	{
		return $this->setProperty($property, $value);
	}
	
	public function __isset($property)
	{
		return $this->hasProperty($property);
	}
	
	public function __unset($property)
	{
		$this->_data[$property] = null;
	}
	
	public function toArray($withId = false)
	{
		if($withId) {
			$data = $this->_data;
			$data['id'] = $this->getId();
			return $data;
		} else {
			return $this->_data;
		}
	}
}