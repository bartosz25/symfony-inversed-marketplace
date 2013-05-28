<?php
namespace Frontend\FrontBundle\Controller;

use Frontend\FrontBundle\Controller\FrontController;
use Symfony\Component\HttpFoundation\Request;
use Frontend\FrontBundle\Entity\Search;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Others\Pager;

class FrontendController extends FrontController
{

  public function indexAction(Request $request)
  {
    // get 8 last added ads
    $ads = $this->enMan->getRepository("AdItemsBundle:Ads")->getForIndex(array("cacheName" => "index_ads", "date" => $this->config['sql']['dateFormat'], "start" => 0, "maxResults" => 8));
    // get 6 last added offers
    $offers = $this->enMan->getRepository("CatalogueOffersBundle:Offers")->getForIndex(array("cacheName" => "index_1", "date" => $this->config['sql']['dateFormat'], "start" => 0, "maxResults" => 7));
    $next = 2;
    $nextA = "";
    $nextSpan = "hidden";
    if(count($offers) != 7)
    {
      $next = 0;
      $nextA = "hidden";
      $nextSpan = "";
    }
    else
    {
      unset($offers[6]);
    }
    // $offers = array();
    // foreach($offersDb as $offer)
    // {
      // if(!array_key_exists($offer["id_of"], $offers)) $offers[$offer["id_of"]] = $offer;
    // }
    $classes = array();
    $classes["ads"] = array("", "hidden", "hidden", "hidden", "hidden", "hidden", "hidden", "hidden");
    $classes["offers"] = array("", "", "", "topLine", "topLine", "topLine");
    $classes["navigation"] = array("nextA" => $nextA, "previousA" => "hidden", "nextSpan" => $nextSpan, "previousSpan" => "");
    return $this->render('FrontendFrontBundle:Frontend:index.html.php', array("ads" => $ads, "offers" => $offers, "classes" => $classes,
    "dir" => $this->config['view']['dirs']['offersImg'], "next" => $next));
  }
  
  public function searchAction(Request $request)
  {
    $isPartial = $this->checkIfPartial();
    $page = (int)$request->attributes->get('page');
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $session = $request->getSession();
    $seaEnt = new Search;
    $helper = new FrontendHelper;
    $word = $this->filtXss->doFilterXss($request->request->get("word", $session->get("word")));
    $seaEnt->setPlace($request->request->get("placeSearch", $session->get("place")));
    $categories = $request->request->get("cat", $session->get("categories"));
    $isAll = (int)$request->request->get("all", $session->get("allSearch", 1));
    $sqlCategories = array();
    if($isAll == 0)
    {
      foreach($categories as $category)
      {
        $sqlCategories[] = (int)$category;
      }
    }
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	  	   'after' => $this->config['pager']['after'], 'all' => 0,
           'page' => $page, 'perPage' => $this->config['pager']['perPage']
	       ));
    if($seaEnt->isAd())
    {
      $label = "annonces";
      $template = "ad";
      $items = $this->enMan->getRepository('AdItemsBundle:Ads')->searchAd($word, $sqlCategories, array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1),
      'date' => $this->config['sql']['dateFormat']));
      $pager->setOption("all", $this->enMan->getRepository("AdItemsBundle:Ads")->countAds($word, $sqlCategories));
      $params = array('class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'ville', 'date')), 'how' => $how, 'column' => $column,
        "routeName" => 'search', 'routeParams' => array('how' => $how, 'column' => $column));
      if($isPartial)
      {
        return $this->render('AdItemsBundle:Items:adsTable.html.php', array_merge($params, array('ads' => $items, 'page' => $page, 'pager' => $pager->setPages())));
      }
    }
    elseif($seaEnt->isOffer())
    {
      $label = "offres";
      $template = "offer";
      $items = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->searchOffer($word, $sqlCategories, array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1),
      'date' => $this->config['sql']['dateFormat']));
      $pager->setOption("all", $this->enMan->getRepository("CatalogueOffersBundle:Offers")->countOffers($word, $sqlCategories));
      $params = array('class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'prix', 'catalogue', 'date')), 'how' => $how, 'column' => $column,
        "routeName" => 'search', 'routeParams' => array('how' => $how, 'column' => $column));
      if($isPartial)
      {
        return $this->render('CatalogueOffersBundle:Offers:offersTable.html.php', array_merge($params, array('offers' => $items, 'page' => $page, 'pager' => $pager->setPages())));
      }
    }
    else
    {
      throw new \Exception("Place not defined");
    }
    $session->set("word", $word);
    $session->set("categories", $categories);
    $session->set("place", $seaEnt->getPlace());
    $session->set("allSearch", $isAll);
    return $this->render('FrontendFrontBundle:Frontend:search.html.php', array_merge($params, array("word" => $word, "items" => $items, "template" => $template, "page" => $page, 'pager' => $pager->setPages(), "label" => $label)));
  }
  
}