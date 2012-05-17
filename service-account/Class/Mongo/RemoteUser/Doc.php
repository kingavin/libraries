<?php
class Class_Mongo_RemoteUser_Doc extends App_Mongo_Db_Document
{
	public function validatePassword($input)
	{
		if($this->password == Class_Session_User::encryptPassword($input)) {
			return true;
		}
		return false;
	}
}