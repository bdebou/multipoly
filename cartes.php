<?php
function tirer_carte_caisse_communaute(){
	$txt_history_caisse = 'Caisse de communauté : ';
	$_SESSION['txt_carte']='Caisse de Communauté : <br />';
	switch(mt_rand(0,11)){
		case 0:	//-----------------------OK
			//Erreur des contributions
			$montant=100;$action='caisseP';
			$_SESSION['txt_carte'].= "Erreur des contribution. Vous touchez $montant €.";
			$txt_history_caisse.='Erreur des contributions';
			break;
		case 1:	//-----------------------OK
			//Frais de restauration batiments
			$montant=0;$action='caisseM';
			$prix_par_maison=25;
			$prix_par_hotel=100;
			$_SESSION['txt_carte'].="Payez pour frais de rénovation de vos maisons et hotels.
			<ul><li>$prix_par_maison € par maison</li>
			<li>$prix_par_hotel € par hotel</li></ul>";
			$sql = "SELECT nb_maison FROM table_carte_propriete WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$nb_maison=0;$nb_hotel=0;
			while ($row = mysql_fetch_assoc($requete)){
				if($row['nb_maison']<5){$nb_maison+=$row['nb_maison'];}
				else{$nb_hotel++;}
			}
			$montant += $prix_par_maison * $nb_maison;
			$montant += $prix_par_hotel * $nb_hotel;
			//$montant = -$montant;
			$txt_history_caisse.='Restauration batiments';
			break;
		case 2:	//-----------------------OK
			// frais de médecin
			$montant=50;$action='caisseM';
			$_SESSION['txt_carte'].="Payez les frais de médecin de $montant €";
			//$montant= -$montant;
			$txt_history_caisse.='Frais de médecin';
			break;
		case 3:	//-----------------------OK
			//Carte Sortie de Prison
			$action='caisseP';$montant=0;
			$_SESSION['txt_carte'].="Vous avez tiré une carte vous permettant de sortir de prison.";
			$_SESSION['sortir_prison_communauté']=true;
			$_SESSION['check_paye']=true;
			$txt_history_caisse.='Carte Sortie de Prison';
			break;
		case 4:	//-----------------------OK
			//Retourner rue Grand, Dinant
			$action='caisseD';$montant=0;
			$_SESSION['txt_carte'].='Retournez rue Grande à Dinant.';
			$_SESSION['position']=2;
			$txt_history_caisse.='Retournez Rue Grande à Dinant';
			break;
		case 5:	//-----------------------OK
			//Allez en prison
			$action='prisonIN';$montant=0;
			$_SESSION['txt_carte'].="Allez directement en prison sans passer par la case Départ.";
			$_SESSION['prison']=true;$_SESSION['position']=11;$_SESSION['replay']=false;
			$txt_history_caisse.='Allez en prison';
			break;
		case 6:	//-----------------------OK
			//Allez à la case départ
			$action='caisseD';$montant=0;
			$_SESSION['txt_carte'].="Allez à la Case Départ. Vous toucherez la somme due.";
			$_SESSION['position']=1;
			$txt_history_caisse.='Allez à la case départ';
			break;
		case 7:	//-----------------------OK
			//Vous avez gagné le 2ème prix de beauté
			$montant=50;$action='caisseP';
			$_SESSION['txt_carte'].="Vous avez gagné le 2ème prix de beauté. Recevez $montant €.";
			$txt_history_caisse.='Prix de beauté';
			break;
		case 8:	//-----------------------OK
			//taxe sur la richesse 10%
			$montant=0;$action='caisseM';
			$taxe=10;	//en %
			$montant=ceil($_SESSION['argent'] * ($taxe / 100));
			$_SESSION['txt_carte'].="Vous avez payé un taxe sur la richesse de $taxe %, ce qui a donné : $montant €.";
			//$montant = -$montant;
			$txt_history_caisse.='Taxe sur richesse';
			break;
		case 9:	//-----------------------OK
			//Frais de garderie
			$montant=100;$action='caisseM';
			$_SESSION['txt_carte'].="Vous avez payé des frais de garderie de $montant €.";
			//$montant = -$montant;
			$txt_history_caisse.='Frais de garderie';
			break;
		case 10:	//-----------------------OK
			//Changement parc informatique
			$prix=5;$action='caisseM';
			$_SESSION['txt_carte'].='Payez pour frais de remplacement du parc IT. Vous avez trouvé un bon plan '.$prix.'€ par maison et '.(10*$prix).'€ par hotel.';
			$sql = "SELECT nb_maison FROM table_carte_propriete WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$nb_maison=0;$nb_hotel=0;
			while ($row = mysql_fetch_assoc($requete)){
				if($row['nb_maison']<5){$nb_maison+=$row['nb_maison'];}
				else{$nb_hotel++;}
			}
			$montant += $prix * $nb_maison;
			$montant += ($prix * 10) * $nb_hotel;
			//$montant = -$montant;
			$txt_history_caisse.='Remplacement Parc IT';
			break;
		case 11:	//-----------------------OK
			//Cadastre
			$montant=0;$action='caisseM';
			//Les gares
			$sql = "SELECT code, prix_location FROM table_gare WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			while ($row = mysql_fetch_assoc($requete)) {
				$montant += $row['prix_location']*(pow(2,(mysql_num_rows($requete)-1)));
			}
			//les compagnies
			$sql = "SELECT code FROM table_compagnie WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			while ($row = mysql_fetch_assoc($requete)) {
				switch (mysql_num_rows($requete)){
					case 1: $montant += ($_SESSION['result_des_1']+$_SESSION['result_des_2'])*4;break;
					case 2: $montant += ($_SESSION['result_des_1']+$_SESSION['result_des_2'])*10;break;
				}
			}
			//les propritétés
			$sql = "SELECT * FROM table_carte_propriete WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			while ($row = mysql_fetch_assoc($requete)) {
				switch($row['nb_maison']){
					case '0':$montant += $row['prix_location'];break;
					case '1':$montant += $row['prix_1_maison'];break;
					case '2':$montant += $row['prix_2_maison'];break;
					case '3':$montant += $row['prix_3_maison'];break;
					case '4':$montant += $row['prix_4_maison'];break;
					case '5':$montant += $row['prix_hotel'];break;
				}
			}
			$montant = ceil($montant / $_SESSION['nb_propriete']);
			$_SESSION['txt_carte'].="Payez votre cadastre qui équivaut au loyé moyen demandé actuellement sur chacun de vos terrains. Dans votre cas, cela vaut $montant €.";
			//$montant = -$montant;
			$txt_history_caisse .= 'Paiement du cadastre';
			break;
	}
	if(!$_SESSION['check_paye']){
		switch($action){
			case 'caisseM':
				$_SESSION['txt_carte'].="<br />Vous avez payé un montant de : $montant €";
				$_SESSION['argent']-=$montant;
				$sql="UPDATE table_plateau SET prix=prix+$montant WHERE num_case='21'";
				mysql_query($sql) or die ( mysql_error() );
				add_history('Parking',$montant,'parkingP','',$_SESSION['login'].' : '.$txt_history_caisse);
				break;
			case 'caisseP':
				$_SESSION['txt_carte'].="<br />Vous avez touché un montant de : $montant €";
				$_SESSION['argent']+=$montant;
				break;
			default: $_SESSION['txt_carte'].='<br />Vous n\'avez rien payé.';break;
		}
	}
	update_tb_utilisateur(false, true);
	add_history($_SESSION['login'],$montant,$action,'',$txt_history_caisse);
	//redir('index.php');
}
function tirer_carte_chance(){
	$txt_history_chance = 'Chance : ';
	$_SESSION['txt_carte']='Chance : <br />';
	switch(mt_rand(0,18)){
		case 0:	//-----------------------OK
			//Erreur de la banque
			$montant=50;$action='chanceP';
			$_SESSION['txt_carte'].="Erreur de la banque en votre faveur. Recevez $montant €.";
			$txt_history_chance.='Erreur de banque';
			break;
		case 1:	//-----------------------OK
			//Frais de restauration batiments
			$montant=0;$action='chanceM';
			$prix_par_maison=40;
			$prix_par_hotel=120;
			$_SESSION['txt_carte'].="Payez pour frais de rénovation de vos maisons et hotels.
			<ul><li>$prix_par_maison € par maison</li>
			<li>$prix_par_hotel € par hotel</li></ul>";
			$sql = "SELECT nb_maison FROM table_carte_propriete WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$nb_maison=0;$nb_hotel=0;
			while ($row = mysql_fetch_assoc($requete)) {
				if($row['nb_maison']<5){$nb_maison+=$row['nb_maison'];}
				else{$nb_hotel++;}
			}
			$montant+=$prix_par_maison * $nb_maison;
			$montant+=$prix_par_hotel * $nb_hotel;
			//$montant=-$montant;
			$txt_history_chance.='Restauration batiments';
			break;
		case 2:	//-----------------------OK
			//Allez en prison
			$action='prisonIN';$montant=0;
			$_SESSION['txt_carte'].='Allez directement en prison sans passer par la case Départ.';
			$_SESSION['prison']=true;$_SESSION['position']=11;$_SESSION['replay']=false;
			$txt_history_chance.='Allez en prison';
			break;
		case 3:	//-----------------------OK
			//Allez Grand Place Mons
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Allez Grand Place à Mons. Si vous passez par la Case Départ, vous toucherez la somme due.';
			if($_SESSION['position']>25){
				$_SESSION['argent']+=200;$_SESSION['nb_tour']++;
				add_history($_SESSION['login'],200,'départ', '', 'est passé par Départ');
			}
			$_SESSION['position']=25;
			$txt_history_chance.='Allez Grand Place Mons';
			break;
		case 4:	//-----------------------OK
			//Allez rue de Diekirch Arlon
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Allez rue de Diekirch à Arlon. Si vous passez par la Case Départ, vous toucherez la somme due.';
			if($_SESSION['position']>12){
				$_SESSION['argent']+=200;$_SESSION['nb_tour']++;
				add_history($_SESSION['login'],200,'départ', '', 'est passé par Départ');
			}
			$_SESSION['position']=12;
			$txt_history_chance.='Allez rue de Diekirch Arlon';
			break;
		case 5:	//-----------------------OK
			//allez rue Neuve Bruxelles
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Allez rue Neuve à Bruxelles. Si vous passez par la Case Départ, vous toucherez la somme due.';
			$_SESSION['position']=40;
			$txt_history_chance.='Allez rue Neuve Bruxelles';
			break;
		case 6:	//-----------------------OK
			//Allez gare centrale
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Allez à la Gare Centrale. Si vous passez par la Case Départ, vous toucherez la somme due.';
			if($_SESSION['position']>16){
				$_SESSION['argent']+=200;$_SESSION['nb_tour']++;
				add_history($_SESSION['login'],200,'départ', '', 'est passé par Départ');
			}
			$_SESSION['position']=16;
			$txt_history_chance.='Allez gare centrale';
			break;
		case 7:	//-----------------------OK
			//Allez à la case départ
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Allez à la Case Départ. Vous toucherez la somme due.';
			$_SESSION['position']=1;
			$txt_history_chance.='Allez à la case départ';
			break;
		case 8:	//-----------------------OK
			//Amende pour exces de vitesse
			$montant=25;$action='chanceM';
			$_SESSION['txt_carte'].="Amende pour exces de vitesse. Payez $montant €.";
			//$montant=-$montant;
			$txt_history_chance.='Amende pour exces de vitesse';
			break;
		case 9:	//-----------------------OK
			//la banque vous verse un dividende
			$montant=100;$action='chanceP';
			$_SESSION['txt_carte'].="La banque vous verse un dividende. Recevez  $montant €.";
			$txt_history_chance.='la banque vous verse un dividende';
			break;
		case 10:	//-----------------------OK
			//Payez les frais de scolarité
			$montant=150;$action='chanceM';
			$_SESSION['txt_carte'].="Payez les frais de scolarité. Payez $montant €.";
			//$montant=-$montant;
			$txt_history_chance.='Payez les frais de scolarité';
			break;
		case 11:	//-----------------------OK
			//Carte Sortie de Prison
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Vous avez tiré une carte vous permettant de sortir de prison.';
			$_SESSION['sortir_prison_chance']=true;
			$_SESSION['check_paye']=true;
			$txt_history_chance.='Carte Sortie de Prison';
			break;
		case 12:	//-----------------------OK
			//Vous êtes imposé pour les réparations de voirie
			$montant=0;$action='chanceM';
			$prix_par_maison=25;
			$prix_par_hotel=80;
			$_SESSION['txt_carte'].="Payez pour frais de rénovation de voiries.
			<ul><li>$prix_par_maison € par maison</li>
			<li>$prix_par_hotel € par hotel</li></ul>";
			$sql = "SELECT nb_maison FROM table_carte_propriete WHERE proprietaire='".$_SESSION['login']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$nb_maison=0;$nb_hotel=0;
			while ($row = mysql_fetch_assoc($requete)) {
				if($row['nb_maison']<5){$nb_maison+=$row['nb_maison'];}
				else{$nb_hotel++;}
			}
			$montant+=$prix_par_maison * $nb_maison;
			$montant+=$prix_par_hotel * $nb_hotel;
			//$montant=-$montant;
			$txt_history_chance.='Réparation de voiries';
			break;
		case 13:	//-----------------------OK
			//Votre immeuble et votre prêt rapportent.
			$montant=200;$action='chanceP';
			$_SESSION['txt_carte'].="Votre immeuble et votre prêt rapportent. Recevez $montant €.";
			$txt_history_chance.='Rapport de vos immeubles';
			break;
		case 14:	//-----------------------OK
			//Reculez de 3 cases
			$action='chanceD';$montant=0;
			$_SESSION['txt_carte'].='Vous avez reculé de 3 cases.';
			$_SESSION['position']-=3;
			//$_SESSION['check_paye']=true;
			$txt_history_chance.='Reculez de 3 cases';
			break;
		case 15:	//-----------------------OK
			//Amende pour ivresse
			$montant=30;$action='chanceM';
			$_SESSION['txt_carte'].="Amende pour ivrese. Payez $montant €.";
			//$montant=-$montant;
			$txt_history_chance.='Amende pour ivresse';
			break;
		case 16:	//-----------------------OK
			//Vous avez gagné le prix de mots croisés
			$montant=100;$action='chanceP';
			$_SESSION['txt_carte'].="Vous avez gagné le prix de mots croisés. Recevez $montant €.";
			$txt_history_chance.='Prix de mots croisés';
			break;
		case 17:	//-----------------------OK
			//anniversaire
			$montant=0;$action='chanceP';
			$montant_a=10;
			$_SESSION['txt_carte'].="C'est votre anniversaire. Chaque joueur vous donne $montant_a €.";
			$sql = "SELECT login, argent FROM table_utilisateur";
			$requete = mysql_query($sql) or die (mysql_error());
			while ($row = mysql_fetch_assoc($requete)) {
				if($row['login']==$_SESSION['login']){
					$montant = (mysql_num_rows($requete) - 1) * $montant_a;
					$_SESSION['argent'] += $montant;
					$txt_history_chance .= 'Bon anniversaire!';
				}else{
				$sql = "UPDATE table_utilisateur SET argent=argent-$montant_a WHERE login='".$row['login']."'";
				mysql_query($sql) or die ( mysql_error() );
				add_history($row['login'],$montant_a,'chanceM', '', 'Chance : '.$_SESSION['login'].' a eu son anniversaire');
				}
			}
			break;
		case 18:	//-----------------------OK
			//Lotterie
			$montant=mt_rand(1,20)*pow(10,mt_rand(1,2));$action='chanceP';
			$_SESSION['txt_carte'].="Vous avez gagné à la lotterie. Recevez $montant €.";
			$txt_history_chance.='Lotterie';
			break;
	}
	if(!$_SESSION['check_paye']){
		switch($action){
			case 'chanceP':
				$_SESSION['txt_carte'].="<br />Vous avez touché un montant de : $montant €";
				$_SESSION['argent']+=$montant;
				break;
			case 'chanceM':
				$_SESSION['txt_carte'].="<br />Vous avez payé un montant de : $montant €";
				$_SESSION['argent']-=$montant;
				$sql="UPDATE table_plateau SET prix=prix+$montant WHERE num_case='21'";
				mysql_query($sql) or die ( mysql_error() );
				add_history('Parking',$montant,'parkingP','',$_SESSION['login'].' : '.$txt_history_chance);
				break;
			default: $_SESSION['txt_carte'].='<br />Vous n\'avez rien payé.';break;
		}
	}
	update_tb_utilisateur(false, true);
	add_history($_SESSION['login'],$montant,$action,'',$txt_history_chance);
	//redir('index.php');
}
?>