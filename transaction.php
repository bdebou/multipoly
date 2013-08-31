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
	<title>BoubouPoly - Transactions</title>
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
	<h1>Transactions immobilières</h1>
	<?php
	if(isset($_GET['action']) and $_GET['action']=='vendre' and !isset($_GET['montant'])){
		echo '<h2>Ajout d\'une transaction</h2>';
		$case=detail_case($_GET['code']);
		echo '
<table style="border:1px #000000 solid;">
	<tr><td>'.$case['0'].'</td></tr>
	<tr>
		<td>
			<form method="get" action="transaction.php" style="text-align:center;">
				<input type="hidden" name="code" value="'.$_GET['code'].'" />
				<input type="text" name="montant" size="5" />
				<input type="submit" name="submit" value="Ajouter" />
			</form>
		</td>
	</tr>
</table>';
	}elseif(isset($_GET['montant']) and is_numeric($_GET['montant'])){
		$sql = "INSERT INTO table_transaction (`id`, `login_vendeur`, `login_acheteur`, `code_propriete`, `prix`, `date_debut_transaction`, `resultat`)";
		$sql .= " VALUES (NULL, '".$_SESSION['login']."', NULL, '".$_GET['code']."', '".$_GET['montant']."', NOW(), NULL);";
		mysql_query($sql) or die ( mysql_error() );
		unset($_GET);
		redir('transaction.php');
	}
	?>
	<h2 style="clear:both;">Les transactions en cours:</h2>
	<table class="transaction-encours"><?php echo affiche_transaction_en_cours();?></table>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>


