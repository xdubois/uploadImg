<?php
/*
uploadImg v0.2
xDubois - 26.09.2012

*/
class uploadImg{

	private $url_src; //source image
	private $targetPath; //chemin d'enregistrement
	public $img; //Info sur l'image
	private $mime; //liste type mime autorisé
	private $name; //nom photo
	private $rename; //Bool 
	private $temp_dir;

	public function __construct($dest_folder = '',$rename = FALSE){
		
		$this->targetPath = $_SERVER['DOCUMENT_ROOT'].$dest_folder.'/';
		$this->mime = array('image/gif','image/jpeg','image/jpg','image/png');
		$this->rename = $rename;
		$this->temp_dir = $this->targetPath.'/temp/';
		$this->_checkDir($this->targetPath);   
		$this->_checkDir($this->temp_dir);   

	}
	
	public function setMime($mime){
	    
	    $this->mime = $mime;
	}
	
	public function get($url){
		
	    $ch = curl_init ($url);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	    $raw=curl_exec($ch);

		if(curl_getinfo($ch, CURLINFO_CONTENT_TYPE) !== FALSE){
			
			$this->img['mime'] = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			curl_close ($ch);
			
			if(file_exists($this->temp_dir.basename($url)))
				unlink($this->temp_dir.basename($url));
		
			$fp = fopen($this->temp_dir.basename($url),'x');
			fwrite($fp, $raw);
			fclose($fp);
				
			$this->img = getimagesize($this->temp_dir.basename($url));
			unlink($this->temp_dir.basename($url));
			if($this->img !== FALSE){
				if($this->checkMime() !== FALSE){
					if($this->rename){
							$this->name = $this->randName().'.'.$this->getExt();//.$this->getExt();
					}
					else
						$this->name = basename($this->url_src);

					$fp = fopen($this->targetPath.$this->name,'x');
					fwrite($fp, $raw);
					fclose($fp);
					
					$this->url_src = $this->targetPath.$this->name;
					$this->img = getimagesize($this->targetPath.$this->name);
					return $this->name;
				}
				else
					return false;
			}
			return false;
			
		}
		else
			return false;
		
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
				return $this->name ;
				}
			else
				return false;
	   }
		return false;
	}
	
	public function createMin($max_width = 200,  $max_height = 400, $minSuffixe= 'min_', $minFolder = ''){
	   
	    $this->_checkDir($this->targetPath.'/'.$minFolder);   
		
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
	
	public function setImg($img){
			
			$this->url_src = __DIR__.'/'.$img;
			$this->name = $img;
			$this->img = getimagesize($this->url_src);
	
	}
	
	public function watermark($text,$font_pos = 'right',$font_size = 12,$font_file,$padding = 10,$font_color = array(255,255,255,0)){

		switch ($this->img['mime']) { 
			case "image/gif": 
				$img = @imagecreatefromgif($this->url_src);
				break; 
			case "image/jpeg": 
				$img = @imagecreatefromjpeg($this->url_src);
				break;
			case "image/png":
				$img = @imagecreatefrompng($this->url_src);
				imagesavealpha($img,true);
				break; 
			default:
				 $im = false;
				break;
		}
		
		//box text
		$rect = imagettfbbox($font_size,0,$font_file,$text);
		$minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
		$maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
		$minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
		$maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));
		$the_box = array(
		 "left"   => abs($minX) - 1,
		 "top"    => abs($minY) - 1,
		 "width"  => $maxX - $minX,
		 "height" => $maxY - $minY,
		 "box"    => $rect
		); 
		
		switch ($font_pos){
			case 'right':
				$posX = $this->img[0] - $the_box["width"] - $padding;
				$posY = $this->img[1] - $padding;
				break;
			case 'left':
				$posX = $padding;
				$posY = $this->img[1] - $padding;
				break;
			case 'center':
				$posX = $the_box["left"] + ($this->img[0] / 2) - ($the_box["width"] / 2);
				$posY = $the_box["top"] + ($this->img[1] / 2) - ($the_box["height"] / 2);
				break;
		
		}
		 if(!isset($font_color[3]))
			 $font_color[3] = 0;

		$letter_color = imagecolorresolvealpha($img, $font_color[0], $font_color[1], $font_color[2],$font_color[3]);
		imagettftext($img,$font_size,0,$posX,$posY,$letter_color,$font_file,$text); 
		
		switch ($this->img['mime']) { 
			case "image/gif": 
				imagegif($img, $this->targetPath.$this->name);
				break; 
			case "image/jpeg": 
				imagejpeg($img, $this->targetPath.$this->name);
				break;
			case "image/png":
				imagepng($img, $this->targetPath.$this->name);
				break; 
		}
		imagedestroy($img);
	}
	
	private function _checkDir($dir){
		if(!is_dir($dir))
			@mkdir($dir);
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
	    return (@fopen($path,"r"));
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