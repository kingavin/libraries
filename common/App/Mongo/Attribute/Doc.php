<?php
class App_Mongo_Attribute_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
		'attributesetId',
		'type',
		'label',
		'description',
		'options',
		'required',
		'sort'
	);
}