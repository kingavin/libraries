<?php
class Class_Func
{
	public static function count(Zend_Db_Table_Select $selector)
    {
    	$countSelect = clone $selector;
    	$table = $countSelect->getTable();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::FROM);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
		$countSelect->from($table, 'count(*)');
		
        $db = Zend_Registry::get('db');
        $totalRecords = $db->fetchOne($countSelect);
        return intval($totalRecords);
    }
}
