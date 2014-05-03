<?
class CFeedBack {

	function bad_email($email) {
	  $result = 0;
	  if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
		$result = 1;
	  }
	  return $result;
	}

	function securite_bdd($string)	{
		$link = mysql_connect(MYSQL_host,MYSQL_user,MYSQL_password);
		// On regarde si le type de string est un nombre entier (int)
		if(ctype_digit($string))
		{
			$string = intval($string);
		}
		// Pour tous les autres types
		else
		{
			$string = mysql_real_escape_string($string);
			$string = addcslashes($string, '%');
		}
		
		return $string;
	}

	function htmlentities_array($array,$options=ENT_QUOTES) {

		foreach($array as $key => $val) {
			if (!is_array($array[$key])) {
				$array[$key] = htmlentities(securite_bdd($val),$options);
			}
			else
			{
				$array[$key] = htmlentities_array($array[$key],$options);
			}
		}
		return $array;
	}

	function mysqlInscription() {
		$_POST = htmlentities_array($_POST);

		$link = mysql_connect(MYSQL_host,MYSQL_user,MYSQL_password);
		if(!$link) return;
		if(!mysql_select_db(MYSQL_database)) return;

		$q = "SELECT * FROM ".MYSQL_table." WHERE cyber_id = ".$_POST['id']." AND list = ".$_POST['list'];

		$res = mysql_query($q);
		
		if(mysql_num_rows($res) > 0 ) $cas = "update";
		
		if($cas != "update")
			{
			
			$q = "INSERT INTO ".MYSQL_table." (cyber_id, name, email, list, ip, date, url, etat) VALUES(".$_POST['id'].",'".$_POST['name']."','".$_POST['email']."',".$_POST['list'].",'".$_POST['ip']."','".$_POST['date']."','".$_POST['url']."','".$_POST['etat']."')";

			mysql_query($q);
			
				foreach($_POST as $key=>$value)
					{
						if($key != 'cyber_id' && $key != 'name' && $key != 'email' && $key != 'list' && $key != 'ip' && $key != 'date' && $key != 'url' && $key != 'etat')
							{
							mysql_query("UPDATE ".MYSQL_table." SET $key = '$value' WHERE cyber_id = ".$_POST['id']." AND list = ".$_POST['list']);
							}
					}
			
			}
		else
			{

			foreach($_POST as $key=>$value)
				{
				if($key != 'cyber_id' && $key != 'email' && $key != 'list' && $key != 'ip' && $key != 'date' && $key != 'url')
					{
					mysql_query("UPDATE ".MYSQL_table." SET $key = '$value' WHERE cyber_id = ".$_POST['id']." AND list = ".$_POST['list']);
					}
				}
			}

		$q = "SELECT ID FROM CYBERMAILING_membres WHERE email = '".$_POST['email']."'";
		$res = mysql_query($q);
		if(mysql_num_rows($res) == 0 ) 
			{
			$q = "INSERT INTO CYBERMAILING_membres (email) VALUES('".$_POST['email']."')";
			mysql_query($q);
			}
			
		return ;
	}
		
	function mysqlDesinscription() {
		$_POST = htmlentities_array($_POST);
		
		$link = mysql_connect(MYSQL_host,MYSQL_user,MYSQL_password);
		if(!$link) return;
		if(!mysql_select_db(MYSQL_database)) return;

		$q = "SELECT * FROM ".MYSQL_table." WHERE cyber_id = ".$_POST['id']." AND list = ".$_POST['list'];
		$res = mysql_query($q);
		
		if(mysql_num_rows($res) > 0 ) 
		
			{
			mysql_query("UPDATE ".MYSQL_table." SET etat = 'annule' WHERE cyber_id = ".$_POST['id']." AND list = ".$_POST['list']);
			}
	return;		
	}

	function save($type) {

		$aTypeLabels['status'] = array(	'A'=>'à valider',
										'V'=>'validé',
										'C'=>'annulé'
								);
		$aTypeLabels['logstatus'] = array(	'A'=>'_avant_validation_',
											'V'=>'_inscription_',
											'C'=>'_annulation_'
									);
		$aTypeLabels['emailsubject'] = array(	'A'=>'Nouvelle pré-inscription CyberMailing Liste ',
												'V'=>'Nouvelle Inscription sur CyberMailing Liste ',
												'C'=>'Annulation inscription CyberMailing Liste '
										);

		if(!empty($_POST)) {
			$_POST['etat'] = $aTypeLabels['etat'][$type];
			foreach($_POST as $key=>$value)
				{
				$key = trim($key,'_');
				$messLgn .= "$key : $value"."\r\n";
				$ficLgn .= '"'.$value.'";';
				
				}

			if(FONCTION_LOG_MYSQL == "ON") {
				if($type == 'C')
					self::MysqlDesinscription();
				else
					self::MysqlInscription();
			}

			if(FONCTION_LOG_FILE == "ON")
				{
					if ($ficNum = fopen('log/'.$_POST['list'].$aTypeLabels['logstatus'][$type].LOG_FILE_SUFFIX,'a'))
						{
						fwrite($ficNum,$ficLgn."\r\n");
						fclose($ficNum);
						}
				}	

			if(FONCTION_LOG_EMAIL == "ON")	
				{
				$headers = 'From: '.FROM_EMAIL . "\r\n" .
		'Reply-To: '.FROM_EMAIL;			
				$sujet = $aTypeLabels['emailsubject'][$type] .$_POST['list'];
				mail(DESTINATION_EMAIL,$sujet,$messLgn,$headers);
				}

			}	


	}

}	
?>	
