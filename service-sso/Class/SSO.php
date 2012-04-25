<?php 
class Class_SSO
{
	const SERVICE_FILE_KEY = 'gioqnfieowhczt7vt87qhitonqfn8eaw9y8s90a6fnvuzioguifeb';
	const SERVICE_CMS_KEY = 'zvmiopav7BbuifbahoUifbqov541huog5vua4ofaweafeq98fvvxreqh';
	const SERVICE_PM_KEY = 'fiewayzgv7z9g784b3o549830yf7gvapojr9021yhb43iuhor78fgv';
	
	public function validateLoginUrl($consumer, $ret, $timeStamp, $token, $sig)
	{
		$serverTime = time();
		
		if($serverTime - $timeStamp > 1800) {
			return 'timeout';
		}
		
		switch($consumer) {
			case 'cms':
				$sigGenerated = md5($consumer.$ret.$timeStamp.$token.self::SERVICE_CMS_KEY);
				break;
			case 'pm':
				$sigGenerated = md5($consumer.$ret.$timeStamp.$token.self::SERVICE_PM_KEY);
				break;
			case 'service-file':
				$sigGenerated = md5($consumer.$ret.$timeStamp.$token.self::SERVICE_FILE_KEY);
				break;
		}
		
		if($sigGenerated == $sig) {
			return 'success';
		}
		return 'fail';
	}
	
	static public function validateSig($consumer, $timeStamp, $token, $sig, $timeout = 1800)
	{
		$serverTime = time();
		
		if($serverTime - $timeStamp > $timeout) {
			return 'timeout';
		}
		
		switch($consumer) {
			case 'cms':
				$sigGenerated = md5($consumer.$ret.$timeStamp.$token.self::SERVICE_CMS_KEY);
				break;
			case 'pm':
				$sigGenerated = md5($consumer.$ret.$timeStamp.$token.self::SERVICE_PM_KEY);
				break;
			case 'service-file':
				$sigGenerated = md5($consumer.$timeStamp.$token.self::SERVICE_FILE_KEY);
				break;
		}
		
		if($sigGenerated == $sig) {
			return 'success';
		}
		return 'fail';
	}
}