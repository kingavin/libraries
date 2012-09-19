<?php
class Class_Image
{
    const TYPE_JPG = 'jpg';
    const TYPE_GIF = 'gif';
    const TYPE_PNG = 'png';
    
    const RIGHTCORNER = 'rightCorner';
    const CENTER = 'center';
    const BOTTOM = 'bottom';
    
    const FIT_TO_FRAME = 'fitToFrame';
    const FIT_TO_SIZE = 'fitToSize';
    
    protected $_im = null;
    protected $_imOrigin = null;
    protected $_waterMark = null;
    protected $_waterPos = null;
    
    protected $_origWidth = null;
    protected $_origHeight = null;
    
    public function setWaterMark($filePath, $pos = 'rightCorner')
    {
        $this->_waterMark = imagecreatefromgif($filePath);
        $this->_waterPos = $pos;
        
    	if (!is_null($this->_waterMark) && !is_null($this->_imOrigin)) {
            $watermarkWidth = imagesx($this->_waterMark);
            $watermarkHeight = imagesy($this->_waterMark);
            $imageWidth = imagesx($this->_imOrigin);
            $imageHeight = imagesy($this->_imOrigin);
            switch($this->_waterPos) {
                case 'center':
                    $dest_x = ($imageWidth - $watermarkWidth)/2;
                    $dest_y = ($imageHeight - $watermarkHeight)/2;
                    break;
                case 'rightCorner':
                    $dest_x = $imageWidth - $watermarkWidth - 5;
                    $dest_y = $imageHeight - $watermarkHeight - 5;
                    break;
                case 'bottom' :
                	$dest_x = ($imageWidth - $watermarkWidth)/2;
                    $dest_y = ($imageHeight - $watermarkHeight)/2+$imageHeight/4;
                    break;
            }
            imagecopymerge($this->_imOrigin, $this->_waterMark, $dest_x, $dest_y, 0, 0, $watermarkWidth, $watermarkHeight,40);  
            imagedestroy($this->_watermark);
        }else{
        	throw new Zend_Exception('waterMark or img not found!');
        }
        return $this;
    }
    
    protected function _filenameToMime($filename)
    {
        $types = array(
            'jpg' => self::TYPE_JPG,
            'gif' => self::TYPE_GIF,
            'png' => self::TYPE_PNG
        );
        foreach($types as $extension => $mime) {
            if(preg_match('/\.'.$extension.'$/', $filename)) {
                return $mime;   
            }
        }
        throw new Zend_Exception('Unknown image type.');
    }
    
    public function readImage($filename, $type = NULL)
    {
        if($type === NULL) {
            $type = $this->_filenameToMime($filename);
        }
        switch($type) {
            case self::TYPE_JPG:
                $this->_imOrigin = imagecreatefromjpeg($filename);
                break;
            case self::TYPE_GIF:
                $this->_imOrigin = imagecreatefromgif($filename);
                break;
            case self::TYPE_PNG:
                $this->_imOrigin = imagecreatefrompng($filename);
                break;
            default:
                break;
        }
        list($this->_origWidth, $this->_origHeight) = getimagesize($filename);
        $this->_im = null;
        
        return $this;
    }
    
    public function writeImage($filename, $quality = 100)
    {
        if(is_null($this->_imOrigin)) {
            throw new Exception('image file not found');
        }
        if(is_null($this->_im)) {
            $this->_im = $this->_imOrigin;
        }
        
        $type = $this->_filenameToMime($filename);
        switch ($type) {
            case self::TYPE_JPG:
                imagejpeg($this->_im, $filename, $quality);
                break;
            case self::TYPE_GIF:
                imagegif($this->_im, $filename, $quality);
                break;
            case self::TYPE_PNG:
            	$quality = ($quality - 10) / 9;
                imagepng($this->_im, $filename, $quality);
                break;
            default:
                break;
        }
        return $this;
    }
    
    public function resize($frameWidth, $frameHeight, $fit)
    {
        if(is_null($this->_imOrigin)) {
            throw new Exception('image file not found');
        }
        
        if ($frameWidth === NULL || $frameHeight === NULL || $fit === NULL) {
            throw new Exception('frame width or height or fit not defined!');
        }
        
        $tmpWidth = $frameWidth;
        $tmpHeight = $frameHeight;
        
        $frameProportion = $frameWidth/$frameHeight;
    	$origProportion = $this->_origWidth / $this->_origHeight;
        
		switch($fit) {
    		case 'fitToSize':
    			if($origProportion > $frameProportion) {
    				$tmpWidth = $this->_origWidth * ($frameHeight / $this->_origHeight);
    			} else {
    				$tmpHeight = $this->_origHeight * ($frameWidth / $this->_origWidth);
   				}
   				break;
  			case 'fitToFrame':
  				if($origProportion > $frameProportion) {
					$tmpHeight = $frameWidth / $origProportion;
				} else {
					$tmpWidth = $frameHeight * $origProportion;
				}
				break;
		}
        
        $tmpIm = imagecreatetruecolor($tmpWidth, $tmpHeight);
        imagecopyresampled($tmpIm, $this->_imOrigin, 0, 0, 0, 0, $tmpWidth, $tmpHeight, $this->_origWidth, $this->_origHeight);
        $finalIm = imagecreatetruecolor($frameWidth, $frameHeight);
        
		switch($fit) {
			case 'fitToSize':
				imagecopy($finalIm, $tmpIm, 0, 0, ($tmpWidth - $frameWidth) / 2, ($tmpHeight - $frameHeight) / 2, $frameWidth, $frameHeight);
				break;
			case 'fitToFrame':
				imagefill($finalIm, 0, 0, imagecolorallocate($finalIm, 255, 255, 255));
				imagecopy($finalIm, $tmpIm, ($frameWidth - $tmpWidth) / 2, ($frameHeight - $tmpHeight) / 2, 0, 0, $tmpWidth, $tmpHeight);
				break;
		}
        $this->_im = $finalIm;
        
        return $this;
    }
    
    public function crop($proportion)
    {
    	$origPro = $this->_origWidth/$this->_origHeight;
    	if($origPro > $proportion) {
    		//crop width
    		$toWidth = $this->_origHeight * $proportion;
    	} else if($origPro < $proportion) {
    		//crop height
    		
    		
    	} else {
    		return $this;
    	}
    }
}