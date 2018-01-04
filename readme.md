Description technique de l' [API](https://www.cybermailing.com/api.php) 

La librairie client de l'API CyberMailing permet de faire communiquer votre serveur avec CyberMailing. Nous avons séparé la fonction *talk*, ui concerne des informations que votre serveur envoi à cybermailing et la fonction *listen* qui consiste à écouter les informations transmises par Cybermailing.

*talk* permet de :

- l’inscription des nouveaux abonnés et la mise à jour ulterieure des informations les concernants
- l'utilisation d'un lien de désinscription à votre nom

*listen* permet de

- réliquer dans votre base de données toutes les informations que cybermailing reçoit.
 

# Identification avec la cyberKey (*talk*)

Cette clef se situe dans votre profil cybermailing, gardez là secrête.

```php
<?php
$oCbm = new CybermailingClient('sdfs987sd9g87s9d87s9f879sd8fglhjkh46f54gh');
?>
```

ou de définir une constante CYBER_KEY avec cette clef

```php
<?php
define('CYBER_KEY','sdfs987sd9g87s9d87s9f879sd8fglhjkh46f54gh');
$oCbm = new CybermailingClient();
?>
```

Les 2 méthodes ci-dessus sont équivalentes.

# Inscription d'un nouvel abonné (*talk*)

```php
<?php
require_once('talk/cybermailing_client');
$oCbm = new CybermailingClient('sdfs987sd9g87s9d87s9f879sd8fglhjkh46f54gh');
$aContact = array(
	'Liste' => '123456',
	'Email' => 'truc@gmail.com',
	'customfield'=>'9999',
	'function' => 'subscribe'
);
$oCbm->talk($aContact);
```

# mise à jour d'un abonné connu (*talk*)

Si vous connaissez la liste et son adresse email vous pouvez utiliser la fonction ci-dessous, elle produira une mise à jour des nouvelles données et ne modifiera pas celles qui ne sont pas transmises.

Où, vous pouvez récupérer la valeur de la variable d'url *clt* (lorsqu'un abonné retourne sur votre site après avoir cliqué sur un lien de vos messages) 
et la transmettre en tant que *tracking_id*, sa valeur remplacera , à la fois, Liste et Email

```php
<?php
require_once('talk/cybermailing_client');
$oCbm = new CybermailingClient('sdfs987sd9g87s9d87s9f879sd8fglhjkh46f54gh');
$aContact = array(
	'tracking_id' => $_GET['clt'], 
	'customfield'=>'77777',
	'function' => 'subscribe'
);
$oCbm->talk($aContact);
```

# Désinscription sur votre site (*talk*)

Dans une page PHP : 
```php
<?php
require_once('talk/cybermailing_client');
$oCbm = new CyberMailingClient('sdfs987sd9g87s9d87s9f879sd8fglhjkh46f54gh');
$oCbm->automaticUnsubscribe(); 
?>
```

La page est fonctionnelle, reste à l'embellire si vous le désirez.

## Pour mettre à jour votre base de donnée utilisez (*listen*)
Pour cela consultez [ce guide](listen/Lisez_moi.htm)


