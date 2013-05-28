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
use Security\FilterXss;

class EbayController extends FrontController
{

  /**
   * Prefix for eBay form.
   * @access protected
   * @var string
   */
  protected $_ebayForm = 'AddOffer';

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
    return $this->render('CatalogueOffersBundle:Ebay:synchronizeEbay.html.php', array(
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
    return $this->render('CatalogueOffersBundle:Ebay:synchronizeEbayItems.html.php', array('maxPages' => $this->config['ebay']['maxPage']));
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
          'fields' => $formFields, 'bidId' => $tmpItem['id'], 'formErrors' => array()));
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
      $response = $this->render('CatalogueOffersBundle:Ebay:ajaxGetEbayItemsList.html.php', array('items' => $ebayItems,
      'catalogues' => $this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array('start' => 0, 'maxResults' => $this->config['pager']['maxResults']), $userAttr['id']),
      'cities' => $this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities(self::FRANCE_ID), 'page' => $page,
      'taxes' => Tax::getTaxesToSelect(false), 'defaultTax' => Tax::getDefaultTax(), 'ebayConnectError' => $ebayConnectError));
      if($ebayConnectError)
      {
        $response->setStatusCode(500); 
      }
      return $response;
    }
    return $this->render('CatalogueOffersBundle:Ebay:ajaxGetEbayItemsListEnd.html.php', array());
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
        $filter = new FilterXss(FilterXss::LIGHT_MODE, array("b", "br", "strong", "em", "ul", "li", "u", "p"));
        $itemEbay = new EbayGetItem(array('debug' => 1, 'credentials' => $this->config['ebay']['creds'], 'currency' => $this->config['ebay']['currency']));
        $itemEbay->templatePath = $this->config['dirs']['templateEbay'];
        $itemEbay->setHeaders();
        $itemEbay->setItemId($itemId);
        $itemEbay->prepareInput();
        $itemEbay->connect();
        $item = $filter->doFilterXss($itemEbay->getItem());
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
      'fields' => $formFields, 'bidId' => $itemId, 'formErrors' => array()));
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
      throw new \Exception("Item's id is not the correct format (only alphanumerical is accepted)");
    }
    $result = array('bid' => $itemId, 'isError' => 1, 'message' => "Une erreur s'est produite", 'fields' => array());
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
              $errors[] = $field['codeName'].$itemId.'|'.$constraint->message;
            }
          }
        }
        $testValidators = (bool)(count($errors) == 0);
        if($testIntegers && $testValidators)
        {
          // start SQL transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            $filter = new FilterXss(FilterXss::LIGHT_MODE, array("b", "br", "strong", "em", "ul", "li", "u", "p"));
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
                'name' => $filter->doFilterXss($data['title']), 'description' => $filter->doFilterXss($itemDb['ebayContent']),
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
            $result = array('bid' => $itemId, 'isError' => 0, 'message' => "L'enchère a été correctement ajoutée dans le catalogue", 'fields' => array());
            // return new Response("1|L'enchère a été correctement ajoutée dans le catalogue");
            echo json_encode($result);
            die();
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
          // validation errors occured, send an error message
          if(!$testIntegers)
          {
            if($catalogueId <= 0 || !isset($catData['id_cat']) || $catData['id_cat'] != $catalogueId)
            {
              $errors[] = 'cat'.$itemId."|Veuillez renseigner le catalogue";            
            }
            if($taxId <= 0)
            {
              $errors[] = 'tax'.$itemId."|Veuillez indiquer la taxe";
            }
            if($cityId <= 0)
            {
              $errors[] = 'city'.$itemId."|Veuillez indiquer la ville";
            }
          }    
          $errorsStart = "Plusieurs erreurs se sont produites";
          if(count($errors) == 1)
          {
            $errorsStart = "Une erreur s'est produite";
          }
          $result = array('bid' => $itemId, 'isError' => 1, 'message' => "".$errorsStart." pendant l'envoi du formulaire ", 'fields' => $errors);
          echo json_encode($result);
          die();
          // return new Response("0|".$errorsStart." pendant l'envoi du formulaire : ".implode('<br />', $errors));
        }
      }
      else
      {
        $result = array('bid' => $itemId, 'isError' => 1, 'message' => "Cette offre a déjà été rajoutée", 'fields' => array());
        echo json_encode($result);
        die();
        // return new Response("0|Cette offre a déjà été rajoutée. Elle ne peut pas être ajoutée à nouveau.");
      }
    }
    else
    {
      throw new \Exception("Item doesn't exist in the database");
    }
    echo json_encode($result);
    die();
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
    if($request->getMethod() == 'POST')
    {
      $result = array('result' => 0, 'message' => "Veuillez renseigner toutes les informations demandées");
      try
      {
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
              $filter = new FilterXss(FilterXss::LIGHT_MODE, array("b", "br", "strong", "em", "ul", "li", "u", "p"));
              $result = $filter->doFilterXss($item);
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
        }
      }
      catch(\Exception $e)
      {
        $result['message'] = $e->getMessage();
      }
      echo json_encode($result);
      die();
    }
    return $this->render('CatalogueOffersBundle:Ebay:ajaxImportEbay.html.php', array());
  }

}