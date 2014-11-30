<?
// adresse email sur laquelle les contacts reçus seront envoyés

DEFINE('FROM_EMAIL','expediteur@gmail.com'); // indiquez l'adresse email qui envoi le message
DEFINE('LOG_EMAIL','destinataire@gmail.com'); // indiquez l'adresse email qui recevra le message
	
// Parâmêtres d'accès à MYSQL ==========================
// En général ces données sont soit disponibles dans l'interface de votre hébergement
// et dans l'email de bienvenue que vous avez recu lors de la souscription de votre hébergement

DEFINE('MYSQL_host','localhost'); 	// indiquez votre host mysql	   
DEFINE('MYSQL_user','Votre_nom_utilisateur_mysql'); // indiquez votre user mysql
DEFINE('MYSQL_password','Votre_mot_de_passe_mysql'); // indiquez le mot de passe pour le user mysql
DEFINE('MYSQL_database','Nom_de_votre_base_de_donnee');	// indiquez le nom de votre base de donnée

// Noms de la table dans la base de donnée
DEFINE('MYSQL_table','CYBERMAILING_Log');	

// suffix des noms de fichiers dans lesquels les données seront écrites
DEFINE('LOG_FILE','prospects.txt');

// === PREFERENCES DE FONCTIONNEMENT ================
// Indiquez dans quelles circonstances vous souhaitez obtenir une sauvegarde mysql, sur fichier ou un envoi d'email
// ================================================

$Actions['inscription en attente de confirmation']['mysql'] = 1; // mettre à 0 pour désactier à 1 pour activer
$Actions['inscription en attente de confirmation']['file'] = 1;
$Actions['inscription en attente de confirmation']['email'] = 0;

$Actions['inscription valide']['mysql'] = 1;
$Actions['inscription valide']['file'] = 1;
$Actions['inscription valide']['email'] = 1;

$Actions['mise a jour']['mysql'] = 1;
$Actions['mise a jour']['file'] = 1;
$Actions['mise a jour']['email'] = 1;

$Actions['desinscription']['mysql'] = 1;
$Actions['desinscription']['file'] = 1;
$Actions['desinscription']['email'] = 0;
?>
