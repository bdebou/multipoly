<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
// On prolonge la session
session_start();
include('config.php');
include('functions.php');
global $temp_attente;
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<script type="text/JavaScript">//<![CDATA[
	//var time=0;  //Changer ici le temps en seconde
	function CountDown(time){
		if(time>0){
			if(time>=1){document.title = "BoubouPoly - Règles - " + ArrangeDate(time);}
			timeb=time-1;
			setTimeout("CountDown(timeb)", 1000);
		}else if(time==0){window.location="regles.php";}
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
	<title>BoubouPoly - Règles</title>
	<link rel="alternate" type="application/rss+xml" href="http://dcboubou.dyndns.org/game/fct/activity.xml" title="Activités BoubouPoly" />
	<link rel="stylesheet" href="./CSS/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<?php
// On teste si la variable de session existe et contient une valeur
if(empty($_SESSION['login'])) {
	?>
	<body>
	<div class="login">
	<form method="get" action="connect.php"> 
		<fieldset><legend>Login : </legend><input type="text" name="login" size="20" /></fieldset>
		<fieldset><legend>Mot de passe : </legend><input type="password" name="motdepasse" size="17" /></fieldset>
		<p><input type="submit" name="submit" value="Se connecter" /></p>
	</form>
	<form method="get" action="inscription.php" style="text-align:center;">
		<p><input type="submit" name="inscription" value="S'inscrire" /></p>
	</form>
	</div>
	<?php
}else{
	if(time()-strtotime($_SESSION['date_last_action'])<$temp_attente and !$_SESSION['replay']){
		echo '<body onload="CountDown('.($temp_attente-(time()-strtotime($_SESSION['date_last_action']))).')">';
	}
	echo '<div class="loginstatus">';affiche_LoginStatus();echo '</div>';
	echo '<div class="proprietes">';affiche_proprietes();echo '</div>';
	echo '<div class="menu">';affiche_menu();echo '</div>';
}
?>
<div class="main">
<h1>Les règles et déroulement</h1>
	<p>Vous démarrez avec 2000€ et sur la case départ. Vous lancez les dés et avancez de leur total. A chaque lancé, vous tomberez sur une case différente et donc une action différente à remplir.</p>
	<p>Vous aurez comme action à remplir :</p>
	<ul>
		<li>Acheter une propriété (terrain, gare ou compagnie)</li>
		<li>Payer un loyer</li>
		<li>Construire sur un terrain</li>
		<li>Vendre une maison</li>
		<li>Mettre en hypothèque une propriété (terrain, gare ou compagnie)</li>
		<li>Payer une taxe</li>
		<li>Tirer une carte "Chance" ou "Caisse de communauté"</li>
		<li>Aller en prison</li>
		<li>Rammasser le contenu du Parking</li>
	</ul>
<h2>Les terrains</h2>
	<p>Si la propriété est libre, vous aurez la possibilité de l'acheter.</p>
	<p>Par contre, si elle est déja en possession d'un autre joueur, vous devrez lui payer un loyé. Le loyé dépend de son état, si il y a ou non une ou plusieurs maison construite dessus.</p>
	<p>Sur chaque passage sur un de ces terrain, vous pourrez construire une maison. Vous ne pouvez construire que 4 maisons ou 1 hotel (équivalent à 5 maisons). Vous pouvez, via le menu immobilier vendre des maisons à la moitié de son prix de construction.</p>
<h2>Les Gares</h2>
	<p>Les gares sont comme les terrains, soit vous les achetez, soit vous payez un loyé. Une petite particularité pour les loyés des gares, c'est en fonction du nombre de gare en possession par le même propriétaire.</p>
	<p>Vous ne pouvez pas construire de maison sur une gare.</p>
<h2>Les compagnies</h2>
	<p>Les compagnies sont comme les terrains, soit vous les achetez, soit vous payez un loyé. Une petite particularité pour les loyés des compagnies, c'est en fonction du total des dés et du nombre de compagnie en possession par le même propriétaire.</p>
	<p>Vous ne pouvez pas construire de maison sur une compagnie.</p>
<h2>Hypothèque</h2>
	<p>Vous pouvez mettre en hypothèque une propriété (terrain, gare ou compagnie). Cette hypothèque vous rapportera la moitié de son prix d'achat.</p>
	<p>Lorsqu'une propriété est hypothéquée, vous ne touchez plus de loyer.</p>
	<p>Vous pouvez, également, lever l'hypothèque d'une propriété en rembourssant l'hypothèqué majorée de 10%.</p>
<h2>Parking</h2>
	<p>A chaque fois que vous devrez payer une taxe, une carte "Chance" ou "Caisse de communauté", vous payerez au parking. Par jour d'innactivitée, vous payerez 10€ au parking</p>
	<p>Quand vous tomberez sur la case "Parking", vous gagnerez cette somme.</p>
<h2>Case Départ</h2>
	<p>A Chaque passage par la case "Départ", vous toucherez 200€. Par contre si vous vous y arrêtez, vous toucherez 400€.</p>
<h2>Prison</h2>
	<p>Quand vous êtes en prison, vous avez 4 possibilités d'en sortir.</p>
	<ol>
		<li>Vous patientez le temp de votre peine (3 lancés de dés).</li>
		<li>Vous obtenez un double au lancé de dés.</li>
		<li>Vous payez une caution dont le montant est <?php global $caution;echo $caution;?>€.</li>
		<li>Vous utilisez l'une de vos cartes de Sortie de Prison (reçue par les cartes Chance ou Caisse de Communauté).</li>
	</ol>
	<p>Pour votre information, quand vous êtes en prison, vous ne touchez aucun loyé.</p>
<h2>Les transactions</h2>
	<p>Sous le menu "transactions", vous verrez toutes les propriétées en cours de d'échange entre joueur. A vrai dire, ce n'est pas un échange mais une vente dont le prix est fixé par le vendeur.</p>
	<p>Le vendeur peut vendre une propriété contenant déjà des maisons. Bien sur il perd toutes ses maisons également. Au vendeur à bien évaluer le prix de vente.</p>
<h2>Particularités</h2>
	<ul>
		<li>Par jour d'inactivitée, vous perdez 10€. Ces 10€ sont versés au parking.</li>
		<li>Si vous êtes ruinés, vous devez jamais attendre que la partie soit totalement terminée pour recommencer.</li>
	</ul>
<h2>Gagner</h2>
	<p>Vous gagnerez quand vous aurez acheté toutes les propriétés (terrains, gares et compagnies).</p>
	<p>OU</p>
	<p>Quand un des autres joueur aura été ruiné <?php global $nb_max_ruine;echo $nb_max_ruine;?> fois.</p>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>