<?php
date_default_timezone_set('Europe/Brussels');
include('config.php');
include('functions.php');

$check=false;
WriteLineInLogFile("----------Lancement script innactivité----------");
$sql="SELECT * FROM table_utilisateur";
$requete = mysql_query($sql) or die (mysql_error());
while ($row = mysql_fetch_assoc($requete)) {
	$ar_innactivity=calcul_innactivity($row['date_last_action'],date('c'));
	if($ar_innactivity['days']>0){
		$check=true;
		$row['nb_innactivity']+=$ar_innactivity['days'];
		$montant=0;
		$montant=$ar_innactivity['days'] * 10 * $row['nb_innactivity'];
		$row['argent']-=$montant;
		$sql="UPDATE table_plateau SET prix=prix+".$montant." WHERE num_case='21'";
		mysql_query($sql) or die ( mysql_error() );
		if($row['argent']<0){$row['ruine']=true;}
		$row['date_last_action']=date('Y-m-d H:i:s',strtotime($row['date_last_action'])+(24*3600*$ar_innactivity['days']));
		$sql="UPDATE table_utilisateur SET 
			argent='".$row['argent']."', 
			ruine=".$row['ruine'].", 
			nb_innactivity=".$row['nb_innactivity'].", 
			date_last_action='".$row['date_last_action']."' 
			WHERE login='".$row['login']."';";
		mysql_query($sql) or die ( mysql_error() );
		$strLogin=$row['login'];
		$strInnactivityDays=$ar_innactivity['days'];
		//$strInnactivityDays=$row['nb_innactivity'];
		$strArgent=$row['argent'];
		WriteLineInLogFile("$strLogin\t$strInnactivityDays\t$strArgent");
		add_history($row['login'],$montant,'innactivite','','Admin : '.$row['nb_innactivity'].' jour d\'innactivite');
		add_history('Parking',$montant,'parkingP','',$row['login'].' a '.$row['nb_innactivity'].' jour d\'innactivité');
	}else{
		$strLogin=$row['login'];
		$strInnactivityDays=$ar_innactivity['days'];
		$strArgent=$row['argent'];
		WriteLineInLogFile("$strLogin\t$strInnactivityDays\t$strArgent");
	}
}
if(!$check){WriteLineInLogFile("Tout le monde a joué dans les 24h.");}
WriteLineInLogFile("----------Fin script innactivité----------");
//End of Script


function WriteLineInLogFile($strLine){
	//localhost
	//$myFile = "d:/wamp/www/game/trunk/admin/log/log_innactivity.log";
	//dcboubou.dyndns.org
	$myFile = "D:\\game\\v2.3\\admin\\log\\Log_innactivity.log";
	
	$fh = fopen($myFile, 'at') or die("can't open file");
	$dateA=date('d/m/Y G:i:s');
	$stringData = "$dateA\t$strLine\n";
	fwrite($fh, $stringData);
	fclose($fh);
}
?>
