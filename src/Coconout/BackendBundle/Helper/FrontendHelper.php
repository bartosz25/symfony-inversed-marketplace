<?php
 
namespace Frontend\FrontBundle\Helper;
 
use Symfony\Component\Templating\Helper\Helper;

class FrontendHelper extends Helper
{
  protected $_defaultMeta = array('title' => "Activités entre amis, conseils pour mieux vivre / Migapi.com",
  'description' => "Vous êtes venez d'arriver dans une ville ? Vous avez besoin d'organiser votre vie sociale, d'obtenir de précieux conseils pour mieux vous retrouver ? Si oui, visitez Migapi.com",
  'keywords' => "activités entre amis, conseils sur la vie, sorties entre amis");

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

  public function makeUrl($url2Transform, $patternAccepted = '([^a-z0-9\-_])+', $separator = '_')
  {
    $fr = array('é', 'è', 'à', 'ç', 'ê', 'ë', 'ä', 'â', 'î', 'ï', 'ù', 'ü', 'û', ' ');
    $ascii = array('e', 'e', 'a', 'c', 'e', 'e', 'a', 'a', 'i', 'i', 'u', 'u', 'u', $separator);
    // 1) Replace whitespaces with passed separator
    $url2Transform = str_replace($fr, $ascii, $url2Transform); 
    // 2) transform string into url friendy
    $str = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $url2Transform));
    $str = preg_replace('/[^a-z0-9-\_]/', '', $str);

    return $str;
  }

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

    public function getName()
    {
        return 'frontend';
    }
}