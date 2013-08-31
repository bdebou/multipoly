<?php
//this needs to reside in its own php page
//you can include that php page in your html as you would an image:
//<IMG SRC="ratingpng.php?rating=25.2" border="0">

function drawRating($rating,$hauteur,$largeur) {
	//$largeur=102;$hauteur=10;
    $image = imagecreate($largeur,$hauteur);
    $back = ImageColorAllocate($image,255,255,255);
    $border = ImageColorAllocate($image,0,0,0);
    $red = ImageColorAllocate($image,255,60,75);
    $fill = ImageColorAllocate($image,44,81,150);
    ImageFilledRectangle($image,0,0,$largeur-1,$hauteur-1,$back);
    ImageFilledRectangle($image,1,1,($largeur/100*$rating),$hauteur-1,$fill);
    ImageRectangle($image,0,0,$largeur-1,$hauteur-1,$border);
    imagePNG($image);
    imagedestroy($image);
}

Header("Content-type: image/png");
drawRating($_GET['rating'],$_GET['h'],$_GET['l']);

?>