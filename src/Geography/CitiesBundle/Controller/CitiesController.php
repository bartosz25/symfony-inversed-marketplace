<?php
namespace Geography\CitiesBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController; 

use Others\Pager;

class CitiesController extends FrontController
{

  /**
   * List all cities. 
   * @return Displayed template.
   */
  public function listCitiesAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $cities = $this->enMan->getRepository('GeographyCitiesBundle:Cities')
    ->getAllCities(array(
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ));
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('citi'),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('GeographyCitiesBundle:Cities:listCities.html.php', array('cities' => $cities, 'pager' => $pager->setPages()));
  }

  /**
   * Gets ads by city.
   * + cache saving
   * @return Displayed template.
   */
  public function getAdsByCityAction(Request $request)
  {
    $cityGet = $request->attributes->get('city');
    $page = (int)$request->attributes->get('page');
    $city = $this->enMan->getRepository('GeographyCitiesBundle:Cities')->findBy(array('cityUrl' => $cityGet));
    if((int)$city[0]->getIdCi() > 0)
    {
      $cacheName = $this->config['cache']['cities'].$city[0]->getIdCi().'/ads/page_'.$page;
      $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdsList(array('cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)
      ), array('adCity' => $city[0]->getIdCi()));
	  $pager = new Pager(array('before' => $this->config['pager']['before'],
	  	  	   'after' => $this->config['pager']['after'], 'all' => $city[0]->getCityAds(),
	  	  	   'page' => $page, 'perPage' => $this->config['pager']['perPage']
	           ));
      return $this->render('GeographyCitiesBundle:Cities:getAdsByCity.html.php', array('ads' => $ads, 'pager' => $pager->setPages(),
      'city' => $cityGet, 'url' => $request->attributes->get('url')));
    }
    throw new Exception('City NOT FOUND');
  }


  /**
   * Gets offers by city.
   * + cache saving
   * @access public
   * @return Displayed template.
   */
  public function getOffersByCityAction(Request $request)
  {
    $city = $request->attributes->get('city');
    // find region by regionUrl field
    $cityRow = $this->enMan->getRepository('GeographyCitiesBundle:Cities')->findBy(array('cityUrl' => $city));
    $page = (int)$request->attributes->get('page');
    $cacheName = $this->config['cache']['cities'].$cityRow[0]->getIdCi().'/offers/page_'.$page;
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getByCity(array('cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)), $cityRow[0]->getIdCi());
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $cityRow[0]->getCityOffers(),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('GeographyCitiesBundle:Cities:getOffersByCity.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'city' => $city, 'url' => $request->attributes->get('url')));
  }

  /**
   * Gets categories list by AJAX request. 
   * @return Displayed template.
   */
  public function getAjaxListAction(Request $request)
  {
    $country = (int)$request->request->get('country');
    $cities = $this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($country);
    return $this->render('GeographyCitiesBundle:Cities:getAjaxList.html.php', array('cities' => $cities));
  }

}