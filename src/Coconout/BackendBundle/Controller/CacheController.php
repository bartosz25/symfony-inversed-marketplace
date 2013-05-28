<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController; 
use Others\Tools;

class CacheController extends BackendController
{

  /**
   * Generate cache files for categories, regions, cities and countries.
   * @return Displayed template.
   */
  public function makeAction(Request $request)
  {
    $file = '<?php 
    $categories = array(); ';
    // first, get categories and make an array
    $categories = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(true);
    $all = count($categories);
    foreach($categories as $c => $categoryParent)
    {
      $file .= '$categories['.$categoryParent['parent']['id_ca'].'] = array("parentId" => '.$categoryParent['parent']['id_ca'].', "parentName" => "'.$categoryParent['parent']['categoryName'].'", "parentUrl" => "'.$categoryParent['parent']['categoryUrl'].'", "children" => array());
      ';
      foreach($categoryParent['children'] as $ch => $child)
      {
        $file .= '$categories['.$categoryParent['parent']['id_ca'].']["children"]['.$ch.'] = array("id" => '.$child['id_ca'].', "name" => "'.$child['categoryName'].'", "url" => "'.$child['categoryUrl'].'");
        ';
      }
    }
    $file .= '
    //$countries = array(
    $geography = array();
    ';
    // make geographical tree
    $initCountries = array();
    $initRegions = array();
    $cities = $this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCitiesAll();
    foreach($cities as $c => $city)
    {
      if(!in_array($city['id_co'], $initCountries))
      {
        $file .= '$geography['.$city['id_co'].'] = array("regions" => array(), "id_co" => '.$city['id_co'].', "countryName" => "'.$city['countryName'].'");
        ';
        $initCountries[] = $city['id_co'];
      }
      if(!in_array($city['id_re'], $initRegions))
      {
        $file .= '$geography['.$city['id_co'].']["regions"]['.$city['id_re'].'] = array("cities" => array(), "id_re" => '.$city['id_re'].', "regionName" => "'.$city['regionName'].'", "regionUrl" => "'.$city['regionUrl'].'");
        ';
        $initRegions[] = $city['id_re'];
      }
      $file .= '    $geography['.$city['id_co'].']["regions"]['.$city['id_re'].']["cities"]['.$city['id_ci'].'] = array("id_ci" => '.$city['id_ci'].', "cityName" => "'.$city['cityName'].'", "cityUrl" => "'.$city['cityUrl'].'", "id_re" => '.$city['id_re'].', "regionName" => "'.$city['regionName'].'", "regionUrl" => "'.$city['regionUrl'].'");
      ';
    }
    // second, get countries
    // $countries = $this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries(); 
    // $all = count($countries);
    // foreach($countries as $co => $country)
    // {
      // $file .= $country['id_co'].' => array("name" => "'.$country['countryName'].'", "id" => "'.$country['id_co'].'")
      // ';
      // if(($co+1) != $all)
      // {
        // $file .= ', ';
      // }
      // $citiesFile .= '$cities['.$country['id_co'].'] = array()';
    // }
    // $file .= ');

    // $regions = array(
    // ';
    // // third, get regions
    // $regions = $this->enMan->getRepository('GeographyRegionsBundle:Regions')->getRegions();
    // $all = count($regions);
    // foreach($regions as $r => $region)
    // {
      // $file .= $region['id_re'].' => array("name" => "'.$region['regionName'].'", "url" => "'.$region['regionUrl'].'", "id" => "'.$region['id_re'].'")
      // ';
      // if(($r+1) != $all)
      // {
        // $file .= ', ';
      // }
    // }
    // $file .= '); 
    // '.$citiesFile.'

    // ';
    // // fourth, get cities
    // $cities = $this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCitiesAll();
    // $all = count($cities);
    // foreach($cities as $c => $city)
    // {
      // $file .= $city['id_ci'].' => array("region" => '.$city['id_re'].', "regionName" => "'.$city['regionName'].'", "regionUrl" => "'.$city['regionUrl'].'",  "name" => "'.$city['cityName'].'", "url" => "'.$city['cityUrl'].'", "id" => "'.$city['id_ci'].'")
      // ';
      // if(($c+1) != $all)
      // {
        // $file .= ', ';
      // }
    // }
    $file .= '$zones = array();';
    $zones = $this->enMan->getRepository('GeographyZonesBundle:DeliveryZones')->getDeliveryZones();
    foreach($zones as $z => $zone)
    {
      $file .= '$zones['.$z.'] = array("id" => '.$zone['id_dz'].', "name" => "'.$zone['zoneName'].'");
      ';
    }
    $file .= ' 
    ?>';
    file_put_contents(rootDir.'cache/lists.php', $file);

    // make /js/vars.js file with cached categories
    $content = file_get_contents(rootDir.'web/js/vars.js');
    // separe categories from other stuff
    $vars = explode('/*CATEGORIES*/', $content);
    $fileJs = $vars[0].'
/*CATEGORIES*/';
    foreach($categories as $c => $categoryParent)
    {
      $fileJs .= 'categoriesLong['.$categoryParent['parent']['id_ca'].'] = "'.$categoryParent['parent']['categoryName'].'"
';
      $fileJs .= 'categoriesShort['.$categoryParent['parent']['id_ca'].'] = "'.Tools::makeShortName($categoryParent['parent']['categoryName'], 4, 6).'"
';
      foreach($categoryParent['children'] as $ch => $child)
      {
        $fileJs .= 'categoriesLong['.$child['id_ca'].'] = "'.$child['categoryName'].'"
';
        $fileJs .= 'categoriesShort['.$child['id_ca'].'] = "'.Tools::makeShortName($child['categoryName'], 4, 6).'"
';
      }
    }
    file_put_contents(rootDir.'web/js/vars.js', $fileJs);
    return $this->render('CoconoutBackendBundle:Cache:make.html.php', array());
  }


