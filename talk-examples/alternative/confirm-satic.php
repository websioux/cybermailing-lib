<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/cybermailing-api-client/_init.php');

$oCbm = new CyberMailingClient();
$oCbm->doubleOptinConfirm(); 
/*
Vous pouvez placer ce script à la racine de votre site.
Renseignez ensuite (dans l'interface cybermailing ) le champs optionnel lien de confirmation pour : 
http://votresite.com/confirm-static.php

Lors des inscriptions double optin, les personnes qui confirment arriveront sur cette page et les information
de confirmation seront remontées à cybermailing

*/


?>

Félicitation vous venez de confirmer votre insription !
