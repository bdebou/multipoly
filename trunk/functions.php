<?php
function affiche_plateau(){
	$sql="SELECT * FROM table_plateau";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<div class="case">';
		echo '<div style="width: 65px;height: 20px; ';
		switch ($row['case_type']){
			case 'propriete':
				$sql = "SELECT couleur, ville, nb_maison, proprietaire, hypotheque FROM table_carte_propriete WHERE code='".$row['link_carte_propriete']."'";
				$requete_propriete = mysql_query($sql) or die (mysql_error());
				$result_carte_propriete = mysql_fetch_array($requete_propriete, MYSQL_ASSOC);
				echo 'background: #'.$result_carte_propriete['couleur'].';';
				if($result_carte_propriete['hypotheque']){echo 'filter:alpha(opacity=50);opacity:0.5;';}
				echo '">'.$result_carte_propriete['ville'].'</div>';
				echo '<div style="width:60px; height:11px; margin-left:auto; margin-right:auto;">';
				if(!empty($result_carte_propriete['proprietaire'])){
					if($result_carte_propriete['nb_maison']==5){
						echo '<div style="height: 5px; width: 40px; margin: 2px; float: left; background: #ff0000; border: 1px #000000 solid;"></div>';
					}else{
						for($i=1;$i<=$result_carte_propriete['nb_maison'];$i++){
							echo '<div style="height: 5px; width: 5px; float: left; border: 1px #000000 solid; margin: 2px; background: #008000;"></div>';
						}
					}
					echo '<p class="loye">Loyé = '.$row['prix'].'€</p>';
				}else{
					echo '<p class="avendre">A Vendre</p>';
				}
				echo '</div>';
				break;
			case 'gare':
				$sql = "SELECT nom, proprietaire, hypotheque FROM table_gare WHERE code='".$row['link_carte_propriete']."'";
				$requete_gare = mysql_query($sql) or die (mysql_error());
				$result_carte_gare = mysql_fetch_array($requete_gare, MYSQL_ASSOC);
				echo 'background: #000000;color: #ffffff;font-size:75%;"';
				if($result_carte_gare['hypotheque']){echo 'filter:alpha(opacity=50);opacity:0.5;';}
				echo '>'.$result_carte_gare['nom'].'</div>';
				echo '<div style="width:60px; height:11px; margin-left:auto; margin-right:auto;">';
				if(empty($result_carte_gare['proprietaire'])){
					echo '<p class="avendre">A Vendre</p>';
				}else{
					echo '<p class="loye">Loyé = '.$row['prix'].'€</p>';
				}
				echo '</div>';
				break;
			case 'communaute':echo 'text-align: center;font-size:75%;">Caisse de Communauté</div>';break;
			case 'chance':echo '">Chance ?</div>';break;
			case 'taxe':echo '">Taxe</div>';echo $row['prix'].'€';break;
			case 'depart':echo '">Départ</div>';break;
			case 'prison':echo '">Prison</div>';break;
			case 'visite':echo '">Visite</div>Prison';break;
			case 'compagnie':
				$sql = "SELECT shortname, proprietaire FROM table_compagnie WHERE code='".$row['link_carte_propriete']."'";
				$requete_cie = mysql_query($sql) or die (mysql_error());
				$result_carte_cie = mysql_fetch_array($requete_cie, MYSQL_ASSOC);
				if($result_carte_gare['hypotheque']){echo 'filter:alpha(opacity=50);opacity:0.5;';}
				echo '">'.$result_carte_cie['shortname'].'</div>';
				if(empty($result_carte_cie['proprietaire'])){
					echo '<div style="width:60px; height:11px; margin-left:auto; margin-right:auto;"><p class="avendre">A Vendre</p></div>';
				}
				break;
			case 'parking': echo '"><a href="./parking.php">'.$row['name'].'</a></div>'.$row['prix'].'€';break;
		}
		if($_SESSION['position']==$row['num_case']){
			echo '<div style="background: #0080c0;width: 20px;height: 20px; margin-top:10px; margin-left:auto; margin-right:auto;"></div>';
		}else{
			$sql="SELECT position FROM table_utilisateur";
			$request = mysql_query($sql) or die (mysql_error());
			while($rowU = mysql_fetch_assoc($request)){
				if($rowU['position']==$row['num_case'] and $rowU['position']!=$_SESSION['position']){
					echo '<div style="background: #c0c0c0;width: 20px;height: 20px; margin-top: 10px; margin-left:auto;margin-right:auto"></div>';
					break;
				}
			}
		}
		echo '</div>';
	}
}
function affiche_proprietes(){
	include('config.php');
	$sql="SELECT * FROM table_carte_propriete WHERE proprietaire='".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<div style="margin:1px; float:left; width:65px; height:20px; text-align:center; background:#'.$row['couleur'].';">'.$row['ville'].'</div>';
	}
	$sql="SELECT * FROM table_gare WHERE proprietaire='".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<div style="margin:1px; font-size:75%; float:left; width:65px; height:20px; text-align:center; background:#000000; color:#ffffff;">'.$row['nom'].'</div>';
	}
	$sql="SELECT * FROM table_compagnie WHERE proprietaire='".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		echo '<div style="margin:1px; float:left; width:65px; height:20px; text-align:center; background:#ffffff; color:#000000;">'.$row['shortname'].'</div>';
	}
}
function lance_des(){
	global $check_achat;$check_achat=false;
	global $check_loye;$check_loye=false;
	global $check_construire;$check_construire=false;
	$_SESSION['step_achat']=0;$_SESSION['step_construction']=0;
	$_SESSION['check_paye']=false;
	$_SESSION['txt_old']=null;
	$_SESSION['txt_carte']=null;
	$des=array(mt_rand(1,6),mt_rand(1,6));
	update_tb_utilisateur(false, true);
	add_history($_SESSION['login'],'0','des','','lancé de dés : '.$des['0'].' et '.$des['1']);
	return $des;
}
function check_des($ar_des){
	$avance=false;$check=false;$txt_history=null;
	if($ar_des['0']==$ar_des['1']){
		$_SESSION['nb_lance_des']++;
		if($_SESSION['prison']==1){
			$_SESSION['prison']=false;$_SESSION['replay']=false;$avance=false;$_SESSION['nb_lance_des']=0;$_SESSION['nb_prison']=0;
			$check=true;$txt_history='a fait un double et sort de prison';$action_prison='prisonOUT';
		}elseif($_SESSION['nb_lance_des']==3){
			$_SESSION['replay']=false;
			$_SESSION['prison']=true;$_SESSION['position']=11;$avance=false;
			$check=true;$txt_history='a fait 3 doubles et va en prison';$action_prison='prisonIN';
		}else{$_SESSION['replay']=true;$avance=true;}
		
	}else{
		if($_SESSION['prison']==1){
			$_SESSION['nb_prison']++;
			if($_SESSION['nb_prison']==3){
				$_SESSION['nb_prison']=0;$_SESSION['prison']=false;
				$check=true;$txt_history='a passé 3 tours et sort de prison';$action_prison='prisonOUT';
			}
			$avance=false;
		}else{
			$_SESSION['replay']=false;
			$_SESSION['nb_lance_des']=0;
			$avance=true;
		}
	}
	update_tb_utilisateur(false);
	if($check){add_history($_SESSION['login'],0,$action_prison,'',$txt_history);}
	return $avance;
}
function affiche_detail_case(){
	date_default_timezone_set('Europe/Brussels');
	global $check_achat, $check_loye, $check_construire, $check_hypotheque;
	$check_construire=false;
	$sql="SELECT * FROM table_plateau WHERE num_case='".$_SESSION['position']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	$result_case = mysql_fetch_array($requete, MYSQL_ASSOC);
	echo '<div style="width:200px; height:350px; border:2px #000000 solid; float:left; margin:5px; padding: 2px;clear:left;">';
	echo '<div style="height:30px; text-align:center; font-weight:bold; padding-top:5px; font-variant:small-caps; border:1px #000000 solid;';
	switch ($result_case['case_type']){
		case 'propriete':
			$sql = "SELECT * FROM table_carte_propriete WHERE code='".$result_case['link_carte_propriete']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_carte_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
			echo ' background: #'.$result_carte_propriete['couleur'].';">'.$result_carte_propriete['ville'].'</div>';
			echo '<div style="width: 106px; margin-left:auto; margin-right:auto;">';
			if($result_carte_propriete['nb_maison']==5){echo '<div class="hotel"></div>';}
			else{for($i=1;$i<=$result_carte_propriete['nb_maison'];$i++){echo '<div class="maison"></div>';}}
			echo '</div>';
			echo '<p style="text-align:center; clear:both;">'.$result_carte_propriete['adresse'].'<br />'.$result_carte_propriete['prix_achat'].'€</p>';
			echo '<p class="propriete">Prix :</p>';
			echo '<ul><li>Loyer = '.$result_carte_propriete['prix_location'].'€</li>';
			echo '<li>Construction : '.$result_carte_propriete['prix_construction'].'€</li>';
			echo '<li>1 maison = '.$result_carte_propriete['prix_1_maison'].'€</li>';
			echo '<li>2 maisons = '.$result_carte_propriete['prix_2_maison'].'€</li>';
			echo '<li>3 maisons = '.$result_carte_propriete['prix_3_maison'].'€</li>';
			echo '<li>4 maisons = '.$result_carte_propriete['prix_4_maison'].'€</li>';
			echo '<li>Hotel = '.$result_carte_propriete['prix_hotel'].'€</li></ul>';
			echo '<p class="propriete">Propriétaire :</p>';
			if(empty($result_carte_propriete['proprietaire'])){echo '<p>Libre</p>';$check_achat=true;}
			else{
				echo '<p>'.$result_carte_propriete['proprietaire'].'</p>';
				if(!$result_carte_propriete['hypotheque']){
					$check_hypotheque=false;
					if($result_carte_propriete['proprietaire']!=$_SESSION['login']){$check_loye=true;}
					else{$check_construire=true;}
				}else{$check_hypotheque=true;}
			}
			break;
		case 'gare':
			$sql = "SELECT * FROM table_gare WHERE code='".$result_case['link_carte_propriete']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_carte_gare = mysql_fetch_array($requete, MYSQL_ASSOC);
			echo ' background: #000000;color: #ffffff;">'.$result_carte_gare['nom'].'</div>';
			echo '<p style="text-align:center;">'.$result_carte_gare['prix_achat'].'€</p>';
			echo '<p class="propriete">Prix :</p><p>Le montant des loyer dépend du nombre de gare en votre possession.</p>';
			echo '<ul><li>1 gare = '.$result_carte_gare['prix_location'].'€</li>';
			echo '<li>2 gares = '.($result_carte_gare['prix_location']*2).'€</li>';
			echo '<li>3 gares = '.($result_carte_gare['prix_location']*4).'€</li>';
			echo '<li>4 gares = '.($result_carte_gare['prix_location']*8).'€</li></ul>';
			echo '<p class="propriete">Propriétaire :</p>';
			if(empty($result_carte_gare['proprietaire'])){echo '<p>Libre</p>';$check_achat=true;}
			else{
				echo '<p>'.$result_carte_gare['proprietaire'].'</p>';
				if(!$result_carte_gare['hypotheque']){
					$check_hypotheque=false;
					if($result_carte_gare['proprietaire']!=$_SESSION['login']){$check_loye=true;}
				}else{$check_hypotheque=true;}
			}
			break;
		case 'communaute':
			echo ' ">Caisse de Communauté</div>';
			
			//if(!$_SESSION['check_paye']){
			if(empty($_SESSION['txt_carte'])){
				include_once('cartes.php');
				tirer_carte_caisse_communaute();
			}
			//echo htmlspecialchars_decode($_SESSION['txt_carte'], ENT_QUOTES);
			echo '<p>'.htmlspecialchars_decode($_SESSION['txt_carte'], ENT_QUOTES).'</p>';
			break;
		case 'chance':
			echo '">Chance ?</div>';
			
			//if(!$_SESSION['check_paye']){
			if(empty($_SESSION['txt_carte'])){
				include_once('cartes.php');
				tirer_carte_chance();
			}
			//echo htmlspecialchars_decode($_SESSION['txt_carte'], ENT_QUOTES);
			echo '<p>'.$_SESSION['txt_carte'].'</p>';
			break;
		case 'taxe': //OK
			echo '">Taxe '.$result_case['prix'].'€</div>';
			echo $result_case['description'];
			if(!$_SESSION['check_paye']){
				$_SESSION['check_paye']=true;
				$_SESSION['argent']-=$result_case['prix'];
				$sql = "SELECT prix FROM table_plateau WHERE num_case='21'";
				$requete = mysql_query($sql) or die (mysql_error());
				$result_parking = mysql_fetch_array($requete, MYSQL_ASSOC);
				update_tb_utilisateur(false);
				$sql="UPDATE table_plateau SET prix=prix+'".$result_case['prix']."' WHERE num_case='21'";
				mysql_query($sql) or die ( mysql_error() );
				add_history($_SESSION['login'],$result_case['prix'],'taxe','','a payé une taxe');
				add_history('Parking',$result_case['prix'],'parkingP','',$_SESSION['login'].' a payé une taxe');
			}
			break;
		case 'depart': //OK
			echo '">Départ</div>';
			if($_SESSION['nb_tour']>0 and !$_SESSION['check_paye']){
				$_SESSION['check_paye']=true;
				$_SESSION['argent']+=400;
				update_tb_utilisateur(false);
				add_history($_SESSION['login'],400,'départ','','arreté sur Départ');
			}
			echo $result_case['description'];
			break;
		case 'prison':
			echo '">Prison</div>';
			echo '<p>Allez directement en prison sans passer par la case Départ.</p>';
			$_SESSION['prison']=true;$_SESSION['position']=11;$_SESSION['replay']=false;
			update_tb_utilisateur(false);
			add_history($_SESSION['login'],0,'prisonIN','','Allez en prison');
			break;
		case 'visite':
			echo '">'.$result_case['name'].'</div>';
			if(!$_SESSION['prison']){echo '<p>'.$result_case['description'].'</p>';}
			else{echo '<p>Vous êtes en prison</p>';}
			break;
		case 'compagnie':
			$sql = "SELECT * FROM table_compagnie WHERE code='".$result_case['link_carte_propriete']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_carte_cie = mysql_fetch_array($requete, MYSQL_ASSOC);
			echo '">'.$result_carte_cie['nom'].'</div>';
			echo '<p style="text-align:center;">'.$result_carte_cie['prix_achat'].'€</p>';
			echo '<p class="propriete">Prix :</p><p>Le montant des loyer dépend du total des dés et si vous possédez 1 ou 2 compagnies.</p>';
			echo '<ul><li>1 compagnie = (total des dés) x 4</li>';
			echo '<li>2 compagnies = (total des dés) x 10</li></ul>';
			echo '<p class="propriete">Propriétaire :</p>';
			if(empty($result_carte_cie['proprietaire'])){echo '<p>Libre</p>';$check_achat=true;}
			else{
				echo '<p>'.$result_carte_cie['proprietaire'].'</p>';
				if(!$result_carte_cie['hypotheque']){
					$check_hypotheque=false;
					if($result_carte_cie['proprietaire']!=$_SESSION['login']){$check_loye=true;}
				}else{$check_hypotheque=true;}
			}
			break;
		case 'parking': //NOK
			echo '">'.$result_case['name'].'</div>';
			if(!$_SESSION['check_paye']){
				$_SESSION['check_paye']=true;
				$sql = "SELECT prix FROM table_plateau WHERE num_case='21'";
				$requete = mysql_query($sql) or die (mysql_error());
				$result_parking = mysql_fetch_array($requete, MYSQL_ASSOC);
				$_SESSION['argent']+=$result_parking['prix'];
				$_SESSION['txt_old']='<p>Le parking valait : <br />'.$result_parking['prix'].'€.</p>';
				update_tb_utilisateur(false);
				$sql="UPDATE table_plateau SET prix='0' WHERE num_case='21';";
				mysql_query($sql) or die (mysql_error());
				add_history($_SESSION['login'],$result_parking['prix'],'parking','','a empoché le Parking');
				add_history('Parking',$result_parking['prix'],'parkingM','',$_SESSION['login'].' a empoché le Parking');
			}
			//echo html_entity_decode($_SESSION['txt_old'], ENT_QUOTES);
			break;
	}
	echo '</div>';
	if(isset($result_carte_propriete['last_action'])){
		return array($result_case['link_carte_propriete'],$result_carte_propriete['last_action']);
	}else{
		return array($result_case['link_carte_propriete'],0);
	}
}
function avance_pion($des){
	$_SESSION['position'] += $des['0']+$des['1'];
	if ($_SESSION['position']>40){
		$_SESSION['position'] -=40;
		$_SESSION['nb_tour']++;
		if($_SESSION['position']>1){
			$_SESSION['argent']+=200;
			add_history($_SESSION['login'],200,'départ', '', 'est passé par Départ');
		}
	}
	update_tb_utilisateur(false);
}
function date_diff($start, $end="NOW", $attente){
	$sdate = strtotime($start);
	$edate = strtotime($end);
	$temp = $edate - $sdate;
	$time = $attente - $temp;
	$timeshift=array('days'=>0,'hrs'=>0,'min'=>0,'sec'=>0,'full'=>'');
	if($time>=0 && $time<=59) {
		// Seconds
		$timeshift['full'] = $time.' seconds ';
		$timeshift['sec'] = $time;
	}elseif($time>=60 && $time<=3599) {
		// Minutes + Seconds
		//$pmin = ($edate - $sdate) / 60;
		$pmin = $time / 60;
		$premin = explode('.', $pmin);
		$presec = $pmin-$premin[0];
		$sec = $presec*60;
		$timeshift['full'] = $premin[0].' min '.round($sec,0).' sec ';
		$timeshift['sec']=round($sec,0);
		$timeshift['min']=$premin[0];
	}elseif($time>=3600 && $time<=86399) {
		// Hours + Minutes
		//$phour = ($edate - $sdate) / 3600;
		$phour = $time / 3600;
		$prehour = explode('.',$phour);
		$premin = $phour-$prehour[0];
		$min = explode('.',$premin*60);
		if(isset($min[1])){$presec = '0.'.$min[1];}else{$presec = 0;}
		$sec = $presec*60;
		$timeshift['full'] = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
		$timeshift['sec']=round($sec,0);
		$timeshift['min']=$min[0];
		$timeshift['hrs']=$prehour[0];
	}elseif($time>=86400) {
		// Days + Hours + Minutes
		//$pday = ($edate - $sdate) / 86400;
		$pday = $time / 86400;
		$preday = explode('.',$pday);
		$phour = $pday-$preday[0];
		$prehour = explode('.',$phour*24); 
		$premin = ($phour*24)-$prehour[0];
		$min = explode('.',$premin*60);
		$presec = '0.'.$min[1];
		$sec = $presec*60;
		$timeshift['full'] = $preday[0].' days '.$prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
		$timeshift['sec']=round($sec,0);
		$timeshift['min']=$min[0];
		$timeshift['hrs']=$prehour[0];
		$timeshift['days']=$preday[0];
	}
	return $timeshift;
}
function redir($url){
	echo '<script language="javascript">';
	echo 'window.location="',$url,'";';
	echo '</script>';
}
function module_achat($propriete){
	$txt_achat=null;
	if(isset($_SESSION['step_achat'])){
	switch($_SESSION['step_achat']){
		case '0':
			echo '<p>Voulez-vous achetez ce terrain?</p>';
			echo '<form method="post"><table class="achat"><tr><td style="width: 50px;"><input type="radio" name="achat" value="oui" /> Oui</td>';
			echo '<td rowspan="2" style="width: 50px;"><input type="submit" name="btachat" value="go" /></td></tr>';
			echo '<tr><td><input type="radio" name="achat" value="non" /> Non</td></tr></table></form>';
			if(isset($_POST['btachat']) and !empty($_POST['achat'])){
				if($_POST['achat']=='oui'){$_SESSION['step_achat']=1;}
				elseif($_POST['achat']=='non'){$_SESSION['step_achat']=2;}
				unset($_POST);
				redir('index.php');
			}
			break;
		case '1':
			$strpropriete=str_split($propriete);
			switch ($strpropriete['0']){
				case 'i': 
					$str_table='table_gare';
					$sql = "SELECT nom, prix_achat, prix_location FROM ".$str_table." WHERE code='".$propriete."'";
					$requete = mysql_query($sql) or die (mysql_error());
					$result_carte_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
					$nom_propriete=$result_carte_propriete['nom'];
					break;
				case 'j': 
					$str_table='table_compagnie';
					$sql = "SELECT nom, prix_achat FROM ".$str_table." WHERE code='".$propriete."'";
					$requete = mysql_query($sql) or die (mysql_error());
					$result_carte_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
					$nom_propriete=$result_carte_propriete['nom'];
					break;
				default: 
					$str_table='table_carte_propriete';
					$sql = "SELECT ville, adresse, prix_achat, prix_location FROM ".$str_table." WHERE code='".$propriete."'";
					$requete = mysql_query($sql) or die (mysql_error());
					$result_carte_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
					$nom_propriete=$result_carte_propriete['ville'].', '.$result_carte_propriete['adresse'];
					break;
			}
			if(!$_SESSION['check_paye']){
				$_SESSION['check_paye']=true;
				$_SESSION['argent']-=$result_carte_propriete['prix_achat'];
				$_SESSION['nb_propriete']++;
				update_tb_utilisateur(false);
				$sql="UPDATE ".$str_table." SET last_action='".date('Y-m-d H:i:s')."', proprietaire='".$_SESSION['login']."' WHERE code='".$propriete."';";
				mysql_query($sql) or die (mysql_error());
				switch($str_table){
					case 'table_carte_propriete':
						$sql="UPDATE table_plateau SET prix='".$result_carte_propriete['prix_location']."' WHERE link_carte_propriete='".$propriete."'";
						mysql_query($sql) or die (mysql_error());
						break;
					case 'table_gare':
						$sql="SELECT code FROM ".$str_table." WHERE proprietaire='".$_SESSION['login']."'";
						$requete = mysql_query($sql) or die (mysql_error());
						while ($row = mysql_fetch_assoc($requete)){
							$sql="UPDATE table_plateau SET prix='".($result_carte_propriete['prix_location']*(pow(2,(mysql_num_rows($requete)-1))))."' WHERE link_carte_propriete='".$row['code']."'";
							mysql_query($sql) or die (mysql_error());
						}
						break;
				}
				add_history($_SESSION['login'],$result_carte_propriete['prix_achat'],'achat',$propriete,'a acheté '.$nom_propriete);
				redir('index.php');
			}
			$txt_achat='<p>Vous avez acheté ce terrain au prix de '.$result_carte_propriete['prix_achat'].'€.</p>';
			break;
		case '2':
			$txt_achat='<p>Vous n\'avez pas acheté ce terrain.</p>';
			break;
	}
	$_SESSION['txt_old']=$txt_achat;
	update_tb_utilisateur(false);
	}
	return $txt_achat;
}
function module_loye($propriete){
	$txt_loye=null;$montant=0;
	$strpropriete=str_split($propriete);
	if(!$_SESSION['check_paye']){
	switch ($strpropriete['0']){
		case 'i':
			$TablePropriete='table_gare';
			$sql = "SELECT nom, proprietaire, prix_location FROM table_gare WHERE code='".$propriete."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
			$sql="SELECT code FROM table_gare WHERE proprietaire='".$result_propriete['proprietaire']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_gare = mysql_fetch_array($requete, MYSQL_ASSOC);
			$montant=$result_propriete['prix_location']*(pow(2,(mysql_num_rows($requete)-1)));
			$nom_propriete=$result_propriete['nom'];
			break;
		case 'j':
			$TablePropriete='table_compagnie';
			$sql = "SELECT nom, proprietaire FROM table_compagnie WHERE code='".$propriete."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
			$sql="SELECT code FROM table_compagnie where proprietaire='".$result_propriete['proprietaire']."'";
			$requete = mysql_query($sql) or die (mysql_error());
			switch (mysql_num_rows($requete)){
				case 1: $montant=($_SESSION['result_des_1']+$_SESSION['result_des_2'])*4;break;
				case 2: $montant=($_SESSION['result_des_1']+$_SESSION['result_des_2'])*10;break;
			}
			$nom_propriete=$result_propriete['nom'];
			break;
		default:
			$TablePropriete='table_carte_propriete';
			$sql = "SELECT * FROM table_carte_propriete WHERE code='".$propriete."'";
			$requete = mysql_query($sql) or die (mysql_error());
			$result_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
			switch($result_propriete['nb_maison']){
				case '0':$montant=$result_propriete['prix_location'];break;
				case '1':$montant=$result_propriete['prix_1_maison'];break;
				case '2':$montant=$result_propriete['prix_2_maison'];break;
				case '3':$montant=$result_propriete['prix_3_maison'];break;
				case '4':$montant=$result_propriete['prix_4_maison'];break;
				case '5':$montant=$result_propriete['prix_hotel'];break;
			}
			$nom_propriete=$result_propriete['ville'].', '.$result_propriete['adresse'];
			break;
	}
	$sql="SELECT prison FROM table_utilisateur WHERE login='".$result_propriete['proprietaire']."';";
	$requete = mysql_query($sql) or die (mysql_error());
	$result_proprietaire = mysql_fetch_array($requete, MYSQL_ASSOC);
	if(!$result_proprietaire['prison']){
		$_SESSION['check_paye']=true;
		$_SESSION['argent']-=$montant;
		$sql="UPDATE table_utilisateur SET argent=argent+".$montant." WHERE login='".$result_propriete['proprietaire']."';";
		mysql_query($sql) or die (mysql_error());
		add_history($_SESSION['login'],$montant,'loyéP',$propriete,'a payé un loyé à '.$result_propriete['proprietaire'].' pour '.$nom_propriete);
		add_history($result_propriete['proprietaire'],$montant,'loyéR',$propriete,'a reçu un loyé de '.$_SESSION['login'].' pour '.$nom_propriete);
		$txt_loye='<p>Vous avez payé un loyé de '.$montant.'€ à '.$result_propriete['proprietaire'].'.</p>';
	}else{
		$_SESSION['check_paye']=true;
		$txt_loye='<p>Vous n\'avez payé aucun loyé car le propriétaire est en prison.</p>';
	}
	$_SESSION['txt_old']=$txt_loye;
	update_tb_utilisateur(false);
	$sql="UPDATE $TablePropriete SET nb_passage=nb_passage+1 WHERE code='$propriete';";
	mysql_query($sql) or die (mysql_error());
	redir('index.php');
	}
	return $txt_loye;
}
function module_construire($propriete){
	global $check_construire, $wait_construire, $wait_construire, $temp_attente, $nb_max_maison;
	$txt_construire=null;
	$strpropriete=str_split($propriete);
	if($strpropriete['0']=='i'){return;}
	$sql = "SELECT * FROM table_carte_propriete WHERE code='".$propriete."'";
	$requete = mysql_query($sql) or die (mysql_error());
	$result_propriete = mysql_fetch_array($requete, MYSQL_ASSOC);
	if(time()-strtotime($result_propriete['last_action'])>$wait_construire){
		switch($_SESSION['step_construction']){
			case '0':
				echo '<p>Vous ne pouvez construire que 1 maison à la fois toutes les '.gmdate('G',$wait_construire).' hrs. Vous avez actuellement ';
				if($result_propriete['nb_maison']<5){
					echo $result_propriete['nb_maison'].' maisons.</p>';
					if($result_propriete['nb_maison']<4){echo '<p>Voulez-vous construire une maison?</p>';}
					else{echo '<p>Voulez-vous construire un hotel?</p>';}
					echo '<form method="post"><table class="construite"><tr><td style="width: 50px;"><input type="radio" name="construire" value="oui" /> Oui</td>';
					echo '<td rowspan="2" style="width: 50px;"><input type="submit" name="btconstruire" value="go" /></td></tr>';
					echo '<tr><td><input type="radio" name="construire" value="non" /> Non</td></tr></table></form>';
					if(isset($_POST['btconstruire']) and !empty($_POST['construire'])){
						switch($_POST['construire']){
							case 'oui':$_SESSION['step_construction']=1;break;
							case 'non':$_SESSION['step_construction']=2;break;
						}
						unset($_POST);
						redir('index.php');
					}
				}else{echo 'un hotel. Donc vous ne pouvez plus rien construire.</p>';}
				break;
			case '1':
				//on construit une maison
				if(!$_SESSION['check_paye']){
					$_SESSION['check_paye']=true;
					$_SESSION['argent']-=$result_propriete['prix_construction'];
					$_SESSION['nb_maison']++;
					update_tb_utilisateur(false);
					$sql="UPDATE table_carte_propriete SET nb_maison=nb_maison+1, last_action='".date('Y-m-d H:i:s')."' WHERE code='".$propriete."';";
					mysql_query($sql) or die (mysql_error());
					$result_propriete['nb_maison']++;
					if($result_propriete['nb_maison']==5){$key_location='prix_hotel';}
					else{$key_location='prix_'.$result_propriete['nb_maison'].'_maison';}
					$sql="UPDATE table_plateau SET prix='".$result_propriete[$key_location]."' WHERE link_carte_propriete='".$propriete."'";
					mysql_query($sql) or die (mysql_error());
					add_history($_SESSION['login'],$result_propriete['prix_construction'],'construction',$propriete,'a construit 1 maison sur '.$result_propriete['ville'].', '.$result_propriete['adresse']);
					redir('index.php');
				}
				$txt_construire.='<p>Vous avez construit une maison.<p>';
				break;
			default:
				$txt_construire.='<p>Vous avez un total de '.$result_propriete['nb_maison'].' maisons sur ce terrain.</p>';
				break;
		}
		$_SESSION['txt_old']=$txt_construire;
		update_tb_utilisateur(false);
	}else{
		$txt_construire= '<p style="display:inline;">Cela fait moins de '.gmdate('G',$wait_construire).' hrs que vous avez acheté ou construit sur ce terrain. Patientez encore : </p>';
		$txt_construire.= '<div style="display:inline;" id="time_construire"></div>';
		//$txt_construire.= '<script language="JavaScript">CountDown('.($temp_attente-(time()-strtotime($_SESSION['date_last_action']))).', '.($wait_construire-(time()-strtotime($result_propriete['last_action']))).', 2);</script>';
	}
	return $txt_construire;
}
function update_tb_utilisateur($checka, $checkb=false){
	$sql="UPDATE table_utilisateur SET ";
	
	$sql.="result_des_1='".$_SESSION['result_des_1']."', result_des_2='".$_SESSION['result_des_2']."', ";
	if($_SESSION['replay']){$sql.="replay='".$_SESSION['replay']."', ";}else{$sql.="replay='0', ";}
	$sql.="nb_lance_des='".$_SESSION['nb_lance_des']."', ";
	if($checka){
		$time=date('Y-m-d H:i:s');
		$sql.="date_last_action='".$time."', ";
		$_SESSION['date_last_action']=$time;
		$sql.="nb_innactivity=0, ";
	}
	if($_SESSION['prison']){$sql.="prison='".$_SESSION['prison']."', ";}else{$sql.="prison='0', ";}
	$sql.="nb_prison='".$_SESSION['nb_prison']."', ";
	if($_SESSION['sortir_prison_chance']){$sql.="sortir_prison_chance='1', ";}else{$sql.="sortir_prison_chance='0', ";}
	if($_SESSION['sortir_prison_communauté']){$sql.="sortir_prison_communauté='1', ";}else{$sql.="sortir_prison_communauté='0', ";}
	if($_SESSION['check_paye']){$sql.="check_paye='".$_SESSION['check_paye']."', ";}else{$sql.="check_paye='0', ";}
	$sql.="nb_tour='".$_SESSION['nb_tour']."', ";
	$sql.="nb_propriete='".$_SESSION['nb_propriete']."', ";
	$sql.="nb_maison='".$_SESSION['nb_maison']."', ";
	$sql.="position='".$_SESSION['position']."', ";
	$sql.="txt_old='".htmlspecialchars($_SESSION['txt_old'], ENT_QUOTES, 'ISO-8859-1')."', ";
	if($checkb){$sql.="txt_carte='".htmlspecialchars($_SESSION['txt_carte'], ENT_QUOTES, 'ISO-8859-1')."', ";}
	if($_SESSION['ruine']){$sql.="ruine='".$_SESSION['ruine']."', ";}else{$sql.="ruine='0', ";}
	$sql.="nb_ruine='".$_SESSION['nb_ruine']."', ";
	$sql.="nb_partie_joue='".$_SESSION['nb_partie_joue']."', ";
	
	$sql.="argent=".$_SESSION['argent']." ";
	$sql.="WHERE login='".$_SESSION['login']."'";
	mysql_query($sql) or die ( mysql_error() );
	//check_ruine();
}
function add_history($login,$montant,$action,$code,$description){
	$sql = "INSERT INTO `game`.`table_history` (`id`, `date`, `login`, `montant`, `action`, `code`, `description`)";
	$sql .= " VALUES (NULL, NOW(), '".$login."', '".$montant."', '".$action."', '".$code."', '".htmlentities($description, ENT_QUOTES)."');";
	mysql_query($sql) or die ( mysql_error() );
	
	redir('./fct/script_rss.php');
}
function check_ruine(){
	if($_SESSION['argent'] < 0 or $_SESSION['ruine']){
		if(!$_SESSION['ruine']){
			$_SESSION['ruine']=true;
			free_properties();
			cancel_all_transaction();
			$_SESSION['nb_propriete']=0;
			$_SESSION['nb_maison']=0;
			$_SESSION['nb_ruine']++;
			add_history($_SESSION['login'],$_SESSION['argent'],'ruiné','','Il est ruiné.');
			update_tb_utilisateur(false);
		}
		return true;
	}
	return false;
}
function reset_utilisateur(){
	$_SESSION['argent']=2000;
	$_SESSION['prison']=false;
	$_SESSION['nb_prison']=0;
	$_SESSION['position']=1;
	$_SESSION['nb_tour']=0;
	$_SESSION['result_des_1']=0;
	$_SESSION['result_des_2']=0;
	$_SESSION['replay']=false;
	$_SESSION['sortir_prison_chance']=false;
	$_SESSION['sortir_prison_communauté']=false;
	$_SESSION['ruine']=false;
	$_SESSION['nb_lance_des']=0;
	$_SESSION['nb_propriete']=0;
	$_SESSION['nb_maison']=0;
	$_SESSION['check_paye']=false;
	$_SESSION['txt_old']='';
	$_SESSION['txt_carte']='';
	$_SESSION['nb_partie_joue']++;
	update_tb_utilisateur(true);
	add_history($_SESSION['login'],0,'reset','','Recommence à jouer');
	redir('index.php');
}
function cancel_all_transaction($login=false){
	if(!$login){$login=$_SESSION['login'];}
	$sql="SELECT id FROM table_transaction WHERE resultat IS NULL AND login_vendeur='$login';";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)){
		$sql="UPDATE table_transaction SET resultat='Ruiné' WHERE id=".$row['id'].";";
		mysql_query($sql) or die (mysql_error());
	}
}
function free_properties($login=false){
	if(!$login){$login=$_SESSION['login'];}
	$sql="SELECT id, code FROM table_carte_propriete WHERE proprietaire='$login'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$sql="UPDATE table_carte_propriete SET 
			hypotheque=FALSE, 
			proprietaire='', 
			nb_maison='0', 
			nb_passage=0, 
			last_action='".date('Y-m-d H:i:s',0)."' 
			WHERE id='".$row['id']."';";
		mysql_query($sql) or die ( mysql_error() );
		//array_push($lstCode, $row['code']);
		$lstCode[]=$row['code'];
	}
	$sql="SELECT id, code FROM table_gare WHERE proprietaire='$login'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$sql="UPDATE table_gare SET 
			hypotheque=FALSE, 
			proprietaire='', 
			nb_passage=0, 
			last_action='".date('Y-m-d H:i:s',0)."' 
			WHERE id='".$row['id']."';";
		mysql_query($sql) or die ( mysql_error() );
		//array_push($lstCode, $row['code']);
		$lstCode[]=$row['code'];
	}
	$sql="SELECT id, code FROM table_compagnie WHERE proprietaire='$login'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$sql="UPDATE table_compagnie SET 
			hypotheque=FALSE, 
			proprietaire='', 
			nb_passage=0, 
			last_action='".date('Y-m-d H:i:s',0)."' 
			WHERE id='".$row['id']."';";
		mysql_query($sql) or die ( mysql_error() );
		//array_push($lstCode, $row['code']);
		$lstCode[]=$row['code'];
	}
	if(isset($lstCode)){
		$sql="SELECT num_case, link_carte_propriete FROM table_plateau WHERE link_carte_propriete IS NOT NULL";
		$requete = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_assoc($requete)) {
			if(array_search($row['link_carte_propriete'], $lstCode)){
				$sql="UPDATE table_plateau SET prix='0' WHERE num_case='".$row['num_case']."';";
				mysql_query($sql) or die ( mysql_error() );
			}
		}
	}
}
function affiche_menu(){
	echo '
	<div class="menu-bt"><a href="./index.php">jeu</a></div>
	<div class="menu-bt"><a href="./regles.php">règles</a></div>
	<div class="menu-bt"><a href="./scores.php">scores</a></div>
	<div class="menu-bt"><a href="./historic.php">historique</a></div>
	<div class="menu-bt"><a href="./immobilier.php">Immobilier</a></div>
	<div class="menu-bt"><a href="./transaction.php">Transactions</a></div>
	<div class="menu-bt"><a href="./option.php">Options</a></div>
	<div class="menu-rss"><a type="application/rss+xml" href="http://dcboubou.dyndns.org/game/fct/activity.xml"><img src="./images/feed-icon.gif" /></a></div>';
}
function check_gagnant_old(){
	/*global $nb_max_ruine;
	$sql="SELECT nb_ruine, login, argent, nb_propriete FROM table_utilisateur ORDER BY argent DESC;";
	$requete = mysql_query($sql) or die (mysql_error());
	$checkA=false;
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if($row['nb_ruine']>=$nb_max_ruine and $_SESSION['login']!=$row['login']){$checkA=true;break;}
	}
	if($checkA){
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['nb_ruine']<$nb_max_ruine and $_SESSION['login']!=$row['login']){
				if($_SESSION['argent']<$row['argent']){
					$checkA=false; break;
				}elseif($_SESSION['argent']==$row['argent'] and $_SESSION['nb_propriete']<$row['nb_propriete']){
					$checkA=false; break;
				}
			}
		}
	}*/
	
	//Si 1 joueur est ruiné
	$sql="SELECT ruine, login FROM table_utilisateur;";
	$requete = mysql_query($sql) or die (mysql_error());
	$checkA=false;
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		//print_r($row);
		if($row['ruine'] and $_SESSION['login']!=$row['login']){$checkA=true;break;}
	}
	$sql="SELECT ruine, login, argent, nb_propriete  FROM table_utilisateur;";
	$requete = mysql_query($sql) or die (mysql_error());
	if($checkA){
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			print_r($row);echo '<br />';
			if(!$row['ruine']){
				if($_SESSION['argent']<$row['argent']){
					$checkA=false; break;
				}elseif($_SESSION['argent']==$row['argent']){
					if($_SESSION['nb_propriete']<$row['nb_propriete']){
						$checkA=false; break;
					}
				}else{break;}
			}
		}
	}
	
	//si un joueur a TOUS les terrains.
	$checkB=false;
	/*
	$checkB=true;
	for($i=1;$i<=3;$i++){
		switch($i){
			case 1:$sql="SELECT proprietaire FROM table_compagnie";break;
			case 2:$sql="SELECT proprietaire FROM table_gare";break;
			case 3:$sql="SELECT proprietaire FROM table_carte_propriete";break;
		}
		$requete = mysql_query($sql) or die (mysql_error());
		while ($row = mysql_fetch_assoc($requete)) {
			if($row['proprietaire']!=$_SESSION['login']){$checkB=false;break 2;}
		}
	}
	*/
	
	if($checkA or $checkB){
		$sql="SELECT id FROM table_utilisateur;";
		$requete_users = mysql_query($sql) or die (mysql_error());
		while($user = mysql_fetch_array($requete_users, MYSQL_ASSOC)){
			$sql="UPDATE table_utilisateur SET last_gagnant='".$_SESSION['login']."' WHERE id='".$user['id']."'";
			mysql_query($sql) or die (mysql_error() );
		}
		return true;
	}else{return false;}
}
function check_gagnant(){
	$sql="SELECT ruine, login FROM table_utilisateur;";
	$requete = mysql_query($sql) or die (mysql_error());
	$checkRuine=false;
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){if($row['ruine']){$checkRuine=true;break;}}
	
	$sql="SELECT ruine, login, argent, nb_propriete  FROM table_utilisateur;";
	$requete = mysql_query($sql) or die (mysql_error());
	$MaxArgent=0;$MaxPropriete=0;
	if($checkRuine){
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			//print_r($row);echo '<br />';
			if(!$row['ruine']){
				if($MaxArgent<$row['argent']){
					$MaxArgent=$row['argent'];
					$MaxPropriete=$row['nb_propriete'];
					$Gagnant=$row['login'];
					//$checkA=false; break;
				}elseif($MaxArgent==$row['argent']){
					if($MaxPropriete<$row['nb_propriete']){
						$MaxPropriete=$row['nb_propriete'];
						$Gagnant=$row['login'];
						//$checkA=false; break;
					}
				}
			}
		}
		$sql="SELECT id FROM table_utilisateur;";
		$requete_users = mysql_query($sql) or die (mysql_error());
		while($user = mysql_fetch_array($requete_users, MYSQL_ASSOC)){
			$sql="UPDATE table_utilisateur SET last_gagnant='$Gagnant' WHERE id='".$user['id']."'";
			mysql_query($sql) or die (mysql_error() );
		}
		restart_game();
		add_reccord_score($Gagnant,$MaxArgent,$MaxPropriete);
		envoie_mail_nouvelle_partie($Gagnant, $MaxArgent);
		if($_SESSION['login']==$Gagnant){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
function restart_game_gagnant(){
	//add_history($_SESSION['login'],0,'gagné','','Vous avez gagné la partie');
	$sql= "	UPDATE table_utilisateur SET 
			nb_partie_gagne=nb_partie_gagne+1, 
			last_gagnant='', 
			WHERE login='".$_SESSION['login']."'";
	mysql_query($sql) or die (mysql_error() );
	$_SESSION['last_gagnant']=null;
	redir('index.php');
}
function restart_game_perdant(){
	$sql="UPDATE table_utilisateur SET last_gagnant='' WHERE login='".$_SESSION['login']."';";
	mysql_query($sql) or die (mysql_error());
	$_SESSION['last_gagnant']=null;
	redir('index.php');
}
function restart_game(){
	$sql="SELECT login FROM table_utilisateur;";
	$requete_users = mysql_query($sql) or die (mysql_error());
	while($user = mysql_fetch_array($requete_users, MYSQL_ASSOC)){
		$sql=	"UPDATE table_utilisateur SET 
				result_des_1='0', result_des_2='0', 
				replay='0', 
				nb_lance_des='0', 
				date_last_action='".date('Y-m-d H:i:s',(time()-(3600*12)))."', 
				prison='0', 
				nb_prison='0', 
				sortir_prison_chance='0', sortir_prison_communauté='0', 
				check_paye='0', 
				nb_tour='0', 
				nb_propriete='0', nb_maison='0', 
				position='1', 
				ruine='0', 
				nb_partie_joue=nb_partie_joue+1, 
				txt_carte='', 
				txt_old='', 
				nb_innactivity='0', 
				argent='2000' WHERE login='".$user['login']."'";
		mysql_query($sql) or die (mysql_error() );
		free_properties($user['login']);
	}
	$sql="TRUNCATE TABLE table_history;";
	mysql_query($sql) or die (mysql_error() );
	$sql="TRUNCATE TABLE table_transaction;";
	mysql_query($sql) or die (mysql_error() );
	$sql="UPDATE table_plateau SET prix='0' WHERE num_case='21';";
	mysql_query($sql) or die (mysql_error());
	redir('index.php');
}
function add_reccord_score($gagnant, $argent, $nb_propriete){
	$sql="INSERT INTO `game`.`table_scores` (`id`, `login`, `argent_final`, `nb_terrain`, `date_gagne`) " ;
	$sql .= "VALUES (NULL, '$gagnant', '$argent', '$nb_propriete', '".date('Y-m-d H:i:s')."');";
	mysql_query($sql) or die ( mysql_error() );
}
function affiche_LoginStatus(){
	echo '<table>';
	echo '<tr><td width="60%">Nom :</td><td>'.$_SESSION['name'].'</td></tr>';
	echo '<tr><td>Prénom :</td><td>'.$_SESSION['firstname'].'</td></tr>';
	echo '<tr><td>Porte-feuille :</td><td>'.$_SESSION['argent'].'€</td></tr>';
	echo '<tr><td colspan="2" style="text-align: center;">';
	echo '<form method="post" action="unconnect.php"><input type="submit" name="submit" value="Se déconnecter" /></form>';
	echo '</td></tr></table>';
}
function Extract_Nom_Propriete($propriete){
	$strpropriete=str_split($propriete['code']);
	switch($strpropriete['0']){
		case 'i': $nom_propriete=$propriete['nom'];break;
		case 'j':$nom_propriete=$propriete['nom'];break;
		default :$nom_propriete=$propriete['ville'].', '.$propriete['adresse'];break;
	}
	return $nom_propriete;
}
function envoie_mail_nouvelle_partie($gagnant, $argent){
	//to
	$to=null;
	$sql="SELECT login, email FROM table_utilisateur;";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($user = mysql_fetch_assoc($requete)) {$to.=$user['login'].'<'.$user['email'].'>, ';}
	
	//Subject
	$subject="MONOPOLY - Nouvelle partie commencée";
	
	//Message
	$message="
    <html>
    <head>
    <title>Monopoly, nouvelle partie</title>
    </head>
    <body>
    <p>Bonjour,</p>
	<p>Une nouvelle parte de <a target=\"_blank\" href=\"http://dcboubou.dyndns.org/game\">monopoly</a> a commencé.</p>
	<p>La gagnant était $gagnant avec $argent €(Eur).</p>
	<p>En Espérant vous y retrouvez très prochainement.</p>
	<p>A très bientot et bonne chance !!</p>
    </body>
    </html>
    ";
	
	//header
	$headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	// Envoi
	mail($to, $subject, $message, $headers);
	//echo $to;
	//echo $message;
}

?>
