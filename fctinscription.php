<?php
class InscriptionFormulaire{
	public $login;
	public $password;
	public $nom;
	public $prenom;
	public $mail;
	public $captchaResult;
	public $sendCheck = null;
	
	public function verif_null($var){
		return (!empty($var))?$var:null;
	}
    public function verif_mail($var){
		return (preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$var))?$var:null;
	}
	public function verif_captcha($var){
		return ($var==$_SESSION['captchaResult'])?$var:null;
	}
	public function verif_login($var){
		require('config.php'); // On réclame le fichier
		if($var=='login existant'){
			return null;
		}else{
			$sql = "SELECT * FROM table_utilisateur WHERE login='".$var."'";
			// On vérifie si ce login existe
			$requete_1 = mysql_query($sql) or die ( mysql_error() );
			return (mysql_num_rows($requete_1)==0)?$var:null;
		}
	}
	public function inputTrue($input,$type = '1'){
		$style_blanc = ' style = "font-family: verdana;border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "font-family: verdana;border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		$test = null;
		if(isset($_POST['nom'])){
			switch($type){
				case '1': $test = $this->verif_null($input);
					break;
				case '2': $test = $this->verif_mail($input);
					break;
				case '3': $test = $this->verif_login($input);$this->login='login existant';
					break;
				case '4': $test = $this->verif_captcha($input);
					break;
			}
			if(empty($test)){
				echo $style_rouge;
			}else{
				echo $style_blanc;
			}
		}
	}
	function envoi_sql(){ //fonction qui envoie la requete SQL
		require('config.php'); // On réclame le fichier
		$sql = 	"INSERT INTO `game`.`table_utilisateur` (
				`id`, 
				`login`, 
				`pass`, 
				`nbr_connect`, 
				`dates`, 
				`name`, 
				`firstname`, 
				`email`, 
				`argent`, 
				`prison`, 
				`nb_prison`, 
				`position`, 
				`nb_tour`, 
				`result_des_1`, `result_des_2`, 
				`replay`, 
				`sortir_prison_chance`, `sortir_prison_communauté`, 
				`nb_propriete`, 
				`nb_maison`, 
				`ruine`, `nb_ruine`, 
				`nb_lance_des`, 
				`date_last_action`, 
				`check_paye`, 
				`txt_old`, 
				`txt_carte`, 
				`nb_partie_joue`, 
				`nb_partie_gagne`, 
				`last_gagnant`, 
				`security`, 
				`nb_innactivity`) 
			VALUES (
				NULL, 
				'".$this->login."', 
				'".$this->password."', 
				'0', 
				'".date('Y-m-d H:i:s')."', 
				'".$this->nom."', 
				'".$this->prenom."', 
				'".$this->mail."', 
				'2000', 
				'0', 
				'0', 
				'1', 
				'0', 
				'0', '0', 
				'0', 
				'0', '0', 
				'0', 
				'0', 
				'0', '0', 
				'0', 
				'".(date('Y-m-d H:i:s',time()-(12*3600)))."', 
				'0', 
				'', 
				'', 
				'0', 
				'0', 
				'', 
				'user', 
				'0');";
		mysql_query($sql) or die ( mysql_error() );
	}
	public function loadForm($data){
		extract($data);
		$this->nom           = trim(htmlentities($nom, ENT_QUOTES));
		$this->prenom        = trim(htmlentities($prenom, ENT_QUOTES));
		$this->login         = trim(htmlentities($login, ENT_QUOTES));
		$this->password      = $password; //trim(htmlentities($password, ENT_QUOTES));
		$this->mail          = $this->verif_mail($mail);
		$this->captchaResult = $this->verif_captcha($captchaResult);
		$test = $this->testForm();
		if(!empty($test)){
			$this->envoi_sql();
			$this->printForm();
			$this->sendCheck = 1;
		}else{
			echo '<div style="padding:5px;border:solid 2px #FF0000;background-color:#FEDFDF;width:600px;color:#ff0000;" >';
			echo 'Veuillez correctement remplir les champs en rouge.';
			echo '</div>';  
		}
	}
	public function testForm(){
		if(
		$this->verif_null($this->nom) and 
		$this->verif_null($this->prenom) and 
		$this->verif_null($this->mail) and 
		$this->verif_null($this->login) and 
		$this->verif_null($this->password)){
			if(
			$this->verif_mail($this->mail) and 
			$this->verif_login($this->login) and 
			$this->verif_captcha($this->captchaResult)){
				return 1;
			}
			return NULL; 
		}
		return NULL; 
	}
	public function printForm(){
		echo '<div style="padding:2px;margin:2px;" >';
		echo '<h3>Vous êtes inscrit</h3>';
		echo '</div>';
		echo '<div style="padding:2px;border:solid 2px #000000;background-color:#000001;width:600px;color:#ffffff;" >';
		echo 'Contenu de votre inscription';
		echo '</div>';
		echo '<div style="padding:2px;border:solid 2px #000000;background-color:#CDE9E5;width:600px;" >';
		echo '<ul><li><b>Votre nom : </b>'.$this->prenom.' '.$this->nom.'</li>';
		echo '<li><b>login : </b>'.$this->login.'</li>';
		echo '<li><b>Votre mail : </b>'.$this->mail.'</li></ul>'; 
		echo '</div>';
		echo '<p><a href="./">Retour à la page d\'accueil</a></p>';
	}
}
?>