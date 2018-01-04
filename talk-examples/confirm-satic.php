<?php
$oCbm = new CyberMailingClient();
$oCbm->doubleOptinConfirm(); 
/*
Vous pouvez placer ce script à la racine de votre site.
Renseignez ensuite (dans l'interface cybermailing ) le champs optionnel "lien de confirmation" pour : 
http://votresite.com/confirm-static.php

Les personnes qui confirment arriveront sur cette page et les information
de confirmation seront remontées à cybermailing - ci dessous un exemple de message qui leur sera affiché.

*/


?>

Félicitation vous venez de confirmer votre insription !
