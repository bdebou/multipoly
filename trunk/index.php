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
	include('config.php');
	include('main.php');
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<script type="text/JavaScript">//<![CDATA[
	//var time=0;  //Changer ici le temps en seconde
	var checkA=false;
	var checkB=false;
	function CountDown(time, timec, place){
		if(time>0 || timec>0){
			if(time>=1 || timec>=1){
				switch(place){
					case 1:
						document.getElementById("time_des").innerHTML = ArrangeDate(time);
						document.title = "BoubouPoly - " + ArrangeDate(time);
						btime = time-1;
						if (btime==0){checkA=true;}
						btimec = timec; checkB=true;
						break;
					case 2:
						document.getElementById("time_des").innerHTML = ArrangeDate(time);
						document.title = "BoubouPoly - " + ArrangeDate(time);
						document.getElementById("time_construire").innerHTML = ArrangeDate(timec);
						btime=time-1;if (btime==0){checkA=true;}
						btimec=timec-1;if (btimec==0){checkB=true;}
						break;
					case 3:
						document.getElementById("time_construire").innerHTML = ArrangeDate(timec);
						btime = time; checkA=true;
						btimec=timec-1;
						if (btimec==0){checkB=true;}
						break;
				}
			}
			bplace=place;
			setTimeout("CountDown(btime, btimec, bplace)", 1000);
		}else if(checkA && checkB){window.location="index.php";}
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
	<link rel="stylesheet" href="./CSS/styles.css" type="text/css" />
	<title>BoubouPoly</title>
	<link rel="alternate" type="application/rss+xml" href="http://dcboubou.dyndns.org/game/fct/activity.xml" title="Activités BoubouPoly" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="loginstatus"><?php affiche_LoginStatus();?></div>
<div class="proprietes"><?php affiche_proprietes();?></div>
<div class="menu"><?php affiche_menu();?></div>
<div class="plateau"><?php affiche_plateau();?></div>
<?php
	if(!isset($_SESSION['innactivity']) or !$_SESSION['innactivity']){check_innactivite();}
	launch_monopoly();
?>

<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>