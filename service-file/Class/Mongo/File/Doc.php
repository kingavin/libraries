<?php
class Class_Mongo_File_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
			"orgCode",
			"userId",
			"groupId",
			"filename",
			"urlname",
			"size",
			"storage",
			"isImage",
			"uploadUnixTime",
			"uploadTime"				
	);
}