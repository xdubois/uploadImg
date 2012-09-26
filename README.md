uploadImg v0.1
==========

Simple remote/form img upload

public method:

$myUpload = new uploadImg($folder_dest,false); 

param 1 : destination folder
param 2 : set if the image name has to be renamed with a random name.

$myUpload->get($url)

upload remote img from url

$myUpload->createMin($max_width = 200,  $max_height = 400, $minSuffixe= 'min_', $minFolder = '/')

Generate a thumbail from the last image uploaded

$myUpload->upload($_FILES['img'])

Upload from a form

$myUpload->setMime($array)

set an array of valid mime

default : array('image/gif','image/jpeg','image/png');
Note: only those mime types are supported currently