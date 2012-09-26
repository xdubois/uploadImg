<?php
/*
uploadImg v0.1
xDubois - 26.09.2012

*/
class uploadImg{

	private $url_src; //source image
	private $targetPath; //chemin d'enregistrement
	private $dest_folder;
	private $img; //Info sur l'image
	private $mime; //liste type mime autorisé
	private $name; //nom photo
	private $rename; //Bool 

	public function __construct($dest_folder = '',$rename = FALSE){
		
		$this->dest_folder = $dest_folder;
		$this->targetPath = $this->dest_folder .'/';
		//$this->targetPath = $_SERVER['DOCUMENT_ROOT'].$this->dest_folder .'/';
		$this->mime = array('image/gif','image/jpeg','image/png');
		$this->rename = $rename;
		    
		$this->_checkDir($this->dest_folder);   
		
	}
	
	public function setMime($mime){
	    
	    $this->mime = $mime;
	}
	
	public function get($url){
		$this->url_src = $url;
	
		if($this->fileExists($this->url_src) !== FALSE){
		
			$this->img = getimagesize($this->url_src);
			if($this->checkMime() !== FALSE){
				 
				 if($this->rename){
				    $this->name = $this->randName().'.'.$this->getExt();
				}
				else
				    $this->name = basename($this->url_src);
		
				$this->moveImg($this->url_src,$this->targetPath.$this->name);
				return $this->name;
			}
			else{
				return false;
			}
		
		}
		else{
			return false;

		}
	}
	
	public function upload($file){
	    $this->img = getimagesize($file['tmp_name']);
		
	    if($this->checkMime() !== FALSE){
			if($this->rename)
				$this->name = $this->randName().'.'.$this->getExt();
			else
				$this->name = $file['name'];
		    
			if(move_uploaded_file($file['tmp_name'],$this->targetPath.$this->name)){
				$this->url_src = $this->targetPath.$this->name;
				return $this->name;
				}
			else
				return false;
	   }
		return false;
	}
	
	public function createMin($max_width = 200,  $max_height = 400, $minSuffixe= 'min_', $minFolder = '/'){
	   
	    $this->_checkDir($this->dest_folder.'/'.$minFolder);   
		
		switch ($this->img['mime']) { 
			case "image/gif": 
				$im = @imagecreatefromgif($this->url_src);
				break; 
			case "image/jpeg": 
				$im = @imagecreatefromjpeg($this->url_src);
				break; 
			case "image/png":
				$im = @imagecreatefrompng($this->url_src);
				break; 
			default:
				 $im = false;
				break;
		}
		if($im){
			//Creation miniature
			$l=$this->img[0]; //largeur
			$h=$this->img[1]; //hauteur
			
			//Calcule taille via un ratio
			$ratioh = $max_height/$l; 
			$ratiow = $max_width/$h; 
			$ratio = min($ratioh,$ratiow); 
			$l = intval($ratio*$l); 
			$h = intval($ratio*$h);
			
			$destination = imagecreatetruecolor($l, $h); // On crée la miniature vide
			$ls = imagesx($im);
			$hs = imagesy($im);
			$ld = imagesx($destination);
			$hd = imagesy($destination);
			//on remplit
			imagecopyresampled($destination, $im, 0, 0, 0, 0, $ld, $hd, $ls, $hs);
			imagejpeg($destination, $this->targetPath.$minFolder.'/'.$minSuffixe.$this->name);
			
			switch ($this->img['mime']) { 
				case "image/gif": 
					imagegif($destination, $this->targetPath.$minFolder.'/'.$minSuffixe.$this->name);
					break; 
				case "image/jpeg": 
					imagejpeg($destination, $this->targetPath.$minFolder.'/'.$minSuffixe.$this->name);
					break; 
				case "image/png":
					imagepng($destination, $this->targetPath.$minFolder.'/'.$minSuffixe.$this->name);
					break; 
			}
	
		}
		else
			return false;
	}
	
	private function _checkDir($dir){
	
		if(!is_dir($dir)){
		mkdir($dir);
		
		}   
	}
	
	
	private function getExt(){
	$ext = explode('/',$this->img['mime']);
	if($ext[1] == 'jpeg')
		$ext[1] = 'jpg';
		
	return $ext[1];
	}
	
	private function checkMime(){
		return in_array($this->img['mime'],$this->mime);
	}
	
	
	private function fileExists($path){
	    return (@fopen($path,"r")==true);
	}
	
	
	private function moveImg($url,$saveto){
	    $ch = curl_init ($url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	    $raw=curl_exec($ch);
	    curl_close ($ch);
	    if(file_exists($saveto)){
		    unlink($saveto);
	    }
	    $fp = fopen($saveto,'x');
	    fwrite($fp, $raw);
	    fclose($fp);
	}
	
	private function randName(){
	    $characters = array(
	    "A","B","C","D","E","F","G","H","J","K","L","M",
	    "N","P","Q","R","S","T","U","V","W","X","Y","Z",
	    "a","b","c","d","e","f","g","h","j","k","l","m",
	    "n","p","q","r","s","t","u","v","w","x","y","z",
	    "1","2","3","4","5","6","7","8","9");
	    
	    $keys = array();
	    while(count($keys) < 7) {
			$x = mt_rand(0, count($characters)-1);
			if(!in_array($x, $keys)) {
			   $keys[] = $x;
			}
	    }
	    $random_chars="";
	    foreach($keys as $key){
	       $random_chars .= $characters[$key];
	    }
	    
	    return $random_chars;
	}
}

?>