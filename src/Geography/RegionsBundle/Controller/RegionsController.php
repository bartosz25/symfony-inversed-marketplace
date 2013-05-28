<?php
namespace Geography\RegionsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController; 
use Others\Pager;

class RegionsController extends FrontController
{

  /**
   * List all regions. 
   * @return Displayed template.
   */
  public function listRegionsAction(Request $request)
  {
    $regions = $this->enMan->getRepository('GeographyRegionsBundle:Regions')
    ->getAllRegions(1); 
    return $this->render('GeographyRegionsBundle:Regions:listRegions.html.php', array('regions' => $regions));
  }

  /**
   * Gets ads by region.
   * + cache saving
   * @access public
   * @return Displayed template.
   */
  public function getAdsByRegionAction(Request $request)
  {
    $region = $request->attributes->get('url');
    // find region by regionUrl field
    $regionRow = $this->enMan->getRepository('GeographyRegionsBundle:Regions')->getByUrl($region);
    $page = (int)$request->attributes->get('page');
    $cacheName = $this->config['cache']['regions'].$regionRow[0]['id_re'].'/ads/page_'.$page;
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getByRegion(array('cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)), $regionRow[0]['id_re']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $regionRow[0]['regionAds'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('GeographyRegionsBundle:Regions:getAdsByRegion.html.php', array('ads' => $ads, 'pager' => $pager->setPages(), 'region' => $regionRow[0]));
  }

  /**
   * Gets offers by region.
   * + cache saving
   * @access public
   * @return Displayed template.
   */
  public function getOffersByRegionAction(Request $request)
  {
    $region = $request->attributes->get('url');
    // find region by regionUrl field
    $regionRow = $this->enMan->getRepository('GeographyRegionsBundle:Regions')->getByUrl($region);
    $page = (int)$request->attributes->get('page');
    $cacheName = $this->config['cache']['regions'].$regionRow[0]['id_re'].'/offers/page_'.$page;
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getByRegion(array('cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)), $regionRow[0]['id_re']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $regionRow[0]['regionAds'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('GeographyRegionsBundle:Regions:getOffersByRegion.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'region' => $regionRow[0]));
  }

}