<?php
require 'class.uploadImg.php';

$test = new uploadImg('caca',true); //dossier de destination - renommer le fichier true/false
$mime = array('image/jpeg');
$test->setMime($mime); //d�finit les types mime image autoris� par d�faut array('image/gif','image/jpeg','image/png');
$res = $test->upload($_FILES['img']); //upload 
if($res !== FALSE)
	$test->createMin(200,400,'min_','min'); //cr�er une miniature: hxw , extension_, dossier

?>