<?php
class Class_Session_Admin
{
	private function __construct(){}
	private function __clone(){}
	private static $_instance = null;
	
	private static $_md5salt = 'Hgoc&639Jgo';
	private static $_md5salt2 = 'jiohGY6&*9';
	
	private $_isLogin = null;
	
    /**
     * @return Class_Session_Admin
     */
    public static function getInstance()
    {
    	if(is_null(self::$_instance)) {
    		self::$_instance = new self();
    	}
    	return self::$_instance;
    }
    
    public function designerLogin($loginArr)
    {
    	$requestHost = $_SERVER['HTTP_HOST'];
    	$topDomainName = App_Func::getDomain($requestHost);
    	if($loginArr['result'] == 'success') {
    		$this->_updateSession(array(
    			'adminType' => 'designer',
    			'loginName' => 'designer',
    			'roleId' => 0,
    			'adminId' => 0,
    			'liv' => md5('designer'.'designer'.self::$_md5salt.'0'.self::$_md5salt2.'0')
    		), $topDomainName);
    		return true;
    	} else {
    		return false;
    	}
    }
    
    public function webAdminLogin($post)
    {
        $admin = null;
        if(is_array($post)) {
            if(!is_null($post['loginName'])) {
                $tb = Class_Base::_('Admin');
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
        $this->_updateSession(array(
        	'adminType' => 'webAdmin',
    		'loginName' => $admin->loginName,
			'roleId' => $admin->roleId,
    		'adminId' => $admin->id,
        	'liv' => md5('webAdmin'.$admin->loginName.self::$_md5salt.$admin->roleId.self::$_md5salt2.$admin->id)
        ));
        $this->_isLogin = true;
        return true;
    }
    
    public function logout()
    {
    	if($this->getAdminType() == 'webAdmin') {
	        setcookie('adminType', '', 1, '/');
	    	setcookie('loginName', '', 1, '/');
			setcookie('roleId', '', 1, '/');
			setcookie('adminId', '', 1, '/');
			setcookie('liv', '', 1, '/');
    	} else {
    		$requestHost = $_SERVER['HTTP_HOST'];
    		$topDomainName = getDomain($requestHost);
    		setcookie('adminType', '', 1, '/', $topDomainName);
	    	setcookie('loginName', '', 1, '/', $topDomainName);
			setcookie('roleId', '', 1, '/', $topDomainName);
			setcookie('adminId', '', 1, '/', $topDomainName);
			setcookie('liv', '', 1, '/', $topDomainName);
    	}
		$this->_isLogin = false;
    }
    
    public function isLogin()
    {
    	if($this->_isLogin == null) {
    		if(isset($_COOKIE['loginName']) && $_COOKIE['loginName'] != '') {
    			$livToken = md5($_COOKIE['adminType'].$_COOKIE['loginName'].self::$_md5salt.$_COOKIE['roleId'].self::$_md5salt2.$_COOKIE['adminId']);
    			if($livToken == $_COOKIE['liv']) {
    				$this->_isLogin = true;
    			} else {
    				$this->_isLogin = false;
    			}
    		} else {
    			$this->_isLogin = false;
    		}
    	}
    	return $this->_isLogin;
    }
    
    public function getAdminType()
    {
    	if($this->isLogin()) {
    		return $_COOKIE['adminType'];
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
    
    public function getRoleId()
    {
        if($this->isLogin()) {
        	return $_COOKIE['roleId'];
        }
        return 'nobody';
    }
    
    public function getAdminId()
    {
    	if($this->isLogin()) {
        	return $_COOKIE['adminId'];
        }
        return 'nobody';
    }
    
    public function setSessionData($name, $value)
    {
    	$session = new Zend_Session_Namespace('admin');
    	$session->$name = $value;
    }
    
    public function getSessionData($name)
    {
    	$session = new Zend_Session_Namespace('admin');
    	return $session->$name;
    }
    
    public function _updateSession($admin, $domainName = null)
    {
    	if($domainName == null) {
	    	setcookie('adminType', $admin['adminType'], time()+60*60*24*7, '/');
	    	setcookie('loginName', $admin['loginName'], time()+60*60*24*7, '/');
			setcookie('roleId', $admin['roleId'], time()+60*60*24*7, '/');
			setcookie('adminId', $admin['adminId'], time()+60*60*24*7, '/');
			setcookie('liv', $admin['liv'], time()+60*60*24*7, '/');
    	} else {
    		setcookie('adminType', $admin['adminType'], time()+60*60*24*7, '/', $domainName);
	    	setcookie('loginName', $admin['loginName'], time()+60*60*24*7, '/', $domainName);
			setcookie('roleId', $admin['roleId'], time()+60*60*24*7, '/', $domainName);
			setcookie('adminId', $admin['adminId'], time()+60*60*24*7, '/', $domainName);
			setcookie('liv', $admin['liv'], time()+60*60*24*7, '/', $domainName);
    	}
    }
}