<?php
function affiche_transaction_en_cours(){
	$txt_result=null; $nb_max_col=3;$nb_col=0;
	$sql="SELECT * FROM table_transaction WHERE resultat IS NULL;";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)){
		if($nb_col==0){$txt_result .= '<tr>';}
		if(time()-strtotime($row['date_debut_transaction'])<(3600*24*5)){
			$case=detail_case($row['code_propriete']);
			$txt_result .= '<td style="text-align:center;">Prix demandé = '.$row['prix'].'€<br />
			<form method="get" action="transaction.php">
			<input type="hidden" name="code" value="'.$row['code_propriete'].'" />
			<input type="hidden" name="vendeur" value="'.$row['login_vendeur'].'" />
			<input type="hidden" name="nompropriete" value="'.$case['1'].'" />
			<input type="hidden" name="prix" value="'.$row['prix'].'" />
			<input type="hidden" name="maison" value="'.$case['2'].'" />
			<input type="submit"';
			if($_SESSION['login']==$row['login_vendeur']){$txt_result .= ' value="C\'est la vôtre" disabled="disabled"';}
			else{$txt_result .= ' value="Accepte la transaction"';}
			$txt_result .= ' />';
			$txt_result .= '</form>'.$case['0'].'</td>';
			$nb_col++;
		}else{
			$sql="UPDATE table_transaction SET resultat='Dépassé' WHERE code_propriete='".$row['code_propriete']."' AND resultat IS NULL;";
			mysql_query($sql) or die (mysql_error());
		}
		if($nb_col==$nb_max_col){$txt_result .= '</tr>';$nb_col=0;}
		if(isset($_GET['code']) and $_GET['code']==$row['code_propriete']){
			transaction_acceptee($_GET['code'],$_GET['prix'], $_GET['vendeur'], $_GET['nompropriete'], $_GET['maison']);
		}
	}
	return $txt_result;
}
function detail_case($num_case){
	$strpropriete=str_split($num_case);$txt_result=null;
	$txt_result .= '<div style="width:200px; height:300px; border:2px #000000 solid; float:left; margin:5px; padding: 2px;clear:left;';
	switch ($strpropriete['0']){
		case 'i':
			$sql = "SELECT * FROM table_gare WHERE code='".$num_case."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_carte_gare = mysql_fetch_array($requete, MYSQL_ASSOC);
			if($result_carte_gare['hypotheque']){$txt_result .= 'filter:alpha(opacity=50);opacity:0.5;">';}else{$txt_result .= '">';}
			$txt_result .= '<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;
			background: #000000;color: #ffffff;">'.$result_carte_gare['nom'].'</div>
			<p style="text-align:center;">'.$result_carte_gare['prix_achat'].'€</p>
			<p class="propriete">Prix :</p><p>Le montant des loyer dépend du nombre de gare en votre possession.</p>
			<ul style="text-align:left;"><li>1 gare = '.$result_carte_gare['prix_location'].'€</li>
			<li>2 gares = '.($result_carte_gare['prix_location']*2).'€</li>
			<li>3 gares = '.($result_carte_gare['prix_location']*4).'€</li>
			<li>4 gares = '.($result_carte_gare['prix_location']*8).'€</li></ul>
			<p class="propriete">Propriétaire :</p><p>'.$result_carte_gare['proprietaire'].'</p>';
			$nom_propriete=$result_carte_gare['nom'];
			$nb_maison=0;
			break;
		case 'j':
			$sql = "SELECT * FROM table_compagnie WHERE code='".$num_case."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_carte_cie = mysql_fetch_array($requete, MYSQL_ASSOC);
			if($result_carte_cie['hypotheque']){$txt_result .= 'filter:alpha(opacity=50);opacity:0.5;">';}else{$txt_result .= '">';}
			$txt_result .= '<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;
			">'.$result_carte_cie['nom'].'</div>
			<p style="text-align:center;">'.$result_carte_cie['prix_achat'].'€</p>
			<p class="propriete">Prix :</p><p>Le montant des loyer dépend du total des dés et si vous possédez 1 ou 2 compagnies.</p>
			<ul style="text-align:left;"><li>1 compagnie = (total des dés) x 4</li>
			<li>2 compagnies = (total des dés) x 10</li></ul>
			<p class="propriete">Propriétaire :</p><p>'.$result_carte_cie['proprietaire'].'</p>';
			$nom_propriete=$result_carte_cie['nom'];
			$nb_maison=0;
			break;
		default:
			$sql = "SELECT * FROM table_carte_propriete WHERE code='".$num_case."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_carte_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
			if($result_carte_propriete['hypotheque']){$txt_result .= 'filter:alpha(opacity=50);opacity:0.5;">';}else{$txt_result .= '">';}
			$txt_result .= '<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;
			background: #'.$result_carte_propriete['couleur'].';">'.$result_carte_propriete['ville'].'</div>
			<div style="width: 106px; margin-left:auto; margin-right:auto;">';
			if($result_carte_propriete['nb_maison']==5){$txt_result .= '<div class="hotel"></div>';}
			else{for($i=1;$i<=$result_carte_propriete['nb_maison'];$i++){$txt_result .= '<div class="maison"></div>';}}
			$txt_result .= '</div>
			<p style="text-align:center; clear:both;">'.$result_carte_propriete['adresse'].'<br />'.$result_carte_propriete['prix_achat'].'€</p>
			<p class="propriete">Prix :</p>
			<ul style="text-align:left;"><li>Loyer = '.$result_carte_propriete['prix_location'].'€</li>
			<li>Construction : '.$result_carte_propriete['prix_construction'].'€</li>
			<li>1 maison = '.$result_carte_propriete['prix_1_maison'].'€</li>
			<li>2 maisons = '.$result_carte_propriete['prix_2_maison'].'€</li>
			<li>3 maisons = '.$result_carte_propriete['prix_3_maison'].'€</li>
			<li>4 maisons = '.$result_carte_propriete['prix_4_maison'].'€</li>
			<li>Hotel = '.$result_carte_propriete['prix_hotel'].'€</li></ul>
			<p class="propriete">Propriétaire :</p><p>'.$result_carte_propriete['proprietaire'].'</p>';
			$nom_propriete=$result_carte_propriete['ville'].', '.$result_carte_propriete['adresse'];
			$nb_maison=$result_carte_propriete['nb_maison'];
			break;
	}
	$txt_result .= '</div>';
	return array($txt_result,$nom_propriete, $nb_maison );
}
function transaction_acceptee($code, $prix, $vendeur, $nom_propriete, $nb_maison){
	$_SESSION['argent']-=$prix;
	$_SESSION['nb_propriete']++;
	$_SESSION['nb_maison'] += $nb_maison;
	update_tb_utilisateur(false);
	$sql="UPDATE ".get_table_name($code)." SET proprietaire='".$_SESSION['login']."' WHERE code='".$code."'";
	mysql_query($sql) or die (mysql_error());
	if(get_table_name($code)=='table_gare'){
		$sql="SELECT code, prix_location FROM table_gare WHERE proprietaire='".$_SESSION['login']."'";
		$requete = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_assoc($requete)){
			$sql="UPDATE table_plateau SET prix='".($row['prix_location']*(pow(2,(mysql_num_rows($requete)-1))))."' WHERE link_carte_propriete='".$row['code']."'";
			mysql_query($sql) or die (mysql_error());
		}
	}
	$sql="UPDATE table_utilisateur SET argent=argent+".$prix.", nb_propriete=nb_propriete-1, nb_maison=nb_maison-".$nb_maison." WHERE login='".$vendeur."'";
	mysql_query($sql) or die (mysql_error());
	$sql="UPDATE table_transaction SET login_acheteur='".$_SESSION['login']."', resultat='OK' WHERE code_propriete='".$code."' AND resultat IS NULL;";
	mysql_query($sql) or die (mysql_error());
	add_history($_SESSION['login'], $prix,'achat',$code,'a acheté '.$nom_propriete.' à '.$vendeur);
	add_history($vendeur, $prix, 'transaction', $code, 'a vendu le terrain '.$nom_propriete.' à '.$_SESSION['login']);
	unset($_GET);
	redir('transaction.php');
}
function get_table_name($code){
	$strcode=str_split($code);
	switch($strcode['0']){
		case 'i': return 'table_gare';
		case 'j': return 'table_compagnie';
		default : return 'table_carte_propriete';
	}
}

?>