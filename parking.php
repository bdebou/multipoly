<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// On prolonge la session
session_start();
// On teste si la variable de session existe et contient une valeur
if(empty($_SESSION['login'])) {
    // Si inexistante ou nulle, on redirige vers le formulaire de login
    header('Location: authentification.php');
    exit();
}else{
	include('functions.php');
	include('config.php');
	$sql="SELECT * FROM table_utilisateur WHERE login='".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	$result_utilisateur = mysql_fetch_array($requete, MYSQL_ASSOC);
	$_SESSION = array_merge($_SESSION,$result_utilisateur);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<script type="text/JavaScript">//<![CDATA[
	//var time=0;  //Changer ici le temps en seconde
	function CountDown(time){
		if(time>0){
			if(time>=1){document.title = "BoubouPoly - Historique - " + ArrangeDate(time);}
			timeb=time-1;
			setTimeout("CountDown(timeb)", 1000);
		}else if(time==0){window.location="historic.php";}
	}
	function ArrangeDate(heure){
		if(heure>=0 && heure<=59){
			// Seconds
			shifttime = heure+" seconds";
		}else if(heure>=60 && heure<=3599) {
			// Minutes + Seconds
			pmin = heure / 60;
			premin = Math.floor(pmin);
			presec = pmin-premin;
			sec = presec*60;
			shifttime = premin+" min "+Math.round(sec)+" sec";
		}else if(heure>=3600 && heure<=86399) {
			// Hours + Minutes 4253
			phour = heure / 3600;
			prehour = Math.floor(phour);
			premin = (phour-prehour)*60;
			min = Math.floor(premin);
			presec = premin-min;
			sec = presec*60;
			shifttime = prehour+" hrs "+min+" min "+Math.round(sec)+" sec";
		}else if(heure>=86400) {
			// Days + Hours + Minutes
			pday = heure / 86400;
			preday = Math.floor(pday);
			phour = (pday-preday)*24;
			prehour = Math.floor(phour);
			premin = (phour-prehour)*60;
			min = Math.floor(premin);
			presec = premin-min;
			sec = presec*60;
			shifttime = preday+" days "+prehour+" hrs "+min+" min "+Math.round(sec)+" sec";
		}
		return (shifttime);
	}
	//]]></script>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>BoubouPoly - Historique Parking</title>
	<link rel="alternate" type="application/rss+xml" href="http://dcboubou.dyndns.org/game/fct/activity.xml" title="Activités BoubouPoly" />
	<link rel="stylesheet" href="./CSS/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body
<?php
	global $temp_attente;
	if(time()-strtotime($_SESSION['date_last_action'])<$temp_attente and !$_SESSION['replay']){
		echo ' onload="CountDown('.($temp_attente-(time()-strtotime($_SESSION['date_last_action']))).')"';
	}
?>
>
<div class="loginstatus"><?php affiche_LoginStatus();?></div>
<div class="proprietes"><?php affiche_proprietes();?></div>
<div class="menu"><?php affiche_menu();?></div>
<div class="main">
	<h2 style="clear:both;">Historique du parking</h2>
	<p>Vous trouverez ici la liste de vos 50 dernières opérations en ce compris vos achats, dépenses, les loyés perçus et de qui.</p>
	<table class="historic">
		<tr><th style="width:150px;">Date</th><th style="width:80px;">Argent avant</th><th style="width:80px;">Montant</th><th style="width:80px;">Argent après</th><th style="width:auto;">Description</th></tr>
		<?php create_table_historic();?>
	</table>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>


<?php
function create_table_historic(){
	$style=null;
	$sql="SELECT prix FROM table_plateau WHERE num_case='21'";
	$requete = mysql_query($sql) or die (mysql_error());
	$parking = mysql_fetch_assoc($requete);
	$sql="SELECT * FROM `table_history` WHERE login = 'Parking' ORDER BY `id` DESC";
	$requete = mysql_query($sql) or die (mysql_error());
	$argent_avant=$parking['prix'];
	$argent_apres=$parking['prix'];
	//$argent_apres=$_SESSION['argent'];
	while ($row = mysql_fetch_assoc($requete)) {
		if($row['action']=='parkingP'){
			$argent_avant-=$row['montant'];
			$style='style="background:#80ff80;"';	//rouge
		}
		elseif($row['action']=='parkingM'){
			$argent_avant+=$row['montant'];
			$style='style="background:#ff6262;"';	//vert
		}
		echo '<tr '.$style.'><td>'.$row['date'].'</td><td>'.$argent_avant.' €</td><td>'.$row['montant'].' €</td><td>'.$argent_apres.' €</td><td>'.$row['description'].'</td></tr>';
		$argent_apres=$argent_avant;
	}
}
?>