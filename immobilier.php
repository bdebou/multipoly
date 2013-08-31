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
}
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<script type="text/JavaScript">//<![CDATA[
	//var time=0;  //Changer ici le temps en seconde
	function CountDown(time){
		if(time>0){
			if(time>=1){document.title = "BoubouPoly - Immobilier - " + ArrangeDate(time);}
			timeb=time-1;
			setTimeout("CountDown(timeb)", 1000);
		}else if(time==0){window.location="immobilier.php";}
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
	<title>BoubouPoly - Immobilier</title>
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
	<h2>Votre parc immobilier</h2>
	<p>Vous avez construit <?php echo $_SESSION['nb_maison'];?> maisons ou équivalent. Attention, vous êtes limités à <?php echo $nb_max_maison;?> maisons. Grace à cette page, vous pourrez revendre des maisons sur certain terrain pour pouvoir en construire sur d'autre. Réfléchissez bien !</p>
	<div style="clear:both;"><?php display_terrains();?></div>
	<div style="clear:both;"><?php display_gares();?></div>
	<div style="clear:both;"><?php display_compagnies();?></div>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>
<?php
function display_terrains(){
	$sql="SELECT * FROM table_carte_propriete WHERE proprietaire = '".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '
<div style="display:block; width:200px; height:auto; border:2px #000000 solid; float:left; margin:5px; padding: 2px;';
		if($row['hypotheque']){
			echo '
	filter:alpha(opacity=50);opacity:0.5;';
		}
		echo '
">';
		echo '
	<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;background: #'.$row['couleur'].';">'.$row['ville'].'</div>';
			echo '
	<div style="width: 106px; margin-left:auto; margin-right:auto;">';
			if($row['nb_maison']==5){echo '
		<div class="hotel"></div>';}
			else{for($i=1;$i<=$row['nb_maison'];$i++){echo '
		<div class="maison"></div>';}}
			echo '
	</div>
	<p style="text-align:center; clear:both;">'.$row['adresse'].'<br />'.$row['prix_achat'].'€</p>
	<p class="propriete">Prix :</p>
	<ul>
		<li>Loyer = '.$row['prix_location'].'€</li>
		<li>Construction : '.$row['prix_construction'].'€</li>
		<li>1 maison = '.$row['prix_1_maison'].'€</li>
		<li>2 maisons = '.$row['prix_2_maison'].'€</li>
		<li>3 maisons = '.$row['prix_3_maison'].'€</li>
		<li>4 maisons = '.$row['prix_4_maison'].'€</li>
		<li>Hotel = '.$row['prix_hotel'].'€</li>
		<li>Vente maison = '.($row['prix_construction']/2).'€</li>
	</ul>
	<p>Gain : '.calcul_gain($row['code']).' € pour '.$row['nb_passage'].' passage';
			if($row['nb_passage']>1){echo 's';}
			echo '
	</p>';
			if($row['nb_maison']>0){
				echo '
	<form method="get" action="immobilier.php" style="text-align:center;">
		<input type="hidden" name="code" value="'.$row['code'].'" />
		<select name="action" style="width:180px;">
			<option value="maison">Vendre 1 maison</option>
			<option value="vendre"';
					$sql="SELECT id FROM table_transaction WHERE resultat IS NULL AND code_propriete='".$row['code']."';";
					$requete_transaction = mysql_query($sql);
					$row_transaction = mysql_fetch_assoc($requete_transaction);
					if($row_transaction){echo ' disabled="disabled"';}
					echo '
				>Vendre la propriété</option>
		</select>
		<br />
		<input type="submit" value="Go" />
	</form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){
					switch($_GET['action']){
						case 'maison': vendre_maison($row); break;
						case 'vendre': redir('transaction.php?action=vendre&code='.$row['code']); break;
					}
				}
			}elseif($row['nb_maison']==0 and !$row['hypotheque']){
				echo '
	<form method="get" action="immobilier.php" style="text-align:center;">
		<input type="hidden" name="code" value="'.$row['code'].'" />
		<select name="action" style="width:180px;">
			<option value="hypothequer">Hypothéquer pour '.($row['prix_achat']/2).'€</option>
			<option value="vendre"';
					$sql="SELECT id FROM table_transaction WHERE resultat IS NULL AND code_propriete='".$row['code']."';";
					$requete_transaction = mysql_query($sql) or die (mysql_error());
					$row_transaction = mysql_fetch_assoc($requete_transaction);
					if($row_transaction){echo ' disabled="disabled"';}
					echo '
				>Vendre la propriété</option>
		</select>
		<br />
		<input type="submit" value="Go" />
	</form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){
					switch ($_GET['action']){
						case 'hypothequer': hypothequer_terrain('table_carte_propriete',$row);break;
						case 'vendre': redir('transaction.php?action=vendre&code='.$row['code']); break;
					}
				}
			}elseif($row['nb_maison']==0 and $row['hypotheque']){
				echo '
	<form method="get" action="immobilier.php" style="text-align:center; position:relative;">
		<input type="hidden" name="code" value="'.$row['code'].'" />
		<input type="submit" value="Lever l\'hypothèque pour '.(round($row['prix_achat']/2*1.1)).'€" style="width:190px; opacity:1.0;" />
	</form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){lever_hypotheque_terrain('table_carte_propriete',$row);}
			}
		echo '
</div>';
	}
}
function display_gares(){
	$sql="SELECT * FROM table_gare WHERE proprietaire = '".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<div style="display:block; width:200px; height:auto; border:2px #000000 solid; float:left; margin:5px; padding: 2px;';
		if($row['hypotheque']){echo 'filter:alpha(opacity=50);opacity:0.5;';}
		echo '">';
			echo '<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;background: #000000;color: #ffffff;">'.$row['nom'].'</div>
			<p style="text-align:center;">'.$row['prix_achat'].'€</p>
			<p class="propriete">Prix :</p><p>Le montant des loyer dépend du nombre de gare en votre possession.</p>
			<ul><li>1 gare = '.$row['prix_location'].'€</li>
			<li>2 gares = '.($row['prix_location']*2).'€</li>
			<li>3 gares = '.($row['prix_location']*4).'€</li>
			<li>4 gares = '.($row['prix_location']*8).'€</li></ul>
			<p>Gain : '.calcul_gain($row['code']).' € pour '.$row['nb_passage'].' passage';
			if($row['nb_passage']>1){echo 's';}
			echo '</p>';
			if(!$row['hypotheque']){
				echo '<form method="get" action="immobilier.php" style="text-align:center;">
				<input type="hidden" name="code" value="'.$row['code'].'" />
				<select name="action">
					<option value="hypothequer">Hypothéquer pour '.($row['prix_achat']/2).'€</option>
					<option value="vendre"';
					$sql="SELECT id FROM table_transaction WHERE resultat IS NULL AND code_propriete='".$row['code']."';";
					$requete_transaction = mysql_query($sql) or die (mysql_error());
					$row_transaction = mysql_fetch_assoc($requete_transaction);
					if($row_transaction){echo ' disabled="disabled"';}
					echo '>Vendre la propriété</option>
				</select>
				<input type="submit" value="Go" /></form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){
					switch ($_GET['action']){
						case 'hypothequer': hypothequer_terrain('table_gare',$row); break;
						case 'vendre': redir('transaction.php?action=vendre&code='.$row['code']); break;
					}
				}
			}else{
				echo '<form method="get" action="immobilier.php" style="text-align:center; position:relative;">
				<input type="hidden" name="code" value="'.$row['code'].'" />
				<input type="submit" value="Lever l\'hypothèque pour '.(round($row['prix_achat']/2*1.1)).'€" style="width:190px;" /></form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){lever_hypotheque_terrain('table_gare',$row);}
			}
		echo '</div>';
	}
}
function display_compagnies(){
	$sql="SELECT * FROM table_compagnie WHERE proprietaire = '".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<div style="display:block; width:200px; height:auto; border:2px #000000 solid; float:left; margin:5px; padding: 2px;';
		if($row['hypotheque']){echo 'filter:alpha(opacity=50);opacity:0.5;';}
		echo '">';
			echo '<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;background: #ffffff;color: #000000;">'.$row['nom'].'</div>
			<p style="text-align:center;">'.$row['prix_achat'].'€</p>
			<p class="propriete">Prix :</p><p>Le montant des loyer dépend du total des dés et si vous possédez 1 ou 2 compagnies.</p>
			<ul><li>1 compagnie = (total des dés) x 4</li>
			<li>2 compagnies = (total des dés) x 10</li></ul>
			<p>Gain : '.calcul_gain($row['code']).' € pour '.$row['nb_passage'].' passage';if($row['nb_passage']>1){echo 's';}echo '</p>';
			if(!$row['hypotheque']){
				echo '<form method="get" action="immobilier.php" style="text-align:center;">
				<input type="hidden" name="code" value="'.$row['code'].'" />
				<select name="action">
					<option value="hypothequer">Hypothéquer pour '.($row['prix_achat']/2).'€</option>
					<option value="vendre"';
					$sql="SELECT id FROM table_transaction WHERE resultat IS NULL AND code_propriete='".$row['code']."';";
					$requete_transaction = mysql_query($sql) or die (mysql_error());
					$row_transaction = mysql_fetch_assoc($requete_transaction);
					if($row_transaction){echo ' disabled="disabled"';}
					echo '>Vendre la propriété</option>
				</select>
				<input type="submit" value="Go" /></form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){
					switch ($_GET['action']){
						case 'hypothequer': hypothequer_terrain('table_compagnie',$row); break;
						case 'vendre': redir('transaction.php?action=vendre&code='.$row['code']); break;
					}
				}
			}else{
				echo '<form method="get" action="immobilier.php" style="text-align:center; position:relative;">
				<input type="hidden" name="code" value="'.$row['code'].'" />
				<input type="submit" value="Lever l\'hypothèque pour '.(round($row['prix_achat']/2*1.1)).'€" style="width:190px;" /></form>';
				if(isset($_GET['code']) and $_GET['code']==$row['code']){lever_hypotheque_terrain('table_compagnie',$row);}
			}
		echo '</div>';
	}
}
function calcul_gain($code){
	$gain=0;
	$sql="SELECT montant FROM table_history WHERE code='".$code."' AND action='loyéR' AND login='".$_SESSION['login']."';";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$gain+=$row['montant'];
	}
	return $gain;
}
function vendre_maison($propriete){
	$_SESSION['argent']+=$propriete['prix_construction'] / 2;
	$_SESSION['nb_maison']--;
	update_tb_utilisateur(false);
	$sql="UPDATE table_carte_propriete SET nb_maison=nb_maison-1 WHERE code='".$propriete['code']."'";
	mysql_query($sql) or die (mysql_error());
	$propriete['nb_maison'] --;
	if($propriete['nb_maison']==0){$key_location='prix_location';}
	else{$key_location='prix_'.$propriete['nb_maison'].'_maison';}
	$sql="UPDATE table_plateau SET prix='".$propriete[$key_location]."' WHERE link_carte_propriete='".$propriete['code']."'";
	mysql_query($sql) or die (mysql_error());
	add_history($_SESSION['login'],($propriete['prix_construction']/2),'vente',$propriete['code'],'a vendu 1 maison de '.$propriete['ville'].', '.$propriete['adresse']);
	unset($_GET);
	redir('immobilier.php');
}
function hypothequer_terrain($table, $propriete){
	$_SESSION['argent']+=$propriete['prix_achat'] / 2;
	update_tb_utilisateur(false);
	$sql="UPDATE $table SET hypotheque=true WHERE code='".$propriete['code']."'";
	mysql_query($sql) or die (mysql_error());
	add_history($_SESSION['login'],($propriete['prix_achat']/2),'hypoP',$propriete['code'],'a mis une hypothéque sur '.Extract_Nom_Propriete($propriete));
	unset($_GET);
	redir('immobilier.php');
}
function lever_hypotheque_terrain($table, $propriete){
	$_SESSION['argent']-=round($propriete['prix_achat'] / 2 * 1.1);
	update_tb_utilisateur(false);
	$sql="UPDATE $table SET hypotheque=false WHERE code='".$propriete['code']."'";
	mysql_query($sql) or die (mysql_error());
	add_history($_SESSION['login'],($propriete['prix_achat']/2*1.1),'hypoM',$propriete['code'],'a levé l\'hypothèque sur '.Extract_Nom_Propriete($propriete));
	unset($_GET);
	redir('immobilier.php');
}


?>