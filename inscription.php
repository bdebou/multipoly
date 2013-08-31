<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	session_start();
	include('fctinscription.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<title>BoubouPoly</title>
	<link rel="stylesheet" href="./CSS/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="main">
<h2>Inscription</h2>
<?php
$contact = new InscriptionFormulaire();
if(isset($_POST['captchaResult'])){
	$contact->loadForm($_POST);
	if($_POST['captchaResult'] === $_SESSION['captchaResult']){$send = $contact->sendCheck;}
}
if(empty($send)){

/* FORMULAIRE DEBUT */ ?>
<form method="post" action="./inscription.php">
<table class="inscription">
	<tr>
		<td width="20%" align="right">&nbsp;&nbsp;</td>
		<td>
			<p>Veuillez remplir ce formulaire :</p>
		</td>
	</tr>
	<tr>
		<td align="right">Login <b>*</b> :</td>
		<td><input type="text" name="login"  size="50" <?php $contact->inputTrue($contact->login,'3'); ?> value="<?php echo $contact->login; ?>" /></td>
	</tr>
	<tr>
		<td align="right">PassWord <b>*</b> :</td>
		<td><input type="password" name="password"  size="50" <?php $contact->inputTrue($contact->password); ?> value="<?php echo $contact->password; ?>" /></td>
	</tr>
	<tr>
		<td align="right">Prénom <b>*</b> :</td>
		<td><input type="text" name="prenom"  size="50" <?php $contact->inputTrue($contact->prenom); ?> value="<?php echo $contact->prenom; ?>" /></td>
	</tr>
	<tr>
		<td align="right">Nom <b>*</b> :</td>
		<td><input type="text" name="nom"  size="50" <?php $contact->inputTrue($contact->nom); ?> value="<?php echo $contact->nom; ?>" /></td>
	</tr>
	<tr>
		<td align="right">E-Mail <b>*</b> :</td>
		<td><input type="text" name="mail" size="50" <?php $contact->inputTrue($contact->mail,'2'); ?> value="<?php echo $contact->mail; ?>" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>(<b>*</b>) Champ obligatoire.</td>
	</tr>
	<tr>
		<td  align="right"><label for="captchaResult">Veuillez recopier le code affiché en majuscule: </label><input type="text" name="captchaResult" size="10" <?php $contact->inputTrue($contact->captchaResult,'4'); ?> value="<?php echo $contact->captchaResult; ?>" /></td>
		<td><img alt="Captcha" src="./captcha/captcha.php" style="vertical-align:middle;" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="submit" style = "font-family: verdana;padding: 5px 45px 5px 45px;border: solid #000000 2px;font-size: 8pt;color: #ffffff;background-color: #32269F"  name="envoyer" value="Envoyer" /></td>
	</tr>
</table>
</form>
<?php 
}
/* FOMULAIRE FIN*/ ?>
</div>
</body>
</html>