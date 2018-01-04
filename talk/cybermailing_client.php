<?php
//?CBM_dbug=05spfidszuf86gspo5xv5d45g

if(!defined('URL_CYBERMAILING_API'))
	define('URL_CYBERMAILING_API','api.cybermailing.com');
if(!defined('URL_CYBERMAILING_APP'))
	define('URL_CYBERMAILING_APP','www.cybermailing.com/mailing/');

class CybermailingClient {
	private $_sCbmKey='';
	private $_sCbmTrackVar='';
	private $_bDbug = false;
	public $sDbug = '<style>.dbug{padding:5px; font-family: "monospace","sans-serif"; font-size: 80%;}
							.section{background-color:#000B2C;}
							.body{margin-bottom:10px; background-color:#F1F1F1;}
							.action{color:rgb(195, 195, 255);}
							.response{color:rgb(144, 255, 144);}
							.info{color: #CCC9C9; background-color: #2C0B00;}
							.success{color:green; font-weight:bold;}
							.fail{color:red; font-weight:bold;}
					</style>';

	public function __construct($sCbmKey,$sCbmTrackVar)
	{
		if(!empty($sCbmKey))
			$this->_sCbmKey = $sCbmKey;
		else
			if(defined('CYBER_KEY'))
				$this->_sCbmKey = $sCbmKey;
		if(!empty($sCbmTrackVar))
			$this->_sCbmTrackVar = $sCbmTrackVar;
		else
			if(defined('CYBER_TRACKVAR'))
				$this->_sCbmTrackVar = $sCbmTrackVar;
			else
				$this->_sCbmTrackVar = 'clt';
		$sErr = '';		
		if(empty($this->_sCbmKey))
			$sErr = 'CyberMailing Secret Key is missing';
		$this->_sCbmTrackVar = $sCbmTrackVar;
		if(isset($_GET['CBM_dbug']) && $_GET['CBM_dbug']==$this->_sCbmKey) {
			$this->_bDbug = true;
			echo $this->sDbug.'<h1>CyberMailing API CLient Dbug Mode</h1>'.$sErr;
		}
	}

	public static function checkInput($aInfo)
	{
		if(empty($aInfo['function']))
			die('ERROR : cybermailing api client function is missing');
		switch($aInfo['function']) {
			case 'subscribe' :
			case 'unsubscribe' :
			case 'update' :
			case 'getListCustomFields' :
			case 'getSubscriberProfile' :	
			case 'personnalizeText' :	
				$aRequired = array('Liste','Email');
				$sAlternative = 'tracking_id';
				break;
			case 'setMessageAutoResponse' :	
			case 'setMessageBroadCast' :	
			case 'setMessageTransaction' :	
				$aRequired = array('Liste');
				$sAlternative = 'tracking_id';
				break;
		}
		if(!empty($aRequired))
			foreach($aRequired as $sField) {
				if(!empty($sAlternative))
					if(empty($aInfo[$sField]) && empty($aInfo[$sAlternative]))
						die('ERROR : '.$sField.' or '.$sAlternative.' is missing');
				else	
					if(empty($aInfo[$sField]))
						die('ERROR : '.$sField.' is missing');
			}
		return $aInfo;	
	}
	
	public function talk($aInfo)
	{
		if(empty($aInfo['CyberKey']))
			$aInfo['CyberKey'] = $this->_sCbmKey;
		$aInfo = self::checkInput($aInfo);
		switch($aInfo['function']) {
			case 'subscribe' :
			case 'unsubscribe' :
			case 'update' :
				$aInfo['Ip'] = empty($_SERVER["REMOTE_ADDR"]) ? 'local_ip' : $_SERVER["REMOTE_ADDR"];
				if (isset($_SERVER['HTTPS'])) {
					if ($_SERVER['HTTPS'] == 'on')
						$http = 'https';	
					else
						$http = 'http';
				}
				else
					$http = 'http';
				if(empty($aInfo['Url']))
					if(!empty($_SERVER['HTTP_HOST']) && !empty($_SERVER['REQUEST_URI']))
						$aInfo['Url'] = $http.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
					else
						$aInfo['Url'] = 'local';
		}
		$sSafeApiUrl = str_replace('http://','',URL_CYBERMAILING_API);
		$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
		switch($aInfo['function']){
			case 'tracking':
				$sCurlUrl = 'https://'.$sSafeApiUrl .'/link/?'.$aInfo['tracking_id'].'&api';
				$ch = curl_init($sCurlUrl);
				curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				break;
			case 'confirm':
				$sCurlUrl =  'https://'.$sSafeAppUrl .'validsub.php?Id='.$aInfo['tracking_id'].'&api';
				$ch = curl_init($sCurlUrl);
				curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				break;
			default :
				$sCurlUrl = 'https://'.$sSafeApiUrl . '/index.php';
				$ch = curl_init($sCurlUrl);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $aInfo);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
				break;
		}
		$sDbug = '';
		if($this->_bDbug)
			$sDbug = '<div class="dbug section action">Query sent to CyberMailing => </div><div class="dbug body">'.$sCurlUrl.'<br>
			POST DATA : '.var_export($aInfo,true).'
			</div>';
			$sCurlReturn = curl_exec($ch);
			curl_close($ch);
		if($this->_bDbug)
			$sDbug .= '<div class="dbug section response"> <= Response from CyberMailing </div><div class="dbug body">'.$sCurlReturn .'</div>';
		if($this->_bDbug)
			echo $this->sDbug.$sDbug;	
		if (!$sCurlReturn)
			return false;
		return $sCurlReturn;
	}
	
	public function sendContact($aInfo,$action = 'subscribe')
	{
		$aInfo['function'] = $action;
		return $this->talk($aInfo);
	}
	public function unsubscribe($aInfo)
	{
		$aInfo['function'] = 'unsubscribe';
		return $this->talk($aInfo);
	}
		
	public function clicTracking($id='')
	{
		if(empty($_GET[$this->_sCbmTrackVar]) && empty($id))
			return;
		if(empty($id))
			$id = $_GET[$this->_sCbmTrackVar];
		$aInfo['function'] = 'tracking';
		$aInfo['tracking_id'] = $id;
		return $this->talk($aInfo);
	}
		
	public function automaticUnsubscribe()
	{
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			if(!empty($_SERVER['QUERY_STRING']))
				echo '<iframe src="https://'. $sSafeAppUrl .'pro/u/?'.$_SERVER['QUERY_STRING'].'" width="600" height="400" style="border-width:0" scrolling="auto"></iframe>';
			else
				echo 'Erreur : URL incomplet..';
	}

	public function reactivate()
	{
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			if(!empty($_GET['Id']))
				header('location:https://'.$sSafeAppUrl .'reactivate.php?Id='.$_GET['Id']);
			else
				echo 'Erreur : URL incomplet..';
				
	}
	
	public function doubleOptinConfirmRedirect()
	{
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			if(!empty($_GET['Id']))
				header('location:https://'.$sSafeAppUrl .'validsub.php?Id='.$_GET['Id']);
			else
				echo 'Erreur : URL incomplet..';
	}

	public function redirect()
	{
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			$data = key($_GET);
			if(!empty($data))
				header('location:http://'.$sSafeAppUrl .'link?'.$data);
			else
				echo 'Erreur : URL incomplet..';
	}

	public function doubleOptinConfirm($id)
	{
		if(empty($_GET['Id']) && empty($id)) {
			echo 'Erreur : URL incomplet..';
			return;
		}
		if(empty($id))
			$id = $_GET['Id'];
		$aInfo['function'] = 'confirm';
		$aInfo['tracking_id'] = $id;
		return $this->talk($aInfo);
	}
		
	private static function array_implode( $glue, $separator, $array ) 
	{
		if ( ! is_array( $array ) ) return $array;
		$string = array();
		foreach ( $array as $key => $val ) {
			if ( is_array( $val ) )
				$val = implode( ',', $val );
			$string[] = "{$key}{$glue}{$val}";
		   
		}
		return implode( $separator, $string );
	   
	}	
	
	private static function string_explode( $glue, $separator, $string ) 
	{
		$exp = explode($separator,$string);
		if(count($exp)==1) return $string;

		foreach($exp as $valuepair)
			{
				$exp2 = explode($glue,$valuepair);
				$arr[$exp2[0]] = $exp2[1];
			}
		return $arr;   
	}

}

?>
