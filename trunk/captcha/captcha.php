<?php

 /*
  * Created on 4 mai 07
  *
  * @autor : The Kankrelune 
  * @copyright : The WebFaktory © 2006/2007
  *
  */
  
/************************************************** Configuration **************************************************/

$chars = 'ABDEFGHKMNPRTUVWXZ3689';										// liste des charactère (certain caractères ne sont pas présents pour éviter les confusions)
$nbChar = 5;															// nombre de charactères du code
$startOffset = 10;														// offset de départ sur l'image (en pixels)
$size_min = 35;															// taille minimum des caractères
$size_max = 60;															// taille maximum des caractères
$angle_min = 0;															// angle minimum d'inclinaison des caractères
$angle_max = 20;														// angle maximum d'inclinaison des caractères
$width = 201;															// largeur de l'image
$height = 71;															// hauteur de l'image
$addBlur = true;														// ajouter un floutage au code
$blurLevel = 1;															// niveau de floutage (entre 1 et 10 / 1 ou 2 conseillé après ça forme une bande autour des caractères)
$policePath =  dirname(__FILE__).DIRECTORY_SEPARATOR.'captcha.ttf';		// chemin de la police à utiliser

/********************************************* Ne rien toucher au dela *********************************************/

if(!isset($_SESSION))
	session_start();

// création de l'image contenant le code
$_charsImgHandler = imagecreatetruecolor($width,$height) OR exit('please activate GD lib');
$white = imagecolorallocate($_charsImgHandler, 255, 255, 255);
imagefill($_charsImgHandler, 0, 0,$white);
 
// on prépare et on copie le code en session et sur l'image
$i = -1;
$pos_x = $startOffset;
$_SESSION['captchaResult'] = '';
$cnt = strlen($chars)-1;

while(++$i<$nbChar)
{
	$char = $chars[mt_rand(0,$cnt)];
	$_SESSION['captchaResult'] .= $char;
	$color = imagecolorallocate($_charsImgHandler,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200));
	$size =  mt_rand($size_min, $size_max);
	$pos_y = mt_rand( $size, $height-3);
	imagettftext($_charsImgHandler, $size, mt_rand($angle_min,$angle_max), $pos_x, $pos_y, $color, $policePath, $char);
	$pos_x += 35;
}

