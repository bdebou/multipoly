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
			if(time>=1){document.title = "BoubouPoly - Gestion Compte - " + ArrangeDate(time);}
			timeb=time-1;
			setTimeout("CountDown(timeb)", 1000);
		}else if(time==0){window.location="option.php";}
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
	<title>BoubouPoly - Gestion Compte</title>
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
	<h1>Gestion compte</h1>
	<fieldset style="width:370px;padding:5px; border:3px double;float:left;margin-right:10px;">
	<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Changer de password :</legend>
		<?php change_password();?>
	</fieldset>
	<fieldset style="width:370px;padding:5px; border:3px double;float:left;margin-right:10px;">
	<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Changer E-Mail :</legend>
		<?php change_email();?>
	</fieldset>
	<fieldset style="width:370px;padding:5px; border:3px double;float:left;margin-right:10px;">
	<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Se désinscrire :</legend>
		<?php echo se_desinscrire($_SESSION['login']);?>
	</fieldset>
</div>
<div class="version"><table><tr><td>Version :</td><td><?php echo $NumVersion;?></td></tr></table></div>
</body>
</html>
<?php
function change_password(){
	$chgpassword = new ClassChangePassword();
	if(isset($_POST['chg_pass'])){
		$chgpassword->loadForm($_POST);
		$changeit = $chgpassword->ChangeCheck;
	}
	if(empty($changeit)){
		echo '<form method="post"><table style="margin:5px;">';
		echo '<tr><td>Ancien password :</td><td><input'.$chgpassword->inputTrue($chgpassword->old_password,'1').' type="password" name="old_password" value="'.$chgpassword->old_password.'" /></td></tr>';
		echo '<tr><td>Nouveau password :</td><td><input'.$chgpassword->inputTrue($chgpassword->password_1,'1').' type="password" name="password_1" value="'.$chgpassword->password_1.'" /></td></tr>';
		echo '<tr><td>Retaper nouveau password :</td><td><input'.$chgpassword->inputTrue($chgpassword->password_2,'1').' type="password" name="password_2" value="'.$chgpassword->password_2.'" /></td></tr>';
		echo '<tr><td colspan="2" style="text-align:center;"><input type="submit" name="chg_pass" value="Envoie" /></td></tr>';
		echo '</table></form>';
	}
}
function change_email(){
	$chgemail = new ClassChangeEmail();
	if(isset($_POST['chg_email'])){
		$chgemail->loadForm($_POST);
		$changeit = $chgemail->ChangeCheck;
	}
	if(empty($changeit)){
		echo '<form method="post"><table style="margin:5px;width:100%;">
		<tr><td>E-Mail actuel :</td><td>'.recup_email().'</td></tr>
		<tr><td>Nouvel E-Mail :</td><td><input size="35"'.$chgemail->inputTrue($chgemail->email).' type="text" name="email" value="'.$chgemail->email.'" /></td></tr>
		<tr><td colspan="2" style="text-align:center;"><input type="submit" name="chg_email" value="Envoie" /></td></tr>
		</table></form>';
	}
}
function recup_email(){
		require('config.php'); // On réclame le fichier
		$sql = "SELECT email FROM table_utilisateur WHERE login='".$_SESSION['login']."'";
		$requete_1 = mysql_query($sql) or die ( mysql_error() );
		$result = mysql_fetch_array($requete_1, MYSQL_ASSOC);
		return $result['email'];
}
class ClassChangePassword{
	public $old_password;
	public $password_1;
	public $password_2;
	public $ChangeCheck = null;
	
	public function verif_null($var){		return (!empty($var))?$var:null;}
	
    public function verif_new_password($pass1,$pass2){return($pass1==$pass2)?$pass1:null;}
	
