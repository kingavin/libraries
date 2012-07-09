<?php
class Class_Mongo_Post_Doc extends App_Mongo_Db_Document
{
	protected $_field = array(
			"content",
			"datatime",
			"httpurl",
			"isShow",
			"lastDatatime",
			"lastReply",
			"lastReplyUsername",
			"md5httpurl",
			"orgCode",
			"parentId",
			"sort",
			"status",
			"title",
			"username"				
	);
}