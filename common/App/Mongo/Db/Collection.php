<?php
abstract class App_Mongo_Db_Collection
{
	const SORT_ORDER_ASC = 'ASC';
    const SORT_ORDER_DESC = 'DESC';
	
	protected static $_defaultDbAdapter = null;
	
	protected $_name = null;
	protected $_documentClass = null;
	protected $_dbAdapter = null;
	
	protected $_fields = array();
	protected $_filters = array();
	protected $_sort = array();
	protected $_pageSize = null;
	protected $_page = null;
	
	public function __construct()
	{
		$this->_init();
	}
	
	public function _init()
	{
		
	}
	
	public static function setDefaultAdapter($dbAdapter)
	{
		self::$_defaultDbAdapter = $dbAdapter;
	}
	
	public static function getDefaultAdapter()
	{
		return self::$_defaultDbAdapter;
	}
	
	public function setAdapter($dbAdapter)
	{
		$this->_dbAdapter = $dbAdapter;
		return $this;
	}
	
	public function getAdapter()
	{
		if($this->_dbAdapter == null) {
			return self::getDefaultAdapter();
		}
		return $this->_dbAdapter;
	}
	
	public function getCollectionName()
	{
		return $this->_name;
	}
	
	public function getCollection()
	{
		return $this->getAdapter()->getCollection($this->getCollectionName());
	}
	
	public function getDocumentClass()
	{
		return $this->_documentClass;
	}
	
	public function create(array $data = array(), $isNew = true)
	{
		$documentClass = $this->getDocumentClass();
		$config = array();
		$config['isNew'] = ($isNew);
		$config['collection'] = $this;
		return new $documentClass($data, $config);
	}
	
	public function find($id, array $fields = array())
	{
		if (!($id instanceof MongoId)) {
			$id = new MongoId($id);
		}
		
		$query = array('_id' => $id);
		$data = $this->getCollection()->findOne($query, $fields);
		if(is_null($data)) {
			return null;
		}
		return $this->create($data, false);
	}
	
	public function fetchOne()
	{
		$data = $this->getCollection()->findOne($this->_filters, $this->_fields);
		
		if (is_null($data))
			return null;
		
		return $this->create($data, false);
	}
	
	public function fetchAll($convertId = false)
	{
		$cursor = $this->getCollection()->find($this->_filters, $this->_fields);
		
		if(!is_null($this->_page) && !is_null($this->_pageSize)) {
			$start = ($this->_page - 1) * $this->_pageSize ;
			$cursor->limit($this->_pageSize)->skip($start);
		}
		
		if(!is_null($this->_sort)) {
			$cursor->sort($this->_sort);
		}
		
		if($convertId) {
			$data = array();
			foreach($cursor as $id => $row) {
				$row['id'] = $id;
				unset($row['_id']);
				$data[] = $row;
			}
			return $data;
		}
		return $cursor;
	}
	
	public function fetchArr($field = null)
	{
		$cursor = $this->getCollection()->find($this->_filters, $this->_fields);
		
		if(!is_null($this->_page) && !is_null($this->_pageSize)) {
			$start = ($this->_page - 1) * $this->_pageSize ;
			$cursor->limit($this->_pageSize)->skip($start);
		}
		
		if(!is_null($this->_sort)) {
			$cursor->sort($this->_sort);
		}
		
		if(is_null($field)) {
			$data = array();
			foreach($cursor as $id => $row) {
				$data[$id] = $row;
			}
		} else if($field === false) {
			$data = array();
			foreach($cursor as $id => $row) {
				$row['id'] = $id;
				unset($row['_id']);
				$data[] = $row;
			}
		} else {
			$data = array();
			foreach($cursor as $id => $row) {
				$data[$id] = $row[$field];
			}
		}
		return $data;
	}
	
	public function fetchDoc()
	{
		$cursor = $this->getCollection()->find($this->_filters, $this->_fields);
		
		if(!is_null($this->_page) && !is_null($this->_pageSize)) {
			$start = ($this->_page - 1) * $this->_pageSize ;
			$cursor->limit($this->_pageSize)->skip($start);
		}
		
		if(!is_null($this->_sort)) {
			$cursor->sort($this->_sort);
		}
		$docs = array();
		foreach($cursor as $id => $row) {
			$docs[] = $this->create($row, false);
		}
		return $docs;
	}
	
	public function count()
	{
		$cursor = $this->getCollection()->find($this->_filters, array('_id'))->count();
		return $cursor;
	}
	
	public function insert(array $document, array $options = array())
	{
		return $this->getCollection()->insert($document, $options);
	}
	
	public function update(array $criteria, array $operations, array $options = array())
	{
		return $this->getCollection()->update($criteria, $operations, $options);
	}
	
	public function delete(array $criteria, array $options = array())
	{
		if (array_key_exists('_id', $criteria) && !($criteria["_id"] instanceof MongoId)) {
			$criteria["_id"] = new MongoId($criteria["_id"]);
		}
		return $this->getCollection()->remove($criteria, $options);
	}
	
	public function drop()
	{
		return $this->getCollection()->drop();
	}
	
	public function ensureIndex(array $keys, $options = array())
	{
		return $this->getCollection()->ensureIndex($keys, $options);
	}
	
	public function addFilter($key, $val, $type = 'and')
	{
//		if(is_array($val)) {
//			$this->_filters[$key] = array('$in' => $val);
//		} else {
			$this->_filters[$key] = $val;
//		}
		return $this;
	}
	
	public function setFields($fields)
	{
		$this->_fields = (array)$fields;
		return $this;
	}
	
	public function addField($field)
	{
		$this->_fields[] = $field;
		return $this;
	}
	
	public function setPage($page)
	{
		$this->_page = $page;
		return $this;
	}
	
	public function setPageSize($pageSize)
	{
		$this->_pageSize = $pageSize;
		return $this;
	}
	
	public function sort($key, $value = 1)
	{
		$this->_sort[$key] = $value;
		return $this;
	}
	
	public function addSort($key, $value = 1)
	{
		$this->_sort[$key] = $value;
		return $this;
	}
}