<?php
class App_Mongo_Order_Co extends App_Mongo_Db_Collection
{
	protected $_name = 'order';
	protected $_documentClass = 'App_Mongo_Order_Doc';
}