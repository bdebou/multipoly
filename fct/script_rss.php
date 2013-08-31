<?php
require('../config.php');

date_default_timezone_set('Europe/Brussels');

// on récupère les news
$sql = "SELECT id, date, login, montant, action, code, description FROM table_history ORDER BY id DESC;";
$nws = mysql_query($sql) or die (mysql_error());

// on crée le fichier XML
$xml_file = new DOMDocument('1.0');

// on initialise le fichier XML pour le flux RSS
$channel = init_news_rss($xml_file);

// on ajoute chaque news au fichier RSS

while($news = mysql_fetch_assoc($nws)){
	$news_titre=null;
	$news_description='<p>';
	//$news_titre=$news['login'].' ';
	switch($news['action']){
		case 'construction': 
			$news_titre.=$news['action'];
			$news_description.=$news['login'].' '.$news['description'].' pour '.$news['montant'].'€';
			break;
		//case 'vente': $stat['construction']-=$row['montant'];break;
		case 'achat':
			$news_titre.=$news['action'];
			$news_description.=$news['login'].' '.$news['description'].' pour '.$news['montant'].'€';
			break;
		case 'taxe': 
			$news_titre.=$news['action'];
			$news_description.=$news['login'].' '.$news['description'].' de '.$news['montant'].'€';
			break;
		case 'des':
			$news_titre.=$news['action'];
			$news_description.=$news['login'].' a '.$news['description'];
			break;
		case 'innactivite': 
			$news_titre.=$news['action'];
			$news_description.=$news['login'].' a '.$news['description'];
			break;
		case 'transaction':
			$news_titre.=$news['action'];
			$news_description.=$news['login'].' '.$news['description'].' pour '.$news['montant'].'€';
			break;
		case 'loyéP':
			$news_titre.='Loyé';
			$news_description.=$news['login'].' '.$news['description'].' pour un montant de '.$news['montant'].'€';
			break;
		case ($news['action']=='hypoP' || $news['action']=='hypoM'):
			$news_titre.='Hypothèque';
			$news_description.=$news['login'].' '.$news['description'].' pour un montant de '.$news['montant'].'€';
			break;
		case ($news['action']=='prisonIN' || $news['action']=='prisonOUTC' || $news['action']=='prisonOUT'):
			$news_titre.='Prison';
			$news_description.=$news['login'].' '.$news['description'];
			break;
		case ($news['action']=='chanceM' || $news['action']=='chanceP' || $news['action']=='chanceD'):
			$news_titre.='Chance';
			$news_description.='<p>'.$news['login'].' a tiré une carte '.$news['description'].'</br>';
			if($news['action']=='chanceM'){$news_description.='Et a payé un montant de '.$news['montant'].'€</p>';}
			if($news['action']=='chanceP'){$news_description.='Et a reçu un montant de '.$news['montant'].'€</p>';}
			break;
		case ($news['action']=='caisseM' || $news['action']=='caisseP' || $news['action']=='caisseD'):
			$news_titre.='Caisse de communauté';
			$news_description.='<p>'.$news['login'].' a tiré une carte '.$news['description'].'</br>';
			if($news['action']=='caisseM'){$news_description.='Et a payé un montant de '.$news['montant'].'€</p>';}
			if($news['action']=='caisseP'){$news_description.='Et a reçu un montant de '.$news['montant'].'€</p>';}
			break;
		case 'départ':
			$news_titre.='Départ';
			$news_description.=$news['login'].' '.$news['description'].' et a touché la somme de '.$news['montant'].'€';
			break;
		case 'parking':
			$news_titre.='Parking';
			$news_description.=$news['login'].' '.$news['description'].' et a touché la somme de '.$news['montant'].'€';
			break;
	}
	$news_description.='</p>';
	if($news_titre!=null){
		add_news_node($xml_file, $channel, $news["id"], nettoie($news["login"]),nettoie($news_titre), nettoie($news_description), date('r',strtotime($news["date"])));
	}
}

// on écrit le fichier
$xml_file->save('./activity.xml');

//retour index.php
header('Location: ../index.php');

