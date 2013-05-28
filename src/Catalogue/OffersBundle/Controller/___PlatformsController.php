<?php
namespace Catalogue\OffersBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use Catalogue\OffersBundle\Form\SynchronizePrestashopFirst;
use User\ProfilesBundle\Form\EditEbayLogin;
use Catalogue\OffersBundle\Entity\SyncPrestashop;
use Others\PrestashopWebService;
use Ad\ItemsBundle\Entity\Ads;
use User\ProfilesBundle\Entity\Users;
use Order\OrdersBundle\Entity\Tax;
use Catalogue\OffersBundle\Entity\EbayTemporary;
use Ebay\EbayWebService;
use Ebay\EbayAuthentication;
use Ebay\EbayGetItemsList;
use Ebay\EbayGetItem;
use Others\FormTemplate;

class PlatformsController extends FrontController
{

  /**
   * Prefix for eBay form.
   * @access protected
   * @var string
   */
  protected $_ebayForm = 'AddOffer';

  /**
   * Show synchronization page for Prestashop. 
   * @access public
   * @return Displayed template.
   */
  public function synchronizePrestashopAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $synDb = new SyncPrestashop();
    $adsEnt = new Ads();
    $oldExists = false;
    $synDb->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
    $synDb->setStates($adsEnt->getObjetStates());
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $synDb->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($flashData['SynchronizePrestashopFirst']['syncCountry']));
      $synDb->setData(array('syncSite' => $flashData['SynchronizePrestashopFirst']['syncSite'], 'syncKey' => $flashData['SynchronizePrestashopFirst']['syncKey'],
      'syncCountry' => $flashData['SynchronizePrestashopFirst']['syncCountry'], 'syncCity' => $flashData['SynchronizePrestashopFirst']['syncCity'], 'syncDefaultState' => $flashData['SynchronizePrestashopFirst']['syncDefaultState'], 'syncTax' => $flashData['SynchronizePrestashopFirst']['syncTax']));
    }
    elseif(count($dbData = $this->enMan->getRepository('CatalogueOffersBundle:SyncPrestashop')->getForUser($userAttr['id'], array('dateFormat' => $this->config['sql']['dateFormat']))))
    {
      $oldExists = true;
      $synDb->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($dbData['id_co']));
      $synDb->setData(array('syncSite' => $dbData['syncSite'], 'syncKey' => $dbData['syncKey'],
      'syncCountry' => $dbData['id_co'], 'syncCity' => $dbData['id_ci'], 'syncDefaultState' => $dbData['syncDefaultState'], 'syncTax' => $dbData['syncTax']));
    }
    SynchronizePrestashopFirst::setSessionToken($this->sessionTicket);
    $synDb->setTicket($this->sessionTicket);
    // make synchronization form
    $synForm = $this->createForm(new SynchronizePrestashopFirst(), $synDb);
    if($request->getMethod() == 'POST') 
    {
      $synForm->bindRequest($request);
      $data = $request->request->all('AddAd');
      if($synForm->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $cityRef = $this->enMan->getReference('Geography\CitiesBundle\Entity\Cities', (int)$data['SynchronizePrestashopFirst']['syncCity']);
          // set $synDb data
          if(!$oldExists)
          {
            $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
            $synDb->setData(array('syncSite' => $data['SynchronizePrestashopFirst']['syncSite'], 'syncKey' => $data['SynchronizePrestashopFirst']['syncKey'],
            'syncLinks' => '', 'syncDate' => '', 'syncCategories' => '', 'syncProducts' => '', 'syncUserId' => $userRef, 
            'syncTax' => Tax::getTaxValue((int)$data['SynchronizePrestashopFirst']['syncTax']), 'syncDefaultState' => (int)$data['SynchronizePrestashopFirst']['syncDefaultState'], 'syncCity' => $cityRef));
            $this->enMan->persist($synDb);
            $this->enMan->flush();
          }
          else
          {
            $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
            ->set('sp.syncSite', '?1')
            ->set('sp.syncKey', '?2')
            ->set('sp.syncDate', '?3')
            ->set('sp.syncTax', '?4')
            ->set('sp.syncDefaultState', '?5')
            ->set('sp.syncCity', '?6')
            ->where('sp.syncUserId = ?7')
            ->setParameter(1, $data['SynchronizePrestashopFirst']['syncSite'])
            ->setParameter(2, $data['SynchronizePrestashopFirst']['syncKey'])
            ->setParameter(3, date('Y-m-d H:i:s'))
            ->setParameter(4, Tax::getTaxValue((int)$data['SynchronizePrestashopFirst']['syncTax']))
            ->setParameter(5, (int)$data['SynchronizePrestashopFirst']['syncDefaultState'])
            ->setParameter(6, $cityRef)
            ->setParameter(7, (int)$userAttr['id'])
            ->getQuery()
            ->execute();
          }
          // commit SQL transaction
          $this->enMan->getConnection()->commit();

          // redirect to get categories page
          return $this->redirect($this->generateUrl('synchronizeMapPrestashop'));
        }          
        catch(Exception $e)
        {
          $this->enMan->getConnection()->rollback();
          $this->enMan->close();
          throw $e;
        }
      }
      else
      {
        $flashSess->setFlash('formData', $data);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($synForm));
      }
      return $this->redirect($this->generateUrl('synchronizePrestashop'));
    }  
    return $this->render('CatalogueOffersBundle:Platforms:synchronizePrestashop.html.php', array('form' => $synForm->createView(),
    'edit' => $oldExists, 'formErrors' => (array)$flashSess->getFlash('formErrors'), 'isSuccess' => (int)$flashSess->getFlash('synSuccess'),
    'prestaCancel' => $flashSess->getFlash('prestaCancel'), 'ticket' => $this->sessionTicket));
  }

  /**
   * The second step of Prestashop synchronization. We load and save store tags.
   * After that we want that store owner map his store's categories with site categories and catalogues. 
   * At the end of this operation we show the third synchronization form.
   * @access public 
   * @return Displayed template.
   */
  public function mapPrestashopCategoriesAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $dbData = $this->enMan->getRepository('CatalogueOffersBundle:SyncPrestashop')->getForUser($userAttr['id'], array('dateFormat' => $this->config['sql']['dateFormat']));
    $validCSRF = $this->validateCSRF();
    if(count($dbData) > 0 && $validCSRF === true)
    {
      if($request->getMethod() != 'POST')
      {
        $categories = (array)unserialize($dbData['syncCategories']);
        $alreadyDb = (bool)(count($categories));
        if(!$alreadyDb)
        {
          // data is not in the database, we have to load it
          $wsClass = new PrestashopWebService(array('key' => $dbData['syncKey'], 'site' => str_replace('&#x2F;', '/', $dbData['syncSite']), 'debug' => 1));
          // first get tags and categories link and, after, tags and categories quantity
          if($wsClass->connect())
          {
            $wsClass->parseLinks();
            // call only tags and categories ressources
            $wsClass->setSite($wsClass->resArray['links']['categories']);
            if($wsClass->connect())
            {
              $wsClass->parseCategories();
              $categories = $wsClass->resArray['categories'];
              $wsClass->setSite($wsClass->resArray['links']['tags']);
              if($wsClass->connect())
              {
                $wsClass->parseTags();
                $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
                ->set('sp.syncCategories', '?1')
                ->set('sp.syncLinks', '?2')
                ->where('sp.syncUserId = ?3')
                ->setParameter(1, serialize($wsClass->resArray['categories']))
                ->setParameter(2, serialize($wsClass->resArray['links']))
                ->setParameter(3, (int)$userAttr['id'])
                ->getQuery()
                ->execute();
              }
            }
          }
        }
      }
      elseif($request->getMethod() == 'POST') 
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          // array with checked catalogues
          $checkedCatalogues = array();
          $categories = (array)unserialize($dbData['syncCategories']);
          foreach($categories as $c => $category)
          {
            $postCate = (int)$request->request->get('category-'.$c);
            $postCata = (int)$request->request->get('catalogue-'.$c);
            if($postCate > 0 && (in_array($postCata, $checkedCatalogues) || $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->ifBelongs($postCata, (int)$userAttr['id'])))
            {
              $categories[$c]['relations'] = array('category' => $postCate, 'catalogue' => $postCata);
              $checkedCatalogues[] = $postCata;
            }
          }
          $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
          ->set('sp.syncCategories', '?1')
          ->where('sp.syncUserId = ?2')
          ->setParameter(1, serialize($categories))
          ->setParameter(2, (int)$userAttr['id'])
          ->getQuery()
          ->execute();

          // redirect to get categories page
          return $this->redirect($this->generateUrl('synchronizeCatPrestashop'));          
        }          
        catch(Exception $e)
        {
            $this->enMan->getConnection()->rollback();
            $this->enMan->close();
            throw $e;
        }
      }
      return $this->render('CatalogueOffersBundle:Platforms:mapPrestashopCategories.html.php', array('categories' => $categories,
      'c' => 0, 'siteCategories' => $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false), 'siteCatalogues' => $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array(
      'maxResults' => 200, 'start' => 0), (int)$userAttr['id']), 'alreadyDb' => $alreadyDb,
      'dbData' => $dbData, 'formErrors' => (array)$flashSess->getFlash('formErrors'), 'isSuccess' => (int)$flashSess->getFlash('synSuccess')));
    }
    return $this->redirect($this->generateUrl('synchronizePrestashop')); 
  }

  /**
   * Second step of Prestashop synchronization. Here we get the categories and make the relation between them
   * and user's catalogues.
   * @access public
   * @return Displayed template.
   */
  public function synchronizeCatPrestashopAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $dbData = $this->enMan->getRepository('CatalogueOffersBundle:SyncPrestashop')->getForUser($userAttr['id'], array('dateFormat' => $this->config['sql']['dateFormat']));
    if(!isset($dbData['syncKey']))
    {
      $flashSess->setFlash('formErrors',array('syncKey' => "Veuillez préciser la clé", 'syncSite' => "Veuillez préciser l'adresse du site"));
      return $this->redirect($this->generateUrl('synchronizePrestashop'));
    }
    $wsClass = new PrestashopWebService(array('key' => $dbData['syncKey'], 'site' => str_replace('&#x2F;', '/', $dbData['syncSite']), 'debug' => 1));
    // if categories was already parsed, not do it the second time
    $categories = (array)unserialize($dbData['syncCategories']);
    // normally must never happen
    if($dbData['syncCategories'] == '' && $wsClass->connect())
    {
      $wsClass->parseCategories();
      $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
      ->set('sp.syncCategories', '?1')
      ->where('sp.syncUserId = ?2')
      ->setParameter(1, serialize($wsClass->resArray['categories']))
      ->setParameter(2, (int)$userAttr['id'])
      ->getQuery()
      ->execute();
      $categories = $wsClass->resArray['categories'];
    }
    $prodsDb = (array)unserialize($dbData['syncProducts']);
    $products = array();
    foreach($categories as $c => $category)
    {
      foreach($category['stats']['products'] as $i => $item)
      {
        if(!array_key_exists($item['id'], $products))
        {
          $products[$item['id']] = array('category' => $category['id'], 'id' => $item['id'], 'href' => $item['href']);
        }
      }
    }
    if($dbData['syncProducts'] == '')
    {
      $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
      ->set('sp.syncProducts', '?1')
      ->where('sp.syncUserId = ?2')
      ->setParameter(1, serialize($products))
      ->setParameter(2, (int)$userAttr['id'])
      ->getQuery()
      ->execute();
    }
    // insert selected products into database
    $validCSRF = $this->validateCSRF();
    if($request->getMethod() == 'POST' && $validCSRF === true) 
    {
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        // Prestashop categories
        $catSystem = array();
        $categories = (array)unserialize($dbData['syncCategories']);
        foreach($categories as $cP => $categoryPresta)
        {
          $catSystem[(int)$categoryPresta['relations']['category']] = (int)$categoryPresta['relations']['category'];
        }
        // form fields to system category
        $categoriesFormFields = $this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFieldsInCategories($catSystem);
        // submitted products
        $productsPost = $request->request->get('product');
        // products already in the database
        $newOffers = 0;
        $addedOffers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getExternalOffersByUser($userAttr['id'], 'prestashop', true);
        foreach($productsPost as $pp => $productPost)
        {
          $prodDb = $prodsDb[$productPost];
          $category = $categories[$prodDb['category']];
          $prodDb['price'] = Tax::getNetPrice($prodDb['price'], $dbData['syncTax']);
          if(in_array($prodDb['id'], $addedOffers['externals']))
          {
            // already in the database, do only content's update
            $key = array_keys($addedOffers['externals'], $prodDb['id']);
            $this->enMan->getRepository('CatalogueOffersBundle:Offers')->editOffer($key[0], array(
              'offer' => array('new' => array('offerCategory' => $category['relations']['category'], 'offerCatalogue' => $category['relations']['catalogue'], 'offerCity' => $dbData['id_ci'], 'offerPrice' => $prodDb['price'],
                'offerObjetState' => $dbData['syncDefaultState'], 'offerName' => $prodDb['name'], 'offerText' => $prodDb['description']), 
                'old' => array('category' => $addedOffers['offers'][$key[0]]['category'], 'city' => $addedOffers['offers'][$key[0]]['city'], 'region' => $addedOffers['offers'][$key[0]]['region'], 'country' => $addedOffers['offers'][$key[0]]['country'], 'catalogue' => $addedOffers['offers'][$key[0]]['catalogue'])),
              'formField' => array('new' => $categoriesFormFields[$category['relations']['category']], 'old' => array()),
              'others' => array()
            ));
          }
          else
          {
            // new element, insert it into database
            $this->enMan->getRepository('CatalogueOffersBundle:Offers')->addNewOffer(
              array('user' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']),
                'catalogue' => $this->enMan->getReference('Catalogue\OffersBundle\Entity\Catalogues', (int)$category['relations']['catalogue']), 'category' => $this->enMan->getReference('Category\CategoriesBundle\Entity\Categories', (int)$category['relations']['category']),
                'city' => $this->enMan->getReference('Geography\CitiesBundle\Entity\Cities', (int)$dbData['id_ci'])
              ),
              array('offer' => array('catalogueId' => $category['relations']['catalogue'], 'cityId' => $dbData['id_ci'], 'categoryId' => $category['relations']['category'],
                'tax' => $dbData['syncTax'], 'price' => $prodDb['price'], 'name' => $prodDb['name'], 'description' => $prodDb['description'],  'state' => $dbData['syncDefaultState'], 'external' => $prodDb['id'], 'system' => 'prestashop'), 
                'tags' => array(), 'formFields' => $categoriesFormFields[$category['relations']['category']]
              )
            );
            $newOffers++;
          }
        }
        // Update stats for offers
        $q = $this->enMan->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Stats', 's')
        ->set('s.statValue', 's.statValue + '.$newOffers)
        ->where('s.key_st = ?1')
        ->setParameter(1, 'offa')
        ->getQuery()
        ->execute();
        $flashSess->setFlash('successImport', 1);
        // redirect to get categories page
        return $this->redirect($this->generateUrl('synchronizeSuccessPrestashop'));          
      }          
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    return $this->render('CatalogueOffersBundle:Platforms:synchronizeCatPrestashop.html.php', array('categories' => $categories,
    'products' => $products, 'c' => 0));
  }

  /**
   * Synchronization success page.
   * @access public
   * @return Displayed template.
   */
  public function successPrestashopAction(Request $request)
  {
    $flashSess = $request->getSession();
    if((int)$flashSess->getFlash('successImport') == 1)
    {
      return $this->render('CatalogueOffersBundle:Platforms:successPrestashop.html.php');
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Reloads Prestashop categories configuration.
   * @access public
   * @return Redirect to categories configuration or to not found page.
   */
  public function reloadPrestashopCatAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
    ->set('sp.syncCategories', '?1')
    ->where('sp.syncUserId = ?2')
    ->setParameter(1, serialize(array()))
    ->setParameter(2, (int)$userAttr['id'])
    ->getQuery()
    ->execute();
    return $this->redirect($this->generateUrl('synchronizeMapPrestashop'));          
  }

  /**
   * Cancel Prestashop synchronization.
   * @access public
   * @return Redirect to first page.
   */
  public function cancelPrestashopAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();    
    $flashSess = $request->getSession();
    $q = $this->enMan->createQueryBuilder()->delete('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
    ->where('sp.syncUserId = ?1')
    ->setParameter(1, (int)$userAttr['id'])
    ->getQuery()
    ->execute();
    $flashSess->setFlash('prestaCancel', 1);
    return $this->redirect($this->generateUrl('synchronizePrestashop'));   
  }

  /**
   * Gets informations about one category products.
   * @access public
   * @return JSON data.
   */
  public function ajaxGetCategoryAction(Request $request)
  {
    $category = (int)$request->request->get('id');
    $userAttr = $this->user->getAttributes();
    $dbData = $this->enMan->getRepository('CatalogueOffersBundle:SyncPrestashop')->getForUser($userAttr['id'], array('dateFormat' => $this->config['sql']['dateFormat']));
    if(!isset($dbData['syncKey']))
    {
      $result['error'] = 1;
      $result['message'] = "Une erreur s'est produite pendant la récupération des produits";
    }
    else
    {
      $categories = (array)unserialize($dbData['syncCategories']);
      $wsClass = new PrestashopWebService(array('key' => $dbData['syncKey'], 'site' => str_replace('&#x2F;', '/', $categories[$category]['href']), 'debug' => 1));
      if($wsClass->connect())
      {
        $wsClass->parseCategory();
        $categories[$category]['stats'] = $wsClass->resArray['category'][$category];
        $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
        ->set('sp.syncCategories', '?1')
        ->where('sp.syncUserId = ?2')
        ->setParameter(1, serialize($categories))
        ->setParameter(2, (int)$userAttr['id'])
        ->getQuery()
        ->execute();
        // Set success messages
        $result['error'] = 0;
        $result['storeCatId'] = $categories[$category]['id'];
        $result['storeCategory'] = $wsClass->resArray['category'][$category]['name'];
        $result['products'] = $categories[$category]['stats']['productsQuantity'];
      }
      else
      {
        $result['error'] = 1;
        $result['message'] = "Une connexion incorrecte à votre boutique Prestashop";
      }
    }
    echo json_encode($result);
    die();
  }

  /**
   * Gets informations about one product.
   * @access public
   * @return JSON data.
   */
  public function ajaxGetProductAction(Request $request)
  {
    // $category = (int)$request->request->get('idCat');
    $product = (int)$request->request->get('idProd');
    $userAttr = $this->user->getAttributes();
    $dbData = $this->enMan->getRepository('CatalogueOffersBundle:SyncPrestashop')->getForUser($userAttr['id'], array('dateFormat' => $this->config['sql']['dateFormat']));
    if(!isset($dbData['syncKey']))
    {
      $result['error'] = 1;
      $result['message'] = "Une erreur s'est produite pendant la récupération des produits";
    }
    else
    {
      // $categories = (array)unserialize($dbData['syncCategories']);
      $products = (array)unserialize($dbData['syncProducts']);
      $wsClass = new PrestashopWebService(array('key' => $dbData['syncKey'], 'site' => str_replace('&#x2F;', '/', $products[$product]['href']), 'debug' => 1));
      if($wsClass->connect())
      {
        $wsClass->parseProduct();
        $category = $products[$product]['category'];
        $products[$product] = $wsClass->resArray['product'];
        $products[$product]['category'] = $category;
        $q = $this->enMan->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\SyncPrestashop', 'sp')
        ->set('sp.syncProducts', '?1')
        ->where('sp.syncUserId = ?2')
        ->setParameter(1, serialize($products))
        ->setParameter(2, (int)$userAttr['id'])
        ->getQuery()
        ->execute();
        // Set success messages
        $result['error'] = 0;
        $result['name'] = $wsClass->resArray['product']['name'];
        $result['id'] = $wsClass->resArray['product']['id'];
      }
      else
      {
        $result['error'] = 1;
        $result['message'] = "Une connexion incorrecte à votre boutique Prestashop";
      }
    }
    echo json_encode($result);
    die();
  }


  /**
   * Open form for import one bid in the page "Add offer" : Prestashop
   * @access public
   * @return HTML template with the form or JSON message for submitted form.
   */
  public function ajaxImportPrestaAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $itemId = $request->request->get('item');
    $storeKey = $request->request->get('key');
    $storeUrl = $request->request->get('store');
    if($itemId != '' && $storeUrl != '' && $storeKey != '')
    {
      // check if item is already in the database
      $offerRow = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getExternalOfferByUser($userAttr['id'], 'prestashop', $itemId);
      if(!isset($offerRow['id_of']))
      {
        $wsClass = new PrestashopWebService(array('key' => $storeKey, 'site' => $storeUrl.'/api/products/'.$itemId, 'debug' => 1));
        if($wsClass->connect())
        {
          $wsClass->parseProduct();
          $result = $wsClass->resArray['product'];
          $result['result'] = 1; 
        }
        else
        {
          $result = array('result' => 0, 'message' => "Une erreur s'est produite pendant la connexion à votre boutique. Veuillez réessayer ou signaler ce problème");
        }
      }
      else
      {
        $result = array('result' => 0, 'message' => "Cette produit a déjà été importé");
      }
      echo json_encode($result);
      die();
    }
    return $this->render('CatalogueOffersBundle:Platforms:ajaxImportPresta.html.php', array());
  }

  /**
   * Show synchronization page for eBay bids : user inserts his login. 
   * @access public
   * @return Displayed template.
   */
  public function synchronizeEbayAction(Request $request)
  {
    $flashSess = $request->getSession();
    $userAttr = $this->user->getAttributes();
    $data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($userAttr['id']);
    $usersEnt = new Users();
    $usersEnt->setUserEbayLogin($data['userEbayLogin']);
    Users::setSessionToken($this->sessionTicket);
    $usersEnt->setTicket($this->sessionTicket);
    $formEditEbay = $this->createForm(new EditEbayLogin(), $usersEnt);
    if($request->getMethod() == 'POST') 
    {
      $formEditEbay->bindRequest($request);
      $data = $request->request->all('EditEbayLogin');
      if($formEditEbay->isValid())
      {
        // set new password
        $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userEbayLogin', '?1')
        ->where('u.id_us = ?2')
        ->setParameter(1, $data['EditEbayLogin']['userEbayLogin'])
        ->setParameter(2, (int)$userAttr['id'])
        ->getQuery()
        ->execute();
        return $this->redirect($this->generateUrl('synchronizeEbayItems', array()));
      }
      else
      {
        $flashSess->setFlash('formData', $request->request->all($data['EditEbayLogin']));
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEditEbay));
      }
      return $this->redirect($this->generateUrl('synchronizeEbay', array()));
    }
    return $this->render('CatalogueOffersBundle:Platforms:synchronizeEbay.html.php', array(
    'form' => $formEditEbay->createView(), 'formErrors' => (array)$flashSess->getFlash('formErrors')));

  }

  /**
   * Show synchronization page for eBay bids : loading of eBay bids list. 
   * @access public
   * @return Displayed template.
   */
  public function synchronizeEbayItemsAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    return $this->render('CatalogueOffersBundle:Platforms:synchronizeEbayItems.html.php', array('maxPages' => $this->config['ebay']['maxPage']));
  }

  /**
   * Gets eBay items list, 50 per page.
   * @access public
   * @return HTML template.
   */
  public function ajaxGetEbayItemsListAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    if($page <= $this->config['ebay']['maxPage'])
    {
      $userAttr = $this->user->getAttributes();
      $ebayItems = array();
      $tmpItems = array();
      $ebayConnectError = false;
      // get eBay user login
      $data = $this->enMan->getRepository('UserProfilesBundle:Users')->getUser($userAttr['id']);
      // Offers done by eBay synchronization
      $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getExternalOffersByUser($userAttr['id'], 'ebay', true);
      // gets already inserted items
      $items = $this->enMan->getRepository('CatalogueOffersBundle:EbayTemporary')->getByUser($userAttr['id'], array('dateFormat' => $this->config['sql']['dateFormat'],
      'maxResults' => $this->config['ebay']['maxResults'], 'start' => 0));
      $inserted = array();
      foreach($items as $i => $item)
      {
        if(!in_array($item['ebayItemId'], $offers['externals']))
        {
          $inserted[$item['ebayItemId']] = array('catalogue' => $item['ebayCatalogue'], 'data' => unserialize($item['ebayData']));
        }
      }
      $listEbay = new EbayGetItemsList(array('debug' => 1, 'credentials' => $this->config['ebay']['creds'], 'currency' => $this->config['ebay']['currency']));
      $listEbay->templatePath = $this->config['dirs']['templateEbay'];
      $listEbay->setHeaders();
      $listEbay->userId = $data['userEbayLogin'];
      $listEbay->perPage = $this->config['ebay']['maxResults'];
      $listEbay->page = $page;
      $listEbay->prepareInput();
      if($listEbay->connect())
      {
        $listEbay->insertedItems = $offers['externals'];
        $tmpItems = $listEbay->getItems(true);
        // Prepares output bids
        foreach($tmpItems as $t => $tmpItem)
        {
          if(array_key_exists($tmpItem['id'], $inserted))
          {
            $ebayItems[$tmpItem['id']] = $inserted[$tmpItem['id']];
          }
          else
          {
            $ebayItems[$tmpItem['id']] = array('catalogue' => 0, 'data' => array('title' => '', 'category' => 0, 'country' => '', 'city' => '')); 
          }
// TODO : récupérer la catégorie correspondante
          $documents = '';
          $correspondedCat = 1;
          $formFields = $this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields($correspondedCat);
          $form = $this->createForm(new FormTemplate($this->_ebayForm, $formFields));
          $pageView = $this->render('CategoryCategoriesBundle:Categories:getAjaxList.html.php', array('form' => $form->createView(),
          'fields' => $formFields));
          $xmlO = simplexml_load_string('<xml>'.$pageView.'</xml>');
          foreach($xmlO->div as $d => $doc)
          {
            $documents .= (string)$doc->asXML();
          }
          $ebayItems[$tmpItem['id']]['fields'] = $documents;
        }
      }
      else 
      {
        $ebayConnectError = true;
      }
      $response = $this->render('CatalogueOffersBundle:Platforms:ajaxGetEbayItemsList.html.php', array('items' => $ebayItems,
      'catalogues' => $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array('start' => 0, 'maxResults' => $this->config['pager']['maxResults']), $userAttr['id']),
      'cities' => $this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities(self::FRANCE_ID), 'page' => $page,
      'taxes' => Tax::getTaxesToSelect(false), 'defaultTax' => Tax::getDefaultTax(), 'ebayConnectError' => $ebayConnectError));
      if($ebayConnectError)
      {
        $response->setStatusCode(500); 
      }
      return $response;
    }
    return $this->render('CatalogueOffersBundle:Platforms:ajaxGetEbayItemsListEnd.html.php', array());
  }

  /**
   * Gets informations about one bid.
   * @access public
   * @return JSON data.
   */
  public function ajaxGetEbayItemAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $itemId = $request->attributes->get('item');
    $result = array('result' => 0, 'message' => "L'enchère n'a pas été trouvée");
    try
    {
      if(!ctype_alnum($itemId))
      {
        throw new \Exception("Incorrect item type (not alphanumerical)");
      }
// TODO : avec les informations sur l'enchère, retourner aussi les champs à remplir (suite à la correspondance entre
// la catégorie eBay et la catégorie UMO
      // check if bid is already in the database
      $itemDb = $this->enMan->getRepository('CatalogueOffersBundle:EbayTemporary')->itemExists($itemId);
      if(isset($itemDb['ebayItemId']) && $itemDb['ebayItemId'] == $itemId)
      {
        $data = unserialize($itemDb['ebayData']);
        $result = array('result' => 1, 'message' => '', 'title' => $data['title']);
        $correspondedCat = $data['category'];
      }
      else
      {
        $itemEbay = new EbayGetItem(array('debug' => 1, 'credentials' => $this->config['ebay']['creds'], 'currency' => $this->config['ebay']['currency']));
        $itemEbay->templatePath = $this->config['dirs']['templateEbay'];
        $itemEbay->setHeaders();
        $itemEbay->setItemId($itemId);
        $itemEbay->prepareInput();
        $itemEbay->connect();
        $item = $itemEbay->getItem();
// TODO : après accepter plusieurs pays, actuellement uniquement pour la France
        if($item['title'] != '' && $item['country'] == 'FR')
        {
          // mapped category UMO - eBay
// TODO : récupérer la VRAIE catégorie correspondante (après le mapping)
          $correspondedCat = 1;
          // all is right, get the categories fields
          $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
          // bid was found; insert it into ebay_temporary table
          $ebtEnt = new EbayTemporary;
          $ebtEnt->setData(array('ebayUser' => $userRef, 'ebayCatalogue' => 0, 'ebayItemId' => $itemId, 
            'ebayData' => serialize(array('title' => $item['title'], 'state' => $item['objectState'], 'city' => $item['city'], 
            'category' => $item['category'], 'offers' => $item['offersCount'], 'category' => $correspondedCat,
            'currency' => $item['offerCurrency'], 'price' => $item['offerPrice'], 'country' => $item['country'])), 
            'ebayDate' => '', 'ebayContent' => $item['desc'] 
          ));
          $this->enMan->persist($ebtEnt);
          $this->enMan->flush();
          // return item name
          $result = array('result' => 1, 'message' => '', 'title' => $item['title']); 
        }
      }
      // get form fields by category
      $documents = '';
      $formFields = $this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields($correspondedCat);
      $form = $this->createForm(new FormTemplate('AddOffer', $formFields));
      $pageView = $this->render('CategoryCategoriesBundle:Categories:getAjaxList.html.php', array('form' => $form->createView(),
      'fields' => $formFields));
      $xmlO = simplexml_load_string('<xml>'.$pageView.'</xml>');
      foreach($xmlO->div as $d => $doc)
      {
        $documents .= (string)$doc->asXML();
      }
      $result['fields'] = $documents;
    }
    catch(\Exception $e)
    {
      $result = array('result' => 0, 'message' => "Une erreur s'est produite pendant la récupération de l'enchère".$e->getMessage());
    }
    echo json_encode($result);
    die();
  }

  /**
   * Submit an adding form.
   * @access public
   * @return JSON message.
   */
  public function ajaxSubmitEbayFormAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $itemId = $request->attributes->get('item');
    if(!ctype_alnum($itemId))
    {
      throw new \Exception("Item's id is not the correct format (alphanumerical)");
    }
    $itemDb = $this->enMan->getRepository('CatalogueOffersBundle:EbayTemporary')->itemExists($itemId);
    if(isset($itemDb['ebayItemId']) && $itemDb['ebayItemId'] == $itemId)
    {
      // check if is not already inserted too
      $offerRow = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getExternalOfferByUser($userAttr['id'], $this->config['ebay']['systemName'], $itemId);
      if(!isset($offerRow['id_of']))
      {
        $data = unserialize($itemDb['ebayData']);
        // get category fields
        $formFields = $this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields($data['category']);
        $catalogueId = (int)$request->request->get('cat'.$itemId);
        $taxId = (int)$request->request->get('tax'.$itemId);
        $cityId = (int)$request->request->get('city'.$itemId);
        // start the validation process
        $catData = $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCatalogueData($catalogueId, $userAttr['id']);
        $testIntegers = (bool)($catalogueId > 0 && $taxId > 0 && $cityId > 0 && isset($catData['id_cat']) && $catData['id_cat'] == $catalogueId);
        $testValidators = false;
        $reqFormFields = $request->request->get($this->_ebayForm);
        $errors = array();
        foreach($formFields as $f => $field)
        {
          $validators = unserialize($field['constraintsForm']);
          $value = $reqFormFields[$field['codeName']];
          foreach($validators as $v => $validator)
          {
            $constraint = new $validator['constraint']($validator['options']);
            $conValString = $validator['constraint']."Validator";
            $constraintVal = new $conValString;
            if(!$constraintVal->isValid($value, $constraint))
            {
              $errors[] = $constraint->message;
            }
          }
          // print_r($field);
        }
        $testValidators = (bool)(count($errors) == 0);
        if($testIntegers && $testValidators)
        {
          // start SQL transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            $ebtEnt = new EbayTemporary;
            // make tags
            $tags = array('tag1' => '', 'tag2' => '', 'tag3' => '', 'tag4' => '', 'tag5' => '', 'tag6' => '', 'tag7' => '', 'tag8' => '', 'tag9' => '', 'tag10' => '');
            // refactoring : add new offer by addNewOffer() method
            $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->addNewOffer(
              array('user' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']),
                'catalogue' => $this->enMan->getReference('Catalogue\OffersBundle\Entity\Catalogues', $catalogueId), 
                'category' => $this->enMan->getReference('Category\CategoriesBundle\Entity\Categories', (int)$data['category']),
                'city' => $this->enMan->getReference('Geography\CitiesBundle\Entity\Cities', $cityId)
              ),
// TODO : FILTRER LES DONNEES POUR LA DESCRIPTION DU CONTENU (GARDER UNIQUEMENT LES BALISES QUI NE SONT PAS BLACKLISTEES, VIRER LE RESTE)
              array('offer' => array('catalogueId' => $catalogueId, 'cityId' => $cityId, 'categoryId' => (int)$data['category'],
                'tax' => Tax::getTaxValue($taxId), 'price' => (float)$data['price'], 
                'name' => $data['title'], 'description' => $itemDb['ebayContent'], 
                'state' => $ebtEnt->getCorrespondedState($data['state']), 
                'external' => $itemId, 'system' => $this->config['ebay']['systemName']), 
                'tags' => $tags, 'delivery' => array(), 'formFields' => $formFields, 'others' => $reqFormFields
              )
            );
            // Add images (move from temporary directory to the original directory)
            $dir = $this->config['images']['offersDir'].'/'.$offer->getIdOf().'/';
            if(!is_dir($dir))
            {
              mkdir($dir);
            }
            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            return new Response("1|L'enchère a été correctement ajoutée dans le catalogue");
          }
          catch(Exception $e)
          {
            $this->enMan->getConnection()->rollback();
            $this->enMan->close();
            throw $e;
          }
        }
        else
        {
          if(!$testIntegers)
          {
            if($catalogueId <= 0 || !isset($catData['id_cat']) || $catData['id_cat'] != $catalogueId)
            {
              $errors[] = "Veuillez renseigner le catalogue";            
            }
            if($taxId > 0)
            {
              $errors[] = "Veuillez indiquer la taxe";
            }
            if($cityId > 0)
            {
              $errors[] = "Veuillez indiquer la ville";
            }
          }    
          $errorsStart = "Plusieurs erreurs se sont produites";
          if(count($errors) == 1)
          {
            $errorsStart = "Une erreur s'est produite";
          }
          return new Response("0|".$errorsStart." pendant l'envoi du formulaire : ".implode('<br />', $errors));
        }
      }
      else
      {
        return new Response("0|Cette offre a déjà été rajoutée. Elle ne peut pas être ajoutée à nouveau.");
      }
    }
    else
    {
      throw new \Exception("Item doesn't exist in the database");
    }
  }

  /**
   * Open form for import one bid in the page "Add offer".
   * @access public
   * @return HTML template with the form or JSON message for submitted form.
   */
  public function ajaxImportEbayAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $itemId = $request->request->get('bidId');
    if($itemId != '')
    {
      // check if item is already in the database
      $offerRow = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getExternalOfferByUser($userAttr['id'], $this->config['ebay']['systemName'], $itemId);
      if(!isset($offerRow['id_of']))
      {
        $itemEbay = new EbayGetItem(array('debug' => 1, 'credentials' => $this->config['ebay']['creds'], 'currency' => $this->config['ebay']['currency']));
        $itemEbay->templatePath = $this->config['dirs']['templateEbay'];
        $itemEbay->setHeaders();
        $itemEbay->setItemId($itemId);
        $itemEbay->prepareInput();
        $itemEbay->connect();
        $item = $itemEbay->getItem();
        if($item['title'] != '' && $item['country'] == 'FR')
        {
          $result = $item;
          // get mapped category
// TODO : récupérer la catégorie correspondante
          $category = 1;
          // get mapped object state
          $ebtEnt = new EbayTemporary;
          $objectStateMapped = $ebtEnt->getCorrespondedState($item['objectState']);

          $result['result'] = 1;
          $result['objectStateMapped'] = $objectStateMapped;
          $result['categoryMapped'] = $category;
        }
      }
      else
      {
        $result = array('result' => 0, 'message' => "Cette enchère a déjà été importée");
      }
      echo json_encode($result);
      die();
    }
    return $this->render('CatalogueOffersBundle:Platforms:ajaxImportEbay.html.php', array());
  }

}