	public function verif_old_password($old){
		require('config.php'); // On réclame le fichier
		$sql = "SELECT pass FROM table_utilisateur WHERE login='".$_SESSION['login']."'";
		$requete_1 = mysql_query($sql) or die ( mysql_error() );
		$result = mysql_fetch_array($requete_1, MYSQL_ASSOC);
		if($result['pass']==$old){return $old;}
		else{return null;}
	}
	
	public function inputTrue($input,$type = '1'){
		$style_blanc = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		$test = null;
		if(isset($_POST['chg_pass'])){
			switch($type){
				case '1': $test = $this->verif_null($input);break;
				case '2': $test = $this->verif_mail($input);break;
			}
			if(empty($test)){
				return $style_rouge;
			}else{
				return $style_blanc;
			}
		}
	}
	function envoi_sql(){ //fonction qui envoie la requete SQL
		require('config.php'); // On réclame le fichier
		$sql = "UPDATE table_utilisateur SET pass='".$this->password_1."' WHERE login='".$_SESSION['login']."';";
		mysql_query($sql) or die ( mysql_error() );
	}
	public function loadForm($data){
		extract($data);
		$this->old_password	= $old_password;
		$this->password_1	= $password_1;
		$this->password_2	= $password_2;
		$test = $this->testForm();
		if(!empty($test)){
			$this->envoi_sql();
			$this->ChangeCheck = 1;
			echo '<p>Password changé.</p>';
		}else{
			echo '<p>Erreur. Veuillez vérifier.</p>';  
		}
	}
	public function testForm(){
		if(
		$this->verif_null($this->old_password) and 
		$this->verif_null($this->password_1) and 
		$this->verif_null($this->password_2)){
			if(
			$this->verif_new_password($this->password_1,$this->password_2) and
			$this->verif_old_password($this->old_password)){
				return 1;
			}
			return NULL; 
		}
		return NULL; 
	}
}
class ClassChangeEmail{
	public $email;
	public $ChangeCheck = null;
	
	public function verif_null($var){		return (!empty($var))?$var:null;}
	public function verif_mail($var){		return (preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$var))?$var:null;}
	
	public function inputTrue($input){
		$style_blanc = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		$test = null;
		if(isset($_POST['chg_email'])){
			$test = $this->verif_null($input);
			$test = $this->verif_mail($input);
			if(empty($test)){
				return $style_rouge;
			}else{
				return $style_blanc;
			}
		}
	}
	function envoi_sql(){ //fonction qui envoie la requete SQL
		require('config.php'); // On réclame le fichier
		$sql = "UPDATE table_utilisateur SET email='".$this->email."' WHERE login='".$_SESSION['login']."';";
		mysql_query($sql) or die ( mysql_error() );
	}
	public function loadForm($data){
		extract($data);
		$this->email	= $email;
		$test = $this->testForm();
		if(!empty($test)){
			$this->envoi_sql();
			$this->ChangeCheck = 1;
			echo '<p>E-mail changé.</p>';
		}else{echo '<p>Erreur. Veuillez vérifier.</p>';  }
	}
	public function testForm(){
		if($this->verif_null($this->email)){
			if($this->verif_mail($this->email)){return 1;}
			return NULL; 
		}
		return NULL; 
	}
}
function se_desinscrire($user){
	$txt_result=null;
	if(!isset($_POST['confirm'])){
		$txt_result .= '<form method="post">
			<p><input type="checkbox" name="confirm" value="yes">Oui, je confirm ma désinscription ce qui engendredra la suppression de mon compte.</input></p>
			<p><input type="submit" value="GO" /></p>
			</form>';
	}elseif(isset($_POST['confirm']) and $_POST['confirm']){
		unset($_POST);
		free_properties($user);
		cancel_all_transaction($user);
		$sql = "DELETE FROM table_utilisateur WHERE login='$user'";
		mysql_query($sql) or die ( mysql_error() );
		//include('config.php');
		$txt_result .= "$user a été supprimé.";
		redir('unconnect.php');
	}
	//unset($_GET);
	return $txt_result;
}

?>