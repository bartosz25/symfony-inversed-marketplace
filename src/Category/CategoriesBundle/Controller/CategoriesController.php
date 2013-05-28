<?php
namespace Category\CategoriesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use Users\ProfilesBundle\Entity\UsersCategoriesAlerts;
use Category\CategoriesBundle\Entity\CategoriesModified;
use Others\FormTemplate;
use Others\Pager;
use Frontend\FrontBundle\Helper\FrontendHelper;

class CategoriesController extends FrontController
{

  /**
   * Protected variable which contains fields handled by dynamic form generation.
   * @access protected
   * @var array
   */
  protected $_handledForms = array('AddAd', 'AddOffer');

  /**
   * Returns categories list.
   * @access public
   * @return Displayed template.
   */
  public function listCategoriesAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');    
    $categories = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')
    ->getCategoriesList(array(
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ));
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('cate'),
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('CategoryCategoriesBundle:Categories:listCategories.html.php', array('categories' => $categories, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket));
  }

  /**
   * Gets ads by category.
   * + cache saving
   * @access public
   * @return Displayed template.
   */
  public function getAdsByCategoryAction(Request $request)
  {
    $this->ifFromNewsletter("category");
    $category = $request->attributes->get('category');
    // find category by categoryUrl field
    $categoryRow = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getByUrl($category);
    // check if user is already subscribed
    $isSubscribed = 2;
    $userAttr = $this->user->getAttributes();
    if(isset($userAttr['id']) && $userAttr['id'] > 0)
    {
      $isSubscribed = (int)$this->enMan->getRepository('UserAlertsBundle:UsersCategoriesAlerts')->alreadySubscribed((int)$userAttr['id'], $categoryRow[0]['id_ca']);
    }
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $cacheName = $this->config['cache']['categories'].$categoryRow[0]['id_ca'].'/ads/page_'.$page.'_'.$how.'_'.$column;
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getByCategory(array('date' => $this->config['sql']['dateFormat'], 'how' => $how, 'column' => $column, 'maxResults' => $this->config['pager']['perPage'], 'cacheName' => $cacheName, 'start' => $this->config['pager']['perPage']*($page-1)), $categoryRow[0]['id_ca']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $categoryRow[0]['categoryAds'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('CategoryCategoriesBundle:Categories:adsTable.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager->setPages(), 'category' => $category,
      'class' => $helper->getClassesBySorter($how, $column, array('titre', 'fourchette-de', 'fourchette-a', 'ville', 'date')), 'how' => $how, 'isSubscribed' => $isSubscribed, 'column' => $column));
    }
    return $this->render('CategoryCategoriesBundle:Categories:getAdsByCategory.html.php', array('ads' => $ads,'page' => $page, 'pager' => $pager->setPages(), 'category' => $category, 'categoryRow' => $categoryRow[0],
    'class' => $helper->getClassesBySorter($how, $column, array('titre', 'ville', 'fourchette-de', 'fourchette-a', 'date')), 'how' => $how, 'column' => $column, 'isSubscribed' => $isSubscribed, 'ticket' => $this->sessionTicket));
  }

  /**
   * Gets offers by category.
   * + cache saving
   * @access public
   * @return Displayed template.
   */
  public function getOffersByCategoryAction(Request $request)
  {
    $category = $request->attributes->get('category');
    // find category by categoryUrl field
    $categoryRow = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getByUrl($category);
    $page = (int)$request->attributes->get('page');
    $cacheName = $this->config['cache']['categories'].$categoryRow[0]['id_ca'].'/offers/page_'.$page;
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getByCategory(array('maxResults' => $this->config['pager']['perPage'], 'cacheName' => $cacheName, 'start' => $this->config['pager']['perPage']*($page-1)), $categoryRow[0]['id_ca']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $categoryRow[0]['categoryOffers'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('CategoryCategoriesBundle:Categories:getOffersByCategory.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'category' => $category));
  }

  /** 
   * Gets offers list for RSS feed.
   * @return Displayed template.
   */
  public function getOffersRssAction(Request $request)
  {
    $category = $request->attributes->get('category');
    // find category by categoryUrl field
    $categoryRow = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getByUrl($category);
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getByCategory(array('maxResults' => $this->config['rss']['perPage'], 'start' => 0), $categoryRow[0]['id_ca']);
    return $this->render('CategoryCategoriesBundle:Categories:getOffersRss.html.php', array('offers' => $offers, 'category' => $categoryRow));
  }

  /** 
   * Gets ads list for RSS feed.
   * @return Displayed template.
   */
  public function getAdsRssAction(Request $request)
  {
    $category = $request->attributes->get('category');
    // find category by categoryUrl field
    $categoryRow = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getByUrl($category);
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getByCategory(array('maxResults' => $this->config['rss']['perPage'], 'start' => 0), $categoryRow[0]['id_ca']);
    return $this->render('CategoryCategoriesBundle:Categories:getAdsRss.html.php', array('ads' => $ads, 'category' => $categoryRow));
  }

  /**
   * Gets categories list by AJAX request. 
   * @return Displayed template.
   */
  public function getAjaxListAction(Request $request)
  {
    $category = (int)$request->request->get('category');
    $formQuery = $request->request->get('form');
    $formData = rawurldecode($request->request->get('data'));
    if(in_array($formQuery, $this->_handledForms))
    {
      $fields = $this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields($category);
      // prepares form data
      $defaultData = array();
      foreach(explode('&', $formData) as $data) 
      {
        if(preg_match_all('/^'.$formQuery.'\[(.*)\]=(.*)$/i', $data, $matches))
        {
          $defaultData[$matches[1][0]] = $this->filtXss->doFilterXss($matches[2][0]);
        }
      }
      // makes form field to render
      $form = $this->createForm(new FormTemplate($formQuery, $fields));
      $form->setData($defaultData);
      return $this->render('CategoryCategoriesBundle:Categories:getAjaxList.html.php', array('form' => $form->createView(),
      'fields' => $fields, 'bidId' => '', "formErrors" => array()));
    }
    else 
    {
      return new Response('');
    }
  }

}