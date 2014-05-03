<?php
include(dirname(__FILE__) .'/listen/feedback.php');

// ======= POUR ENVOYER UN EMAIL A UN DESTINATAIRE A CHAQUE INSCRIPTION / DESINSCRIPTION

DEFINE('DESTINATION_EMAIL','destinataire@gmail.com'); // adresse email sur laquelle les informations seront envoyés

DEFINE('FROM_EMAIL','expediteur@gmail.com'); // adresse email depuis laquelle les contacts reçus seront envoyés

// ======= POUR REALISER UNE SAUVEGARDE DES ABONNES DANS VOTRE PROPRE BASE DE DONNEE

DEFINE('MYSQL_host','localhost'); 	// indiquez votre host mysql
DEFINE('MYSQL_user','Votre_nom_utilisateur_mysql'); // indiquez votre user mysql
DEFINE('MYSQL_password','Votre_mot_de_passe_mysql'); // indiquez le mot de passe pour le user mysql
DEFINE('MYSQL_database','Nom_de_votre_base_de_donnee');	// indiquez le nom de votre base de donnée
DEFINE('MYSQL_table','CYBERMAILING_Log');	

// ======= POUR REALISER UNE SAUVEGARDE DES ABONNES DANS DES FICHIERS TEXTES
DEFINE('LOG_FILE_SUFFIX','log_cybermailing.txt');
/* pour les inscriptions sur la liste 12345, les fichiers seront par exemple:
		/cybermailing-api-client/listen/log/1234_inscription_log_cybermailing.txt
*/
?>
