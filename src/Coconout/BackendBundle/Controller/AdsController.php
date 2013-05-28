<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager; 
use Ad\ItemsBundle\Form\AddAd;
use Ad\ItemsBundle\Entity\Ads;
use Order\OrdersBundle\Entity\Tax;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Frontend\FrontBundle\Entity\EmailsTemplates;

class AdsController extends BackendController
{

  /**
   * List ads.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $adsEnt = new Ads();
    $flashSess = $request->getSession();
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAllAdsBackend(array('type' => 'all', 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('adsa'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Ads:list.html.php', array('ads' => $ads, 'pager' => $pager->setPages(), 'aorSuccess' => (int)$flashSess->getFlash('AORSuccess'),
    'deleteSuccess' => (int)$flashSess->getFlash('deleteSuccess'), 'deletedState' => $adsEnt->getDeletedState(),
    'notAcceptedState' => $adsEnt->getNotAcceptedState(), 'ticket' => $this->sessionTicket)); 
  }

  /**
   * List only new ads.
   * @access public
   * @return Displayed template.
   */
  public function listNewAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $adsEnt = new Ads();
    $flashSess = $request->getSession();
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAllAdsBackend(array('type' => 'new', 'maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('adsn'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Ads:list.html.php', array('ads' => $ads, 'pager' => $pager->setPages(), 'aorSuccess' => (int)$flashSess->getFlash('AORSuccess'),
    'deleteSuccess' => (int)$flashSess->getFlash('deleteSuccess'), 'deletedState' => $adsEnt->getDeletedState(),
    'notAcceptedState' => $adsEnt->getNotAcceptedState(), 'ticket' => $this->sessionTicket));    
  }

