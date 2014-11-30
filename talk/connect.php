<?php
//?CBM_dbug=05spfidszuf86gspo5xv5d45g

if(!defined('URL_CYBERMAILING_API'))
	define('URL_CYBERMAILING_API','api.cybermailing.com');
if(!defined('URL_CYBERMAILING_APP'))
	define('URL_CYBERMAILING_APP','www.cybermailing.com/mailing/');

class cyberMailing_connect {
	
	const _cyberKey = CYBER_KEY; 
	const _trackVar = CYBER_TRACKVAR; 
	const _redirectID = CYBER_REDIRECTID; 
	const _dbug = 0; 
	
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

	public function checkInput($aInfo) {
		if(empty($aInfo['function']))
			die('ERROR : cybermailing api client function is missing');
		if(empty($aInfo['CyberKey']))
			$aInfo['CyberKey'] = self::_cyberKey;
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
	
	public function talk($aInfo,$mode='synchrone') {
		global $sDbug;
//		print_r($aInfo); die;
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

		if($mode == 'synchrone') {	// mode syncrhone
			$sSafeApiUrl = str_replace('http://','',URL_CYBERMAILING_API);
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			switch($aInfo['function']){
				case 'tracking':
					$sCurlUrl = 'http://'.$sSafeApiUrl .'/link/?'.$aInfo['tracking_id'].'&api';
					$ch = curl_init($sCurlUrl);
					curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
					break;
				case 'confirm':
					$sCurlUrl =  'http://'.$sSafeAppUrl .'validsub.php?Id='.$aInfo['tracking_id'].'&api';
					$ch = curl_init($sCurlUrl);
					curl_setopt($ch,CURLOPT_FOLLOWLOCATION,FALSE);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
					break;
				default :
					$sCurlUrl = 'http://'.$sSafeApiUrl . '/talk.php';
					$ch = curl_init($sCurlUrl);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $aInfo);
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
					//echo $sCurlUrl.'<br>
					//	POST DATA : '.var_export($aInfo,true);
					break;
			}
			$sDbug = '';
			if(self::_dbug)
				$sDbug = '<div class="dbug section action">Query sent to CyberMailing => </div><div class="dbug body">'.$sCurlUrl.'<br>
				POST DATA : '.var_export($aInfo,true).'
				</div>';
				$sCurlReturn = curl_exec($ch);
				curl_close($ch);
			if(self::_dbug)
				$sDbug .= '<div class="dbug section response"> <= Response from CyberMailing </div><div class="dbug body">'.$sCurlReturn .'</div>';
			if(self::_dbug)
				echo '<style>	.dbug{padding:5px; font-family: "monospace","sans-serif"; font-size: 80%;}
							.section{background-color:#000B2C;}
							.body{margin-bottom:10px; background-color:#F1F1F1;}
							.action{color:rgb(195, 195, 255);}
							.response{color:rgb(144, 255, 144);}
							.info{color: #CCC9C9; background-color: #2C0B00;}
							.success{color:green; font-weight:bold;}
							.fail{color:red; font-weight:bold;}
					</style>'.$sDbug;
			
			if (!$sCurlReturn)
				return false;
			return $sCurlReturn;
		}
		if($mode == 'asynchrone') {
			// mode asynchrone à developper
			$sCommand = array_implode( '==', '||', $aInfo); 
			// Deux possibilités 
			//1. exec('php /home/web/bin/......async.php '.$sCommand.' > /dev/null 2>/dev/null &) ce qui indique qu'aucun retour d'execution n'est attendu.
			//2. remmplir une table mysql avec $sCommand, cette table est lue par un processus permanent parallele bin/async.php qui efface les commandes au fur et
			//à mesure qu'il les execute.
			// Dans les 2 cas le script bin/async.php reconstruit le tableau $aInfo à partir de $sCommand et execute la commande talk
			// $aInfo = cyberMailing_connect::string_explode( '==', '||', $_SERVER['argv']); // pour l'exemple 1
			// $aInfo = cyberMailing_connect::talk($aInfo,'synchrone');
		}
	}
	
	public function sendContact($aInfo,$action = 'subscribe') {
		$aInfo['function'] = $action;
		return self::talk($aInfo);
	}
	public function unsubscribe($aInfo) {
		$aInfo['function'] = 'unsubscribe';
		return self::talk($aInfo);
	}
		
	public function clicTracking($id='',$mode='') {
		if(empty($_GET[self::_trackVar]) && empty($id))
			return;
		if(empty($id))
			$id = $_GET[self::_trackVar];
		if(!empty($id))
			if(empty($mode)) {
				$aInfo['function'] = 'tracking';
				$aInfo['tracking_id'] = $id;
				return self::talk($aInfo);
			}
			else {
				$function = 'clicTracking';
				$arguments = $id;
				self::asynchrone($function, $arguments);
			}
	}
		
	public function automaticUnsubscribe() {
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			if(!empty($_SERVER['QUERY_STRING']))
				echo '<iframe src="http://'. $sSafeAppUrl .'pro/u/?'.$_SERVER['QUERY_STRING'].'" width="600" height="400" style="border-width:0" scrolling="auto"></iframe>';
			else
				echo 'Erreur : URL incomplet..';
	}

	public function reactivate() {
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			if(!empty($_GET['Id']))
				header('location:http://'.$sSafeAppUrl .'reactivate.php?Id='.$_GET['Id']);
			else
				echo 'Erreur : URL incomplet..';
				
	}
	
	public function doubleOptinConfirmRedirect() {
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			if(!empty($_GET['Id']))
				header('location:http://'.$sSafeAppUrl .'validsub.php?Id='.$_GET['Id']);
			else
				echo 'Erreur : URL incomplet..';
	}

	public function redirect() {
			$sSafeAppUrl = str_replace('http://','',URL_CYBERMAILING_APP);
			$data = key($_GET);
			if(!empty($data))
				header('location:http://'.$sSafeAppUrl .'link?'.$data);
			else
				echo 'Erreur : URL incomplet..';
	}

	public function doubleOptinConfirm($id,$mode='') {
		if(empty($_GET['Id']) && empty($id)) {
			echo 'Erreur : URL incomplet..';
			return;
		}
		if(empty($id))
			$id = $_GET['Id'];
		if(empty($mode)) {
			$aInfo['function'] = 'confirm';
			$aInfo['tracking_id'] = $id;
			return self::talk($aInfo);
		}
		else {
			$function = 'doubleOptinConfirm';
			$arguments = $id;
			self::asynchrone($function, $arguments);
		}
	}
		
	public function clicManager($id='',$mode='') {
		global $url;
		if(empty($mode)) self::clicTracking();
		else self::clicTrackingAsync();
		if(empty($_GET[self::_redirectID]) && empty($id)) 
				die('Erreur : URL de rediction incomplet..');
		if(empty($id))
			$id = $_GET[self::_redirectID];
		if(empty($url[$id]))
			die('identifiant de redirection inconnu');
		else
			header('location:'.$url[$id]);
	}
}

?>
