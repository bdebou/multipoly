<?php 
$DB_serveur = 'localhost'; // Nom du serveur
$DB_utilisateur = 'ugame'; // Nom de l'utilisateur de la base
$DB_motdepasse = 'passgame'; // Mot de passe pour accèder à la base
$DB_base = 'game'; // Nom de la base
$MAX_essai = 3;


$connection = mysql_connect($DB_serveur, $DB_utilisateur, $DB_motdepasse) // On se connecte au serveur
                or die (mysql_error().' sur la ligne '.__LINE__);

            mysql_select_db($DB_base, $connection)  // On se connecte à la BDD
                    or die (mysql_error().' sur la ligne '.__LINE__);
?>

