<?php
	if(FONCTION_LOG_FILE == "ON")
		{
			// Tente l'ecriture d'un fichier
			if ($ficNum = fopen("log_test.txt",'a'))
					{
					fwrite($ficNum,"test �criture fichier ok\r\n");
					fclose($ficNum);
					$verif = "<p>SUCCES tentative d'�criture dans un fichier : <br>v�rifiez qu'un fichier 'log_test.txt' a �t� cr�� et qu'il contient la phrase 'test �criture fichier ok'</p>";
					}
			else
				{
					$verif = "<p>ERREUR : tentative d'�criture dans un fichier a �chou�<br>Changez les droits de lecture / ecriture de votre repertoir pour 0777</p>";
				
				}
		
			if(LOG_FILE == "") $verif .= "<p> ERREUR : Votre d�finition de nom de fichier est vide, l'�criture dand un nom de fichier vide est impossible, si vous souhaitez d�sactiver les logs par fichier vous devez d�finir 'FONCTION_LOG_FILE' � 'OFF'</p>";
		}

	if(FONCTION_LOG_EMAIL == "ON")	
		{

			// Tente l'envoi d'un email
			if(bad_email(LOG_EMAIL))
				{
				$verif .= "<p>ERREUR : La syntaxe de l'adresse email : '".LOG_EMAIL."' du fichier de d�finition semble incorrecte'</p>";
				
				}
			else
				{
				mail(LOG_EMAIL,"tentative log email","succ�s de la tentative");

				$verif .= "<p>NOTE : Un email de test a �t� envoy� � l'adresse : '".LOG_EMAIL."' son sujet est 'tentative log email'</p>";

				}
		}	

	if(FONCTION_LOG_MYSQL == "ON")
		{
			// Tente l'installation de la base de donn�e

			// 1. tente la connection � la base de donn�e
			
			$link = @mysqli_connect(MYSQL_host,MYSQL_user,MYSQL_password);
			 
			if(!$link) 
				{
					$verif .= "<p>ERREUR : La connection �  Mysql a �chou�e : l'une vos d�finitions MYSQL_host, MYSQL_user et MYSQL_password est incorrecte</p>";
					die($verif);
				}
			else
				{
					$verif .= "<p>SUCCES : Connection �  Mysql OK</p>";

				}
				
			 if(!mysqli_select_db(MYSQL_database)) 
				{
				$verif .= "<p>ERREUR : La base de donn�e ".MYSQL_database." n'existe pas !</p>";
				die($verif);
				}
			else
				{
					$verif .= "<p>SUCCES : Base de donn�e ".MYSQL_database."trouv�e</p>";
				}
			
			// 2. v�rifie si la table CYBERMAILING_log est install�e

			$q = 'SHOW TABLES FROM '.MYSQL_database.' LIKE \''.MYSQL_table.'\'';
			$res = mysqli_query($link,$q);
			if(mysqli_num_rows($res) == 0) //  si la table est absente, installe la table

				{
					$q = "CREATE TABLE  `".MYSQL_database."`.`".MYSQL_table."` (
						`ID` INT NOT NULL auto_increment,
						`cyber_id` INT NOT NULL ,
						`name` VARCHAR( 100 ) NOT NULL ,
						`email` VARCHAR( 100 ) NOT NULL ,
						`list` INT NOT NULL ,
						`ip` VARCHAR( 20 ) NOT NULL ,
						`date` VARCHAR( 12 ) NOT NULL ,
						`url` VARCHAR( 255 ) NOT NULL ,
						`etat` VARCHAR( 10 ) NOT NULL ,
						PRIMARY KEY (  `ID` ) ,
						INDEX (  `cyber_id` ,  `list` )
						) ENGINE = MYISAM";
					mysqli_query($link,$q);
					$q = 'SHOW TABLES FROM '.MYSQL_database.' LIKE \''.MYSQL_table.'\'';
					$res = mysqli_query($link,$q);
					if(mysqli_num_rows($res) == 0) //  si l'installation a �chou�e
						{
							$verif .= "<p>ERREUR : la table ".MYSQL_table." n'a pas pu �tre install�e : votre nom d'utilisateur n'a probablement pas les droits CREATE_TABLE sur la base de donne ".MYSQL_database." ou vous n'avez pas recharg� les privil�ges apr�s avoir attribu� les droits de votre utilisateur</p>";
							die($verif);
						}
					else
						{
							$verif .= "<p>SUCCES : La table ".MYSQL_table." a �t� correctement cr�e <br> INSTALLATION TERMINEE<br>Pour des raisons de s�curit�, changez le nom du r�pertoir 'install' ou effacez le</p>";
						}
				}
			else
				{
					$verif .= "<p>SUCCES : La table ".MYSQL_table." est pr�sente <br> INSTALLATION TERMINEE<br>Pour des raisons de s�curit�, changez le nom du r�pertoir 'install' ou effacez le</p>";
				}

			$q = 'SHOW TABLES FROM '.MYSQL_database.' LIKE \'CYBERMAILING_membres\'';
			$res = mysqli_query($link,$q);
			if(mysqli_num_rows($res) == 0) //  si la table est absente, installe la table

				{
					$q = "CREATE TABLE  `".MYSQL_database."`.`CYBERMAILING_membres` (
						`ID` INT NOT NULL auto_increment,
						`email` VARCHAR( 100 ) NOT NULL ,
						PRIMARY KEY (  `ID` )
						) ENGINE = MYISAM";
					mysqli_query($link,$q);
					$q = 'SHOW TABLES FROM '.MYSQL_database.' LIKE \'CYBERMAILING_membres\'';
					$res = mysqli_query($link,$q);
					if(mysqli_num_rows($res) == 0) //  si l'installation a �chou�e
						{
							$verif .= "<p>ERREUR : la table CYBERMAILING_membres n'a pas pu �tre install�e : votre nom d'utilisateur n'a probablement pas les droits CREATE_TABLE sur la base de donne ".MYSQL_database." ou vous n'avez pas recharg� les privil�ges apr�s avoir attribu� les droits de votre utilisateur</p>";
							die($verif);
						}
					else
						{
							$verif .= "<p>SUCCES : La table CYBERMAILING_membres a �t� correctement cr�e <br> INSTALLATION TERMINEE<br>Pour des raisons de s�curit�, changez le nom du r�pertoir 'install' ou effacez le</p>";
						}
				}
			else
				{
					$verif .= "<p>SUCCES : La table CYBERMAILING_membres est pr�sente <br> INSTALLATION TERMINEE<br>Pour des raisons de s�curit�, changez le nom du r�pertoir 'install' ou effacez le</p>";
				}				

		}
	
	die($verif);
?>
