<?php
require '../class.uploadImg.php';

$url = 'http://www.url.com/my_img.png';

$test = new uploadImg('upload',true);
$res = $test->get($url);
if($res !== FALSE)
	$test->createMin(200,400,'min_','min_folder');

?>