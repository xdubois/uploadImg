<?php
require("uploadImg.php");

$test = new uploadImg('caca',true);
$test->upload($_FILES['img']);
$test->creatMin();

?>