  /**
   * Generate cache files for SEO stuff, based on random items.
   * @return Displayed template.
   */
  public function makeSeoAction(Request $request)
  {
    // gets 200 tags
    $tags = $this->enMan->getRepository('FrontendFrontBundle:Tags')->getRandomTags(200, $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('tags'));
    // gets 20 categories
    $categories = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getRandomCategories(20, $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('cate'), true);
    // gets 50 offers
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getRandomOffers(50, $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('offa'));
    // gets 50 ads
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getRandomAds(50, $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('adsa'));

    $file = '<?php
      $randTags = array();
      $randCats = array();
      $randOff = array();
      $randAds = array();
';
    foreach($tags as $t => $tag)
    {
      $file .= '$randTags['.$t.'] = array("id" => '.$tag['id_ta'].', "name" => "'.$tag['tagName'].'", "offers" => '.$tag['tagOffers'].', "ads" => '.$tag['tagAds'].');
';
    }
    foreach($categories as $c => $category)
    {
      $file .= '$randCats['.$c.'] = array("id" => '.$category['id_ca'].', "name" => "'.$category['categoryName'].'", "url" => "'.$category['categoryUrl'].'");
';
    }
    foreach($offers as $o => $offer)
    {
      $file .= '$randOff['.$o.'] = array("id" => '.$offer['id_of'].', "catalogueId" => '.$offer['id_cat'].', "catalogueName" => "'.$offer['catalogueName'].'", "name" => "'.$offer['offerName'].'");
';
    }
    foreach($ads as $a => $ad)
    {
      $file .= '$randAds['.$a.'] = array("id" => '.$ad['id_ad'].', "categoryName" => "'.$ad['categoryName'].'", "category" => "'.$ad['categoryUrl'].'", "name" => "'.$ad['adName'].'");
';
    }
    file_put_contents(rootDir.'cache/seo.php', $file);
    return $this->render('CoconoutBackendBundle:Cache:makeSeo.html.php', array());    
  }

  /**
   * Cleans site cache.
   * @access public
   * @return Displayed template.
   */
  public function cleanAction(Request $request)
  {
    return $this->render('CoconoutBackendBundle:Cache:clean.html.php', array("directories" => $this->config['cache']));
  }

  /**
   * Cleans cache files from one directory.
   * @access public
   * @return Displayed template.
   */
  public function cleanDirectoryAction(Request $request)
  {
    $result = array("continue" => 0); // 0 => continue cleaning after this call; 1 => the directory was completely cleaned; pass the cleaning to the next directory
    $dir = $request->attributes->get("directory");
    $this->cacheManager->localizeLastDirectoryToClean($this->cacheManager->getBaseDir().$this->config['cache'][$dir]);
    $this->cacheManager->cleanDirCache($this->cacheManager->getBaseDir().$this->config['cache'][$dir]);
// if($this->cacheManager->getLastDirectoryToClean() == $this->cacheManager->getLastCleanedDirectory())
// {
// $result["continue"] = 1;
// }
    echo json_encode($result);
    die();
  }

}