/* on choisi et on applique l'effet brouillant */
switch(mt_rand(0,4))
{
	/* effet de dispersion */
	case 0:
		$x = -1;
		while(++$x<$width) 
		{
			$y = -1;
			while(++$y<$height) 
			{	
				$dispx = 1/(mt_rand(0,1) ? mt_rand(-2,-5) : mt_rand(2,5));
				$dispy = 1/(mt_rand(0,1) ? mt_rand(-2,-5) : mt_rand(2,5));
				
				if (($x + $dispx >= $width) || 
					($y + $dispy >= $height) || 
					($x + $dispx < 0) || 
					($y + $dispy < 0)) 
					continue;
			
				$oldcol = imagecolorat($_charsImgHandler, $x, $y);
				$newcol = imagecolorat($_charsImgHandler, $x + $dispx, $y + $dispy);
				imagesetpixel($_charsImgHandler, $x, $y, $newcol);
				imagesetpixel($_charsImgHandler, $x + $dispx, $y + $dispy, $oldcol);
			}
		}
	break;
	
	/* effet de cryptage */
	case 1:
		$ystop = $height-3;
		$y = -1;
	
		while(++$y<$ystop)
		{
			$j = $y+mt_rand()%3;
			$x = -1;
			
			while(++$x<$width)
			{
				$pixel = @imagecolorat($_charsImgHandler, $x, $y);
				$rgb = array( 'red' => ($pixel >> 16) & 0xFF, 'green' => ($pixel >> 8) & 0xFF, 'blue' => $pixel & 0xFF);
				$pixel = @imagecolorat($_charsImgHandler, $x, $j);
				$rgb2 = array( 'red' => ($pixel >> 16) & 0xFF, 'green' => ($pixel >> 8) & 0xFF, 'blue' => $pixel & 0xFF);
				
				$tmp = $rgb2['red'];
				$rgb2['red'] = $rgb['red'];
				$rgb['red'] = $tmp;
				
				$tmp = $rgb2['green'];
				$rgb2['green'] = $rgb['green'];
				$rgb['green'] = $tmp;
				
				$tmp = $rgb2['blue'];
				$rgb2['blue'] = $rgb['blue'];
				$rgb['blue'] = $tmp;
				
				imagesetpixel($_charsImgHandler,$x,$y,imagecolorallocate($_charsImgHandler,$rgb['red'],$rgb['green'],$rgb['blue']));
				imagesetpixel($_charsImgHandler,$x,$j,imagecolorallocate($_charsImgHandler,$rgb2['red'],$rgb2['green'],$rgb2['blue']));
			}
		}
	break;
	
	/* vagues horizontales */
	case 2:
		$_tempImg = imagecreatetruecolor($width,$height);
		imagefill($_tempImg,0,0,$white);
		$up = true;
		$offset = 0;
		$stop = $width-3;
		
		for($y=0;$y<$height;++$y)
		{	
			if($up === true)
				$offset++;
					else
						$offset--;
			
			for($x=3;$x<$stop;++$x)
			{
				$color = @imagecolorat($_charsImgHandler, $x, $y);
				imagesetpixel($_tempImg,$x+$offset,$y, $color ? $color : $white);
			}
			
			if($offset === 3)
				$up = false;
					elseif(empty($offset))
						$up = true;
		}
		imagedestroy($_charsImgHandler);
		$_charsImgHandler = $_tempImg;
	break;
	
	/* vagues verticales */
	case 3:
		$_tempImg = imagecreatetruecolor($width,$height);
		imagefill($_tempImg,0,0,$white);
		$up = true;
		$offset = 0;
		$stop = $height-3;
		
		for($x=0;$x<$width;++$x)
		{	
			if($up === true)
				$offset++;
					else
						$offset--;
			
			for($y=3;$y<$stop;++$y)
			{
				$color = @imagecolorat($_charsImgHandler, $x+$offset, $y);
				imagesetpixel($_tempImg,$x,$y, $color ? $color : $white);
			}
			
			if($offset === 3)
				$up = false;
					elseif(empty($offset))
						$up = true;
		}
		imagedestroy($_charsImgHandler);
		$_charsImgHandler = $_tempImg;
	break;
	
	/* effet fish eye */
	default:
		$_tempImg = imagecreatetruecolor($width,$height);
		imageFill($_tempImg,0,0,$white);
		$xmid = (int)($width/2);
		$ymid = (int)($height/2);
		$start = (int)sqrt((float)($xmid*$xmid+$ymid*$ymid));
		
		$x = -1;
		while(++$x<$width) 
		{
			$y = -1;
			while(++$y<$height) 
			{
				$nx = $xmid-$x;
				$ny = $ymid-$y;
				$radius = sqrt((float)($nx*$nx+$ny*$ny));
				
				if($radius < $start) 
				{
					$angle = atan2((double)$ny,(double)$nx);
					$rnew = ($radius*$radius/$start);
					$nx = $xmid + (int)($rnew * cos($angle));
					$ny = $y;
					
					$nx = max(0,min($nx,$width));
					$ny = max(0,min($ny,$height));
					
					if(false === ($color = @imagecolorat($_charsImgHandler, $nx, $ny)))
						$color = $white;
				}
				else $color = $white;
			
				imagesetpixel($_tempImg, $x, $y, $color);
			}
		}
	
		imagedestroy($_charsImgHandler);
		$_charsImgHandler = imagecreatetruecolor($width, $height); 
	
		for ($x=0;$x<$width;$x++)
			imagecopy($_charsImgHandler,$_tempImg, $x, 0, $width - $x - 1, 0, 1, $height);
	
		imagedestroy($_tempImg);
}

if($addBlur === true)
{
	if($blurLevel < 1)
		$blurLevel = 1;
			elseif($blurLevel > 10)
				$blurLevel = 10;
	
	$coeffs = array (
				array ( 1),
				array ( 1,  1), 
				array ( 1,  2,  1),
				array ( 1,  3,  3,   1),
				array ( 1,  4,  6,   4,   1),
				array ( 1,  5, 10,  10,   5,   1),
				array ( 1,  6, 15,  20,  15,   6,   1),
				array ( 1,  7, 21,  35,  35,  21,   7,   1),
				array ( 1,  8, 28,  56,  70,  56,  28,   8,   1),
				array ( 1,  9, 36,  84, 126, 126,  84,  36,   9,  1),
				array ( 1, 10, 45, 120, 210, 252, 210, 120,  45, 10,  1)
			);
	
	$sum = pow(2, $blurLevel);
	$temp1 = imagecreatetruecolor($width, $height);
	$temp2 = imagecreatetruecolor($width, $height);
	imagecopy($temp2,$_charsImgHandler,0,0,0,0,$width,$height);
	
	$y = -1;
	while(++$y<=$height)
	{
		$x = -1;
		while(++$x<=$width)
		{
			$sumr = 0; 
			$sumg = 0; 
			$sumb = 0;
			$k = -1;
			
			while(++$k<=$blurLevel)
			{
				$color = @imagecolorat($_charsImgHandler,($x-(($blurLevel)/2)+$k), $y);
				$sumr += (($color >> 16) & 0xFF) * $coeffs[$blurLevel][$k];
				$sumg += (($color >> 8) & 0xFF) * $coeffs[$blurLevel][$k];
				$sumb += ($color & 0xFF) * $coeffs[$blurLevel][$k];
			}
			
			$color = imagecolorallocate ($temp1,($sumr/$sum),($sumg/$sum),($sumb/$sum));
			imagesetpixel($temp1,$x,$y,$color);
		} 
	}
	
	imagedestroy($_charsImgHandler);
	$_charsImgHandler = $temp2;
	
	for($x=0;$x<$width;++$x) 
	{
		for($y=0;$y<$height;++$y) 
		{
			$sumr=0; $sumg=0; $sumb=0;
			
			for($k=0;$k<=$blurLevel;++$k) 
			{
				$color = @imagecolorat($temp1, $x,($y-(($blurLevel)/2)+$k));
				$sumr += (($color >> 16) & 0xFF) * $coeffs[$blurLevel][$k];
				$sumg += (($color >> 8) & 0xFF) * $coeffs[$blurLevel][$k];
				$sumb += ($color & 0xFF) * $coeffs[$blurLevel][$k];
			}
			
			$color = imagecolorallocate ($_charsImgHandler,($sumr/$sum),($sumg/$sum),($sumb/$sum));
			imagesetpixel($_charsImgHandler,$x,$y,$color);
		} 
	}
	
	imagedestroy($temp1);
}

