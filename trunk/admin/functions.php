<?php
function affiche_menu(){
	echo '<div class="menu-elements"><a href="users.php">Utilisateurs</a></div>';
	echo '<div class="menu-elements"><a href="update.php">Update</a></div>';
	echo '<div class="menu-elements"><a href="reset.php">Reset</a></div>';
}
function add_history($login,$montant,$action,$code,$description){
	$sql = "INSERT INTO `game`.`table_history` (`id`, `date`, `login`, `montant`, `action`, `code`, `description`)";
	$sql .= " VALUES (NULL, NOW(), '".$login."', '".$montant."', '".$action."', '".$code."', '".htmlspecialchars($description, ENT_QUOTES)."');";
	mysql_query($sql) or die ( mysql_error() );
}
function free_properties($user){
	$sql="SELECT id, code FROM table_carte_propriete WHERE proprietaire='$user'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$sql="UPDATE table_carte_propriete SET proprietaire='', nb_maison='0', last_action='".date('Y-m-d H:i:s',0)."' WHERE id='".$row['id']."';";
		mysql_query($sql) or die ( mysql_error() );
		array_push($lstCode, $row['code']);
	}
	$sql="SELECT id, code FROM table_gare WHERE proprietaire='$user'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$sql="UPDATE table_gare SET proprietaire='', last_action='".date('Y-m-d H:i:s',0)."' WHERE id='".$row['id']."';";
		mysql_query($sql) or die ( mysql_error() );
		array_push($lstCode, $row['code']);
	}
	$sql="SELECT id, code FROM table_compagnie WHERE proprietaire='$user'";
	$requete = mysql_query($sql) or die (mysql_error());
	while ($row = mysql_fetch_assoc($requete)) {
		$sql="UPDATE table_compagnie SET proprietaire='', last_action='".date('Y-m-d H:i:s',0)."' WHERE id='".$row['id']."';";
		mysql_query($sql) or die ( mysql_error() );
		array_push($lstCode, $row['code']);
	}
	if(isset($lstCode)){
		$sql="SELECT num_case, link_carte_propriete FROM table_plateau WHERE link_carte_propriete IS NOT NULL";
		$requete = mysql_query($sql) or die (mysql_error());
		print_r($lstCode);
		while ($row = mysql_fetch_assoc($requete)) {
			if(in_array($row['link_carte_propriete'], $lstCode)){
				$sql="UPDATE table_plateau SET prix='0' WHERE num_case='".$row['num_case']."';";
				mysql_query($sql) or die ( mysql_error() );
			}
		}
	}
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
function reset_user($user){
	$sql="UPDATE table_utilisateur SET ";
	$sql.="result_des_1='0', result_des_2='0', ";
	$sql.="replay='0', nb_lance_des='0', date_last_action='".date('Y-m-d H:i:s')."', ";
	$sql.="prison='0', nb_prison='0', sortir_prison_chance='0', sortir_prison_communauté='0', ";
	$sql.="check_paye='0', txt_old='', txt_carte='', nb_propriete='0', nb_maison='0', ";
	$sql.="nb_tour='0', position='1', nb_partie_joue=nb_partie_joue+1, ";
	$sql.="ruine='0', nb_ruine='0', argent='2000' ";
	$sql.="WHERE login='$user'";
	mysql_query($sql) or die ( mysql_error() );
	free_properties($user);
}
?>