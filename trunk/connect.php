<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Game</title>
	<link rel="stylesheet" href="./CSS/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="login">
<?php
$BtReessayer='<form method="post" action="inscription.php" style="text-align:center;"><input type="submit" name="inscription" value="S\'inscrire" style="width: 160px;" /></form>';
$BtInscription='<form method="post" action="authentification.php" style="text-align:center;"><input type="submit" name="Recommencer" value="Réessayer" style="width: 160px;" /></form>';

session_start();
if(empty($_GET['login']) && empty($_GET['motdepasse'])){
	header('Location: authentification.php');
}else{
	// On va vérifier les variables
	if(!preg_match('/^[[:alnum:]]+$/', $_GET['login']) or
		!preg_match('/^[[:alnum:]]+$/', $_GET['motdepasse'])){
		echo "
<table>
	<tr><td><p>Vous devez entrer uniquement des lettres ou des chiffres.</p></td></tr>
	<tr><td>$BtReessayer</td></tr>
	<tr><td>$BtInscription</td></tr>
</table>";
		//exit();
	}else{
		require('config.php'); // On réclame le fichier
		$login = $_GET['login'];
		$motdepasse = $_GET['motdepasse'];
		$sql = "SELECT * FROM table_utilisateur WHERE login='".mysql_escape_string($login)."'";
		// On vérifie si ce login existe
		$requete_1 = mysql_query($sql) or die ( mysql_error() );
		if(mysql_num_rows($requete_1)==0){
			echo "
<table>
	<tr><td><p>Ce login n'existe pas !</p></td></tr>
	<tr><td>$BtReessayer</td></tr>
	<tr><td>$BtInscription</td></tr>
</table>";
			//exit();
		}else{
			// On vérifie si le login et le mot de passe correspondent au compte utilisateur
			$requete_2 = mysql_query($sql." AND pass='".mysql_escape_string($motdepasse)."'") or die ( mysql_error() );
			if(mysql_num_rows($requete_2)==0){
				// On va récupérer les résultats
				$result = mysql_fetch_array($requete_1, MYSQL_ASSOC);
				// On va récupérer la date de la dernière connexion
				$lastconnection = explode(' ', $result["dates"]);
				$lastjour = explode('-', $lastconnection[0]);
				// On va récupérer le nombre de tentative et l'affecter
				$nbr_essai = $result["nbr_connect"];
				if($lastjour[2]==date("d") && $MAX_essai==$nbr_essai){
					echo '<p>Vous avez atteint le quota de tentative, essayez demain !</p>';
					//exit();
				}else{
					$nbr_essai++;
					$update = "UPDATE table_utilisateur SET nbr_connect='".$nbr_essai."', dates=NOW() WHERE id='".$result["id"]."'";
					mysql_query($update) or die ( mysql_error() );
					echo "
<table>
	<tr><td><p>Le mot de passe et/ou le login sont incorrectes.</p></td></tr>
	<tr><td>$BtReessayer</td></tr>
	<tr><td>$BtInscription</td></tr>
</table>";
					//exit();
				}
			}else{
				// On va récupérer les résultats
				$result = mysql_fetch_array($requete_2, MYSQL_ASSOC);
				$nbr_essai = 0;
				$update = "UPDATE table_utilisateur SET nbr_connect='".$nbr_essai."', dates=NOW() WHERE id='".$result["id"]."'";
				mysql_query($update) or die ( mysql_error() );
				//$_SESSION['data_connect'] = serialize(array($login, $motdepasse));
				$_SESSION = array_merge($_SESSION,$result);
				// On redirige vers la partie membre
				header('Location: ./index.php');
				//echo '<script language="javascript">window.location="index.php"</script>';
			}
		}
	}
}
?>
</div>
<div class="main">
	<p>Ceci est mon petit jeux créé totalement en PHP et MySQL. Il est tiré du monopoly. Il se joue comme le monopoly.</p>
	<p>J'essaie de l'améliorer le plus possible et le plus souvent.</p>
	<p>Si vous recontrez un bug, n'hésistez pas à m'en parler.</p>
	<p>Pour jouer, il vous suffit de vous inscrire, biensur, c'est totalement gratuit. Bon jeu!</p>
	<p>Si vous voulez lire les règles et déroulement du jeu, vous pouvez lire le document "<a href="regles.php">Règles</a>". Bonne lecture</p>
</div>
</body>
</html>