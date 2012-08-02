<?php
class Class_Session_Admin
{
	private $_isLogin = null;
	
	private function __construct(){}
    private function __clone(){}
    private static $_instance = null;
    
    /**
     * @return Class_Session_Admin
     * Enter description here ...
     */
    public static function getInstance()
    {
    	if(is_null(self::$_instance)) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    
    public function login($post)
    {
        $admin = null;
        if(is_array($post)) {
            if(!is_null($post['loginName'])) {
                $tb = new Zend_Db_Table('admin');
                $select = $tb->select()->where("loginName = ?", $post['loginName'])
                    ->where('password = ?', md5($post['password'].MD5_SALT));
		        $admin = $tb->fetchRow($select);
            }
        } else if($post instanceof Class_Model_Admin_Row) {
            $admin = $post;
        }
        
        if(is_null($admin)) {
            return false;
        }
        $admin->lastLogin = date("Y-m-d H:i:s");
        $admin->save();
      	$this->updateSession($admin);
        return true;
    }
    
	public function updateSession(Zend_Db_Table_Row_Abstract $admin)
    {
		setcookie('loginName', $admin->loginName, time()+60*60*24*7, '/');
		//setcookie('validation', md5($admin->password.' '.), time()+60*2, '/');
		setcookie('roleId', $admin->roleId, time()+60*60*24*7, '/');
    }
    
    public function logout()
    {
		setcookie('loginName', '', time()-3600);
//		setcookie('validation', '', time()-3600);
		setcookie('roleId', '', time()-3600);
    }
    
    public function isLogin()
    {
    	if($this->_isLogin == null) {
    		if(isset($_COOKIE['loginName'])) {
    			$this->_isLogin = true;
    		} else {
    			$this->_isLogin = false;
    		}
    	}
    	return $this->_isLogin;
    }
    
    public function getRoleId()
    {
        if($this->isLogin()) {
        	return $_COOKIE['roleId'];
        }
        return 'nobody';
    }
    
    public function getLoginName()
    {
    	if($this->isLogin()) {
        	return $_COOKIE['loginName'];
        }
        return 'nobody';
    }
}