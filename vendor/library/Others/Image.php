<?php
namespace Others;
use Frontend\FrontBundle\Helper\FrontendHelper;

/**
 * Class which handles uploaded images. 
 * @author : Bartosz KONIECZNY (http://www.bart-konieczny.com)
 * @version : 1.0 
 */

// TODO : catch exceptions during image parsing
// TODO : make file validation by space already occuped in the upload directory
// (for exemple : directory A accepts no more than 10mb. Actually his files take 9,5mb of space.
// So, we can't accept files bigger than 500kb).

class Image {  

  /** 
   * Public array with errors of uploaded files.
   * Every entry contains another array composed by 2 fields : 
   * - field : form field which isn't valid 
   * - type : error type (can be : tooBig, fileExt, reqNotUpl)
   * @var array
   * @access public
   */
  public $errors = array();

  /** 
   * Public array with files correctly uploaded.
   * @var array
   * @access public
   */
  public $uploadedFiles = array();

  /** 
   * Options used by this class. These options are used before the beginning of files upload.
   * List of applicable options :  
   * - maxSize : max size of uploaded file (int)
   * - extensions : list of accepted exensions (array)
   * - files : form fields of uploaded files (array)
   * - required : list with boolean values (false|true) which determines
   *              if the file upload is required (array)
   * - names : list with the names of new generated files (array)
   * @var array
   * @access private
   */
  private $options = array();

  /** 
   * Options used directly by uploaded files, after the first validation.
   * List of applicable options : 
   * - directory : destination directory of uploaded files (string)
   * - alias : aliases list applied to thumbnails (array)
   * - dimensions : list of thumbnails dimensions, like 400x200 for an image of 
                    400px of width and 200 of height (array)
   * - ratio : list of ratio when we want to change files dimensions (array, 
   *           valid values are : 
               - AUTO : the class set new dimensions (by the longer side)
               - WIDTH : the class set new dimensions by width of original image (width size is fixed, only height size changes)
               - HEIGHT : the class set new dimensions by height of original image (height size is fixed, only width changes)
               - STATIC: no ratio, file conserves the dimensions (not adviced, risk of image quality 
                 damage)
              )
   * - thumbs : informs if we generate thumbnails (bool)
   * @var array
   * @access private
   */
  private $fileOptions = array();

  /** 
   * FrontendHelper's instance, used to rewrite filename.
   * @var FrontendHelper
   * @access private
   */
  private $helper;

  /** 
   * Determines if the file is uploaded by uploadify.
   * @var boolean
   * @access private
   */
  private $isUploadify;

  /**
   * Constructor initializes options and validates uploaded images.
   * @param array $options Array with options like max file size, form fields to upload.
   * @param class $helper Helper used to generate "beautiful" file name.
   * @return void  
   */
  public function __construct($options, $helper) {
    $this->options = $options; 
    $this->isUploadify = $options['isUploadify']; 
    $this->validateImages();
    $this->helper = $helper;
  }

  /**
   * Private checker of uploaded files. It checks if the extensions are accepted and if the file size 
   * isn't too big.
   * Only required files are checked.
   * When an arror occures, it is put into $errors.
   * @return void
   */  
  private function validateImages() {
    $e = 0;
    $f = 0;
	foreach($this->options['files'] as $i => $image) {
      if(/*$this->options['required'][$i] ||*/ $_FILES[$image]['name'] != '') {
        $fileExt = explode('/', $_FILES[$image]['type']);	
        if($this->isUploadify)
        {
          // prepares file specialy for jQuery uploadify plugin
          $fileExt = explode('.', $_FILES[$image]['name']);
          $fileExt[1] = $fileExt[count($fileExt)-1];
        }  
	    if(!in_array($fileExt[1], $this->options['extensions'])) {
          $this->errors[$e] = array('field' => $image, 'type' => 'fileExt');
          $e++;		
	    }
        elseif($_FILES[$image]['size'] > $this->options['maxSize']) {
          $this->errors[$e] = array('field' => $image, 'type' => 'tooBig');
          $e++;
		}	
        else {
          // no errors, add the file into final list
          $this->files2Upload[$f] = array('image' => $image, 'name' => $this->options['names'][$i], 'ext' => $fileExt[1]);
          $f++;
        }
      }
      elseif($this->options['required'][$i] && $_FILES[$image]['name'] == '') {
        $this->errors[$e] = array('field' => $image, 'type' => 'reqNotUpl');
        $e++;
	  }  
    }
    if(count($this->errors) > 0) {
      $this->hasErrors = true;
    }
    else {
      $this->hasErrors = false;
    } 
  }
	 

