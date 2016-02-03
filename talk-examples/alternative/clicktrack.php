<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/cybermailing-api-client/_init.php');
$oCbm = new CyberMailingClient();
$oCbm->clicTracking(); 

/*

Conseil : Vous devez placer ce script à la racine de votre domaine
et devriez l'appeller depuis toutes les pages de votre site succeptibles d'être visualisée après un clic

*/

?>
