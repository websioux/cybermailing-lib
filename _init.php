<?php 
require_once('config_PRO_talk.php');
require_once(dirname(__FILE__).'/talk/connect.php');
cyberMailing_connect::clicTracking();
list($sFname, $sExt) = explode('.',$_SERVER['SCRIPT_NAME']);
if($sExt=='pdf') {
	$file = $_SERVER['SCRIPT_FILENAME'];
	$filename = $sFname .'.pdf'; 
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file));
header('Accept-Ranges: bytes');
@readfile($file);
}

/*

Conseil : Vous devriez l'appeller depuis toutes les pages de votre site succeptibles d'être visualisée après un clic

*/

?>
