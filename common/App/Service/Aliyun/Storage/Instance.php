<?php
//define('OSS_ACCESS_ID', '92uhi025kwjnpvj1m662vz8z');
//define('OSS_ACCESS_KEY', 'XUpFWqd+ZqlV3kYugooVQpr9LJU=');

class App_Service_Aliyun_Storage_Instance
{
	protected $_username = '92uhi025kwjnpvj1m662vz8z';
	protected $_apikey = 'ZqlV3kYugooVQpr9LJU';
	protected $_auth = null;
	
	$this->_options = array();
	
	public function __construct()
	{
		
	}
	
	/**
	 * @return CF_Authentication
	 * Enter description here ...
	 */
	public function getAuth()
	{
		if(is_null(self::$_auth)) {
			$serializer = Zend_Serializer::factory('PhpSerialize');
			$frontendOptions = array('lifetime' => 3600);
		    $backendOptions = array('cache_dir' => GENERAL_CACHE_PATH);
		    $authCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		    
			if(($serializedAuthData = $authCache->load('rackspaceFileAuth')) === false) {
				$auth = new CF_Authentication(self::$_username, self::$_apikey);
				$auth->authenticate();
				$serializedAuthData = $serializer->serialize($auth);
				$authCache->save($serializedAuthData, 'rackspaceFileAuth');
			} else {
				$auth = $serializer->unserialize($serializedAuthData);
			}
			self::$_auth = $auth;
		}
		return self::$_auth;
	}
	
	public function regenerateAuth()
	{
		$serializer = Zend_Serializer::factory('PhpSerialize');
		$frontendOptions = array('lifetime' => 3600);
		$backendOptions = array('cache_dir' => GENERAL_CACHE_PATH);
		$authCache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		$authCache->  remove('rackspaceFileAuth');
		self::$_auth = null;
		return $this->getAuth();
	}
	
	public function getContainer($containerName)
	{
		if(empty($containerName)) {
			throw new Exception('container name can\'t be null');
		}
		$auth = $this->getAuth();
		try {
			$conn = new CF_Connection($auth);
			$container = $conn->create_container($containerName);
		} catch(Exception $e) {
			$this->regenerateAuth();
			$conn = new CF_Connection($auth);
			$container = $conn->create_container($containerName);
		}
		return $container;
	}
	
	public function createFolder()
	{
		
	}
	
	public function createObject($file, $filepath, $host)
	{
		
	}
	
	public function putFile($filename, $filepath)
	{
		$bucket = '32';
		
//		$filename = '31天反射.pdf';
//		$file = "D:\\Book\\silverlight\\".$filename;
//		$file = iconv("UTF-8", "GBK", $file); 
		
		$object = 'object0003/'.$filename;
		$content = '';
		$length = 0;
		$handle = fopen($filepath,'r');
		if($handle){
			$f = fstat($handle);
			$length = $f['size'];
			while(!feof($handle)){
				$content .= fgets($handle,8192);
			}
		}
		$this->setOptionHeader(array(
			'content' => $content,
			'length' => $length,
		));
		
		$this->addOptionHeader("Content-Disposition", 'attachment; filename=' . $basename);

		$options['bucked'] = $bucket;
		$options['method'] = 'PUT';
		$options['object'] = $object;
		$options['length'] = array('length' => $length);
		if(isset($content_type) && !empty($content_type)){
			$options[self::OSS_CONTENT_TYPE] = $content_type;
		}

		$response = $this->auth ( $options );
		$this->log(($response->isOK () ? OSS_UPLOAD_FILE_BY_CONTENT_SUCCESS : OSS_UPLOAD_FILE_BY_CONTENT_FAILED)." Response: [" . $response->body . "]", $options );
		return $response;
		
		
	}
	
	public function auth($options)
	{
		//构造url
		$url = $this->make_url($options);
		$options['url'] = $url;
		
        //创建请求
		$request = new RequestCore($url);
		$headers = array (
			self::OSS_CONTENT_MD5 => (isset($options[self::OSS_CONTENT_MD5]) && !empty($options[self::OSS_CONTENT_MD5]))?$options[self::OSS_CONTENT_MD5]:'',
			self::OSS_CONTENT_TYPE => (isset($options[self::OSS_CONTENT_TYPE]) && !empty($options[self::OSS_CONTENT_TYPE]) )?$options[self::OSS_CONTENT_TYPE]:'application/x-www-form-urlencoded',
			self::OSS_DATE => gmdate('D, d M Y H:i:s \G\M\T'),
			self::OSS_HOST => self::DEFAULT_OSS_HOST,
		);

		//合并 HTTP headers
		if (isset ( $options [self::OSS_HEADERS] )) {
			$headers = array_merge ( $headers, $options [self::OSS_HEADERS] );
		}

		//构造resource串
		$resource = $this->make_resource($options);

		//获取签名
		$sign = $this->create_sign_for_nomal_auth($options[self::OSS_METHOD], $headers, $resource);
		
		//设置签名
		$headers[self::OSS_AUTHORIZATION] = $sign;

		//设置请求方法
		if (isset ( $options [self::OSS_METHOD] )) {
			$request->set_method ( $options [self::OSS_METHOD] );
		}

		//设置Http-Body
		if (isset ( $options [self::OSS_CONTENT] )) {
			$request->set_body ( $options [self::OSS_CONTENT] );
		}

		//增加Http-Header
		foreach ( $headers as $header_key => $header_value ) {
			$header_value = str_replace ( array ("\r", "\n" ), '', $header_value );
			if ($header_value !== '') {
				$request->add_header ( $header_key, $header_value );
			}
		}
		
		//发送请求
		$request->send_request();
		
		//返回ResponseCore
		return new ResponseCore ( $request->get_response_header (), $request->get_response_body (), $request->get_response_code () );
	}
	
	private function make_url($options)
	{

		$url = "";
		$url .= 'http://storage.aliyun.com';
		$url .= '/' . $options [self::OSS_BUCKET];
		if (isset ( $options [self::OSS_OBJECT] ) && '/' !== $options [self::OSS_OBJECT]) {
			$url .= "/" . str_replace('%2F','/',rawurlencode ( $options [self::OSS_OBJECT] ));
		}

		//Acl
		if(isset($options[self::OSS_ACL]) && $options[self::OSS_ACL]){
			$url .= '?acl';
		}

		return $url;
	}
	
	private function make_resource($options)
	{
		$resource = '';
		
		$resource .= '/'.$options[self::OSS_BUCKET];
		
		if (isset ( $options [self::OSS_OBJECT] ) && '/' !== $options [self::OSS_OBJECT]) {
			$resource .= "/" . str_replace('%2F','/',rawurlencode ( $options [self::OSS_OBJECT] ));
		}

		//Acl
		if(isset($options[self::OSS_ACL])){
			$resource .= '?acl';
		}

		//Group
		if(isset($options[self::OSS_OBJECT_GROUP])){
			$resource .= '?group';
		}

		return $resource;
	}
	
	public function setOptionHeader($op)
	{
		$this->_options['header'] = $op;
	}
	
	public function addOptionHeader($key, $value)
	{
		$this->_options['header'][$key] = $value;
	}
	
	public function getProvider()
	{
		return "Aliyun oss";
	}
}