<?php
class Class_Mongo_RemoteSite_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'orgCode',
		'remoteId',
		'globalId',
		'subdomainName',
		'language',
		'label'
	);
	
	protected function _preInsert()
	{
		$db = $this->getDb();
		$result = $db->command(array(
			"findandmodify" => "counter",
			"query" => "remote_site",
			"update" => array('$inc' => array("value"=> 1)),
		));
		$result = $result['value'];
		$count = $result['value'];
		
		$this->globalId = $count;
	}
}