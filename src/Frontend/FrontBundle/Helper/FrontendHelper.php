<?php
 
namespace Frontend\FrontBundle\Helper;
 
use Symfony\Component\Templating\Helper\Helper;

class FrontendHelper extends Helper
{
  protected $_defaultMeta = array('title' => "Activités entre amis, conseils pour mieux vivre / Migapi.com",
  'description' => "Vous êtes venez d'arriver dans une ville ? Vous avez besoin d'organiser votre vie sociale, d'obtenir de précieux conseils pour mieux vous retrouver ? Si oui, visitez Migapi.com",
  'keywords' => "activités entre amis, conseils sur la vie, sorties entre amis");

  private $fr = array('é', 'è', 'à', 'ç', 'ê', 'ë', 'ä', 'â', 'î', 'ï', 'ù', 'ü', 'û');
  private $ascii = array('e', 'e', 'a', 'c', 'e', 'e', 'a', 'a', 'i', 'i', 'u', 'u', 'u');

  /** 
   * Helper which shows default or customized meta 
   * @return String Meta string.
   */
  public function renderMeta($meta)
  {
    if($meta['title'] == '')
    {
      $meta['title'] = $this->_defaultMeta['title'];
    }
    if($meta['description'] == '')
    {
      $meta['description'] = $this->_defaultMeta['description'];
    }
    if($meta['keywords'] == '') 
    {
      $meta['keywords'] = $this->_defaultMeta['keywords'];
    }
    return $meta;
  }

  /** 
   * Helper which returns some activities in the footer. 
   * @return Array Array with activities
   */
  public function renderFooter()
  {
    $activites = array();
// stand by for now
    return $activites;
  }

  /**
   *
   */
  public function transformTags($tags, $limit)
  {
    $genTags = array();
    $all = count($tags);
    while(count($genTags) < $limit)
    {
      $random = rand(0, ($all-1));
      if(isset($tags[$random]))
      {
        $genTags[] = array("id_ta" => $tags[$random]["id"], "tagName" => $tags[$random]["name"]);
        unset($tags[$random]);
      }
    }
    return $genTags;
  }

   // public function __construct() {
 // echo 'test this funky helper'; die();
  // }

  /**
   * Deletes file according to their prefixes.
   * @param string $file File name.
   * @param string $directory Directory name.
   * @param array $prefix List of prefixes.
   * @return bool True if correctly deleted, false if not
   */
  // function delete_files($file, $directory, $prefix) 
  // {
    // for($d=0; $d < count($prefix); $d++) { //echo "".$directory."".$prefix[$d]."".$file." ---- ";
      // @unlink("".$directory."".$prefix[$d]."".$file."");
    // }
    // if(@unlink("".$directory."".$file."")) {
      // return true;
    // }
    // return false;
  // }

