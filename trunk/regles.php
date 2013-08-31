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
			if(time>=1){document.title = "BoubouPoly - R�gles - " + ArrangeDate(time);}
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
	<title>BoubouPoly - R�gles</title>
	<link rel="alternate" type="application/rss+xml" href="http://dcboubou.dyndns.org/game/fct/activity.xml" title="Activit�s BoubouPoly" />
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
<h1>Les r�gles et d�roulement</h1>
	<p>Vous d�marrez avec 2000� et sur la case d�part. Vous lancez les d�s et avancez de leur total. A chaque lanc�, vous tomberez sur une case diff�rente et donc une action diff�rente � remplir.</p>
	<p>Vous aurez comme action � remplir :</p>
	<ul>
		<li>Acheter une propri�t� (terrain, gare ou compagnie)</li>
		<li>Payer un loyer</li>
		<li>Construire sur un terrain</li>
		<li>Vendre une maison</li>
		<li>Mettre en hypoth�que une propri�t� (terrain, gare ou compagnie)</li>
		<li>Payer une taxe</li>
		<li>Tirer une carte "Chance" ou "Caisse de communaut�"</li>
		<li>Aller en prison</li>
		<li>Rammasser le contenu du Parking</li>
	</ul>
<h2>Les terrains</h2>
	<p>Si la propri�t� est libre, vous aurez la possibilit� de l'acheter.</p>
	<p>Par contre, si elle est d�ja en possession d'un autre joueur, vous devrez lui payer un loy�. Le loy� d�pend de son �tat, si il y a ou non une ou plusieurs maison construite dessus.</p>
	<p>Sur chaque passage sur un de ces terrain, vous pourrez construire une maison. Vous ne pouvez construire que 4 maisons ou 1 hotel (�quivalent � 5 maisons). Vous pouvez, via le menu immobilier vendre des maisons � la moiti� de son prix de construction.</p>
<h2>Les Gares</h2>
	<p>Les gares sont comme les terrains, soit vous les achetez, soit vous payez un loy�. Une petite particularit� pour les loy�s des gares, c'est en fonction du nombre de gare en possession par le m�me propri�taire.</p>
	<p>Vous ne pouvez pas construire de maison sur une gare.</p>
<h2>Les compagnies</h2>
	<p>Les compagnies sont comme les terrains, soit vous les achetez, soit vous payez un loy�. Une petite particularit� pour les loy�s des compagnies, c'est en fonction du total des d�s et du nombre de compagnie en possession par le m�me propri�taire.</p>
	<p>Vous ne pouvez pas construire de maison sur une compagnie.</p>
<h2>Hypoth�que</h2>
	<p>Vous pouvez mettre en hypoth�que une propri�t� (terrain, gare ou compagnie). Cette hypoth�que vous rapportera la moiti� de son prix d'achat.</p>
	<p>Lorsqu'une propri�t� est hypoth�qu�e, vous ne touchez plus de loyer.</p>
	<p>Vous pouvez, �galement, lever l'hypoth�que d'une propri�t� en rembourssant l'hypoth�qu� major�e de 10%.</p>
<h2>Parking</h2>
	<p>A chaque fois que vous devrez payer une taxe, une carte "Chance" ou "Caisse de communaut�", vous payerez au parking. Par jour d'innactivit�e, vous payerez 10� au parking</p>
	<p>Quand vous tomberez sur la case "Parking", vous gagnerez cette somme.</p>
<h2>Case D�part</h2>
	<p>A Chaque passage par la case "D�part", vous toucherez 200�. Par contre si vous vous y arr�tez, vous toucherez 400�.</p>
<h2>Prison</h2>
	<p>Quand vous �tes en prison, vous avez 4 possibilit�s d'en sortir.</p>
	<ol>
		<li>Vous patientez le temp de votre peine (3 lanc�s de d�s).</li>
		<li>Vous obtenez un double au lanc� de d�s.</li>
		<li>Vous payez une caution dont le montant est <?php global $caution;echo $caution;?>�.</li>
		<li>Vous utilisez l'une de vos cartes de Sortie de Prison (re�ue par les cartes Chance ou Caisse de Communaut�).</li>
	</ol>
	<p>Pour votre information, quand vous �tes en prison, vous ne touchez aucun loy�.</p>
<h2>Les transactions</h2>
	<p>Sous le menu "transactions", vous verrez toutes les propri�t�es en cours de d'�change entre joueur. A vrai dire, ce n'est pas un �change mais une vente dont le prix est fix� par le vendeur.</p>
	<p>Le vendeur peut vendre une propri�t� contenant d�j� des maisons. Bien sur il perd toutes ses maisons �galement. Au vendeur � bien �valuer le prix de vente.</p>
<h2>Particularit�s</h2>
	<ul>
		<li>Par jour d'inactivit�e, vous perdez 10�. Ces 10� sont vers�s au parking.</li>
		<li>Si vous �tes ruin�s, vous devez jamais attendre que la partie soit totalement termin�e pour recommencer.</li>
	</ul>
<h2>Gagner</h2>
	<p>Vous gagnerez quand vous aurez achet� toutes les propri�t�s (terrains, gares et compagnies).</p>
	<p>OU</p>
	<p>Quand un des autres joueur aura �t� ruin� <?php global $nb_max_ruine;echo $nb_max_ruine;?> fois.</p>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>