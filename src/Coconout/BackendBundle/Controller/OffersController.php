<?php
namespace Coconout\BackendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Coconout\BackendBundle\Controller\BackendController;
use Others\Pager; 
use Ad\ItemsBundle\Form\AddAd;
use Ad\ItemsBundle\Entity\Ads;
use Order\OrdersBundle\Entity\Tax;
use Frontend\FrontBundle\Helper\FrontendHelper;
use Catalogue\OffersBundle\Form\AddOffer;
use Catalogue\OffersBundle\Entity\Offers;

class OffersController extends BackendController
{

  /**
   * List offers.
   * @access public
   * @return Displayed template.
   */
  public function listAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $flashSess = $request->getSession();
    $offers = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getAllOffers(array('maxResults' => $this->config['pager']['perPage'], 'start' => $this->config['pager']['perPage']*($page-1)));
    $pager = new Pager(array('before' => $this->config['pager']['before'],
            'after' => $this->config['pager']['after'], 'all' => $this->enMan->getRepository('FrontendFrontBundle:Stats')->getStats('offa'),
            'page' => $page, 'perPage' => $this->config['pager']['perPage']
            ));
    return $this->render('CoconoutBackendBundle:Offers:list.html.php', array('offers' => $offers, 'pager' => $pager->setPages(), 'aorSuccess' => (int)$flashSess->getFlash('AORSuccess'), 'deleteSuccess' => (int)$flashSess->getFlash('deleteSuccess'),
    'ticket' => $this->sessionTicket)); 
  }

  /**
   * Edit one offer.
   * @access public
   * @return Displayed template.
   */
  public function editAction(Request $request)
  {
    require_once($this->cacheManager->getBaseDir().'lists.php');
    $id = (int)$request->attributes->get('id');
    $flashSess = $request->getSession();
    $postData = $flashSess->getFlash('formData');
    $offEnt = new Offers;
    $offEnt->setEditedData($this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($id));
    $offEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
    $offEnt->categoriesList = $this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false);
    if($offEnt->getIdOf() != '')
    {
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
      $offEnt->setCategoriesList($this->enMan->getRepository('CategoryCategoriesBundle:Categories')->getCategories(false));
      $offerImages = $this->enMan->getRepository('CatalogueImagesBundle:OffersImages')->getImagesByOffer($id);
      $canAddImages = (bool)(count($offerImages) < $this->config['images']['configuration']['offer']['maxImages']);
      $data = $request->request->all('AddOffer');
      // form fields list => form submitted
      if(isset($data['AddOffer']))
      {
        $offEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($data['AddOffer']['offerCountry']));
        $offEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$data['AddOffer']['offerCategory']));
      }
      elseif($offEnt->getOfferCategory() != '')
      {
        $offEnt->setCitiesList($this->enMan->getRepository('GeographyCitiesBundle:Cities')->getCities($offEnt->getOfferCountry()));
        $offEnt->setFormFields($this->enMan->getRepository('CategoryCategoriesBundle:FormFieldsCategories')->getFields((int)$offEnt->getOfferCategory()));
      }
      $offEnt->setCataloguesList($this->enMan->getRepository('CatalogueOffersBundle:Catalogues')->getCataloguesByUser(array(
      'maxResults' => $this->config['pager']['maxResults'],
      'start' => 0
      ), $offEnt->getOfferAuthor()));
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
            $data = $request->request->all('AddOffer');
            // refactoring : edit offer with editOffer() method
            $this->enMan->getRepository('CatalogueOffersBundle:Offers')->editOffer($id, array(
              'offer' => array('new' => array('offerCategory' => $data['AddOffer']['offerCategory'], 'offerCatalogue' => $data['AddOffer']['offerCatalogue'], 'offerCity' => $data['AddOffer']['offerCity'], 'offerPrice' => $data['AddOffer']['offerPrice'],
                'offerObjetState' => $data['AddOffer']['offerObjetState'], 'offerName' => $data['AddOffer']['offerName'], 'offerText' => $data['AddOffer']['offerText']), 
                'old' => array('category' => $dbOfferCategory, 'city' => $dbOfferCity, 'region' => '', 'country' => '', 'catalogue' => $dbOfferCatalogue)),
              'formField' => array('new' => $offEnt->getFormFields(), 'old' => $this->enMan->getRepository('CatalogueOffersBundle:OffersFormFields')->getFieldsByOffer($id, $dbOfferCategory)),
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
      return $this->render('CoconoutBackendBundle:Offers:edit.html.php', array('form' => $formEdit->createView(),
      'formErrors' => (array)$flashSess->getFlash('formErrors'), 'offerId' => $id, 'canAddImages' => $canAddImages,
      'formFields' => $offEnt->getFormFields(), 'fees' => $fees, 'zones' => $zones, 'offerImages' => $offerImages,
      'isSuccess' => (int)$flashSess->getFlash('editSuccess'), 'dir' => $this->config['view']['dirs']['offersImg'].$id.'/'));
    }
  }

  /**
   * Delete offer.
   * @access public
   * @return Displayed template.
   */
  public function deleteAction(Request $request)
  {
    if($this->validateCSRF())
    {
      $id = (int)$request->attributes->get('id');
      $flashSess = $request->getSession();
      // get all informations about this offer
      $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($id);
      // start SQL transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        $this->enMan->getRepository('CatalogueOffersBundle:Offers')->deleteOffer($id, $offer, array('title' => $this->config['deleted']['offerDeleted'],
        'prefix' => $this->config['images']['configuration']['offer']['prefix'], 'offersDir' => $this->config['images']['offersDir'],
        'mailer' => $this->get('mailer'), 'from' => $this->from['mail']));
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
      return $this->redirect($this->generateUrl('offersList'));
    }
  }

}