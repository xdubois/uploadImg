uploadImg v0.2
===============

Simple remote/form img upload

basic
-----

$myUpload = new uploadImg($folder_dest,false); 

param 1 : destination folder
param 2 : set if the image name has to be renamed with a random name.

public method
-------------

$myUpload->get('www.myurl.com/image.jpg');
Upload from an url;

$myUpload->upload($_FILES['img']);
Upload from a form

**Tools**

* $myUplaod->setImg('img.jpg');
If you just need to apply a watermark or create a thumb, you can set an image here.

* $myUpload->createMin($max_width = 200,  $max_height = 400, $minSuffixe= 'min_', $minFolder = '');
Generate a thumbail from the last image uploaded.

* $myUpload->watermark($text,$font_pos,$font_size,$font_file,$padding,$font_color);
Apply a watermark to the image

$text : "My watermark"
$font_pos : right,left,center
$font_size : default is 12
$font_file : path of your font_file
$padding: padding with the border of image
$font_color : RGB color of the text $font_color = array(255,255,255,0) : Default is white


* $myUpload->setMime($array);
set an array of valid mime
default : array('image/gif','image/jpeg','image/png');

Spec


$myUpload->get($url)
youn need a temp folder in $folder_dest
myfolder/temp/

upload remote img from url

$myUpload->createMin($max_width = 200,  $max_height = 400, $minSuffixe= 'min_', $minFolder = '/')



$myUpload->upload($_FILES['img'])

Upload from a form

$myUpload->setMime($array)

set an array of valid mime

default : array('image/gif','image/jpeg','image/png');