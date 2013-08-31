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
	<title>Users - Admin - Game</title>
	<link rel="stylesheet" href="./styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="loginstatus">
	<table>
		<tr><td width="60%">Nom :</td><td><?php echo $_SESSION['name'];?></td></tr>
		<tr><td>Prénom :</td><td><?php echo $_SESSION['firstname'];?></td></tr>
		<tr><td colspan="2" style="text-align: center;"><p><form method="get" action="unconnect.php"><input type="submit" name="submit" value="Se déconnecter" /></form></p></td></tr>
	</table>
</div>
<div class="main">
	<div class="menu"><?php affiche_menu();?></div>
	<div class="result">
		<h1>Gestion utilisateurs</h1>
		<p>Cela permet de supprimer ou mettre à zéro un utilisateur.</p>
		<form method="get" action="users.php">
			<select name="user">
		<?php 
			$sql="SELECT login FROM table_utilisateur;";
			$requete = mysql_query($sql) or die (mysql_error());
			while ($row = mysql_fetch_assoc($requete)) {
				echo '<option value="'.$row['login'].'"';
				if(isset($_GET['user']) and $_GET['user']==$row['login']){echo ' selected="selected"';}
				echo '>'.$row['login'].'</option>';
			}
		?>
			</select>
			<input type="submit" value="Select" />
		</form>
		<?php
		if(isset($_GET['user']) and !isset($_GET['action'])){
			echo detail_user($_GET['user']);
		}elseif(isset($_GET['user']) and isset($_GET['action'])){
			switch($_GET['action']){
				case 'remove': echo form_to_remove_user($_GET['user']); break;
				case 'reset': echo form_to_reset_user($_GET['user']); break;
			}
		}
		?>
		
	</div>
</div>
</body>
</html>
<?php
function detail_user($user){
	$txt_result=null;
	$sql="SELECT * FROM table_utilisateur WHERE login='$user';";
	$requete = mysql_query($sql) or die (mysql_error());
	$row = mysql_fetch_assoc($requete);
	foreach($row as $key=>$item){
		if(	$key=='position' or 
			$key=='date_last_action' or 
			$key=='nb_maison' or 
			$key=='nb_propriete'){$txt_result .= "$key = $item <br />";}
	}
	$txt_result .= '<form method="get" action="users.php">';
	$txt_result .= '<input type="text" name="user" value="'.$user.'" style="visibility:hidden;" />';
	$txt_result .= '<table><tr><td><input type="radio" name="action" value="reset">Reset user</input></td>';
	$txt_result .= '<td rowspan="2"><input type="submit" value="GO" /></td></tr>';
	$txt_result .= '<tr><td><input type="radio" name="action" value="remove">Remove user</input></td></td>';
	$txt_result .= '</form>';
	return $txt_result;
}
function form_to_remove_user($user){
	$txt_result=null;
	if(!isset($_POST['confirm'])){
		$txt_result .= '<form method="post"><input type="checkbox" name="confirm" value="yes">Oui, je confirm la suppression de '.$user.'</input>';
		$txt_result .= '<input type="submit" value="GO" /></form>';
	}elseif(isset($_POST['confirm']) and $_POST['confirm']){
		unset($_POST);
		free_properties($user);
		$connection = mysql_connect('localhost', 'dgame', 'PetiteLaura1208') // On se connecte au serveur
		or die (mysql_error().' sur la ligne '.__LINE__);
		mysql_select_db('game', $connection)  // On se connecte à la BDD
		or die (mysql_error().' sur la ligne '.__LINE__);
		$sql = "DELETE FROM table_utilisateur WHERE login='$user'";
		mysql_query($sql) or die ( mysql_error() );
		include('config.php');
		$txt_result .= "$user a été supprimé.";
	}
	unset($_GET);
	return $txt_result;
}
function form_to_reset_user($user){
	$txt_result=null;
	$sql="SELECT date_last_action FROM table_utilisateur WHERE login='$user';";
	$requete = mysql_query($sql) or die (mysql_error());
	$row = mysql_fetch_assoc($requete);
	$ar_innactivity=calcul_innactivity($row['date_last_action'],date('c'));
	if($ar_innactivity['days']>0){
		reset_user($user);
		$txt_result .= "$user a été mis à zéro.";
		add_history($user,0,'reset','','Admin : Reset de joueur car bloque le jeu.');
	}else{$txt_result .= "$user déjà remis à zéro ou vient de jouer";}
	unset($_GET);
	return $txt_result;
}
?>