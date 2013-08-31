<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// On prolonge la session
session_start();
// On teste si la variable de session existe et contient une valeur
if(empty($_SESSION['login']) or $_SESSION['security']!='admin') {
    // Si inexistante ou nulle, on redirige vers le formulaire de login
    header('Location: admin.php');
    exit();
}elseif($_SESSION['security']=='admin'){
	include('config.php');
	include('functions.php');
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>Admin - Game</title>
	<link rel="stylesheet" href="./styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="loginstatus">
	<table>
		<tr><td width="60%">Nom :</td><td><?php echo $_SESSION['name'];?></td></tr>
		<tr><td>Prénom :</td><td><?php echo $_SESSION['firstname'];?></td></tr>
		<tr><td colspan="2" style="text-align: center;">
		<p><form method="get" action="unconnect.php"><input type="submit" name="submit" value="Se déconnecter" /></form></p>
		</td></tr>
	</table>
</div>
<div class="main">
<div class="menu"><?php affiche_menu();?></div>
<div class="result">

<p>UPDATE `game`.`table_carte_propriete` SET `proprietaire` = '' WHERE `table_carte_propriete`.`id` =1 LIMIT 1 ;</p>
</div>
</div>
</body>
</html>