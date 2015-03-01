<?
class CyberMailing_listen{
	private $_aInput = array();
	private $_sFileLine = '';
	private $_sMessLine = '';

	function __construct(){
		if(FONCTION_LOG_MYSQL == "ON") {
			$link = mysql_connect(MYSQL_host,MYSQL_user,MYSQL_password);
			mysql_select_db(MYSQL_database);
		}
		foreach($_POST as $sKey=>$sValue) {
			$sKey = trim($sKey,'_');
			$this->_aInput[$sKey] = trim($sValue);
			$sKey = strtolower(trim($sKey));
			if($sKey=='id')
				$sKey = 'cyber_id';
			if($sKey=='liste')
				$sKey = 'list';
			$this->_aLowerInput[$sKey] = trim($sValue);
			}
		$this->_aInput = self::htmlentities_array($this->_aInput);	
		$this->_aLowerInput = self::htmlentities_array($this->_aLowerInput);	
	}

	function logFile($sType) {
		if(!file_exists($this->_aLowerInput['list'].'_'.$sType.'_'.LOG_FILE)){
			foreach($this->_aInput as $sK=>$sV) {
				$this->_sFileHead .= '"'.$sK.'";';
			}
			if ($ficNum = fopen($this->_aLowerInput['list'].'_'.$sType.'_'.LOG_FILE,'a')){
				fwrite($ficNum,$this->_sFileHead."\r\n");
				fclose($ficNum);
			}
		}
		foreach($this->_aInput as $sK=>$sV) {
			$this->_sFileLine .= '"'.$sV.'";';
		}
		if ($ficNum = fopen($this->_aLowerInput['list'].'_'.$sType.'_'.LOG_FILE,'a')){
			fwrite($ficNum,$this->_sFileLine."\r\n");
			fclose($ficNum);
		}
	}

	function myurldecode($sString){
		$sString = urldecode($sString);
		if(strpos($sString,'@')!==false) // c'est un email
			$sString = str_replace(' ','+',$sString);
		$sString = str_replace(array('&agrave; valider','valid&eacute;'),array('à valider','validé'),$sString);
		return $sString;
	}	
	
	function sendMail($sSubject) {
		foreach($this->_aLowerInput as $sK=>$sV) {
			$this->_sMessLine .= ucfirst(self::myurldecode($sK)).' : '.self::myurldecode($sV)."\r\n";
		}
		$sHeaders = 'From: '.FROM_EMAIL . "\r\n" . 'Reply-To: '.FROM_EMAIL;			
		$sSubject .= 'Liste '.$_POST['list'];
		mail(LOG_EMAIL,$sSubject,$this->_sMessLine,$sHeaders);
	}

	function bad_email($email) {
	  $result = 0;
	  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
		$result = 1;
	  }
	  return $result;
	}

	function securite_bdd($string)
		{
			// On regarde si le type de string n'est pas fait de chiffres nombre entier (int)
			if(FONCTION_LOG_MYSQL == "ON" && !ctype_digit($string))
			{
				$string = mysql_real_escape_string($string);
				$string = addcslashes($string, '%');
			}
	
			return $string;
		}

	function htmlentities_array($array,$options=ENT_QUOTES) {

		foreach($array as $key => $val) {
			if (!is_array($array[$key])) {
				$array[$key] = htmlentities(self::securite_bdd($val),$options);
			}
			else
			{
				$array[$key] = htmlentities_array($array[$key],$options);
			}
		}

		return $array;
	}
	function inscription() {
		$q = 'SELECT * FROM `'.MYSQL_table.'` WHERE cyber_id = '.$this->_aLowerInput['cyber_id'];
		$res = mysql_query($q);
		if(mysql_num_rows($res) == 0 ) { // new abo
			$q = 'INSERT INTO `'.MYSQL_table.'`
					(cyber_id,
					name,
					email,
					list,
					ip,
					date,
					url,
					etat)
					VALUES(
					'.$this->_aLowerInput['cyber_id'].',
					"'.$this->_aLowerInput['name'].'",
					"'.$this->_aLowerInput['email'].'",
					'.$this->_aLowerInput['list'].',
					"'.$this->_aLowerInput['ip'].'",
					"'.$this->_aLowerInput['date'].'",
					"'.$this->_aLowerInput['url'].'",
					"'.$this->_aLowerInput['etat'].'"
					)';
			mysql_query($q);
		}
		$q = 'SHOW COLUMNS FROM  `'.MYSQL_table.'`';
		$res = 	mysql_query($q);
		while($row = mysql_fetch_array($res)) {
			$aFields[] = $row['Field'];
		}
		foreach($this->_aLowerInput as $key=>$value){
			$key = str_replace(' ','',$key);
			if($key != 'callback_type' && $key != 'id' && $key != 'cyber_id'
				&& $key != 'email' && $key != 'list' && $key != 'ip'
				&& $key != 'date' && $key != 'url' && $key != 'etat'
				&& $key != 'liste')
			if(!in_array($key,$aFields) && !empty($key)) {
					$q = 'ALTER TABLE `'.MYSQL_table .'` ADD  `'.$key.'` VARCHAR( 255 ) NOT NULL';
					// echo "\n$q";
					mysql_query($q);
			}
			$q = 'UPDATE `'.MYSQL_table .'` SET `'.$key.'`="'.$value.'" WHERE cyber_id = '.$this->_aLowerInput['cyber_id'];
			mysql_query($q);
		}
		$q = 'SELECT ID FROM CYBERMAILING_membres WHERE email = "'.$this->_aLowerInput['email'].'"';
		$res = mysql_query($q);
		if(mysql_num_rows($res) == 0 ) {
			$q = 'INSERT INTO CYBERMAILING_membres (email) VALUES("'.$this->_aLowerInput['email'].'")';
			mysql_query($q);
			}
		return;
	}
		
	function desinscription() {
		mysql_query('UPDATE '.MYSQL_table.' SET etat = "annule" WHERE cyber_id = '.$this->_aLowerInput['cyber_id']);
	return ;		
	}
}
?>	