  /**
   * If no errors, this function is called to initialize the files upload.
   * @param array $fileOptions Array with options applied to files.
   * @return void
   */ 
  public function uploadFiles($fileOptions) {
    //print_r($this->files2Upload); 
    $this->fileOptions = $fileOptions;
	$this->uploadTmpFiles();
  }

  /**
   * Handles files upload.
   * @return void
   */
  private function uploadTmpFiles() {
    $this->uploadedFiles = array();
    foreach($this->files2Upload as $i => $image) {
      move_uploaded_file($_FILES[$image['image']]['tmp_name'], $this->fileOptions['directory'].$_FILES[$image['image']]['name']);
      $imgDim = getimagesize($this->fileOptions['directory'].$_FILES[$image['image']]['name']);      
      $ext = $image['ext'];
      if($ext == 'jpg' || $ext == 'pjpeg') {
        $ext = 'jpeg';		
      }  
	  // if we haven't a default name
	  if(!$image['name']) {
	    $this->newName = rand(0, time()).$this->helper->makeFileName($_FILES[$image['image']]['name']);
      }
	  else {
	    $this->newName = $image['name'];
	  }
	  $newImage = $this->resizeImage(array('dim' => array('width' => $imgDim[0], 'height' => $imgDim[1]), 'file' => $this->fileOptions['directory'].$_FILES[$image['image']]['name'], 'extension' => $ext, 'alias' => ''));
      if($this->fileOptions['thumbs']) { 
//echo $this->fileOptions['directory'].$this->newName.'hhhhhhhhhhhhhh';
        $this->makeThumbs($this->fileOptions['directory'].$this->newName, $ext, $imgDim);
      }
      unlink($this->fileOptions['directory'].$_FILES[$image['image']]['name']);	  
	  $this->uploadedFiles[$i] = $this->newName;
    }
  }
  
  /** 
   * Makes files thumbnails.
   * @param string $originalFile Path to original file.
   * @param string $extension Extension of uploaded file.
   * @param array $imgDim Dimensions of new image.
   * @return void
   */
  private function makeThumbs($originalFile, $extension, $imgDim) {  
	foreach($this->fileOptions['alias'] as $a => $alias) { 
	  $dim = explode('x' , $this->fileOptions['dimensions'][$a]);
      $fileRatio = $this->fileOptions['ratio'][$a];
	  if($fileRatio != 'STATIC') {
        $dim = $this->calculateDimensions($fileRatio, $dim, $imgDim);
      }  
      else {
        $dim = array('width' => $dim[0], 'height' => $dim[1]);
      }
      $this->resizeImage(array('dim' => $dim, 'file' => $originalFile, 'extension' => $extension, 'alias' => $alias));
	}
  }
  
  /**
   * Calculates dimensions used to new image.
   * @param string $type Ratio type.
   * @param array $dimensions Dimensions which we want to apply to the new image.
   * @param array $imageDimensions Dimensions of uploaded image.
   * @return array List of dimensions
   */
  private function calculateDimensions($type, $dimensions, $imageDimensions) {
    $allowResize = true; 
	if($type == 'AUTO') { 
      if($imageDimensions[0] > $imageDimensions[1]) {   
        if($imageDimensions[0] < $dimensions[0]) {
          $allowResize = false;
        }
        else {
          $type = 'WIDTH';
		}
      }
      elseif($imageDimensions[1] > $imageDimensions[0]) {    
        if($imageDimensions[1] < $dimensions[1]) {
          $allowResize = false;
        }
        else {
          $type = 'HEIGHT';
        }
      }
      else {
        if($imageDimensions[1] < $dimensions[1] && $imageDimensions[0] < $dimensions[0]) {
          $allowResize = false;
		}
        else {
          $type = 'WIDTH';
        }
	  }
	}
    if($allowResize) {
      if($type == 'WIDTH') { 
        $newDim = $this->getRatioDimensions($dimensions, $imageDimensions, 'WIDTH');  
// Pour les images qui sont plus grandes (hauteur) que les dimensions indiquées, il faut 
// recalculer la ration en se basant sur le côté 
		if($newDim['height'] > $dimensions[1]) {  
          $newDim = array($newDim['width'], $newDim['height']);
          $newDim = $this->getRatioDimensions($dimensions, $newDim, 'HEIGHT');
        }     
	  }
      elseif($type == 'HEIGHT') { 
        $newDim = $this->getRatioDimensions($dimensions, $imageDimensions, 'HEIGHT');
		if($newDim['width'] > $dimensions[0]) {  
          $newDim = array($newDim['width'], $newDim['height']);
          $newDim = $this->getRatioDimensions($dimensions, $newDim, 'WIDTH');
        } 
      }
    }
    else {
      // garder les dimensions de l'image car elle est trop petite pour être redimensionnée
      $newDim = array('width' => $imageDimensions[0], 'height' => $imageDimensions[1]);
	}	
	// print_r($newDim); echo '<hr />';
    return $newDim;
  }

