<?php
namespace Ad\ItemsBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Cookie;
use Frontend\FrontBundle\Controller\FrontController;
use Ad\ItemsBundle\Form\AddAd;
use Ad\ItemsBundle\Entity\Ads;
use Ad\ItemsBundle\Entity\AdsFormFields;
use Ad\ItemsBundle\Entity\AdsUsers;
use Ad\ItemsBundle\Entity\AdsPayments;
use Ad\ItemsBundle\Entity\AdsHomepage;
use Ad\ItemsBundle\Entity\AdsTags;
use Ad\ItemsBundle\Helper\ItemsHelper;
use Category\CategoriesBundle\Entity\FormFields;
use Frontend\FrontBundle\Entity\Tags;
use Others\Pager;
use Others\Tools;
use User\ProfilesBundle\Entity\Users; 
use Order\OrdersBundle\Entity\Orders;
use Order\OrdersBundle\Entity\OrdersComments;
use Order\OrdersBundle\Entity\Delivery;
use Order\OrdersBundle\Form\OrderFirstStep;
use User\AddressesBundle\Entity\UsersAddresses;
use User\AddressesBundle\Form\FirstStep;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Order\OrdersBundle\Entity\Tax;
use Geography\CountriesBundle\Entity\Countries;
use Ad\ItemsBundle\AdNotFoundException;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates; 

class ItemsController extends FrontController
{

