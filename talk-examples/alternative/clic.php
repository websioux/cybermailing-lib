<?php 
require_once($_SERVER['DOCUMENT_ROOT'].'/cybermailing-api-client/_init.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/cybermailing-api-client/classes/redirectLibrary.php');

cyberMailing_connect::clicManager();

/*

Vous pouvez placer ce script à la racine de votre site.
Il ne vous reste qu'à modifier les url de redirection, le tracking des clics est effectué

exemple de lien qui traque le clic et redirige sur votresite.com/mapage.php

http://votresite.com/clic.php?clc=%trackID%&m=mp

*/
?>
