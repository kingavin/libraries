<?
class Class_Acl extends Zend_Acl
{
	protected static $_acl = null;
	
	protected function __construct() {}
	
	protected function __clone() {}
	
	public function loadRules()
	{
		$this->addResource('admin-index');
		$this->addResource('admin-anonymous');
		$this->addResource('rest-file');
		$this->addResource('rest-group');
		
		
		$this->addRole(new Zend_Acl_Role('nobody'));
		$this->deny('nobody', null);

		$this->addRole(new Zend_Acl_Role(0));
		$this->allow(0, null);

		$this->allow(null, 'admin-anonymous');
	}
	
	/**
	 * @return Class_Acl
	 * Enter description here ...
	 */
	public static function getInstance()
	{
		if(is_null(self::$_acl)) {
			self::$_acl = new self();
			self::$_acl->loadRules();
		}
		
		return self::$_acl;
	}
}