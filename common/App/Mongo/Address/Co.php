<?php
class App_Mongo_Address_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'address';
	protected $_documentClass = 'App_Mongo_Address_Doc';
}