<?php
if(!isset($_POST['callback_type']))
	die('miss callback_type');
if(file_exists('./definition-me.php'))	
	require_once('./definition-me.php');
else
	require_once('./definition-me.php');
require_once('./fonctions/fonctions.php');
$aTranslate = array(
'subscribe_accept' => 'inscription valide',
'subscribe_update' => 'mise a jour',
'subscribe_pending' => 'inscription en attente de confirmation',
'subscribe_cancel' => 'desinscription',
);
DEFINE("FONCTION_LOG_FILE",$Actions[$aTranslate[$_POST['callback_type']]]['file'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_MYSQL",$Actions[$aTranslate[$_POST['callback_type']]]['mysql'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_EMAIL",$Actions[$aTranslate[$_POST['callback_type']]]['email'] ? 'ON' : 'OFF'); 
if(!empty($_POST)) {
	switch($_POST['callback_type']) {
		case 'subscribe_accept':
		case 'subscribe_update':
				$_POST['etat'] = "valide";
				$CBM = new CyberMailing_listen();
				if(FONCTION_LOG_EMAIL == "ON")
					$CBM->sendMail('Nouveau contact CyberMailing');
				if(FONCTION_LOG_MYSQL == "ON")
					$CBM->inscription();
				if(FONCTION_LOG_FILE == "ON")
					$CBM->logFile('inscription');
			break;
		case 'subscribe_pending':
				$_POST['etat'] = "à valider";
				$CBM = new CyberMailing_listen();
				if(FONCTION_LOG_EMAIL == "ON")
					$CBM->sendMail('Nouveau contact CyberMailing');
				if(FONCTION_LOG_MYSQL == "ON")
					$CBM->inscription();
				if(FONCTION_LOG_FILE == "ON")
					$CBM->logFile('avant_validation');
			break;
		case 'subscribe_cancel':
				$_POST['etat'] = "annulé";
				$CBM = new CyberMailing_listen();
				if(FONCTION_LOG_EMAIL == "ON")
					$CBM->sendMail('Annulation CyberMailing');
				if(FONCTION_LOG_MYSQL == "ON")
					$CBM->deinscription();
				if(FONCTION_LOG_FILE == "ON")
					$CBM->logFile('annulation');
			break;	
	}	
}
else {
	if(file_exists('./install/install.php'))
		include('./install/install.php');
	echo ('aucune variable transmise');
}