  /**
   * Getter of ratio dimensions.
   * @param array $dim Array with new dimensions.
   * @param array $dimImg Array with original dimensions of image.
   * @param string $type Ratio type.
   */
  private function getRatioDimensions($dim, $dimImg, $type) { 
    if($type == 'WIDTH') { 
      $newDim['width'] = $dim[0];
      $ratio = $dimImg[0]/$dim[0];
      $newDim['height'] = $dimImg[1]/$ratio;
    }
    else { 
      $newDim['height'] = $dim[1];
      $ratio = $dimImg[1]/$dim[1];
      $newDim['width'] = $dimImg[0]/$ratio; 
    } 
	return $newDim;
  }

  /**
   * Image resizer. 
   * @param array $options Array with options applied to this new image : 
   * - dim : dimensions of generated file
   * - file : original file
   * - extension : file extension
   * - alias : file alias
   */
  private function resizeImage($options) {  
    $fParseName = 'parse'.ucfirst($options['extension']).''; 
    $fCreateName = 'imagecreatefrom'.$options['extension'].'';
    $img = $fCreateName($options['file']);
    $imgMini = imagecreatetruecolor($options['dim']['width'], $options['dim']['height']); 
    $imgResult = $this->$fParseName(array('width' => $options['dim']['width'], 'height' => $options['dim']['height'], 'imgMini' => $imgMini,
	'initWidth' => imagesx($img), 'initHeight' => imagesy($img), 'img' => $img, 'alias' => $options['alias']));
    imagedestroy($imgMini);	  
  }

  /**
   * Function parses jpg original file and makes a thumbnail from it.
   * @param array $options
   * @return void
   */
  private function parseJpeg($options) { 
    imagecopyresampled($options['imgMini'], $options['img'], 0, 0, 0, 0, $options['width'] , $options['height'], $options['initWidth'] , $options['initHeight']);     
    imagejpeg($options['imgMini'], $this->fileOptions['directory'].$options['alias'].$this->newName, 100);
  }
	
  /**
   * Function parses gif original file and makes a thumbnail from it.
   * @param array $options
   * @return void
   */
  private function parseGif($options) { 
    $background=ImageColorAllocate($options['imgMini'],255 ,255 ,255);
    ImageFill($options['imgMini'], 0, 0, $background) ;
    imagecopyresampled($options['imgMini'], $options['img'], 0, 0, 0, 0, $options['width'] , $options['height'],$options['initWidth'] , $options['initHeight']);     
    imagegif($options['imgMini'], $this->fileOptions['directory'].$options['alias'].$this->newName, 100);	
  }

  /**
   * Function parses png original file and makes a thumbnail from it.
   * @param array $options
   * @return void
   */
  private function parsePng($options) {
    imagealphablending($options['imgMini'], false);
    imagesavealpha($options['imgMini'], true);  
    // $source = imagecreatefrompng("".$this->destination."$fileUpload");
    imagealphablending($options['img'], true);
    imagecopyresampled($options['imgMini'], $options['img'], 0, 0, 0, 0, $options['width'] , $options['height'], $options['initWidth'] , $options['initHeight']);
    imagepng($options['imgMini'], $this->fileOptions['directory'].$options['alias'].$this->newName);
  }		 
 
}    