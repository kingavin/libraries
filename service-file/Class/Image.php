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
    
    public function resize($width = NULL, $height = NULL, $fit = false)
    {
        if(is_null($this->_imOrigin)) {
            throw new Exception('image file not found');
        }
        
        if ($width === NULL || $height === NULL) {
            return $this;
        }
        
        $frameWidth = $width;
        $frameHeight = $height;
        
    	if($fit != false) {
    		switch($fit) {
    			case 'cropMiddle':
    				$this->crop($width/$height);
    				break;
    			case 'fitToFrame':
    				$frameProportion = $width/$height;
    				$origProportion = $this->_origWidth / $this->_origHeight;
    				if($origProportion > $frameProportion) {
    					$height = $width / $origProportion;
    				} else {
    					$width = $height * $origProportion;
    				}
    				
    				break;
    		}
        }
        
        $origWidth = imagesx($this->_imOrigin);
        $origHeight = imagesy($this->_imOrigin);

        $tmpIm = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmpIm, $this->_imOrigin, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
        
        if($fit != false) {
        	switch($fit) {
    			case 'cropMiddle':
    				//$this->crop($width/$height);
    				break;
    			case 'fitToFrame':
    				$finalIm = imagecreatetruecolor($frameWidth, $frameHeight);
    				imagefill($finalIm, 0, 0, imagecolorallocate($finalIm, 255, 255, 255));
    				imagecopy($finalIm, $tmpIm, ($frameWidth - $width) / 2, ($frameHeight - $height) / 2, 0, 0, $width, $height);
    				$tmpIm = $finalIm;
    				break;
    		}
        }
        $this->_im = $tmpIm;
        
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