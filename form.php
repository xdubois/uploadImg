<?php
require 'class.uploadImg.php';

$test = new uploadImg('upload',true); //dossier de destination - renommer le fichier true/false
$mime = array('image/jpeg');
$test->setMime($mime); //d�finit les types mime image autoris� par d�faut array('image/gif','image/jpeg','image/png');
$res = $test->upload($_FILES['img']); //upload 

$string =  "J'aime bien les �l�phants";
$padding = 10;
$color = array(250,140,88);


if($res !== FALSE){
	$test->watermark($string,'center',25,'./impact.ttf',$padding,$color);
	$test->createMin(200,400,'min_','min_folder'); //cr�er une miniature: hxw , extension_, dossier
}

?>