  /**
   * List only new ads.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    require_once($this->cacheManager->getBaseDir().'lists.php');
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $adsEnt = new Ads;
    $adsEnt->setEditedData($this->enMan->getRepository('AdItemsBundle:Ads')->getOneAd($id, false));
    if($adsEnt->getIdAd() != '')
    {
      $dbAdCategory = $adsEnt->getAdCategory();
      $dbAdCity = $adsEnt->getAdCity();
      $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
      if(count($flashData = $flashSess->getFlash('formData')) > 0)
      {
        $adsEnt->setDataAdded($flashData['AddAd']);
        $adsEnt->setAdTax(Tax::getTaxByValue((float)$data['adTax']));
      }
      else
      {
        $adsEnt->setFormFieldsData($this->enMan->getRepository('AdItemsBundle:AdsFormFields')->getFieldsByAd($id, $dbAdCategory));
        $adsEnt->setPaymentMethods($this->enMan->getRepository('AdItemsBundle:AdsPayments')->getPaymentsByAd($id));
        $adsEnt->setAdTax(Tax::getTaxByValue((float)$adsEnt->getAdTax()));
      }
      // $adsEnt->setCategoriesList($this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(true));
      $adsEnt->setCategoriesList($categories);
      $adsEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($adsEnt->getAdCountry()));
      $data = $request->request->all('AddAd');
      // form fields list => form submitted
      if(isset($data['AddAd']))
      {
        $adsEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddAd']['adCategory']));
        $adsEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities((int)$data['AddAd']['adCountry']));
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
      return $this->render('CoconoutBackendBundle:Ads:edit.html.php', array('edit' => true, 'add' => false, 'form' => $formEdit->createView(),
      'formErrors' => (array)$flashSess->getFlash('formErrors'), 'adId' => $id,
      'formFields' => $adsEnt->getFormFields(),
      'isSuccess' => (int)$flashSess->getFlash('editSuccess')));
    }
  }

  /**
   * List only new ads.
   * @access public
   * @return Displayed template.
   */
  public function acceptOrDenyAction(Request $request)
  {
    $id = (int)$request->attributes->get('id');
    $actionName = $request->attributes->get('actionName');
    $flashSess = $request->getSession();
    $adData = $this->enMan->getRepository('AdItemsBundle:Ads')->getOneAd($id, false);
    if($this->validateCSRF() === true && isset($adData['id_ad']) && $id == $adData['id_ad'] && $adData['adState'] == 0)
    {
      $actionTemplate = 'ad_accept';
      if($actionName == 'deny')
      {
        $actionTemplate = 'ad_deny';
      }
      if($request->getMethod() == 'POST') 
      {
        // start SQL transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          if($actionName == 'accept')
          {
            // increment all counters
            $this->enMan->getRepository('AdItemsBundle:Ads')->acceptAd($id, array('city' => $adData['id_ci'], 'category' => $adData['id_ca'])); 
            // remove concerned cache files
            $this->cacheManager->cleanDirCache('ads/all/');
            $this->cacheManager->cleanDirCache('categories/'.$adData['id_ca'].'/ads/');
            $this->cacheManager->cleanDirCache('cities/'.$adData['id_ci'].'/ads/');
            $this->cacheManager->cleanDirCache('regions/'.$adData['id_re'].'/ads/');
            $tags = $this->enMan->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($id);
            foreach($tags as $t => $tag) 
            {
              $this->cacheManager->cleanDirCache('tags/'.$tag['id_ta'].'/ads/');
            }
            $titleEmail = "Votre annonce ".$adData['adName']." vient d'être activée";
            $result = 1;
            if((int)$request->request->get('atHomePage') == 1)
            {
              // ad will be displayed at home page : removes cache files
              $this->cacheManager->delete($this->cacheManager->getBaseDir()."/index_ads".$this->cacheManager->getExtension());
            }
          }
          elseif($actionName == 'deny')
          {
            // decrement all countes
            $this->enMan->getRepository('AdItemsBundle:Ads')->denyAd($id);
            $titleEmail = "Votre annonce ".$adData['adName']." est réfusée";
            $result = 2;
          }
          // send e-mail to ad's author (if body is empty, take default body text)
          $bodyEmail = $request->request->get('body');
          if(trim($bodyEmail) == "")
          {
            $bodyEmail = file_get_contents(rootDir.'mails/'.$actionTemplate.'.maildoc');
          }
          // avoid backend.php in the URL, set app prefix
          $emtEnt = new EmailsTemplates;
          $this->setBaseUrl($this->getRouteUrl()); 
          $helper = new FrontendHelper;
          $tplVals = array('{AD_TITLE}', '{AD_URL}');
          $realVals = array($adData['adName'], $this->generateUrl('adsShowOne', array('category' => $adData['categoryUrl'], 'url' => $helper->makeUrl($adData['adName']), 'id' => $id), true));
          $templateMail = str_replace($tplVals, $realVals, $bodyEmail);
          $message = \Swift_Message::newInstance()
          ->setSubject($titleEmail)
          ->setFrom($this->from['mail'])
          ->setTo($adData['email'])
          ->setContentType("text/html")
          ->setBody($emtEnt->getHeaderTemplate().$templateMail.$emtEnt->getFooterTemplate());
          $this->get('mailer')->send($message);
          // return to coconout.php in the URL
          $this->setBaseUrl($this->getBackendUrl());
          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('AORSuccess', (int)$result);
          return $this->redirect($this->generateUrl('adsList'));
        }
        catch(Exception $e)
        {
          $this->enMan->getConnection()->rollback();
          $this->enMan->close();
          throw $e;
        }
      }
      return $this->render('CoconoutBackendBundle:Ads:acceptOrDeny.html.php', array('formErrors' => (array)$flashSess->getFlash('formErrors'), 'adId' => $id,
      'isHome' => $this->enMan->getRepository('AdItemsBundle:AdsHomepage')->isForHomepage($id), 'template' => file_get_contents(rootDir.'mails/'.$actionTemplate.'.maildoc'), 'actionName' => $actionName, 'aorSuccess' => (int)$flashSess->getFlash('AORSuccess'), 'ticket' => $this->sessionTicket));
    }
  }

  /**
   * Delete ad.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    if($this->validateCSRF() === false)
    {
      return false;
    }
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    // get all informations about this offer
    $cacheName = $this->config['cache']['ads'].$id.'/add_offers';
    $ad = $this->enMan->getRepository('AdItemsBundle:Ads')->getOneAd($id);
    // start SQL transaction
    $this->enMan->getConnection()->beginTransaction();
    try
    {
      $this->enMan->getRepository('AdItemsBundle:Ads')->deleteAd($id, $ad, array('title' => $this->config['deleted']['adDeleted'],
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
    if((int)$request->request->get('json') == 1)
    {
      echo json_encode(array('success' => 1));
      die();
    }
    return $this->redirect($this->generateUrl('adsList'));
  }

}