function nettoie($chaine){
	$chaine = html_entity_decode(stripslashes($chaine),ENT_QUOTES);
	$chaine = strip_tags($chaine);
	$chaine = str_replace("&euro;","€",$chaine);
	$chaine = str_replace("&","&amp;",$chaine);
	$chaine = str_replace("€;","€",$chaine);
	$chaine = utf8_encode($chaine);
	$chaine = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC),  $chaine); // €
	return $chaine;
}
function &init_news_rss(&$xml_file){
        $root = $xml_file->createElement("rss"); // création de l'élément
        $root->setAttribute("version", "2.0"); // on lui ajoute un attribut
		$root->setAttribute("xmlns:atom", "http://www.w3.org/2005/Atom");
        $root = $xml_file->appendChild($root); // on l'insère dans le nœud parent (ici root, qui est "rss")
        
        $channel = $xml_file->createElement("channel");
        $channel = $root->appendChild($channel);
                
        $desc = $xml_file->createElement("description");
        $desc = $channel->appendChild($desc);
        $text_desc = $xml_file->createTextNode(nettoie("Les évènements du BoubouPoly en temps réel.")); // on insère du texte entre les balises <description></description>
        $text_desc = $desc->appendChild($text_desc);
        
        $link = $xml_file->createElement("link");
        $link = $channel->appendChild($link);
        $text_link = $xml_file->createTextNode("http://dcboubou.dyndns.org/game/index.php");
        $text_link = $link->appendChild($text_link);
        
		$link = $xml_file->createElement("atom:link");
		$link->setAttribute("href", "http://dcboubou.dyndns.org/game/fct/activity.xml");
		$link->setAttribute("rel", "self");
		$link->setAttribute("type", "application/rss+xml");
        $link = $channel->appendChild($link);
		
		$language = $xml_file->createElement("language");
        $language = $channel->appendChild($language);
        $text_language = $xml_file->createTextNode('fr-be');
        $text_language = $language->appendChild($text_language);
		
		$lastBuildDate = $xml_file->createElement("lastBuildDate");
        $lastBuildDate = $channel->appendChild($lastBuildDate);
        $text_lastBuildDate = $xml_file->createTextNode(date('r'));
        $text_lastBuildDate = $lastBuildDate->appendChild($text_lastBuildDate);
		
        $title = $xml_file->createElement("title");
        $title = $channel->appendChild($title);
        $text_title = $xml_file->createTextNode(nettoie("Activité BoubouPoly"));
        $text_title = $title->appendChild($text_title);
        
        return $channel;
}
function add_news_node(&$parent, $root, $id, $pseudo, $titre, $contenu, $date){
        $item = $parent->createElement("item");
        $item = $root->appendChild($item);
        
        $title = $parent->createElement("title");
        $title = $item->appendChild($title);
        $text_title = $parent->createTextNode($titre);
        $text_title = $title->appendChild($text_title);
        
		$link = $parent->createElement("link");
        $link = $item->appendChild($link);
        $text_link = $parent->createTextNode("http://dcboubou.dyndns.org/game/index.php");
        $text_link = $link->appendChild($text_link);
        
        $desc = $parent->createElement("description");
        $desc = $item->appendChild($desc);
        $text_desc = $parent->createTextNode($contenu);
        $text_desc = $desc->appendChild($text_desc);
        
        /*
		$com = $parent->createElement("comments");
        $com = $item->appendChild($com);
        $text_com = $parent->createTextNode("http://www.bougiemind.info/news-11-".$id.".html");
        $text_com = $com->appendChild($text_com);
		*/
        /*
        $author = $parent->createElement("author");
        $author = $item->appendChild($author);
        $text_author = $parent->createTextNode($pseudo);
        $text_author = $author->appendChild($text_author);
        */
		
        $pubdate = $parent->createElement("pubDate");
        $pubdate = $item->appendChild($pubdate);
        $text_date = $parent->createTextNode($date);
        $text_date = $pubdate->appendChild($text_date);
        
        $guid = $parent->createElement("guid");
        $guid = $item->appendChild($guid);
        $text_guid = $parent->createTextNode('BoubouPoly-'.$id);
        $text_guid = $guid->appendChild($text_guid);
        
		/*
        $src = $parent->createElement("source");
        $src = $item->appendChild($src);
        $text_src = $parent->createTextNode("http://dcboubou.dyndns.org");
        $text_src = $src->appendChild($text_src);
		*/
}
?>