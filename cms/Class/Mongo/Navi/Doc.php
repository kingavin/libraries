<?php 
class Class_Mongo_Navi_Doc extends App_Mongo_Tree_Doc
{
	protected $_field = array(
		'label',
		'description'
	);
	
	protected function _getIndex()
	{
		return $this->naviIndex;
	}
}