<?
include('definition.php');
include('./fonctions/fonctions.php');
DEFINE("FONCTION_LOG_FILE",$Actions['inscription valide']['file'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_MYSQL",$Actions['inscription valide']['mysql'] ? 'ON' : 'OFF'); 
DEFINE("FONCTION_LOG_EMAIL",$Actions['inscription valide']['email'] ? 'ON' : 'OFF'); 
if(!empty($_POST)) {
	$_POST['etat'] = "valide";
	$CBM = new CyberMailing_listen();
	if(FONCTION_LOG_EMAIL == "ON")
		$CBM->sendMail('Nouveau contact CyberMailing');
	if(FONCTION_LOG_MYSQL == "ON")
		$CBM->inscription();
	if(FONCTION_LOG_FILE == "ON")
		$CBM->logFile('inscription');
}	
else {
	if(file_exists('./install/install.php'))
		include('./install/install.php');
	echo ('aucune variable transmise');
}
?>
