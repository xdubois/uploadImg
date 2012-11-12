<?php
require 'class.uploadImg.php';

$up = new UploadImg('uploadImg');

//set our image
$up->setIMg('lol.png');


$string =  "J'aime bien les éléphants";
$padding = 10;
$color = array(250,140,88);
$up->watermark($string,'left',25,'./impact.ttf',$padding,$color);

//you can also create a thumb of it
$up->createMin();

?>

