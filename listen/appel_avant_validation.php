<?
include('definition.php');
include('./fonctions/fonctions.php');
DEFINE("FONCTION_LOG_FILE",$Actions['inscription en attente de confirmation']['file'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_MYSQL",$Actions['inscription en attente de confirmation']['mysql'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_EMAIL",$Actions['inscription en attente de confirmation']['email'] ? 'ON' : 'OFF'); 
if(!empty($_POST)) {
	$_POST['etat'] = "à valider";
	$CBM = new CyberMailing_listen();
	if(FONCTION_LOG_MYSQL == "ON")
		$CBM->inscription();
	if(FONCTION_LOG_FILE == "ON")
		$CBM->logFile('avant_validation');
	if(FONCTION_LOG_EMAIL == "ON")	
		$CBM->sendMail('Nouvelle pré-inscription CyberMailing');
}	
else {
	if(file_exists('./install/install.php'))	include('./install/install.php');
	echo ('aucune variable transmise');
}
?>