  public function makeUrl($url2Transform, $patternAccepted = '([^a-z0-9\-_])+', $separator = '-')
  {
    $this->fr[] = ' ';
    $this->ascii[] =  $separator;
    // 1) Replace whitespaces with passed separator
    $url2Transform = str_replace($this->fr, $this->ascii, $url2Transform); 
    // 2) transform string into url friendy
    $str = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $url2Transform));
    $str = preg_replace('/[^a-z0-9-\_]/', '', $str);

    return $str;
  }

  // /**
   // * Unaccents a word.
   // * @access public
   // * @param string $word Word to unaccent.
   // * @return string Unaccented string.
   // */
  // public function unaccent($word)
  // {
    
    
  // }

  /**
   * Gets localization label.
   * @access public
   * @param array $params List of parameters.
   * @return string Localization label (city, region, country)
   */
  public function getLocalizationLabel($params)
  {
    switch($params['adSellerGeo'])
    {
      case 0:
        return 'partout';
      break;
      case 1:
        return $params['countryName'];
      break;
      case 2:
        return $params['regionName'];
      break;
      case 3:
        return $params['cityName'];
      break;
    }
  }

  /**
   * Gets payments labels and create a string to display.
   * @access public
   * @param array $payments Payments array.
   * @param array $labels Labels array.
   * @return string String to display.
   */
  public function getPaymentLabels($payments, $labels)
  {
    $methods = array();
    foreach($payments as $payment)
    {
      $methods[] = $labels[$payment['payments_id_pa']];
    }
    return implode(',', $methods);
  }

  /**
   * Prepares pagination view based on Others\Pager class parameters.
   * @access public
   * @param array $params List of parameters.
   * @return string HTML code to display.
   */
  public function makePagination($params)
  {
    
  }

  /**
   * Makes filename.
   * @access public
   * @param string $fileName Original filename.
   * @return string Transformed filename.
   */
  public function makeFileName($fileName)
  {
    // detect file extension
    $ext = $this->getFileExtension($fileName, 'jpg|gif|png|jpeg');
    // transform all non alphanumerical characters
    $fr = array('é', 'è', 'à', 'ç', 'ê', 'ë', 'ä', 'â', 'î', 'ï', 'ù', 'ü', 'û', ' ');
    $ascii = array('e', 'e', 'a', 'c', 'e', 'e', 'a', 'a', 'i', 'i', 'u', 'u', 'u', '_');
    // 1) Replace whitespaces with passed separator
    $url2Transform = str_replace($fr, $ascii, $fileName); 
    // 2) transform string into url friendy
    $str = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $fileName));
    $str = preg_replace('/[^a-z0-9-\_]/', '', $str);
    $length = strlen($str);
    return substr($str, 0, ($length - strlen($ext))).'.'.$ext;
  }

  /**
   * Makes random filename.
   * @access public
   * @param string $fileName Original filename.
   * @param string $lastFile Last generated name.
   * @param int $chars Maximal number of filename characters.
   * @return string New filename.
   */
  // public function makeRandomFilename($fileName, $lastFile, $chars)
  // {
     
  // }

  /**
   * Detects file extension.
   * @access public
   * @param string $fileName Filename.
   * @param string $ext Accepted extensions
   * @return string Extension.
   */
  public function getFileExtension($fileName, $ext) 
  {
    preg_match_all('/(.*).('.$ext.')/i', $fileName, $match);
    if(count($match[2]) > 0)
    {
      return $match[2][0];
    }
    return '';
  }

  /**
   * Prepares error message to be displayed at the page.
   * @access public
   * @param array $messages Array with messages.
   * @param string $separator HTML separator.
   * @return string Final message.
   */
  public function prepareErrorMessage($messages, $separator = '<br />')
  {
    $message = array();
    foreach($messages as $m => $msg)
    {
      if(is_array($message))
      {
        foreach($msg as $p => $msgPart)
        {
          $message[] = $msgPart;
        }
      }
      else
      {
        $message[] = $msg;
      }
    }
    return implode('<br />', $message);
  }

  /**
   * Gets random array with uniq ids.
   * @access public
   * @param int $max Elements in the array.
   * @param int $limit Limit of elements to load.
   * @return array Array with random numbers.
   */
  public function getUniqRandom($max, $limit) 
  {
    $randoms = array();
    for($s = 0; $s <= $limit; $s++)
    { 
      $randoms[$s] = $this->getRandomSimple($max, $randoms);
    }
    return $randoms;
  }

  /**
   * Gets item which is not in the array.
   * @access public
   * @param int $max Elements in the array.
   * @param array $randoms Array with random elements.
   * @return int Random number.
   */
  private function getRandomSimple($max, $randoms)
  {
    $r = rand(0, $max);
    if(in_array($r, $randoms))
    {
      $this->getRandomSimple($max, $randoms);
    }
    return $r;
  }

  /**
   * Get class by order parameter.
   * @access public
   * @param string $how Order parameter.
   * @return string CSS class name.
   */
  public function getClassBySorter($how)
  {
    $classes = array('asc' => 'onSortUp', 'desc' => 'onSortDown');
    return $classes[$how];
  }

  /**
   * Get classes for sorted columns.
   * @access public
   * @param string $how Order parameter.
   * @param string $how Sorted column name.
   * @param array $columns Columns in the page which can be sorted.
   * @return string CSS class name.
   */
  public function getClassesBySorter($how, $column, $columns)
  {
    $classes = array('asc' => 'onSortUp', 'desc' => 'onSortDown');
    $finalClasses = array();
    foreach($columns as $c => $columnC)
    {
      if($columnC == $column)
      {
        $finalClasses[$columnC] = $classes[$how];
      }
      else
      {
        $finalClasses[$columnC] = '';
      }
    }
    return $finalClasses;
  }

  /**
   * Get reverse order by actual order parameter.
   * @access public
   * @param string $how Order parameter.
   * @return string CSS class name.
   */
  public function getViewOrderRand($how)
  {
    $classes = array('asc' => 'desc', 'desc' => 'asc');
    return $classes[$how];
  }
 
  /**
   * Checks if columns is actually ordered.
   * @access public
   * @param string $column Column to check.
   * @param string $actualColumn Column actually checked.
   * @return string CSS class name or empty string if the column isn't ordered.
   */
  public function getOnSort($column, $actualColumn)
  {
    if($column != $actualColumn) return "";
    return "onSort";
  }

    public function getName()
    {
        return 'frontend';
    }
}