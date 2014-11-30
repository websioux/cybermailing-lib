<?
include('definition.php');
include('./fonctions/fonctions.php');
DEFINE("FONCTION_LOG_FILE",$Actions['desinscription']['file'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_MYSQL",$Actions['desinscription']['mysql'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_EMAIL",$Actions['desinscription']['email'] ? 'ON' : 'OFF'); 
if(!empty($_POST)) {
	$_POST['etat'] = "annulé";
	$CBM = new CyberMailing_listen();
	if(FONCTION_LOG_MYSQL == "ON")
		$CBM->desinscription();
	if(FONCTION_LOG_FILE == "ON")
		$CBM->logFile('annulation');
	if(FONCTION_LOG_EMAIL == "ON")	
		$CBM->sendMail('Annulation inscription CyberMailing');
}	
else {
	if(file_exists('./install/install.php'))	include('./install/install.php');
	echo ('aucune variable transmise');
}
?>