// on rend transparent le fond de l'image contenant les caractères
imagecolortransparent($_charsImgHandler,$white);

// création image de fond
$_bgImgHandler = imagecreatetruecolor($width,$height);
imagefill($_bgImgHandler, 0, 0,$white);

// choix de la couleur des lignes et du type de quadrillage
$line = imagecolorallocate($_bgImgHandler,mt_rand(200,230),mt_rand(200,230),mt_rand(200,230));
$lineType = mt_rand(0,11);

switch($lineType)
{
	/* Lignes et grilles "normales" */
	case 0:
	case 1:
	case 2:
		if($lineType !== 0) // ligne horizontales ou grille
		{
			for($y=0;$y<$height;$y+=5)
				imageline( $_bgImgHandler, 0, $y, $width, $y,$line);
		}
		if($lineType !== 1) // ligne verticales ou grille
		{
			for($y=0;$y<$width;$y+=5)
				imageline( $_bgImgHandler, $y, 0, $y, $width, $line);
		}
	break;	
	
	/* éventails */
	case 3:
	case 4:
	case 5:
		if($lineType !== 3) // eventail horizontal ou en grille
		{
			for($y=0,$z=0; $y<$width; ++$y,$z+=5)
				imageline( $_bgImgHandler, $width*2, 0, 0, $z, $line);
		}
		if($lineType !== 4) // eventail vertical ou en grille
		{
			for($y=0,$z=0; $y<$height; ++$y,$z+=5)
				imageline( $_bgImgHandler, 0, $width, $z, 0, $line);
		}	
	break;
	
	/* effet "matrix" */
	case 6: 
		for($x=0;$x<$width;$x+=mt_rand(1,4))
		{
			for($y=0;$y<$height;$y+=mt_rand(1,4))
				imagesetpixel( $_charsImgHandler, $x, $y, $line);
		}
	break;
	
	/* grille de points */
	case 7: 
		for($x=0;$x<$width;$x+=2)
		{
			for($y=0;$y<$height;$y+=2)
				imagesetpixel( $_charsImgHandler, $x, $y, $line);
		}
	break;
	
	/* autre grille de points */
	case 8: 
		for($x=0;$x<$width;$x+=3)
		{
			for($y=0;$y<$height;$y+=2)
				imagesetpixel( $_charsImgHandler, $x, $y, $line);
		}
	break;
	
	/* vaguelettes horizontales */
	case 9: 
		$up = true;
		$offset = 0;
		
		for($y=0;$y<$height;$y+=5)
		{	
			for($x=0;$x<$width;++$x)
			{
				if($up === true)
					$offset++;
						else
							$offset--;
			
				imagesetpixel( $_bgImgHandler, $x, $y+$offset, $line);
			
				if($offset === 3)
					$up = false;
						elseif(empty($offset))
							$up = true;
			}
		}
	break;
	
	/* vaguelettes verticales */
	case 10: 
		$up = true;
		$offset = 0;
		
		for($x=0;$x<$width;$x+=5)
		{	
			for($y=0;$y<$height;++$y)
			{
				if($up === true)
					$offset++;
						else
							$offset--;
				
				imagesetpixel( $_bgImgHandler, $x+$offset, $y, $line);
				
				if($offset === 3)
					$up = false;
						elseif(empty($offset))
							$up = true;
			}
		}
	break;
	
	/* grille irrégulière */
	default: 
		for($y=0;$y<$height;$y+=mt_rand(2,5))
			imageline( $_bgImgHandler, 0, $y, $width, $y,$line);
	
		for($y=0;$y<$width;$y+=mt_rand(2,5))
			imageline( $_bgImgHandler, $y, 0, $y, $width, $line);
}

// fusion du code et du fond
imagecopymerge($_bgImgHandler, $_charsImgHandler, 0, 0, 0, 0, $width, $height,100);
imagedestroy($_charsImgHandler);

// on affiche
header('Pragma: no-cache');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false);
header ('Content-type: image/gif');
imagegif($_bgImgHandler);

?> 