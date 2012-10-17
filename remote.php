<?php
require 'class.uploadImg.php';

$url = 'http://www.pikanus.net/img/2010/jXsTZ5q.jpg';

$test = new uploadImg('caca',true);
$res = $test->get($url);
if($res !== FALSE)
	$test->createMin(200,400,'anus_','min');

?>