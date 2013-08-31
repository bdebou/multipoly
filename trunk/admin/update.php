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
	<title>Update - Admin - Game</title>
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
<h1>Update table</h1>
<p>C'est pour lancer le script qui va calculer le nombre de jour d'innactivité des utilisateurs et leur diminuer la valeur de portefeuille.</p>
<?php
	if(!isset($_POST['launch']) and !isset($_POST['reset'])){
		echo '<form method="post"><table style="border-collapse:collapse; width:250px;"><tr><td colspan="2">Voulez vous lancer le script?</td></tr>';
		echo '<tr><td style="text-align:center;"><input type="submit" name="launch" value="Oui" /></td><td style="text-align:center;"><input type="submit" name="dontlaunch" value="Non" /></td></tr></table></form>';
	}elseif(isset($_POST['launch']) or isset($_POST['reset'])){launch_script_update();}
?>
</div>
</div>
</body>
</html>
<?php
function launch_script_update(){
	$sql="SELECT * FROM table_utilisateur";
	$requete = mysql_query($sql) or die (mysql_error());
	echo '<table><tr><th>Login</th><th>Last action date</th><th>Nb jour</th><th>Montant</th><th>Ruine?</th></tr>';
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<tr>';
		echo '<td>'.$row['login'].'</td>';
		echo '<td>'.$row['date_last_action'].'</td>';
		$ar_innactivity=calcul_innactivity($row['date_last_action'],date('c'));
		echo '<td>'.$ar_innactivity['days'].'</td>';
		if($ar_innactivity['days']>0){
			$montant=0;
			//for($i=1;$i<=$ar_innactivity['days'];$i++){$montant+=ceil(($row['argent']-$montant)*0.10);}
			$montant=$ar_innactivity['days']*10;
			$row['argent']-=$montant;
			$sql="UPDATE table_plateau SET prix=prix+".$montant." WHERE num_case='21'";
			mysql_query($sql) or die ( mysql_error() );
			if($row['argent']<0){$row['ruine']=true;}
			$row['date_last_action']=date('Y-m-d H:i:s',strtotime($row['date_last_action'])+(24*3600*$ar_innactivity['days']));
			$sql="UPDATE table_utilisateur SET argent='".$row['argent']."', ruine=".$row['ruine'].", date_last_action='".$row['date_last_action']."' WHERE login='".$row['login']."';";
			mysql_query($sql) or die ( mysql_error() );
			add_history($row['login'],$montant,'innactivite','','Admin : '.$ar_innactivity['days'].' jours d\'innactivite');
		}
		echo '<td>'.$row['argent'].'€</td>';
		if($row['ruine']){
			echo '<td style="background: #ff0000; text-align:center;">';
			echo form_to_reset_user($row['login']);
		}else{echo '<td style="background:#00B000; text-align:center;">OK';}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}
function form_to_reset_user($login){
	$txt_reset=null;
	if(!isset($_POST['reset'])){
		$txt_reset.='<form method="post">';
		$txt_reset.='<input type="submit" name="reset" value="Reset" />';
		$txt_reset.='</form>';
	}else{
		reset_user($login);
		add_history($login,0,'reset','','Admin : Reset de joueur car bloque le jeu.');
		$txt_reset.='Utilisateur mis à zéro';
	}
	return $txt_reset;
}
?>