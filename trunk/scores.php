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
			if(time>=1){document.title = "BoubouPoly - Scores - " + ArrangeDate(time);}
			timeb=time-1;
			setTimeout("CountDown(timeb)", 1000);
		}else if(time==0){window.location="scores.php";}
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
	<title>BoubouPoly - Scores</title>
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
	<h1 style="clear:both;">Les scores</h1>
	<table class="scores" style="border-style: none;"><tr><td style="border-style: none;">
	<h2>Etat partie en cours</h2>
	<table>
		<tr><th>Joueur</th><th>Portefeuille</th><th>Nb propriétés</th><th>Nb Maison</th><th>Position</th><th>Nb tours</th><th>Nb ruiné</th></tr>
		<?php
		$nbProprietes = 0;
		$sql="SELECT login, argent, nb_propriete, position, nb_tour, nb_ruine, nb_maison, prison FROM table_utilisateur ORDER BY argent DESC, nb_propriete DESC";
		$requete = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_assoc($requete)){
			if($row['prison']){echo '<tr style="background:url(\'./images/prison.png\')">';}
			else{echo '<tr>';}
			echo '<td><a href="./';
			if($row['login']==$_SESSION['login']){echo 'historic.php';}
			else{echo 'userhist.php?user='.$row['login'];}
			echo '">'.$row['login'].'</a></td><td>'.$row['argent'].' €</td><td>'.$row['nb_propriete'].'</td><td>'.$row['nb_maison'].'</td><td>case n°'.$row['position'].'</td><td>'.$row['nb_tour'].'</td><td>'.$row['nb_ruine'].'</td></tr>';
			$nbProprietes += $row['nb_propriete'];
		}?>
	</table>
	<p>Il reste encore <?php global $nb_proprietes_total; echo ($nb_proprietes_total - $nbProprietes);?> propriétées à acheter.</p>
	</td><td style="border-style: none;">
	<h2>Le Top 10</h2>
	<table>
		<tr><th>Argent final</th><th>Joueur</th><th>nb terrain</th><th>Quand?</th></tr>
		<?php
		$argent=$_SESSION['argent'];
		$sql="SELECT * FROM table_scores ORDER BY argent_final DESC LIMIT 0, 10";
		$requete = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_assoc($requete)) {
			echo '<tr><td>'.$row['argent_final'].' €</td><td>'.$row['login'].'</td><td>'.$row['nb_terrain'].'</td><td>'.$row['date_gagne'].'</td></tr>';
		}?>
	</table>
	</td></tr></table>
	<script type="text/javascript"><!--
	google_ad_client = "ca-pub-2161674761092050";
	/* Pub 4 */
	google_ad_slot = "3979131772";
	google_ad_width = 728;
	google_ad_height = 90;
	//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>