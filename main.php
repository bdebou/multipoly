<?php
//session_start();
if(empty($_SESSION['login'])) {
    header('Location: authentification.php');
    exit();
}else{
include('functions.php');
function launch_monopoly(){
	//include('functions.php');
	include('config.php');
	date_default_timezone_set('Europe/Brussels');
	global $temp_attente, $check_achat, $check_loye, $check_construire, $nb_max_maison, $check_hypotheque, $nb_max_ruine;
	$sql="SELECT * FROM table_utilisateur WHERE login='".$_SESSION['login']."'";
	$requete = mysql_query($sql) or die (mysql_error());
	$result_utilisateur = mysql_fetch_array($requete, MYSQL_ASSOC);
	$_SESSION = array_merge($_SESSION,$result_utilisateur);
	echo '<div class="main">';
	if(check_gagnant()){
		echo '<p class="gagnant">Félicitation!!!<br />Vous avez gagné.</p>';
		//$_SESSION['nb_partie_gagne']++;
		if(!isset($_POST['restart'])){
			echo '
<form method="post">
	<table>
		<tr><td>Prêt à recommencer?</td></tr>
		<tr><td style="text-align:center;"><input type="submit" name="restart" value="OK" /></td>
	</table>
</form>';
		}elseif(isset($_POST['restart'])){
			restart_game_gagnant();
			//restart_game('gagnant');
		}
	}elseif(!empty($_SESSION['last_gagnant'])){
		echo '<p>'.$_SESSION['last_gagnant'].' a gagné la partie. Il va relancer la partie.</p>';
		if(!isset($_POST['restart'])){
			echo '
<form method="post">
	<table>
		<tr><td>Prêt à recommencer?</td></tr>
		<tr><td style="text-align:center;"><input type="submit" name="restart" value="OK" /></td>
	</table>
</form>';
		}elseif(!empty($_POST['restart'])){
			unset($_POST);
			restart_game_perdant();
		}
	}elseif(check_ruine()){
		//-------------------Vous etes ruiné
		echo '<p class="ruine">Vous êtes ruiné</p>';
		if(!isset($_POST['recommence'])){
			echo '
<form method="post">
	<table>
		<tr><td colspan="2">Voulez-vous recommencer?</td></tr>
		<tr>
			<td><input type="submit" name="recommence" value="Oui" /></td>
			<td><input type="button" value="Non" /></td>
		</tr>
	</table>
</form>';
		}else{
			redir('index.php');
		}
	}elseif($_SESSION['prison']=='1'){
		// ----------------- vous etes en prison
		if(time()-strtotime($_SESSION['date_last_action'])<$temp_attente){
			$ar_date_diff=date_diff($_SESSION['date_last_action'],date('Y-m-d H:i:s'),$temp_attente);
			$num_propriete=affiche_detail_case();
			echo '
<p style="display:inline;">Cela fait moins de '.gmdate('G',$temp_attente).' hrs que vous avez joué. Patientez encore : </p>
<div style="display:inline;" id="time_des"></div>
<script type="text/JavaScript">CountDown('.($temp_attente-(time()-strtotime($_SESSION['date_last_action']))).', 0, 1);</script>
<p>Au lancé de dés, vous aviez obtenu : '.$_SESSION['result_des_1'].' et '.$_SESSION['result_des_2'].'.</p>';
		}else{
			global $caution;
			affiche_detail_case();
			echo case_carte_chance_caisse();
			echo '
<p>Vous etes en prison! Vous devez faire un double pour sortir ou payer une caution de '.$caution.'€</p>
<p>Pour rappel: Quand vous êtes en prison, vous ne touchez aucun loyé.</p>
<form method="post"">
	<input type="submit" name="lancerdes" value="Lancer les Dés" />
	<input type="submit" name="paie_caution" value="Paie la caution" />';
			if($_SESSION['sortir_prison_chance'] or $_SESSION['sortir_prison_communauté']){
				echo '
	<input type="submit" name="carte_sortie" value="Carte Sortie de prison" />';
			}
			echo '
</form>';
			if (isset($_POST['lancerdes'])){
				unset($_POST);
				$result_des=lance_des();
				$_SESSION['result_des_1']=$result_des['0'];$_SESSION['result_des_2']=$result_des['1'];
				echo 'Vous avez obtenu : '.$result_des['0'].' et '.$result_des['1'].'.';
				check_des($result_des);
				update_tb_utilisateur(true);
				redir('index.php');
			}
			if (isset($_POST['paie_caution'])){
				unset($_POST);
				$_SESSION['argent'] -= $caution;$_SESSION['prison']=false;$_SESSION['replay']=false;$_SESSION['result_des_1']=0;$_SESSION['result_des_2']=0;
				$sql="UPDATE table_plateau SET prix=prix+$caution WHERE num_case='21'";
				mysql_query($sql) or die ( mysql_error() );
				add_history($_SESSION['login'],$caution,'prisonOUTC','','a payé la caution pour sortir de prison');
				add_history('Parking',$caution,'parkingP','',$_SESSION['login'].' a payé sa caution');
				update_tb_utilisateur(true);
				redir('index.php');
			}
			if (isset($_POST['carte_sortie'])){
				unset($_POST);
				$_SESSION['prison']=false;$_SESSION['replay']=false;$_SESSION['result_des_1']=0;$_SESSION['result_des_2']=0;
				if($_SESSION['sortir_prison_chance']){$_SESSION['sortir_prison_chance']=false;}
				elseif($_SESSION['sortir_prison_communauté']){$_SESSION['sortir_prison_communauté']=false;}
				update_tb_utilisateur(true);
				add_history($_SESSION['login'],0,'prisonOUT','','a utilisé sa carte sortie de prison');
				redir('index.php');
			}
		}
		echo AffichePub().'</div>';
	}elseif (time()-strtotime($_SESSION['date_last_action'])<$temp_attente and !$_SESSION['replay']){
		//---------------------Veuillez patienter
		$ar_date_diff=date_diff($_SESSION['date_last_action'],date('Y-m-d H:i:s'),$temp_attente);
		$num_propriete=affiche_detail_case();
		echo '
<p style="display:inline;">Cela fait moins de '.gmdate('G',$temp_attente).' hrs que vous avez joué. Patientez encore : </p>
<div style="display:inline;" id="time_des"></div>
<p>Au lancé de dés, vous aviez obtenu : '.$_SESSION['result_des_1'].' et '.$_SESSION['result_des_2'].'.</p>';
		if($check_achat){module_achat($num_propriete['0']);}
		if($check_loye){module_loye($num_propriete['0']);}
		if($check_construire and isset($_SESSION['step_construction']) and $_SESSION['nb_maison']<$nb_max_maison){
			echo module_construire($num_propriete['0']);
			$select_counter=2;
			$timeA=$temp_attente-(time()-strtotime($_SESSION['date_last_action']));
			$timeB=$wait_construire-(time()-strtotime($num_propriete['1']));
		}else{
			$select_counter=1;
			$timeA=$temp_attente-(time()-strtotime($_SESSION['date_last_action']));
			$timeB=0;
			if($_SESSION['nb_maison']==$nb_max_maison){
				echo "<p>Vous avez atteint le maximum de construction possible qui est de $nb_max_maison. 
				Si vous voulez quand même construire sur ce terrain, vous avez la possibilité de vendre une maison d'un autre terrain via le menu <a href=\"immobilier.php\">Immobilier</a>.</p>";
			}
		}
		if($check_hypotheque){echo '<p class="hypotheque">Le terrain a été hypothéqué.</p>';}
		echo '<script type="text/JavaScript">CountDown('.$timeA.', '.$timeB.', '.$select_counter.');</script>';
		if(!empty($_SESSION['txt_old'])){echo htmlspecialchars_decode($_SESSION['txt_old'], ENT_QUOTES);}
		echo case_carte_chance_caisse();
		echo AffichePub().'</div>';
	}else{
		//----------------------Jouez
		$num_propriete=affiche_detail_case();
		if(isset($_SESSION['replay']) and $_SESSION['replay']){
			if($check_achat){module_achat($num_propriete['0']);}
			if($check_loye){module_loye($num_propriete['0']);}
			if($check_construire and isset($_SESSION['step_construction']) and $_SESSION['nb_maison']<$nb_max_maison){
				echo module_construire($num_propriete['0']);
				echo '<script type="text/JavaScript">CountDown(0,'.($wait_construire-(time()-strtotime($num_propriete['1']))).', 3);</script>';
			}elseif($_SESSION['nb_maison']==$nb_max_maison){
				echo "<p>Vous avez atteint le maximum de construction possible qui est de $nb_max_maison. 
				Si vous voulez quand même construire sur ce terrain, vous avez la possibilité de vendre un maison d'un autre terrain via le menu <a href=\"immobilier.php\">Immobilier</a>.</p>";
			}
			if($check_hypotheque){echo '<p class="hypotheque">Le terrain a été hypothéqué.</p>';}
			if(!empty($_SESSION['txt_old'])){echo htmlspecialchars_decode($_SESSION['txt_old'], ENT_QUOTES);}
			echo case_carte_chance_caisse();
			echo '<p>Au lancé de dés, vous aviez obtenu : '.$_SESSION['result_des_1'].' et '.$_SESSION['result_des_2'].'. Vous avez donc fait un double, rejouez.</p>';
		}
		echo '<form method="post"><input type="submit" name="lancerdes" value="Lancer les Dés" /></form>';
		if (isset($_POST['lancerdes'])){
			unset($_POST);
			$result_des=lance_des();
			//$result_des['0']=5;$result_des['1']=4;
			$_SESSION['result_des_1']=$result_des['0'];$_SESSION['result_des_2']=$result_des['1'];
			echo 'Au lancé de dés, vous aviez obtenu : '.$result_des['0'].' et '.$result_des['1'].'.';
			if(check_des($result_des)){avance_pion($result_des);};
			$num_propriete=affiche_detail_case();
			update_tb_utilisateur(true);
			redir('index.php');
		}
		echo AffichePub().'</div>';
	}
	//echo '</div>';
}
function AffichePub(){
	return '
	<div class="pub_index">
		<script type="text/javascript"><!--
		google_ad_client = "ca-pub-2161674761092050";
		/* Pub 1 */
		google_ad_slot = "5833330125";
		google_ad_width = 200;
		google_ad_height = 200;
		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	</div>';
}
function case_carte_chance_caisse(){
	$txt=null;
	if(!empty($_SESSION['txt_carte'])){
		$txt = '<p>Vous aviez tiré une carte chance ou caisse de communauté qui disait :</p>
		<table style="border-collapse: collapse;border: 1px #000000 solid;"><tr><td>';
		$txt .= htmlspecialchars_decode($_SESSION['txt_carte'], ENT_QUOTES);
		$txt .= '</td></tr></table>';
	}
	return $txt;
}
function check_innactivite(){
	$ar_innactivity=calcul_innactivity($_SESSION['date_last_action'],date('Y-m-d H:i:s'));
	if($ar_innactivity['days']>0){
		$montant=0;
		$_SESSION['nb_innactivity']+=$ar_innactivity['days'];
		$montant=$ar_innactivity['days'] * 10 * $_SESSION['nb_innactivity'];
		$_SESSION['argent']-=$montant;
		$_SESSION['date_last_action']=date('Y-m-d H:i:s',strtotime($row['date_last_action'])+(3600*24 * $ar_innactivity['days']));
		$sql="UPDATE table_utilisateur SET 
		date_last_action='".$_SESSION['date_last_action']."', 
		nb_innactivity=".$_SESSION['nb_innactivity'].", 
		argent='".$_SESSION['argent']."' 
		WHERE login='".$_SESSION['login']."'";
		mysql_query($sql) or die ( mysql_error() );
		$sql="UPDATE table_plateau SET prix=prix+".$montant." WHERE num_case='21'";
		mysql_query($sql) or die ( mysql_error() );
		add_history($_SESSION['login'],$montant,'innactivite','',$_SESSION['nb_innactivity'].' jours d\'innactivité');
		add_history('Parking',$montant,'parkingP','',$_SESSION['login'].' a '.$_SESSION['nb_innactivity'].' jours d\'innactivité');
		check_ruine();
	}
	$_SESSION['innactivity']=true;
}
function calcul_innactivity($start, $end="NOW"){
	$sdate = strtotime($start);
	$edate = strtotime($end);
	//$temp = $edate - $sdate;
	$time = $edate - $sdate;
	//$time = $attente - $temp;
	$timeshift=array('days'=>0,'hrs'=>0,'min'=>0,'sec'=>0,'full'=>'');
	if($time>=0 && $time<=59) {
		// Seconds
		$timeshift['full'] = $time.' seconds ';
		$timeshift['sec'] = $time;
	}elseif($time>=60 && $time<=3599) {
		// Minutes + Seconds
		$pmin = ($edate - $sdate) / 60;
		//$pmin = $time / 60;
		$premin = explode('.', $pmin);
		$presec = $pmin-$premin[0];
		$sec = $presec*60;
		$timeshift['full'] = $premin[0].' min '.round($sec,0).' sec ';
		$timeshift['sec']=round($sec,0);
		$timeshift['min']=$premin[0];
	}elseif($time>=3600 && $time<=86399) {
		// Hours + Minutes
		$phour = ($edate - $sdate) / 3600;
		//$phour = $time / 3600;
		$prehour = explode('.',$phour);
		$premin = $phour-$prehour[0];
		$min = explode('.',$premin*60);
		$presec = '0.'.$min[1];
		$sec = $presec*60;
		$timeshift['full'] = $prehour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
		$timeshift['sec']=round($sec,0);
		$timeshift['min']=$min[0];
		$timeshift['hrs']=$prehour[0];
	}elseif($time>=86400) {
		// Days + Hours + Minutes
		$pday = ($edate - $sdate) / 86400;
		//$pday = $time / 86400;
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
}
?>