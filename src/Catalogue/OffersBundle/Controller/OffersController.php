<?php
namespace Catalogue\OffersBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Catalogue\OffersBundle\Form\AddOffer;
use Catalogue\OffersBundle\Entity\Offers;
use Catalogue\OffersBundle\Entity\OffersFormFields;
use Catalogue\OffersBundle\Entity\OffersTags; 
use Ad\ItemsBundle\Entity\AdsTags;
use Ad\ItemsBundle\Entity\Ads;
use Ad\ItemsBundle\Entity\AdsOffers;
use Ad\ItemsBundle\Entity\AdsOffersPropositions;
use Category\CategoriesBundle\Entity\FormFields;
use Category\CategoriesBundle\Entity\FormFieldsCategories;
use Frontend\FrontBundle\Entity\Tags;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Others\Pager;
use Others\ProposeValidators;
use Database\DatabaseTools;
use User\ProfilesBundle\Entity\Users;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Order\OrdersBundle\Entity\Tax;
use Geography\CountriesBundle\Entity\Countries;
use Catalogue\OffersBundle\OfferNotFoundException;
use Validators\Csrf;
use Validators\CsrfValidator;
use User\ProfilesBundle\Entity\UsersSessions;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class OffersController extends FrontController
{

  /**
   * Add offer action. 
   * @return Displayed template.
   */
  public function addOfferAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    // session handler for uploadify plugin session's persistance
    $useEnt = new UsersSessions;
    $this->enMan->getRepository('UserProfilesBundle:UsersSessions')->deleteInvalid($userAttr['id']);

    if($this->isTest)
    {
      $from = array('src/Catalogue/OffersBundle/Controller', 'src\Catalogue\OffersBundle\Controller');
      $to = array('', '');
      $dir = str_replace($from, $to, dirname(__FILE__));
      // echo file_get_contents($dir.'\cache\lists.php');
      require($dir.'/cache/lists.php');
    }
    else
    {
      require($this->cacheManager->getBaseDir().'lists.php');
    }
    // prepares cache for geography elements (cities, regions, countries)
    $conEnt = new Countries();
    $conEnt->setSource($geography);
    $flashSess = $request->getSession();
    // temporary offer id
    if($request->request->get('tmpId') != null)
    {
      $flashSess->setFlash('offerTmp', $request->request->get('tmpId'));
    }
    elseif($flashSess->getFlash('offerTmp') == '' || (int)$flashSess->getFlash('addSuccess') == 1)
    {
      $flashSess->setFlash('offerTmp', sha1(time().rand(0, 999).$_SERVER['REMOTE_ADDR']));
    }
    else
    {
      $flashSess->setFlash('offerTmp', $flashSess->getFlash('offerTmp'));
    }
    $postData = $flashSess->getFlash('formData');
    $offEnt = new Offers;
    $offEnt->setCataloguesList($this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array(
      'maxResults' => $this->config['pager']['maxResults'],
      'start' => 0
    ), (int)$userAttr['id']));
    // check if user has catalogues; if not, show error page
    if(!$offEnt->hasCatalogues())
    {
      return $this->render("CatalogueOffersBundle:Offers:cantAddOffer.html.php", array("message" => $offEnt->getReasonMessage(Offers::NO_CATALOGUES), "add" => true, "edit" => false, "titleAction" => "Ajout d'une offre"));
    }
    // FROM DB : $offEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
    $offEnt->setCountriesList($conEnt->getCountries());
    // FROM DB : $offEnt->categoriesList = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false);
    $offEnt->categoriesList = $categories;
    $data = $request->request->all('AddOffer');
    $fees = array();
    // form fields list and delivery fees
    if(isset($data['AddOffer']))
    {
      $offEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddOffer']['offerCategory']));
      $offEnt->fieldsValues = $data['AddOffer'];
      // FROM DB : $offEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($data['AddOffer']['offerCountry']));
      $offEnt->setCitiesList($conEnt->getCitiesByCountry($data['AddOffer']['offerCountry']));
    }
    else
    {
      // FROM DB : $offEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($offEnt->getOfferCountry()));
      $offEnt->setCitiesList($conEnt->getCitiesByCountry($offEnt->getOfferCountry()));
      if(count($postData) > 0)
      {
        $offEnt->setDataAdded($postData['AddOffer']);
        $fees = $postData['AddOffer']['delivery'];
      }
    }
    // get temporary images
    $offerImages = array();
    if(ctype_alnum($flashSess->getFlash('offerTmp')))
    {
      $images = $this->enMan->getRepository('CatalogueImagesBundle:OffersImagesTmp')->getByOfferIdAndUser($flashSess->getFlash('offerTmp'), $userAttr['id']);
      foreach($images as $im => $image)
      {
        $offerImages[$image['id_oit']] = array('id_oi' => $image['id_oit'], 'imageName' => $image['tmpName'].'.'.$image['tmpExt']);
      }
    }
    Offers::setSessionToken($this->sessionTicket);
    $offEnt->setTicket($this->sessionTicket);
    $formAdd = $this->createForm(new AddOffer(), $offEnt);
    if($request->getMethod() == 'POST') 
    {  
      $formAdd->bindRequest($request); 
      $data = $request->request->all('AddOffer');
      // make delivery zones fees
      $delivery = array();
      if((int)$data['AddOffer']['deliveryYN'] == 1)
      {
        foreach($zones as $z => $zone)
        {
          $zoneValue = $request->request->get('zone'.$zone['id']);
          if(trim($zoneValue) != '')
          {
            $delivery[$zone['id']] = (float)$zoneValue;
          }
        }
      }
      if($formAdd->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $attr = $this->user->getAttributes();
          // make tags
          $tags = array();
          for($i = 1; $i < 11; $i++)
          {
            $tags['tag'.$i] = $data['AddOffer']['tag'.$i];
          }
          // refactoring : add new offer by addNewOffer() method
          $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->addNewOffer(
            array('user' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$attr['id']),
              'catalogue' => $this->enMan->getReference('Catalogue\OffersBundle\Entity\Catalogues', (int)$offEnt->getOfferCatalogue()), 'category' => $this->enMan->getReference('Category\CategoriesBundle\Entity\Categories', (int)$offEnt->getOfferCategory()),
              'city' => $this->enMan->getReference('Geography\CitiesBundle\Entity\Cities', (int)$offEnt->getOfferCity())
            ),
            array('offer' => array('catalogueId' => $offEnt->getOfferCatalogue(), 'cityId' => $offEnt->getOfferCity(), 'categoryId' => $offEnt->getOfferCategory(),
              'tax' => Tax::getTaxValue((int)$data['AddOffer']['offerTax']), 'price' => $data['AddOffer']['offerPrice'], 'name' => $offEnt->getOfferName(), 'description' => $offEnt->getOfferText(),  'state' => $offEnt->getOfferObjetState(), 'external' => '', 'system' => ''), 
              'tags' => $tags, 'delivery' => $delivery, 'formFields' => $offEnt->getFormFields(), 'others' => $data['AddOffer']
            )
          );
          // Add images (move from temporary directory to the original directory)
          $dir = $this->config['images']['offersDir'].'/'.$offer->getIdOf().'/';
          if(!is_dir($dir) && !$this->isTest)
          {
            mkdir($dir);
          }
          // look for temporary image (by session temporary offer id)
          try
          {
            $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->uploadImages($offer, $this->enMan->getRepository('CatalogueImagesBundle:OffersImagesTmp')->getByOfferId($flashSess->getFlash('offerTmp')), 
              array('temporary' => $this->config['images']['offersTmpDir'], 'final' => $dir, 
                'prefixes' => $this->config['images']['configuration']['offer']['prefix'],
                'maxImages' => $this->config['images']['configuration']['offer']['maxImages']
              )
            );
          }
          catch(Exception $e)
          {
            // revert files
            $files = scandir($dir);
            foreach($files as $f => $file)
            {
              rename($dir.$file, $this->config['images']['offersTmpDir'].$file);
            }
            $this->enMan->getConnection()->rollback();
            $this->enMan->close();
            throw $e;
          }
          // Update stats for offers
          $q = $this->enMan->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Stats', 's')
          ->set('s.statValue', 's.statValue + 1')
          ->where('s.key_st = ?1')
          ->setParameter(1, 'offa')
          ->getQuery()
          ->execute();
          $flashSess->setFlash('addSuccess', 1);
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          if($this->isTest)
          {
            return new Response('added_successfully');
          }
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
        $data['AddOffer']['delivery'] = $delivery;
        $flashSess->setFlash('formData', $data);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formAdd));
      }
      if((int)$data['AddOffer']['offerAd'] == 0)
      {
        return $this->redirect($this->generateUrl('offersAdd'));
      }
      else
      {
        return $this->redirect($this->generateUrl('offerProposeSend', array('ad' => (int)$data['AddOffer']['offerAd'], 'id' => $offer->getIdOf())).'?ticket='.$this->sessionTicket);
      }
    }
    return $this->render('CatalogueOffersBundle:Offers:add.html.php', array('offerId' => 0, 'add' => true, 'edit' => false, 'form' => $formAdd->createView(),
    'ticket' => $this->sessionTicket, 'formErrors' => (array)$flashSess->getFlash('formErrors'), 'formFields' => $offEnt->getFormFields(), 'tmpId' => $flashSess->getFlash('offerTmp'),
    'isSuccess' => (int)$flashSess->getFlash('addSuccess'), 'offerImages' => array(), 'zones' => $zones, 'fees' => $fees, 'dir' => $this->config['view']['dirs']['offersTmp'],
    'offerImages' => $offerImages, 'canAddImages' => true, 'titleAction' => "Ajouter une offre", 'configUploadify' => $this->config['uploadify'],
    'route' => 'offersImagesDelTmp', 'randomId' => $this->enMan->getRepository('UserProfilesBundle:UsersSessions')->setNewRandom($userAttr['id'])));
  }

  /**
   * Edit user's offer.
   * @return Displayed template
   */
  public function editOfferAction(Request $request)
  {
    require_once($this->cacheManager->getBaseDir().'lists.php');
    // prepares cache for geography elements (cities, regions, countries)
    $conEnt = new Countries();
    $conEnt->setSource($geography);
    $attr = $this->user->getAttributes();
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 1 && $testResult == 0)
    {
      $attr = array('id' => (int)$request->attributes->get('user'));
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attr = array('id' => (int)$request->attributes->get('elUser1'));
    }
    $postData = $flashSess->getFlash('formData');
    $offEnt = new Offers;
    $offEnt->setEditedData($this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOfferData($id, (int)$attr['id']));
    // FROM DB : $offEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
    $offEnt->setCountriesList($conEnt->getCountries());
    // FROM DB : $offEnt->categoriesList = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false);
    $offEnt->categoriesList = $categories;
    if($offEnt->getIdOf() != '')
    {
      // session handler for uploadify plugin session's persistance
      $useEnt = new UsersSessions;
      $this->enMan->getRepository('UserProfilesBundle:UsersSessions')->deleteInvalid($attr['id']);

      $offerImages = $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->getImagesByOfferAndUser($id, $attr['id']);
      $canAddImages = (bool)(count($offerImages) < $this->config['images']['configuration']['offer']['maxImages']);
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $dbOfferCategory = $offEnt->getOfferCategory();
      $dbOfferCatalogue = $offEnt->getOfferCatalogue();
      $dbOfferCity = $offEnt->getOfferCity();
      if(count($flashData = $flashSess->getFlash('formData')) > 0)
      {
        $offEnt->setDataAdded($flashData['AddOffer']);
        $fees = $flashData['AddOffer']['delivery'];
      }
      else
      {
        $offEnt->setFormFieldsData($this->enMan->getRepository('CatalogueOffersBundle:OffersFormFields')->getFieldsByOffer($id, $dbOfferCategory));
        // if delivery fees 
        $fees = $offEnt->getPreparedFees($this->enMan->getRepository('CatalogueOffersBundle:OffersDeliveryZones')->getFeesByOffer($id));
      }
      // FROM DB : $offEnt->setCategoriesList($this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false));
      $offEnt->setCategoriesList($categories);
      $data = $request->request->all('AddOffer');
      // form fields list => form submitted
      if(isset($data['AddOffer']))
      {
        // FROM DB : $offEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($data['AddOffer']['offerCountry']));
        $offEnt->setCitiesList($conEnt->getCitiesByCountry($data['AddOffer']['offerCountry']));
        $offEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddOffer']['offerCategory']));
      }
      elseif($offEnt->getOfferCategory() != '')
      {
        // FROM DB : $offEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($offEnt->getOfferCountry()));
        $offEnt->setCitiesList($conEnt->getCitiesByCountry($offEnt->getOfferCountry()));
        $offEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$offEnt->getOfferCategory()));
      }
      $offEnt->setCataloguesList($this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array(
      'maxResults' => $this->config['pager']['maxResults'],
      'start' => 0
      ), (int)$attr['id']));
      $offEnt->setDeliveryYN((int)(count($fees) > 0));
      Offers::setSessionToken($this->sessionTicket);
      $offEnt->setTicket($this->sessionTicket);
      $formEdit = $this->createForm(new AddOffer(), $offEnt);
      if($request->getMethod() == 'POST') 
      {
        $formEdit->bindRequest($request);
        // make delivery zones fees
        $delivery = array();
        if((int)$data['AddOffer']['deliveryYN'] == 1)
        {
          foreach($zones as $z => $zone)
          {
            $zoneValue = $request->request->get('zone'.$zone['id']);
            if(trim($zoneValue) != '')
            {
              $delivery[$zone['id']] = (float)$zoneValue;
            }
          }
        }
        if($formEdit->isValid())
        {
          // start SQL transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            // refactoring : edit offer with editOffer() method
            $this->enMan->getRepository('CatalogueOffersBundle:Offers')->editOffer($id, array(
              'offer' => array('new' => array('offerCategory' => $data['AddOffer']['offerCategory'], 'offerCatalogue' => $data['AddOffer']['offerCatalogue'], 'offerCity' => $data['AddOffer']['offerCity'], 'offerPrice' => $data['AddOffer']['offerPrice'],
                'offerObjetState' => $data['AddOffer']['offerObjetState'], 'offerName' => $data['AddOffer']['offerName'], 'offerText' => $data['AddOffer']['offerText']), 
                'old' => array('category' => $dbOfferCategory, 'city' => $dbOfferCity, 'region' => '', 'country' => '', 'catalogue' => $dbOfferCatalogue)),
              'formField' => array('new' => $offEnt->getFormFields(), 'old' => array()),
              'others' => $data['AddOffer'], 'delivery' => $delivery
            ));
            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            $flashSess->setFlash('editSuccess', 1);
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
          $data['AddOffer']['delivery'] = $delivery;
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
        } 
        return $this->redirect($this->generateUrl('offersEdit', array('id' => $id)));
      }
      return $this->render('CatalogueOffersBundle:Offers:add.html.php', array('edit' => true, 'add' => false, 'form' => $formEdit->createView(),
      'ticket' => $this->sessionTicket, 'formErrors' => (array)$flashSess->getFlash('formErrors'), 'offerId' => $id,
      'formFields' => $offEnt->getFormFields(), 'tmpId' => 0, 'fees' => $fees, 'offerImages' => $offerImages, 'canAddImages' => $canAddImages,
      'randomId' => $this->enMan->getRepository('UserProfilesBundle:UsersSessions')->setNewRandom($attr['id']), 'route' => 'offersImagesDelete', 'isSuccess' => (int)$flashSess->getFlash('editSuccess'), 'zones' => $zones, 'dir' => $this->config['view']['dirs']['offersImg'].$id.'/', 'titleAction' => "Editer une offre", 'configUploadify' => $this->config['uploadify']));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Delete user's offer.
   * @return Displayed template
   */
  public function deleteOfferAction(Request $request)
  {
    $attr = $this->user->getAttributes();
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $result = array('isError' => 1, 'message' => "Vous n'avez pas le droit d'exécuter cette action");
    if($isTest == 1 && $testResult == 0)
    {
      $attr = array('id' => (int)$request->attributes->get('user'));
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attr = array('id' => (int)$request->attributes->get('elUser1'));
      $validCSRF = true;
    }
    elseif($this->isTest)
    {
      $validCSRF = true;
    }
    else
    {
      $validCSRF = $this->validateCSRF();
    }
    $offerData = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOfferData($id, (int)$attr['id']);
    if($validCSRF === true && isset($offerData['id_of']) && $offerData['id_of'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        $this->enMan->getRepository('CatalogueOffersBundle:Offers')->deleteOffer($id, $offerData, array('title' => $this->config['deleted']['offerDeleted'],
        'prefix' => $this->config['images']['configuration']['offer']['prefix'], 'offersDir' => $this->config['images']['offersDir'],
        'mailer' => $this->get('mailer'), 'from' => $this->from['mail']));
        $result['isError'] = 0;
        $result['message'] = "L'offre a été correctement supprimée";
        // commit SQL transaction
        $this->enMan->getConnection()->commit();
      }
      catch(Exception $e)
      {
        $result['message'] = "Une erreur s'est produite";
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    if(!$validCSRF)
    {
      $result['message'] = "Votre session a expiré. Veuillez réessayer";
    }
    echo json_encode($result);
    die();
  }


  /**
   * Delete user's offer by queue (X offers by request).
   * @access public
   * @return Displayed template
   */
  public function deleteOffersQueueAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    $result = array('result' => 0, 'deleted' => 0);
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $validCSRF = true;
    }
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')
    ->getOffersListByUser(array(
      'maxResults' => $this->config['pager']['perPageQueue'],
      'start' => 0
    ), (int)$userAttr['id']);
    if($validCSRF === true && count($offers) > 0)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        foreach($offers as $o => $offer)
        {
          $this->enMan->getRepository('CatalogueOffersBundle:Offers')->deleteOffer($offer['id_of'], $offer, array('title' => $this->config['deleted']['offerDeleted'],
          'prefix' => $this->config['images']['configuration']['offer']['prefix'], 'offersDir' => $this->config['images']['offersDir'],
          'mailer' => $this->get('mailer'), 'from' => $this->from['mail']));
        }
        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        // set correct delete
        $result['result'] = 1;
        $result['deleted'] = count($offers);
      }
      catch(Exception $e)
      {
        $flashSess->setFlash('deleteSuccess', 0);
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    elseif(count($offers) <= 0)
    {
      $result['result'] = count($offers); // no more offers
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    echo json_encode($result);
    die();
  }

  /**
   * User's offers list.
   * @return Displayed template
   */
  public function listUserOffersAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $flashSess = $request->getSession();
    $userAttr = $this->user->getAttributes();
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')
    ->getOffersListByUser(array('column' => $column, 'how' => $how,
      'maxResults' => $this->config['pager']['perPage'], 'dateFormat' => $this->config['sql']['dateFormat'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['offers'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('CatalogueOffersBundle:Offers:userOffersTable.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'page' => $page,
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('nom', 'date')), 'how' => $how, 'column' => $column));
    }
    return $this->render('CatalogueOffersBundle:Offers:listUserOffers.html.php', array('offers' => $offers, 'pager' => $pager->setPages(),
    'deleteSuccess' => (int)$flashSess->getFlash('deleteSuccess'), 'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('nom', 'date')), 'how' => $how, 'column' => $column));
  }

  /**
   * Show offers by user's id.
   * @return Displayed template.
   */
  public function listOffersByUserAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $userAttr = $this->user->getAttributes();
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')
    ->getOffersListByUser(array(
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['offers'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    return $this->render('CatalogueOffersBundle:Offers:listUserOffers.html.php', array('offers' => $offers, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket));
 }

  /**
   * Show offer.
   * @return Displayed template.
   */
  public function showOfferAction(Request $request)
  {
    $id = (int)$request->attributes->get('offerId');
    $flashSess = $request->getSession();
    $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($id);
    if(isset($offer['id_of']))
    {
      $adsEnt = new Ads;
      // check if user can propose offer from his catalogue
      $canPropose = false;
      if(parent::checkIfConnected())
      {
        $attrs = $this->user->getAttributes();
        if($attrs['id'] != $offer['id_us'])
        {
          $canPropose = true;
        }
      }
// TODO : make intelligent counter
      return $this->render('CatalogueOffersBundle:Offers:showOffer.html.php', array('offer' => $offer, 'adsStates' => $adsEnt->getObjetStates(),
      'offerProposed' => (int)$flashSess->getFlash('offerProposed'),
      'offers' => array(), 
      'offerUrl' => $request->attributes->get("offer"), 
      'fields' => $this->enMan->getRepository('CatalogueOffersBundle:OffersFormFields')->getFieldsByOffer($id, $offer['id_ca']),
      'tags' => $this->enMan->getRepository('CatalogueOffersBundle:OffersTags')->getTagsByOffer($id), 'canPropose' => $canPropose,
      'ticket' => $this->sessionTicket, 'offersDir' => $this->config['view']['dirs']['offersImg'].$id.'/', 'images' => $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->getImagesByOffer($id, array('dateFormat' => $this->config['sql']['dateFormat']))));
    }
    throw new OfferNotFoundException("Offer $id doesn't exist");
  }

  /**
   * Gets offers list by defined tags.
   * + cache saving
   * @return Displayed template.
   */
  public function getOffersByTagsAction(Request $request)
  {
    $tagGet = (int)$request->attributes->get('tag');
    $page = (int)$request->attributes->get('page');
    $tag = $this->enMan->getRepository('FrontendFrontBundle:Tags')->find($tagGet);
    if((int)$tag->getIdTa() > 0)
    {
      $cacheName = $this->config['cache']['tags'].$tagGet.'/offers/page_'.$page;
      $offers = $this->enMan->getRepository('CatalogueOffersBundle:OffersTags')->getOffersList(array('cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)
      ), $tagGet);
	  $pager = new Pager(array('before' => $this->config['pager']['before'],
	  	  	   'after' => $this->config['pager']['after'], 'all' => $tag->getTagOffers(),
	  	  	   'page' => $page, 'perPage' => $this->config['pager']['perPage']
	           ));
      return $this->render('CatalogueOffersBundle:Offers:getOffersByTag.html.php', array('offers' => $offers, 'pager' => $pager->setPages(),
      'params' => array('tag' => $tagGet, 'url' => $request->attributes->get('url'))));
    }
    throw new \Exception('Tag NOT FOUND');
  }

  /** 
   * Propose an offer to ad.
   * @access public
   * @return Displayed template
   */
  public function proposeAction(Request $request)
  {
    require_once($this->cacheManager->getBaseDir().'lists.php');
    // check if ad is active (get all data to check if user can propose his offer)
    $attrs = $this->user->getAttributes();
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    // $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $attrs = array('id' => (int)$request->attributes->get('elUser1'));
      // $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attrs = array('id' => (int)$request->attributes->get('user'));
      // $validCSRF = true;
    }
    $ad = $this->enMan->getRepository('AdItemsBundle:Ads')->getOneAd($id);
    if(/*$validCSRF === true && */isset($ad['id_ad']) && $attrs['id'] != $ad['id_us'])
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // check possibility to propose user's offer
      // check by : city, minimal opinion, seller type
      $useEnt = new Users;
      $result = 1;
      $errors = array();
      $values = array('opinion' => $attrs['average'], 'seller' => $attrs['type']);
      $criteria = array('opinion' => $ad['adMinOpinion'], 'seller' => $ad['adSellerType']);
      $validClass = new ProposeValidators($criteria, $values);
      // set labels options
      $validClass->setLabelOptions('seller', 'accepted', array('alias' => $useEnt->getAliasType($ad['adSellerType'])));
      $validClass->setLabelOptions('seller', 'given', array('alias' => $useEnt->getAliasType($attrs['type'])));
      $validClass->setLabelOptions('opinion', 'accepted', array('alias' => $ad['adMinOpinion']));
      $validClass->setLabelOptions('opinion', 'given', array('alias' => $attrs['average']));
      if(!$validClass->validate())
      {
        $result = 0;
        $errors = $validClass->notValid;
        return $this->render('CatalogueOffersBundle:Offers:proposeError.html.php', array('nextSent' => '', 'id' => $id, 'ad' => $ad, 'errors' => $errors));
      }
      else
      {
        // prepares offer's adding form
        $flashSess = $request->getSession();
        $postData = $flashSess->getFlash('formData');
        $offEnt = new Offers;
        $offEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
        $offEnt->categoriesList = $categories; //$this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false);
        $offEnt->setCataloguesList($this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array(
          'maxResults' => $this->config['pager']['maxResults'],
          'start' => 0
        ), (int)$attrs['id']));
        // temporary offer id
        if($flashSess->getFlash('offerTmp') == '' || (int)$flashSess->getFlash('addSuccess') == 1)
        {
          $flashSess->setFlash('offerTmp', sha1(time().rand(0, 999).$_SERVER['REMOTE_ADDR']));
        }
        else
        {
          $flashSess->setFlash('offerTmp', $flashSess->getFlash('offerTmp'));
        }
        // get temporary images
        $offerImages = array();
        if(ctype_alnum($flashSess->getFlash('offerTmp')))
        {
          $images = $this->enMan->getRepository('CatalogueImagesBundle:OffersImagesTmp')->getByOfferIdAndUser($flashSess->getFlash('offerTmp'), $attrs['id']);
          foreach($images as $im => $image)
          {
            $offerImages[$image['id_oit']] = array('imageName' => $image['tmpName'].'.'.$image['tmpExt']);
          }
        }
        $data = $request->request->all('AddOffer');
        // form fields list
        if(isset($data['AddOffer']))
        {
          $offEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddOffer']['offerCategory']));
          $offEnt->fieldsValues = $data['AddOffer'];
        }
        $offEnt->setOfferAd($id);
        $formAdd = $this->createForm(new AddOffer(), $offEnt);
        // set ad data used to compare with offer informations
        $adsEnt = new Ads;
        $adRow = array('category' => $ad['categoryName'], 'country' => $ad['countryName'], 'objectState' => $adsEnt->getAdObjectState($ad['adObjetState']), 'city' => $ad['cityName'], 'region' => $ad['regionName'], /*'priceFrom' => $ad['adBuyFrom'],*/ 'priceTo' => $ad['adBuyTo']);
        return $this->render('CatalogueOffersBundle:Offers:propose.html.php', array('adId' => $id, 'ad' => $ad, 'result' => $result,
        'id' => $id, 'errors' => $errors, 'configUploadify' => $this->config['uploadify'], 'form' => $formAdd->createView(), 'tmpId' => $flashSess->getFlash('offerTmp'), 'formFields' => $offEnt->getFormFields(),
        'zones' => $zones, 'randomId' => $this->enMan->getRepository('UserProfilesBundle:UsersSessions')->setNewRandom($attrs['id']), 'offerImages' => $offerImages, 'canAddImages' => true, 'adRow' => $adRow));
      }
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Gets offers to one ad.
   * @access public
   * @return Displayed template
   */
  public function ajaxGetToAdAction(Request $request)
  {
    $attrs = $this->user->getAttributes();
    $page = (int)$request->attributes->get('page');
    $ad = (int)$request->attributes->get('ad');
    $category = (int)$request->request->get('adCategory');
    $state = (int)$request->request->get('adState');
    $from = (float)$request->request->get('adFrom');
    $to = (float)$request->request->get('adTo');
    $geo = (int)$request->request->get('adGeo');
    $city = (int)$request->request->get('adCity');
    $region = (int)$request->request->get('adRegion');
    $country = (int)$request->request->get('adCountry');
    $adsEnt = new Ads();
    $geoQuery = $adsEnt->getGeoToQuery($geo, array('city' => $city, 'cityName' => '', 'offerCity' => '',
    'region' => $region, 'regionName' => '', 'offerRegion' => '',
    'country' => $country, 'countryName' => '', 'offerCountry' => ''));
    $params = array_merge($geoQuery, array('cat.id_ca' => $category, 'o.offerObjetState' => $state,
    'o.offerPrice' => array('type' => 'BETWEEN', 'from' => $from, 'to' => $to)));

    $datTools = new DatabaseTools();
    $query = $datTools->makeQueryString($params);
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOffersByFilterAndUser(array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)
    ), $query['query'], $query['params'], (int)$attrs['id']);
    $count = 0;
    if(count($offers) > 0)
    {
      $count = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->countOffersByFilterAndUser($query['query'], $query['params'], (int)$attrs['id']);
    }
    $pager = new Pager(array('before' => $this->config['pager']['before'],
    'after' => $this->config['pager']['after'], 'all' => $count,
    'page' => $page, 'perPage' => $this->config['pager']['perPage']
    ));
    return $this->render('CatalogueOffersBundle:Offers:ajaxGetToAd.html.php', array('ad' => $ad, 'count' => $count, 'offers' => $offers, 'pager' => $pager->setPages(), 'ticket' => $this->sessionTicket));
  }


  /**
   * Propose an precised offer from catalogue.
   * @access public
   * @return Displayed template
   */
  public function sendOfferAction(Request $request)
  {
    $offerId = (int)$request->attributes->get('id');
    $adId = (int)$request->attributes->get('ad');
    $attrs = $this->user->getAttributes();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $validCSRF = $this->validateCSRF();
    if($isTest == 1 && $testResult == 0)
    {
      $attrs = array('id' => (int)$request->attributes->get('user'));
      $adId = (int)$request->attributes->get('id2');
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attrs = array('id' => (int)$request->attributes->get('elUser1'));
      $adId = (int)$request->attributes->get('id2');
      $validCSRF = true;
    }
    elseif($this->isTest)
    {
      $attrs = array('id' => 2, 'average' => 5.0, 'type' => 1);
      $validCSRF = true;
    }
    $flashSess = $request->getSession();
    // final offer validation
    $ad = $this->enMan->getRepository('AdItemsBundle:Ads')->getOneAd($adId);
    $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOfferData($offerId, (int)$attrs['id']);
    if($validCSRF === true && isset($offer['id_of']) && $ad['id_us'] != $attrs['id'])
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // check by : city, minimal opinion, seller type
      $adsEnt = new Ads();
      $geoQuery = $adsEnt->getGeoToQuery($ad['adSellerGeo'], array('city' => $ad['id_ci'], 'cityName' => $ad['cityName'], 'offerCity' => $offer['cityName'], 
      'region' => $ad['id_re'], 'regionName' => $ad['regionName'], 'offerRegion' => $offer['regionName'], 
      'country' => $ad['id_co'], 'countryName' => $ad['countryName'], 'offerCountry' => $offer['countryName']));
      $geoOffer = '';
      $geoCondition = '';
      foreach($geoQuery as $gq => $geoQ)
      {
        $geoCondition = $geoQ;
        $key = explode('.', $gq);
        $geoOffer = $offer[$key[1]];
        // we make only the one loop
        break;
      }
      $result = 1;
      $errors = array();
      $values = array('opinion' => $attrs['average'], 'seller' => $attrs['type'],
      'geo' => $geoOffer, 'price' => $offer['offerPrice'], 'object' => $offer['offerObjetState']);
      $criteria = array('opinion' => $ad['adMinOpinion'], 'seller' => $ad['adSellerType'],
      'geo' => $geoCondition, 'price' => array(/*'from' => $ad['adBuyFrom'],*/ 'to' => $ad['adBuyTo']), 
      'object' => $ad['adObjetState']);
      $validClass = new ProposeValidators($criteria, $values);
      // set labels options
      // seller type
      $useEnt = new Users;
      $validClass->setLabelOptions('seller', 'accepted', array('alias' => $useEnt->getAliasType($ad['adSellerType'])));
      $validClass->setLabelOptions('seller', 'given', array('alias' => $useEnt->getAliasType($attrs['type'])));
      // price
      $validClass->setLabelOptions('price', 'accepted', array('alias' => /*'de '.$ad['adBuyFrom'].' à*/ 'Prix max : '.$ad['adBuyTo']));
      $validClass->setLabelOptions('price', 'given', array('alias' => $offer['offerPrice']));
      // object state
      $validClass->setLabelOptions('object', 'accepted', array('alias' => $adsEnt->getAdObjectState($ad['adObjetState'])));
      $validClass->setLabelOptions('object', 'given', array('alias' => $adsEnt->getAdObjectState($offer['offerObjetState'])));
      // opinion criterion
      $validClass->setLabelOptions('opinion', 'accepted', array('alias' => $ad['adMinOpinion']));
      $validClass->setLabelOptions('opinion', 'given', array('alias' => $attrs['average']));
      // geo criterion
      if($ad['adSellerGeo'] > 0)
      {
        $validClass->setLabelOptions('geo', 'accepted', array('alias' => $geoQuery['label']));
        $validClass->setLabelOptions('geo', 'given', array('alias' => $geoQuery['offer']));
      }
      if(!$validClass->validate())
      {
        $result = 0;
        $errors = $validClass->notValid;
        return $this->render('CatalogueOffersBundle:Offers:proposeError.html.php', array('nextSent' => "<br /><br />L'offre a quand même été rajoutée dans votre catalogue.", 'id' => $ad['id_ad'], 'ad' => $ad, 'errors' => $errors, 'result' => $result));
      }
      else
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          // add offer to ads_offers table
          $aofEnt = new AdsOffers;
          $aofEnt->setAdsIdAd($this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $adId));
          $aofEnt->setOffersIdOf($this->enMan->getReference('Catalogue\OffersBundle\Entity\Offers', $offerId));
          $aofEnt->setAddedDate('');
          $this->enMan->persist($aofEnt);
          $this->enMan->flush();

          // update offers quantity for this ad
          $this->enMan->getRepository('AdItemsBundle:Ads')->updateOffersQuantity(1, $adId);

          // notify ad's author about the new offer
          $emtEnt = new EmailsTemplates;
          $tplVals = array('{AD_TITLE}', '{OFFER_NAME}', '{OFFER_PRICE}');
          $realVals = array($ad['adName'], $offer['offerName'], $offer['offerPrice']);
          $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/offer_submitted.maildoc'));
          $message = \Swift_Message::newInstance()
          ->setSubject("Une nouvelle offre proposée")
          ->setFrom($this->from['mail'])
          ->setTo($ad['email'])
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
          $this->get('mailer')->send($message);

          // remove concerned cache files
          $this->cacheManager->cleanDirCache($this->cacheManager->getBaseDir().$this->config['cache']['ads'].$adId.'/');
          // set flash session data to make this proposition as "added"
          $flashSess->setFlash('offerAdded', 1);

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          if($this->isTest)
          {
            return new Response('added_successfully');
          }
          // It's OK, redirect to ad page and show here the message
          $helper = new FrontendHelper;
          return $this->redirect($this->generateUrl('adsShowOne', array('id' => $adId, 'url' => $helper->makeUrl($ad['adName']), 'category' => $helper->makeUrl($ad['categoryName']))));
        }
        catch(Exception $e)
        {
          $this->enMan->getConnection()->rollback();
          $this->enMan->close();
          throw $e;
        }
      }
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Propose buy a product. Shows and handle the proposition's form.
   * @access public
   * @return Displayed template.
   */
  public function proposeBuyAction(Request $request)
  {
    $partial = 0;
    if(isset($_GET['partial']))
    {
      $partial = (int)$_GET['partial'];
    }
    $id = (int)$request->attributes->get('offer');
    $page = (int)$request->attributes->get('page', 1);
    $attrs = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 1 && $testResult == 0)
    {
      $attrs = array('id' => (int)$request->attributes->get('elUser1'));
      $id  = (int)$request->attributes->get('id');
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attrs = array('id' => (int)$request->attributes->get('user'));
      $id  = (int)$request->attributes->get('id');
    }
    $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($id);
    if(isset($offer['id_of']) && $offer['id_us'] != $attrs['id'])
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $validator = new Csrf;
      $validator->sessionToken = $this->sessionTicket;
      $validatorCsrf = new CsrfValidator;
      $validCSRF = $validatorCsrf->isValid($request->request->get('ticket'), $validator);
      if($request->getMethod() == 'POST' && $validCSRF === true) 
      {
        $ad = (int)$request->request->get('adChoosen');
        $adData = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdData($ad, (int)$attrs['id']);
        if(isset($adData['id_ad']) && $adData['id_ad'] == $ad  && $adData['adState'] == 1)
        {
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            $alreadyExists = $this->enMan->getRepository('AdItemsBundle:AdsOffersPropositions')->exists($id, $ad);
            if(!$alreadyExists)
            {
              $helper = new FrontendHelper;
              $destUser = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', $offer['id_us']);
              $author = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$attrs['id']);

              // Save proposition into database
              $aopEnt = new AdsOffersPropositions();
              $aopEnt->setAdsIdAd($this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $ad));
              $aopEnt->setOffersIdOf($this->enMan->getReference('Catalogue\OffersBundle\Entity\Offers', $id));
              $aopEnt->setUsersIdUs($destUser);
              $aopEnt->setPropositionDate('');
              $this->enMan->persist($aopEnt);
              $this->enMan->flush();

              // Gets private message template and mail template
              $templatePm = file_get_contents(rootDir.'messages/buy_proposition.message');
              $templateMail = file_get_contents(rootDir.'mails/buy_proposition.maildoc');
              $vars = array('{URL}', '{AD_URL}', '{AD_NAME}', '{LOGIN}');
              $values = array($this->generateUrl('offerPropositions'), $this->generateUrl('adsShowOne', array('category' => $helper->makeUrl($adData['categoryName']), 'url' => $helper->makeUrl($adData['adName']),'id'=> $ad)), $ad['adName'], $this->user->getUser());
              $templatePm = str_replace($vars, $values, $templatePm);
              $templateMail = str_replace($vars, $values, $templateMail);

              // Insert private message
              $message = array(
                'title' => "Une proposition d'achat pour votre offre",
                'content' => $templatePm,
                'type' => 2,
                'state' => 1
              );
              $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($author, $destUser, $message);

              // Send e-mail message
              $emtEnt = new EmailsTemplates;
              $message = \Swift_Message::newInstance()
              ->setSubject("Annonce ajoutée")
              ->setFrom($this->from['mail'])
              ->setTo($offer['email'])
              ->setContentType("text/html")
              ->setBody($emtEnt->getHeaderTemplate().$templateMail.$emtEnt->getFooterTemplate());
              $this->get('mailer')->send($message); 

              // commit SQL transaction
              $this->enMan->getConnection()->commit();
              if($this->isTest)
              {
                return new Response('proposed_successfully');
              }
              $flashSess->setFlash('offerProposed', 1);

              // It's OK, redirect to ad page and show here the message
              return $this->redirect($this->generateUrl('offerShow', array('catalogue' => $helper->makeUrl($offer['catalogueName']), 'catalogueId' => $offer['id_cat'], 'offer' => $helper->makeUrl($offer['offerName']), 'offerId' => $id)));
            }
            else
            {
              // This offer is already proposed to this ad
              return $this->render('CatalogueOffersBundle:Offers:proposeBuy.html.php', array('error' => 1, 'id' => $id,
              'ticket' => $this->sessionTicket, 'partial' => $partial));
            }
          }
          catch(Exception $e)
          {
            $this->enMan->getConnection()->rollback();
            $this->enMan->close();
            throw $e;
          }
        }
        elseif(!$validCSRF)
        {
          // CSRF ticket error
          $flashSess->setFlash('csrfError', 1);
          return $this->redirect($this->generateUrl('offerProposeBuy', array('offer' => $id, 'page' => $page)));
        }
        return $this->redirect($this->generateUrl('badElement'));
      }
      else
      {
        $adsEnt = new Ads;
        $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdsListByUser(array(
          'maxResults' => $this->config['pager']['perPage'], 'strict' => true,
          'start' => $this->config['pager']['perPage']*($page-1)
        ), (int)$attrs['id']);
	    $pager = new Pager(array('before' => $this->config['pager']['before'],
          'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('AdItemsBundle:Ads')->countAdsByUserAndState($attrs['id'], $adsEnt->getActiveState()),
          'page' => $page, 'perPage' => $this->config['pager']['perPage']
        ));
        return $this->render('CatalogueOffersBundle:Offers:proposeBuy.html.php', array('offer' => $offer, 
        'ticket' => $this->sessionTicket, 'ads' => $ads, 'pager' => $pager->setPages(), 'error' => 0, 'id' => $id, 'csrfError' => (int)$flashSess->getFlash('csrfError'),
        'partial' => $partial));
      }
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Lists X recent offers for RSS feed.
   * @return Displayed template
   */
  public function showOffersRssAction(Request $request)
  {
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getLastOffers($this->config['rss']['perPage']);
    return $this->render('CatalogueOffersBundle:Offers:showOffersRss.html.php', array('offers' => $offers, 'rss' => array('title' => "Les dernières offres",
    'link' => "#", 'description' => "Retrouvez les dernières offres")));
  }

  /**
   * Gets all offers.
   * @access public
   * @return Displayed template.
   */
  public function listAllOffersAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $cacheName = $this->config['cache']['offers'].'/all/page_'.$page.'_'.$how.'_'.$column;
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getAllOffers(array('cacheName' => $cacheName, 'date' => $this->config['sql']['dateFormat'], 'how' => $how, 'column' => $column, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
	  	  	'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('offa'),
	  	  	'page' => $page, 'perPage' => $this->config['pager']['perPage']
	        ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('CatalogueOffersBundle:Offers:offersTable.html.php', array('offers' => $offers, 'page' => $page, 'pager' => $pager->setPages(),
      'routeName' => 'offersAll', 'class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'catalogue', 'prix', 'date')), 'how' => $how, 'column' => $column));
    }
    return $this->render('CatalogueOffersBundle:Offers:listAllOffers.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'page' => $page,
    'class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'catalogue', 'prix', 'date')), 'how' => $how, 'column' => $column));
  }

  /**
   * Checks if offer corresponds to ad criteria.
   * @access public
   * @return Displayed template.
   */
  public function checkAjaxAdOfferAction(Request $request)
  {
    $idOffer = (int)$request->attributes->get('offer');
    $idAd = (int)$request->request->get('ad');
    $attr = $this->user->getAttributes();
    $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($idOffer);
    $ad = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdData($idAd, (int)$attr['id']);
    // Check if ad belongs to logged user and that offer doesn't belong to him
    if(isset($ad['id_ad']) && $ad['id_us'] == $attr['id'] && $ad['adState'] == 1 && isset($offer['id_of']) && $offer['id_us'] != $attr['id'])
    {
      // check correspondence between ad and offer with ProposeValidators
      $message = "L'offre est conforme aux critères déterminés dans l'enchère";
      $result = 1;
      $adsEnt = new Ads();
      $geoQuery = $adsEnt->getGeoToQuery($ad['adSellerGeo'], array('city' => $ad['id_ci'], 'cityName' => $ad['cityName'], 'offerCity' => $offer['cityName'],
      'region' => $ad['id_re'], 'regionName' => $ad['regionName'], 'offerRegion' => $offer['regionName'],
      'country' => $ad['id_co'], 'countryName' => $ad['countryName'], 'offerCountry' => $offer['countryName']));
      foreach($geoQuery as $gq => $geoQ)
      {
        $geoCondition = $geoQ;
        $key = explode('.', $gq);
        $geoOffer = $offer[$key[1]];
      }
      $values = array('opinion' => $attr['average'], 'seller' => $attr['type'],
      'geo' => $geoOffer, 'price' => $offer['offerPrice'], 'object' => $offer['offerObjetState']);
      $criteria = array('opinion' => $ad['adMinOpinion'], 'seller' => $ad['adSellerType'],
      'geo' => $geoCondition, 'price' => array(/*'from' => $ad['adBuyFrom'],*/ 'to' => $ad['adBuyTo']), 
      'object' => $ad['adObjetState']);
      $validClass = new ProposeValidators($criteria, $values);
      if(!$validClass->validate())
      {
        $result = 2;
        $message = "Certaines valeurs ne correspondent pas à celles déterminées par vous dans l'enchère : ".implode(',', $validClass->notValid);
      }    
      $return = array('message' => $message, 'result' => $result);
    }
    elseif($ad['adState'] != 1)
    {
      $return = array('message' => "L'enchère n'existe pas", 'result' => 0);
    }
    elseif(!isset($ad['id_ad']) || $ad['id_us'] != $attr['id'])
    {
      $return = array('message' => "Vous n'avez pas d'accès à cette enchère", 'result' => 0);
    }
    elseif(!isset($offer['id_of']) || $offer['id_us'] == $attr['id'])
    {
      $return = array('message' => "Vous n'avez pas d'accès à cette offre", 'result' => 0);
    }
    echo json_encode($return);
    die();
  }

  /**
   * Gets 6 offers for index.
   * @access public
   * @return HTML offers.
   */
  public function getAjaxForIndexAction(Request $request)
  {
    $page = (int)$request->attributes->get("page");
    $offers = $this->enMan->getRepository("CatalogueOffersBundle:Offers")->getForIndex(array("cacheName" => "index_".$page, "date" => $this->config['sql']['dateFormat'], "start" => (($page-1)*7), "maxResults" => 7));
    $next = $page+1;
    $previous = $page-1;
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
    $previousA = "";
    $previousSpan = "hidden";
    if($previous < 1)
    {
      $previousA = "hidden";
      $previousSpan = "";
    }
    $classes = array();
    $classes["ads"] = array("", "hidden", "hidden", "hidden", "hidden", "hidden", "hidden", "hidden");
    $classes["offers"] = array("", "", "", "topLine", "topLine", "topLine");
    $classes["navigation"] = array("nextA" => $nextA, "previousA" => $previousA, "nextSpan" => $nextSpan, "previousSpan" => $previousSpan);
    return $this->render("FrontendFrontBundle:Frontend:offers.html.php", array("offers" => $offers, "classes" => $classes,
    "page" => $page, "dir" => $this->config['view']['dirs']['offersImg'], "previous" => ($page-1), "next" => $next));
  }
  /**
   * Gets the best ads list.
   * @access public
   * @return Displayed template.
   */
  public function bestOfAction(Request $request)
  {
    $offers = $this->enMan->getRepository("CatalogueOffersBundle:Offers")->getBestOffers(array("cacheName" => "", "date" => $this->config['sql']['dateFormat']));
    return $this->render("CatalogueOffersBundle:Offers:bestOf.html.php", array("offers" => $offers));
  }

}