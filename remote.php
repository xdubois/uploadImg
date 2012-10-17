<?php
require 'class.uploadImg.php';

$url = 'http://www.url.com/my_img.png';

$test = new uploadImg('caca',true);
$res = $test->get($url);
if($res !== FALSE)
	$test->createMin(200,400,'anus_','min');

?>