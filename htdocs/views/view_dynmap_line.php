<?php
/* This file is part of JFFNMS
 * Copyright (C) <2002-2005> Javier Szyszlican <javier@szysz.com>
 * This program is licensed under the GNU GPL, full terms in the LICENSE file
 */
/* This is a separate little function */
$totalx = filter_input(INPUT_POST, 'totalx', FILTER_SANITIZE_NUMBER_INT);
$totaly = filter_input(INPUT_POST, 'totaly', FILTER_SANITIZE_NUMBER_INT);
$con = filter_input(INPUT_POST, 'con', FILTER_SANITIZE_NUMBER_INT, FILTER_FORCE_ARRAY);
if ($totalx < 1 || $totaly < 1)
  die("Unable to create image that is $totalx x $totaly");

$im =imagecreate($totalx,$totaly);
$cRed=ImageColorAllocate($im,255,0,0);
ImageColorTransparent($im,$cRed);
$cBlack=ImageColorAllocate($im,0,0,0);

foreach ($con as $aux) {
  list ($x1,$y1,$x2,$y2) = explode (",",$aux); 
  ImageLine($im,$x1,$y1,$x2,$y2,$cBlack);  
}
    
Header("Content-type: image/png"); 
ImagePNG($im);
?>
