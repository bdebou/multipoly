<?php 
$DB_serveur 			= 'localhost'; 		// Nom du serveur
$DB_utilisateur 		= 'ugame'; 			// Nom de l'utilisateur de la base
$DB_motdepasse 			= 'passgame'; 		// Mot de passe pour accèder à la base
$DB_base 				= 'game'; 			// Nom de la base
$NumVersion				= '2.8.2';				// Numéro de Version

$MAX_essai 				= 3;				// Nombre maximum d'essai de connection
$temp_attente			= 3600*3;			// temp d'attente entre chaque lancé de dés
$wait_construire		= 3600*12;			// temp d'attente entre chaque opération sur terrain
$nb_proprietes_total	= 28;				// nombre de propriété total disponible à la vente
$nb_max_maison			= 20;				// limite du nombre de construction par joueur
$nb_max_ruine			= 3;				// limite du nombvre de ruine par joueur avant gagnant
$caution				= 50;				// montant de la caution pour sortir de prison

$connection = mysql_connect($DB_serveur, $DB_utilisateur, $DB_motdepasse) // On se connecte au serveur
                or die ('MySQL error '.mysql_errno().': '.mysql_error());

            mysql_select_db($DB_base, $connection)  // On se connecte à la BDD
                    or die ('MySQL error '.mysql_errno().': '.mysql_error());
?>

