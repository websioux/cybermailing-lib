<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/cybermailing-api-client/_init.php'); 
$oCbm = new CyberMailingClient();
$oCbm->automaticUnsubscribe(); 
/*

Vous pouvez placer ce script à la racine de votre site.
Renseignez ensuite (dans l'interface cybermailing ) le champs optionnel lien d'annulation pour : 
http://votresite.com/stop.php

*/


?>
