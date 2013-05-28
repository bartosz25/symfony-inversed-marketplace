<?php
namespace User\AddressesBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Ad\ItemsBundle\Entity\Ads;
use User\AddressesBundle\Entity\UsersAddresses;
use User\AddressesBundle\Form\FirstStep;
use User\AddressesBundle\Form\AddAddress;
use Others\Pager;
use Frontend\FrontBundle\Helper\FrontendHelper;

class AddressesController extends FrontController
{

  /**
   * User's addresses list.
   * @return Displayed template
   */
  public function listUserAddressesAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isAjax = (int)$request->request->get('isAjax');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $addresses = $this->enMan->getRepository('UserAddressesBundle:UsersAddresses')
    ->getUserAddresses(array('column' => $column, 'how' => $how,
      'maxResults' => $this->config['pager']['perPage'],
      'start' => $this->config['pager']['perPage']*($page-1)
    ), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['addresses'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isAjax == 1)
    {
      return $this->render('UserAddressesBundle:Addresses:ajaxUserAddresses.html.php', array('addresses' => $addresses, 'pager' => $pager->setPages()));
    }
    elseif($isPartial)
    {
      return $this->render('UserAddressesBundle:Addresses:addressesTable.html.php', array('addresses' => $addresses, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    return $this->render('UserAddressesBundle:Addresses:listUserAddresses.html.php', array('addresses' => $addresses, 'pager' => $pager->setPages(),
    'ticket' => $this->sessionTicket, 'how' => $how, 'column' => $column, 'class' => $helper->getClassBySorter($how)));
  }

  /**
   * Adds new address.
   * @access public
   * @return Displayed template.
   */
  public function addAddressAction(Request $request)
  {
    $userAttr = $this->user->getAttributes();
    $flashSess = $request->getSession();
    $formUrl = $this->generateUrl('addressAd', array());
    $uasEnt = new UsersAddresses;
    if(count($flashData = $flashSess->getFlash('formData')) > 0)
    {
      $uasEnt->setFirstStepData($flashData['AddAddress']);
    }
    $adsEnt = new Ads;
    $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
    $uasEnt->setCountriesList($adsEnt->getCountriesList()); 
    UsersAddresses::setSessionToken($this->sessionTicket);
    UsersAddresses::$em = $this->enMan;
    $uasEnt->setTicket($this->sessionTicket);
    $form = $this->createForm(new AddAddress(), $uasEnt);
    $data = $request->request->all('AddAddress');
    if($request->getMethod() == 'POST') 
    {
      $form->bindRequest($request);
      if($form->isValid())
      {
        // start transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          // insert address data
          $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
          $uasEnt->setData(array('addressState' => 1, 'addressUser' => $userRef, 'addressCountry' => $this->enMan->getReference('Geography\CountriesBundle\Entity\Countries', (int)$data['AddAddress']['addressCountry']), 'addressFirstName' => $data['AddAddress']['addressFirstName'], 'addressLastName' => $data['AddAddress']['addressLastName']
          , 'addressPostalCode' => $data['AddAddress']['addressPostalCode'], 'addressCity' => $data['AddAddress']['addressCity'], 'addressStreet' => $data['AddAddress']['addressStreet'], 'addressInfos' => $data['AddAddress']['addressInfos']));
          $this->enMan->persist($uasEnt);
          $this->enMan->flush();

          // update user's addresses quantity
          $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.userAddresses', 'u.userAddresses + 1')
          ->where('u.id_us = ?1')
          ->setParameter(1, (int)$userAttr['id'])
          ->getQuery()
          ->execute();

          // commit SQL transaction
          $this->enMan->getConnection()->commit();
          $flashSess->setFlash('addAddressSuccess', 1);
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
        $flashSess->setFlash('formErrors', $this->getAllFormErrors($form));
      }
      return $this->redirect($formUrl);
    }
    return $this->render('UserAddressesBundle:Addresses:addAddress.html.php', array('formUrl' => $formUrl, 'form' => $form->createView(),
    'add' => true, 'formErrors' => $flashSess->getFlash('formErrors', array()), 'success' => (int)$flashSess->getFlash('addAddressSuccess', -1),
    'titleBox' => "Ajouter une adresse", "id" => 0));
  }

  /**
   * Edits new address.
   * @access public
   * @return Displayed template.
   */
  public function editAddressAction(Request $request)
  {
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $id = (int)$request->attributes->get('id');
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
    // check if address belongs to connected user
    $address = $this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->getUserAddress($id, (int)$userAttr['id']);
    if(isset($address[0]['id_ua']) && $address[0]['id_ua'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $flashSess = $request->getSession();
      $formUrl = $this->generateUrl('addressEdit', array('id' => $id));
      $uasEnt = new UsersAddresses;
      if(count($flashData = $flashSess->getFlash('formData')) > 0)
      {
        $uasEnt->setData($flashData['FirstStep']);
      }
      else
      {
        $uasEnt->setData(array('addressCountry' => (int)$address[0]['id_co'], 'addressFirstName' => $address[0]['addressFirstName'], 'addressLastName' => $address[0]['addressLastName']
        , 'addressPostalCode' => $address[0]['addressPostalCode'], 'addressCity' => $address[0]['addressCity'], 'addressStreet' => $address[0]['addressStreet'], 'addressInfos' => $address[0]['addressInfos']));
      }
      UsersAddresses::setSessionToken($this->sessionTicket);
      $uasEnt->setTicket($this->sessionTicket);
      $adsEnt = new Ads;
      $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
      $uasEnt->setCountriesList($adsEnt->getCountriesList()); 
      $form = $this->createForm(new FirstStep(), $uasEnt);
      $data = $request->request->all('FirstStep');
      if($request->getMethod() == 'POST') 
      {
        $form->bindRequest($request);
        if($form->isValid())
        {
          // start transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            // check if address is used in the order (if is, insert the new address row, otherwise, update the old)
            if(!$this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->isUsedInOrder($id))
            {
              // update address data 
              $q = $this->enMan->createQueryBuilder()->update('User\AddressesBundle\Entity\UsersAddresses', 'ua')
              ->set('ua.addressCountry', (int)$data['FirstStep']['addressCountry'])
              ->set('ua.addressFirstName', '?1')
              ->set('ua.addressLastName', '?2')
              ->set('ua.addressPostalCode', '?3')
              ->set('ua.addressCity', '?4')
              ->set('ua.addressStreet', '?5')
              ->set('ua.addressInfos', '?6')
              ->where('ua.id_ua = ?7')
              ->setParameter(1, $data['FirstStep']['addressFirstName'])
              ->setParameter(2, $data['FirstStep']['addressLastName'])
              ->setParameter(3, $data['FirstStep']['addressPostalCode'])
              ->setParameter(4, $data['FirstStep']['addressCity'])
              ->setParameter(5, $data['FirstStep']['addressStreet'])
              ->setParameter(6, $data['FirstStep']['addressInfos'])
              ->setParameter(7, $id)
              ->getQuery()
              ->execute();
            }
            else
            {
              $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
              $uasEnt->setData(array('addressState' => 1, 'addressUser' => $userRef, 'addressCountry' => $this->enMan->getReference('Geography\CountriesBundle\Entity\Countries', (int)$data['FirstStep']['addressCountry']), 'addressFirstName' => $data['FirstStep']['addressFirstName'], 'addressLastName' => $data['FirstStep']['addressLastName']
               , 'addressPostalCode' => $data['FirstStep']['addressPostalCode'], 'addressCity' => $data['FirstStep']['addressCity'], 'addressStreet' => $data['FirstStep']['addressStreet'], 'addressInfos' => $data['FirstStep']['addressInfos']));
              $this->enMan->persist($uasEnt);
              $this->enMan->flush();

              // update address state 
              $q = $this->enMan->createQueryBuilder()->update('User\AddressesBundle\Entity\UsersAddresses', 'ua')
              ->set('ua.addressState', 2)
              ->where('ua.id_ua = ?1')
              ->setParameter(1, $id)
              ->getQuery()
              ->execute();
  
              $formUrl = $this->generateUrl('addressEdit', array('id' => $uasEnt->getIdUa()));
            }
            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            $flashSess->setFlash('addAddressSuccess', 1);
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
          $flashSess->setFlash('formErrors', $this->getAllFormErrors($form));
        }
        return $this->redirect($formUrl);
      }
      return $this->render('UserAddressesBundle:Addresses:addAddress.html.php', array('formUrl' => $formUrl, 'form' => $form->createView(),
      'titleBox' => "Editer une adresse", "id" => $id, 'add' => false, 'formErrors' => $flashSess->getFlash('formErrors', array()), 'success' => (int)$flashSess->getFlash('addAddressSuccess', -1)));
    }
    // access tests case
    if($isTest == 1)
    {
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    return $this->generateUrl('badElement');
  }

  /**
   * Deletes an address.
   * @access public
   * @return JSON response.
   */
  public function deleteAddressAction(Request $request)
  {
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $type = $request->attributes->get('type');
    $id = (int)$request->attributes->get('id');
    if($isTest == 0)
    {
      $userAttr = $this->user->getAttributes();
      $validCSRF = $this->validateCSRF();
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $validCSRF = true;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $validCSRF = true;
    }
    $response = array();
    $response['isError'] = 1;
    // check if address belongs to connected user
    $address = $this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->getUserAddress($id, (int)$userAttr['id']);
    if($validCSRF === true && isset($address[0]['id_ua']) && $address[0]['id_ua'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // start transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        // check if address is used in the order (if is, insert the new address row, otherwise, update the old)
        if(!$this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->isUsedInOrder($id))
        {
          // update address data 
          $q = $this->enMan->createQueryBuilder()->delete('User\AddressesBundle\Entity\UsersAddresses', 'ua')
          ->where('ua.id_ua = ?1')
          ->setParameter(1, $id)
          ->getQuery()
          ->execute();
        }
        else
        {
          // update address state 
          $q = $this->enMan->createQueryBuilder()->update('User\AddressesBundle\Entity\UsersAddresses', 'ua')
          ->set('ua.addressState', 2)
          ->where('ua.id_ua = ?1')
          ->setParameter(1, $id)
          ->getQuery()
          ->execute();
        }

        // update user's addresses quantity
        $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userAddresses', 'u.userAddresses - 1')
        ->where('u.id_us = ?1')
        ->setParameter(1, (int)$userAttr['id'])
        ->getQuery()
        ->execute();

        // JSON replies
        $response['isError'] = 0;
        $response['message'] = "Addresse a été correctement supprimée";
        // commit SQL transaction
        $this->enMan->getConnection()->commit();
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    elseif($validCSRF !== true)
    {
      $response['message'] = "Votre session a expiré. Veuillez réessayer.";
    }
    else
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      $response['message'] = "Vous n'avez pas le droit d'accédér à cet élément";
    }
    echo json_encode($response);
    die();
  }

  /**
   * Chooses an address from addressbook.
   * @access public
   * @return JSON response (if not error, with address data; otherwise with error message)
   */
  public function chooseAddressAction(Request $request)
  {
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $id = (int)$request->attributes->get('id');
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
    $response = array();
    $response['isError'] = 1;
    // check if address belongs to connected user
    $address = $this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->getUserAddress($id, (int)$userAttr['id']);
    if(isset($address[0]['id_ua']) && $address[0]['id_ua'] == $id)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      $response['addressCountry'] = $address[0]['id_co'];
      $response['addressId'] = $address[0]['id_ua'];
      unset($address[0]['id_ua']);
      unset($address[0]['id_co']);
      foreach($address[0] as $key => $value)
      {
        $response[$key] = $value;
      }
      $response['isError'] = 0;
    }
    else
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 0), 200);
      }
      $response['message'] = "Vous n'avez pas le droit d'accédér à cet élément";
    }
    echo json_encode($response);
    die();
  }

}