  /**
   * Add ad item action. 
   * @return Displayed template.
   */
  public function addAdAction(Request $request)
  {
    if($this->isTest)
    {
      $from = array('src/Ad/ItemsBundle/Controller', 'src\Ad\ItemsBundle\Controller');
      $to = array('', '');
      $dir = str_replace($from, $to, dirname(__FILE__));
      require($dir.'/cache/lists.php');
    }
    else
    {
      require_once($this->cacheManager->getBaseDir().'lists.php');
    }
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $adsEnt = new Ads();
    // prepares cache for geography elements (cities, regions, countries)
    $conEnt = new Countries();
    $conEnt->setSource($geography);
    // FROM DB : $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
    $adsEnt->setCountriesList($conEnt->getCountries());
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $adsEnt->setDataAdded($flashData['AddAd']);
    }
    // FROM DB : $adsEnt->setCategoriesList($this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false));
    $adsEnt->setCategoriesList($categories);
    // FROM DB : $adsEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($adsEnt->getAdCountry()));
    $adsEnt->setCitiesList($conEnt->getCitiesByCountry($adsEnt->getAdCountry()));
    $data = $request->request->all('AddAd');
    // form fields list
    if(isset($data['AddAd']))
    {
      $adsEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddAd']['adCategory']));
      $adsEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities((int)$data['AddAd']['adCountry']));
    }
    elseif($adsEnt->getAdCategory() != '')
    {
      $adsEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$adsEnt->getAdCategory()));
    }
    // cities list
    if(!isset($data['AddAd']) && $adsEnt->getAdCountry() != '')
    {
      $adsEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities((int)$adsEnt->getAdCountry()));
    }
    Ads::setSessionToken($this->sessionTicket);
    $adsEnt->setTicket($this->sessionTicket);
    $formAdd = $this->createForm(new AddAd(), $adsEnt);
    $formAdd->setData($adsEnt);
    if($request->getMethod() == 'POST') 
    {  
      $formAdd->bindRequest($request);
      if($formAdd->isValid())
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          $attr = $this->user->getAttributes();
          $data = $request->request->all('AddAd');
          $tags = array();
          for($i = 1; $i < 11; $i++)
          {
// TODO : s'assurer que cela passe par SQL filter
            if($data['AddAd']['tag'.$i] != '' && !in_array($data['AddAd']['tag'.$i], $tags))
            {
              $tags['tag'.$i] = $data['AddAd']['tag'.$i];
            }
          }
          // add new ad
          $this->enMan->getRepository('AdItemsBundle:Ads')->addAd(
            array('user' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users',(int)$attr['id']),
              'category' => $this->enMan->getReference('Category\CategoriesBundle\Entity\Categories', (int)$adsEnt->getAdCategory()),
              'city' => $this->enMan->getReference('Geography\CitiesBundle\Entity\Cities', (int)$adsEnt->getAdCity())
            ),
            array('ad' => array('name' => $adsEnt->getAdName(), 'text' => $adsEnt->getAdText(), 'minOpinion' => $adsEnt->getAdMinOpinion(), 'objetState' => $adsEnt->getAdObjetState(), 
              'seller' => $adsEnt->getAdSellerType(), 'length' => $adsEnt->getAdLength(), 'home' => $adsEnt->getAdAtHomePage(), /*'buyFrom' => $adsEnt->getAdBuyFrom(),*/ 'validity' => $adsEnt->getAdValidity(), 
              'buyTo' => $adsEnt->getAdBuyTo(), 'sellerGeo' => $adsEnt->getAdSellerGeo(), 'tax' => $data['AddAd']['adTax'], 'category' => $data['AddAd']['adCategory'], 'city' => $data['AddAd']['adCity']),
              'formFields' => $adsEnt->getFormFields(), 'others' => $data['AddAd'], 'tags' => $tags, 'payments' => $data['AddAd']['adPayments']
            )
          );
          // // get e-mail template, parse and send it
          $emtEnt = new EmailsTemplates;
          $tplVals = array('{AD_TITLE}');
          $realVals = array($adsEnt->getAdName());
          $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/ad_added.maildoc'));
          $message = \Swift_Message::newInstance()
          ->setSubject("Annonce ajoutée")
          ->setFrom($this->from['mail'])
          ->setTo($attr['email'])
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$template.$emtEnt->getFooterTemplate());
          $this->get('mailer')->send($message);
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
        $flashSess->setFlash('formData', $data);
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($formAdd));
      } 
      return $this->redirect($this->generateUrl('adsAdd'));
    }
    return $this->render('AdItemsBundle:Items:add.html.php', array('add' => true, 'edit' => false, 'form' => $formAdd->createView(),
    'formErrors' => (array)$flashSess->getFlash('formErrors'), 'formFields' => $adsEnt->getFormFields(),
    'isSuccess' => (int)$flashSess->getFlash('addSuccess'), 'adId' => 0, 'titleAction' => "Ajouter une annonce"));
  }

  /**
   * Edits ad.
   * @return Displayed template
   */
  public function editAdAction(Request $request)
  {
    require_once($this->cacheManager->getBaseDir().'lists.php');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 0)
    {
      $attr = $this->user->getAttributes();
      $id = (int)$request->attributes->get('id');
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $attr = array('id' => (int)$request->attributes->get('user'));
      $id = (int)$request->attributes->get('id');
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $attr = array('id' => (int)$request->attributes->get('elUser1'));
      $id = (int)$request->attributes->get('id');
    }
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $adsEnt = new Ads;
    $adsEnt->setEditedData($this->enMan->getRepository('AdItemsBundle:Ads')->getAdData($id, (int)$attr['id']));
    if($adsEnt->getIdAd() != '')
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $dbAdCategory = $adsEnt->getAdCategory();
      $dbAdCity = $adsEnt->getAdCity();
      // prepares cache for geography elements (cities, regions, countries)
      $conEnt = new Countries();
      $conEnt->setSource($geography);
      // FROM DB $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
      $adsEnt->setCountriesList($conEnt->getCountries());
      if(count($flashData = $flashSess->getFlash('formData')) > 0)
      {
        $adsEnt->setDataAdded($flashData['AddAd']);
        $adsEnt->setAdTax(Tax::getTaxByValue((float)$flashData['AddAd']['adTax']));
      }
      else
      {
        $adsEnt->setFormFieldsData($this->enMan->getRepository('AdItemsBundle:AdsFormFields')->getFieldsByAd($id, $dbAdCategory));
        $adsEnt->setPaymentMethods($this->enMan->getRepository('AdItemsBundle:AdsPayments')->getPaymentsByAd($id));
        $adsEnt->setAdTax(Tax::getTaxByValue((float)$adsEnt->getAdTax()));
      }
      // FROM DB : $adsEnt->setCategoriesList($this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false));
      $adsEnt->setCategoriesList($categories);
      // FROM DB : $adsEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($adsEnt->getAdCountry()));
      $adsEnt->setCitiesList($conEnt->getCitiesByCountry($adsEnt->getAdCountry()));
      $data = $request->request->all('AddAd');
      // form fields list => form submitted
      if(isset($data['AddAd']))
      {
        $adsEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddAd']['adCategory']));
        $adsEnt->setCitiesList($conEnt->getCitiesByCountry((int)$data['AddAd']['adCountry']));
      }
      elseif($adsEnt->getAdCategory() != '')
      {
        $adsEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$adsEnt->getAdCategory()));
      }
      Ads::setSessionToken($this->sessionTicket);
      $adsEnt->setTicket($this->sessionTicket);
      $formEdit = $this->createForm(new AddAd(), $adsEnt);
      // $formEdit->setData($adsEnt);
      if($request->getMethod() == 'POST') 
      {
        $formEdit->bindRequest($request); 
        if($formEdit->isValid())
        {
          // start SQL transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            $this->enMan->getRepository('AdItemsBundle:Ads')->editAd($id, 
              array(
                'ad' => array(
                  'new' => array('category' => $data['AddAd']['adCategory'], 'city' => $data['AddAd']['adCity'],
                    'minOpinion' => $data['AddAd']['adMinOpinion'], 'objetState' => $data['AddAd']['adObjetState'], 
                    'sellerType' => $data['AddAd']['adSellerType'], /*'buyFrom' => $data['AddAd']['adBuyFrom'],*/ 'buyTo' => $data['AddAd']['adBuyTo'],
                    'sellerGeo' => $data['AddAd']['adSellerGeo'], 'tax' => $data['AddAd']['adTax'], 'name' => $data['AddAd']['adName'], 'text' => $data['AddAd']['adText']
                  ),
                  'old' => array('category' => $dbAdCategory, 'city' => $dbAdCity)
                ),
                'formFields' => array('new' => $adsEnt->getFormFields()),
                'others' => $data['AddAd'], 'payments' => $data['AddAd']['adPayments']
              )
            );
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
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('formErrors', $this->getAllFormErrors($formEdit));
        } 
        return $this->redirect($this->generateUrl('adsEdit', array('id' => $id)));
      }
      return $this->render('AdItemsBundle:Items:add.html.php', array('edit' => true, 'add' => false, 'form' => $formEdit->createView(),
      'formErrors' => (array)$flashSess->getFlash('formErrors'), 'adId' => $id,
      'formFields' => $adsEnt->getFormFields(), 'titleAction' => "Editer une annonce",
      'isSuccess' => (int)$flashSess->getFlash('editSuccess')));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Deletes ad.
   * @return Displayed template
   */
  public function deleteAdAction(Request $request)
  {
    $attr = $this->user->getAttributes();
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
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
    $flashSess = $request->getSession();
    $adData = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdData($id, (int)$attr['id']);
    if($validCSRF === true && isset($adData['id_ad']) && $adData['id_ad'] == $id)
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
        $cacheName = $this->config['cache']['ads'].$id.'/add_offers';
        $this->enMan->getRepository('AdItemsBundle:Ads')->deleteAd($id, $adData, array('title' => $this->config['deleted']['adDeleted'],
        'dateFormat' => $this->config['sql']['dateFormat'], 'cacheName' => $cacheName));
        $flashSess->setFlash('deleteSuccess', 1); 
        // commit SQL transaction
        $this->enMan->getConnection()->commit();
      }
      catch(Exception $e)
      {
        $flashSess->setFlash('deleteSuccess', 0);
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
      return $this->redirect($this->generateUrl('adsMyList'));
    }
    if($this->isTest && !isset($adData['id_ad']))
    {
      return new Response('ad_not_deleted');
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * End user's ad.
   * @access public
   * @return Displayed template
   */
  public function endAdAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 0)
    {
      $userAttr = $this->user->getAttributes();
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
    }
    $flashSess = $request->getSession();
    // check if ad belongs to connected user and if it didn't expire
    $adData = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdData($id, (int)$userAttr['id']);
    if(isset($adData['id_ad']) && $id == $adData['id_ad'] && $adData['adState'] == 1)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $adsEnt = new Ads;
      $useEnt = new Users;
      $ordEnt = new Orders;
      $offer = array();
      $formView = null;
      $formUrl = $this->generateUrl('adsEnd', array('id' => $id));
      $formType = 'adEnd';
      if($adData['adOffer'] == 0)
      {
        $hasOffer = false;
        $cacheName = $this->config['cache']['ads'].$id.'/add_offers';
        return $this->render('AdItemsBundle:Items:endAdNoOffers.html.php', array('ticket' => $this->sessionTicket, 'id' => $id, 'offers' => $this->enMan->getRepository('AdItemsBundle:AdsOffers')->getOffersByAd(array('cacheName' => $cacheName, 'date' => $this->config['sql']['dateFormat']), $id)));
      }
      else
      { 
        $hasOffer = true;
        // get informations about choosen offer
        $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer((int)$adData['adOffer']);
        $postData = $flashSess->getFlash('formData');
        $uasEnt = new UsersAddresses;
        UsersAddresses::$em = $this->enMan;
        UsersAddresses::$staticId = (int)$userAttr['id'];
        $ordEnt->setDeliveryTypes(Delivery::getDeliveryTypes());
        if(count($flashData = $flashSess->getFlash('formData')) > 0)
        {
          $uasEnt->setFirstStepData($flashData['FirstStep']);
          $ordEnt->setFirstStepData($flashData['OrderFirstStep']);
        }
        $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
        $uasEnt->setCountriesList($adsEnt->getCountriesList()); 
        $ordEnt->setPaymentTypes($adsEnt->getPayments());
        $ordEnt->setWho('buyer');
        $ordEnt->setOrderState(0);
        Orders::setSessionToken($this->sessionTicket);
        UsersAddresses::setSessionToken($this->sessionTicket);
        $uasEnt->setTicket($this->sessionTicket);
        $ordEnt->setTicket($this->sessionTicket);
        // create form which fill up initial data 
        $form2 = $this->createForm(new FirstStep(), $uasEnt);
        $form = $this->createForm(new OrderFirstStep(), $ordEnt);
        $formView = $form->createView();
        $formView2 = $form2->createView();
      }
      $data = $request->request->all('FirstStep');
      if($request->getMethod() == 'POST') 
      {
        $form->bindRequest($request);
        $form2->bindRequest($request);
        if($form->isValid() && $form2->isValid())
        {
          // start transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            $this->enMan->getRepository('OrderOrdersBundle:Orders')->initNewOrder($id, $data, 
              array('userId' => $userAttr['id'], 'sellerId' => $offer['id_us'], 'userLogin' => $this->user->getUser(), 'offerName' => $offer['offerName'], 'offerPrice' => $offer['offerPrice'],
                'email' => $offer['email'], 'from' => $this->from['mail'],  'insertBuyerAddress' => true, 
                'url' => $this->generateUrl('orderUpdateData', array('id' => $id)), 'mailer' => $this->get('mailer')
              ), 
              $ordEnt,
              $uasEnt 
            );

            // calculate new "our users have already economised XXX €" amount
            $helper = new ItemsHelper;
            $helper->makeAmountBlock($this->enMan->getRepository("AdItemsBundle:Ads")->countEconomized(), $this->container->get('templating.engine.php'));

            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            $flashSess->setFlash('initOrderSuccess', 1);
            return $this->redirect($this->generateUrl('orderUpdateData', array('id' => $id))/*$realVals[2]*/); // redirect to update order page
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
          $flashSess->setFlash('formErrors', array_merge($this->getAllFormErrors($form), 
          $this->getAllFormErrors($form2)));
        } 
        return $this->redirect($formUrl);
      }
      $adData["orderState"] = 0;
      return $this->render('AdItemsBundle:Items:endAd.html.php', array('orderPage' => false, 'hasOffer' => $hasOffer, 'offer' => $offer, 
      'orderStates' => $ordEnt->getOrderLabels(), 'ad' => $adData, 'states' => $adsEnt->getObjetStates(), 'userTypes' => $useEnt->getUserTypesAliases(), 'spanClass' => 'hidden',
      'formClass' => '', 'form' => $formView, 'formUrl' => $formUrl, 'formType' => $formType, 'page' => 'initOrder',
      'formErrors' => $flashSess->getFlash('formErrors', array()), 'form2' => $formView2, 'success' => (int)$flashSess->getFlash('initOrderSuccess'),
      'showForm' => true, 'role' => 'buyer', 'lastState' => false, 'commentsList' => null, 'showFormPay' => false, 'errorStates' => $ordEnt->getErrorOrderStates(true),
      'stepDescription' => $ordEnt->getStepDescription()));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement')); 
  }

  /**
   * Ends ad without choosen offer.
   * @access public
   * @return Displayed template.
   */
  public function endAdWithoutOffersAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 0)
    {
      $userAttr = $this->user->getAttributes();
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
    }
    $flashSess = $request->getSession();
    // check if ad belongs to connected user and if it didn't expire
    $adData = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdData($id, (int)$userAttr['id']);
    if(isset($adData['id_ad']) && $id == $adData['id_ad'] && $adData['adState'] == 1)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // end the ad (do not remove anything)
      $this->enMan->getRepository('AdItemsBundle:Ads')->endAd($ad, $adData, array('ads' => $this->config['cache']['ads'], 'mailer' => $this->get('mailer'), 'from' => $this->from['mail'], 'dateFormat' => $this->config['sql']['dateFormat']));

      return $this->render('AdItemsBundle:Items:endAdWithoutOffers.html.php', array('hasOffers' => (bool)(count($offers) > 0)));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->redirect($this->generateUrl('badElement')); 
  }

  /**
   * User's ads list.
   * @return Displayed template
   */
  public function listUserAdsAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $flashSess = $request->getSession();
    $userAttr = $this->user->getAttributes();
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')
    ->getAdsListByUser(array('column' => $column, 'how' => $how, 
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['ads'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $adsEnt = new Ads;
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdItemsBundle:Items:userAdsTable.html.php', array('ads' => $ads, 'pager' => $pager->setPages(),
      'states' => $adsEnt->states, 'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    return $this->render('AdItemsBundle:Items:listUserAds.html.php', array('ads' => $ads, 'pager' => $pager->setPages(),
    'class' => $helper->getClassBySorter($how), 'column' => $column, 'how' => $how, 'states' => $adsEnt->states, 'deleteSuccess' => (int)$flashSess->getFlash('deleteSuccess'), 'ticket' => $this->sessionTicket));
  }

  /**
   * Show ad.
   * + cache saving
   * @return Displayed template.
   */
  public function showAdAction(Request $request)
  {
    $this->ifFromNewsletter("ad");
    $id = (int)$request->attributes->get('id');
    $ad = $this->enMan->getRepository('AdItemsBundle:Ads')->getOneAd($id);
    if(isset($ad['id_ad']))
    {
      $flashSess = $request->getSession();
      // check if user can propose offer from his catalogue
      $catAction = false;
      $userId = 0;
      $connected = false;
      $isSubscribed = 2;
      if(parent::checkIfConnected())
      {
        $connected = true;
        $attrs = $this->user->getAttributes();
        $userId = (int)$attrs['id'];
        if($attrs['id'] != $ad['id_us'])
        {
          $catAction = true;
        }
        // check if user is already subscribed
        if($attrs['id'] > 0)
        {
          $isSubscribed = (int)$this->enMan->getRepository('UserAlertsBundle:UsersAdsAlerts')->alreadySubscribed((int)$attrs['id'], $id);
        }
      }
      $cacheName = $this->config['cache']['ads'].$id.'/add_offers';
      $offers = $this->enMan->getRepository('AdItemsBundle:AdsOffers')->getOffersByAd(array('cacheName' => $cacheName, 'date' => $this->config['sql']['dateFormat']), $id);
      $adsEnt = new Ads;
      $useEnt = new Users;
// TODO : make intelligent counter
      return $this->render('AdItemsBundle:Items:showAd.html.php', array('ad' => $ad, 'adsStates' => $adsEnt->getObjetStates(),
      'usersAvg' => $useEnt->getAveragesAliases(), 'userType' => $useEnt->getUserTypesAliases(), 'payLabels' => $adsEnt->getPayments(),
      'fields' => $this->enMan->getRepository('AdItemsBundle:AdsFormFields')->getFieldsByAd($id, $ad['id_ca']),
      'tags' => $this->enMan->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($id), 'offers' => $offers,
      'payments' => $this->enMan->getRepository('AdItemsBundle:AdsPayments')->getPaymentsByAd($id), 'connected' => $connected,
      'catAction' => $catAction, 'offerAdded' => (int)$flashSess->getFlash('offerAdded', -1), 'userId' => $userId,
      'url' => array('id' => $id, 'url' => $request->attributes->get('url'), 'category' => $request->attributes->get('category')), 
      'isSubscribed' => $isSubscribed, 'ticket' => $this->sessionTicket,
      'ads' => $this->enMan->getRepository('AdItemsBundle:Ads')->getByCategory(array('date' => $this->config['sql']['dateFormat'], 'how' => 'asc', 'column' => 'titre', 'maxResults' => 20, 'cacheName' => "", 'start' => 0), $ad['id_ca'])
      ));
    }
    throw new  AdNotFoundException("Ad $id was not found");
  }

  /**
   * Gets ads list by defined tags.
   * + cache saving
   * @return Displayed template.
   */
  public function getAdsByTagsAction(Request $request)
  {
    $tagGet = (int)$request->attributes->get('tag');
    $page = (int)$request->attributes->get('page');
    $tag = $this->enMan->getRepository('FrontendFrontBundle:Tags')->find($tagGet);
    if((int)$tag->getIdTa() > 0)
    {
      $isPartial = $this->checkIfPartial();
      $cacheName = $this->config['cache']['tags'].$tagGet.'/ads/page_'.$page;
      $ads = $this->enMan->getRepository('AdItemsBundle:AdsTags')->getAdsList(array('cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1),
      'date' => $this->config['sql']['dateFormat']), $tagGet);
	  $pager = new Pager(array('before' => $this->config['pager']['before'],
	  	  	   'after' => $this->config['pager']['after'], 'all' => $tag->getTagAds(),
	  	  	   'page' => $page, 'perPage' => $this->config['pager']['perPage']
	           ));
      $how = $request->attributes->get('how');
      $column = $request->attributes->get('column');
      $helper = new FrontendHelper;
      if($isPartial)
      {
        return $this->render('AdItemsBundle:Items:adsTable.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager->setPages(),
        'class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'ville', 'date')), 'how' => $how, 'column' => $column,
        "routeName" => 'adsByTags', 'routeParams' => array('how' => $how, 'column' => $column, 'url' => $request->attributes->get('url'), 'tag' => $tagGet)));
      }
      return $this->render('AdItemsBundle:Items:getAdsByTag.html.php', array('ads' => $ads, 'pager' => $pager->setPages(),
      "tag" => $tag->getTagName(), "page" => $page, 'params' => array('tag' => $tagGet, 'url' => $request->attributes->get('url')),
       "routeName" => 'adsByTags', 'routeParams' =>  array('how' => $how, 'column' => $column, 'url' => $request->attributes->get('url'), 'tag' => $tagGet), 'class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'ville', 'date')), 'how' => $how, 'column' => $column));
    }
    throw new Exception('Tag was not found');
  }

  /**
   * Lists X recent ads for RSS feed.
   * @access public
   * @return Displayed template
   */
  public function showAdsRssAction(Request $request)
  {
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getLastAds($this->config['rss']['perPage']);
    return $this->render('AdItemsBundle:Items:showAdsRss.html.php', array('ads' => $ads, 'rss' => array('title' => "Les dernières annonces",
    'link' => "#", 'description' => "Retrouvez les dernières annonces")));
  }

  /**
   * List all ads.
   * @access public
   * @return Displayed template.
   */
  public function listAllAdsAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $cacheName = $this->config['cache']['ads'].'/all/page_'.$page.'_'.$how.'_'.$column;
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAllAds(array('how' => $how, 'column' => $column, 'date' => $this->config['sql']['dateFormat'], 'cacheName' => $cacheName, 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
	  	  	'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('adsa'),
	  	  	'page' => $page, 'perPage' => $this->config['pager']['perPage']
	        ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('AdItemsBundle:Items:adsTable.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager->setPages(),
      'class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'ville', 'date')), 'how' => $how, 'column' => $column,
      "routeName" => 'adsAll', 'routeParams' => array('how' => $how, 'column' => $column)));
    }
    return $this->render('AdItemsBundle:Items:listAllAds.html.php', array('ads' => $ads, 'page' => $page, 'pager' => $pager->setPages(),
    'class' => $helper->getClassesBySorter($how, $column, array('titre', 'categorie', 'ville', 'date')), 'how' => $how, 'column' => $column,
    "routeName" => 'adsAll', 'routeParams' => array('how' => $how, 'column' => $column))); 
  }

  /**
   * Counts ads displays. We don't distinguish if not connected user uses the same PC as connected user. 
   * That means that we will add 2 visits for not connected and connected user  if both use the same PC to view this
   * $ad.
   * @access public
   * @return void
   */
  public function ajaxCounterAction(Request $request)
  {
    $ad = (int)$request->attributes->get('ad');
    $increment = false;
    if(parent::checkIfConnected())
    {
      // for connected user, operate with the database
      $userAttr = $this->user->getAttributes();
      if(!$this->enMan->getRepository('AdItemsBundle:AdsUsers')->alreadySeen($userAttr['id'], $ad))
      {
        $aduEnt = new AdsUsers;
        $aduEnt->setCounterAd($this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $ad));
        $aduEnt->setCounterUser($this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']));
        $aduEnt->setCounterDate(new \DateTime());
        $this->enMan->persist($aduEnt);
        $this->enMan->flush();
        $increment = true;
      }
    }
    else
    {
      // for not connected user, work with cookies
      if(!isset($_COOKIE['see_ad_'.$ad]))
      {
        $expire = time()+60*60*24*12000;
        setcookie('see_ad_'.$ad, 1, $expire, '/', '.'.$this->config['site']['domain'], false, true);
        $increment = true;
      }
    }
    if($increment)
    {
      // add new visit to counter
      $q = $this->enMan->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
      ->set('a.adVisits', 'a.adVisits + 1')
      ->where('a.id_ad = ?1')
      ->setParameter(1, $ad)
      ->getQuery()
      ->execute();
    }
    die();
  }
  
  /**
   * Gets the best ads list.
   * @access public
   * @return Displayed template.
   */
  public function bestOfAction(Request $request)
  {
    $ads = $this->enMan->getRepository("AdItemsBundle:Ads")->getBestAds(array("cacheName" => "", "date" => $this->config['sql']['dateFormat']));
    return $this->render("AdItemsBundle:Items:bestOf.html.php", array("ads" => $ads));
  }
  
}