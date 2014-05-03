<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/cybermailing-api-client/_init.php');

cyberMailing_connect::doubleOptinConfirmRedirect(); 
/*
Vous pouvez placer ce script à la racine de votre site.
Renseignez ensuite (dans l'interface cybermailing ) le champs optionnel lien de confirmation pour : 
http://votresite.com/confirm.php
*/


?>
