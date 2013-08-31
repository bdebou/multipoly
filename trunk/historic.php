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
	<title>BoubouPoly - Historique</title>
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
	<h2 style="clear:both;">Vos statistiques</h2>
		<div style="width:600px; float:left;"><?php create_table_stat();?></div>
		<div style="width:350px; float:left;"><?php create_table_stat_loye();?></div>
		<div style="width:350px; float:left; margin-top:10px;"><?php create_table_possession();?></div>
	<h2 style="clear:both;">Votre historique</h2>
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
function create_table_possession(){
	echo '<table class="possession">';
	echo '<tr><th colspan="2">Carte sortie de prison</th></tr>';
	echo '<tr><td>de Chance</td><td ';
	if($_SESSION['sortir_prison_chance']){echo 'style="background:#80ff80;">Oui';}else{echo 'style="background:#ff6262;">Non';}
	echo '</td></tr>';
	echo '<tr><td>de Caisse de Communauté</td><td ';
	if($_SESSION['sortir_prison_communauté']){echo 'style="background:#80ff80;">Oui';}else{echo 'style="background:#ff6262;">Non';}
	echo '</td></tr>';
	echo '</table>';
}
function create_table_stat(){
	$keys = array('construction', 'achat', 'taxe', 'innactivite','nb_innactivite','loyéR','loyéP','chance','caisse','nb_départ','départ','nb_prison','prison','parking','transaction');
	$stat = array_fill_keys($keys, 0);
	$sql="SELECT * FROM `table_history` WHERE login = '".$_SESSION['login']."' ORDER BY `id` DESC";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		switch($row['action']){
			case 'reset': break 2;
			case 'construction': $stat['construction']+=$row['montant'];break;
			case 'vente': $stat['construction']-=$row['montant'];break;
			case 'achat': $stat['achat']+=$row['montant'];break;
			case 'taxe': $stat['taxe']+=$row['montant'];break;
			case 'innactivite': 
				$stat['innactivite']+=$row['montant'];
				//$stat['nb_innactivite']+=($row['montant']/10);
				$stat['nb_innactivite']++;
				break;
			case 'loyéP': $stat['loyéP']-=$row['montant'];break;
			case 'chanceM': $stat['chance']-=$row['montant'];break;
			case 'caisseM': $stat['caisse']-=$row['montant'];break;
			case 'loyéR': $stat['loyéR']+=$row['montant'];break;
			case 'chanceP': $stat['chance']+=$row['montant'];break;
			case 'caisseP': $stat['caisse']+=$row['montant'];break;
			case 'départ': 
				$stat['départ']+=$row['montant'];
				$stat['nb_départ']++;
				break;
			case 'prisonIN':
				$stat['nb_prison']++;
				break;
			case 'prisonOUTC':
				$stat['prison']-=$row['montant'];
				break;
			case 'parking': $stat['parking']+=$row['montant'];break;
			case 'transaction': $stat['transaction']+=$row['montant']; break;
		}
	}
	echo '<table class="stat">';
	echo '<tr><td>Vous avez construit pour un total de :</td><td>'.$stat['construction'].' €</td></tr>';
	echo '<tr><td>Vous avez acheté pour un total de :</td><td>'.$stat['achat'].' €</td></tr>';
	echo '<tr><td>Vous avez vendu des terrains pour un total de :</td><td>'.$stat['transaction'].' €</td></tr>';
	echo '<tr><td>Vous avez payé des taxes pour un total de :</td><td>'.$stat['taxe'].' €</td></tr>';
	echo '<tr><td>Vous ne vous êtes pas connecté pendant '.$stat['nb_innactivite'].' jours :</td><td>'.$stat['innactivite'].' €</td></tr>';
	echo '<tr><td>La chance vous a sourit à hauteur de :</td><td>'.$stat['chance'].' €</td></tr>';
	echo '<tr><td>La caisse de communauté vous a fait épargné :</td><td>'.$stat['caisse'].' €</td></tr>';
	echo '<tr><td>Vous êtes passé '.$stat['nb_départ'].' fois par la case Départ :</td><td>'.$stat['départ'].' €</td></tr>';
	echo '<tr><td>Vous êtes allé en prison '.$stat['nb_prison'].' fois et payé des caution pour un total de :</td><td>'.$stat['prison'].' €</td></tr>';
	echo '<tr><td>Vous avez gagné par le parking un total de :</td><td>'.$stat['parking'].' €</td></tr>';
	echo '<tr><td colspan="2">Vous avez été ruiné '.$_SESSION['nb_ruine'].' fois.</td></tr>';
	echo '</table>';
}
function create_table_stat_loye(){
	$stat_loyeP=0;$stat_loyeR=0;$nb_loyeP=0;$nb_loyeR=0;
	$sql="SELECT * FROM table_history WHERE login = '".$_SESSION['login']."' ORDER BY `id` DESC";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		switch($row['action']){
			case 'reset': break 2;
			case 'loyéP': $stat_loyeP-=$row['montant'];$nb_loyeP++;break;
			case 'loyéR': $stat_loyeR+=$row['montant'];$nb_loyeR++;break;
		}
	}
	echo '<table style="width:100%;"><tr><th colspan="3">Loyés</th></tr>';
	echo "<tr><td>Loyés reçus :</td><td>$nb_loyeR</td><td>$stat_loyeR €</td></tr>";
	echo "<tr><td>Loyés payés :</td><td>$nb_loyeP</td><td>$stat_loyeP €</td></tr>";
	echo '<tr><td colspan="2">Total :</td><td ';
	if(($stat_loyeR+$stat_loyeP)<0){echo 'style="background:#ff6262;">';}else{echo 'style="background:#80ff80;">';}
	echo ($stat_loyeR+$stat_loyeP).' €</td></tr></table>';
}
function create_table_historic(){
	include('config.php');
	$style=null;
	$argent=$_SESSION['argent'];
	$sql="SELECT * FROM `table_history` WHERE login = '".$_SESSION['login']."' ORDER BY `id` DESC LIMIT 0, 50";
	$requete = mysql_query($sql) or die (mysql_error());
	$argent_avant=$_SESSION['argent'];
	$argent_apres=$_SESSION['argent'];
	while ($row = mysql_fetch_assoc($requete)) {
		if($row['action']=='reset'){break;}
		if(	$row['action']=='construction' or 
			$row['action']=='achat' or 
			$row['action']=='taxe' or 
			$row['action']=='innactivite' or
			$row['action']=='loyéP' or
			$row['action']=='chanceM' or
			$row['action']=='prisonIN' or
			$row['action']=='prisonOUTC' or
			$row['action']=='hypoM' or
			$row['action']=='caisseM'){
				$argent_avant+=$row['montant'];
				$style='style="background:#ff6262;"';	//rouge
			}
		elseif(	$row['action']=='loyéR' or
				$row['action']=='chanceP' or
				$row['action']=='caisseP' or
				$row['action']=='départ' or
				$row['action']=='vente' or
				$row['action']=='transaction' or
				$row['action']=='prisonOUT' or
				$row['action']=='hypoP' or
				$row['action']=='parking'){
					$argent_avant-=$row['montant'];
					$style='style="background:#80ff80;"';	//vert
				}
		elseif(	$row['action']=='des'){$style='style="background:#ffff80;"';}
		echo '<tr '.$style.'><td>'.$row['date'].'</td><td>'.$argent_avant.' €</td><td>'.$row['montant'].' €</td><td>'.$argent_apres.' €</td><td '.color_propriete($row['action'],$row['code']).'>'.$row['description'].'</td></tr>';
		$argent_apres=$argent_avant;
	}
}
function color_propriete($action,$code){
	$txt_color=null;
	$str_code=str_split($code);
	if(	$action=='achat' or
		$action=='construction' or
		$action=='loyéR' or $action=='loyéP' or 
		$action=='hypoP' or $action=='hypoM' or
		$action=='transaction' or
		$action=='vente'){
		switch($str_code[0]){
			case 'i': $txt_color ='style="background:#000000;color:#ffffff;"';break;
			case 'j': $txt_color ='style="background:#ffffff;color:#000000;"';break;
			default: 
				$sql="SELECT couleur FROM table_carte_propriete WHERE code='".$code."'";
				$requete = mysql_query($sql) or die (mysql_error());
				if(!$requete){break;}
				$result = mysql_fetch_assoc($requete);
				$txt_color ='style="background:#'.$result['couleur'].';"';
				break;
		}
	}
	return $txt_color;
}
?>