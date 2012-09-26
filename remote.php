<?php
require("uploadImg.php");

$url = 'http://www.pikanus.net/img/2010/jXsTZ5q.jpg';

$test = new uploadImg('caca',true);
$test->get($url);
$test->createMin(200,400,'anus_','min');

?>