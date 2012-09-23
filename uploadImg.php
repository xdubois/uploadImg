<?php
class uploadImg{

	private $url_src; //source image
	private $targetPath; //chemin d'enrgistrement
	private $dest_folder;
	private $img; //Info sur l'image
	private $ext; //extension
	private $mime; //liste type mime autorisé
	private $im; //miniature
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
	    //todo
	    
	}
	
	public function _checkDir($dir){
	    
	    if(!is_dir($dir)){
		mkdir($dir);
		
	    }   
	}
	
	public function get($url){
		$this->url_src = $url;
		
		if($this->fileExists($this->url_src) !== FALSE){
		
			$this->img = getimagesize($this->url_src);
			if($this->checkMime() !== FALSE){
				 
				 if($this->rename)
				    $this->name = $this->randName().$this->ext;
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
	    $this->url_src = $file['tmp_name'];
	    $this->img = getimagesize($file['tmp_name']);
	    
	
	    if($this->checkMime() !== FALSE){
		if($this->rename)
		    $this->name = $this->randName().$this->ext;
		else
		    $this->name = $file['name'];
		    
		if(move_uploaded_file($file['tmp_name'],$this->targetPath.$this->name))
		    return $this->name;
		else
		    return false;
	   }
	}
    
	
	private function checkMime(){
	    if(in_array($this->img['mime'],$this->mime)){
		
		switch ($this->img['mime']) { 
		    case "image/gif": 
			    $this->ext = '.gif';
			    $this->im = @imagecreatefromgif($this->url_src);
			    break; 
		    case "image/jpeg": 
			    $this->ext = '.jpg';
			    $this->im = @imagecreatefromjpeg($this->url_src);
			    break; 
		    case "image/png":
			    $this->ext = '.png';
			    $this->im = @imagecreatefrompng($this->url_src);
			    break; 
		}
	    }
	    else{
		return false;
	    }
	    
	}
	
	
	function fileExists($path){
	    return (@fopen($path,"r")==true);
	}
	
	public function creatMin($max_width = 200,  $max_height = 400, $minSuffixe= 'min_', $minFolder = '/'){
	   
	    $this->_checkDir($this->dest_folder.'/'.$minFolder);   
	
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
	    $ls = imagesx($this->im);
	    $hs = imagesy($this->im);
	    $ld = imagesx($destination);
	    $hd = imagesy($destination);
	    //on remplit
	    imagecopyresampled($destination, $this->im, 0, 0, 0, 0, $ld, $hd, $ls, $hs);
	    imagejpeg($destination, $this->targetPath.$minFolder.'/'.$minSuffixe.$this->name);
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