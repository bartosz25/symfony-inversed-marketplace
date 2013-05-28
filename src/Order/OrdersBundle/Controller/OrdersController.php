<?php
namespace Order\OrdersBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Frontend\FrontBundle\Controller\FrontController;
use Ad\ItemsBundle\Entity\Ads;
use Others\Pager;
use User\ProfilesBundle\Entity\Users;
use Order\OrdersBundle\Entity\Orders;
use Order\OrdersBundle\Entity\OrdersComments;
use Order\OrdersBundle\Entity\Delivery;
use Order\OrdersBundle\Form\OrderNextStep;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use User\AddressesBundle\Form\FirstStep;
use User\AddressesBundle\Entity\UsersAddresses;
use Order\OrdersBundle\Form\OrderFirstStep;
use Order\OrdersBundle\Form\OrderFormState;
use Order\OrdersBundle\Form\OrderDelivery;
use Frontend\FrontBundle\Helper\FrontendHelper;

class OrdersController extends FrontController
{

  /**
   * Update order state
   * @return Displayed template.
   */
  public function updateDataAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $userAttr = $this->user->getAttributes();
    $adOffer = 0;
    $flashSess = $request->getSession();
    $id = (int)$request->attributes->get('id');
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    if($isTest == 1 && $testResult == 0)
    {
      $userAttr = array('id' => (int)$request->attributes->get('user'));
      $adOffer = 1;
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $userAttr = array('id' => (int)$request->attributes->get('elUser1'));
      $adOffer = 1;
    }
    // get order data (only when connected user is seller or buyer)
    $order = $this->enMan->getRepository('OrderOrdersBundle:Orders')->getOrderByUsers($id, (int)$userAttr['id']);
    if($isTest == 0)
    {
      $adOffer = (int)$order['adOffer'];
    }
    if(isset($order['id_ad']) && $order['id_ad'] == $id && $adOffer != 0)
    {
      // access tests case
      if($isTest == 1)
      {
        return new Response(parent::testAccess($testResult, 1), 200);
      }
      // get order comments and make the pagination
      $commentsList = $this->enMan->getRepository('OrderOrdersBundle:OrdersComments')->getCommentsByOrder(array(
        'date' => $this->config['sql']['dateFormat'], 'maxResults' => $this->config['pager']['perPage'],
        'start' => $this->config['pager']['perPage']*($page-1)
        ), $id
      );
      $pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $order['orderComments'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
      // get informations about choosen offer
      $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer((int)$order['adOffer']);
      // get entities
      $adsEnt = new Ads;
      $useEnt = new Users;
      $ordEnt = new Orders;
      // utilities
      $formUrl = $this->generateUrl('orderUpdateData', array('id' => $id));
      $formType = 'orderUpdateData';
      // order payment and delivery preferencies
      if($order['orderPayment'] != '' && $order['orderPayment'] > 0)
      {
        $order['orderPaymentLabel'] = $adsEnt->getPaymentLabel($order['orderPayment']);
      }
      else
      {
        $order['orderPaymentLabel'] = '';
      }
      if($order['orderPreferedDelivery'] != '')
      {
        $ordEnt->setOrderPreferedDelivery($order['orderPreferedDelivery']);
        // $deliveryModes = explode(';', $order['orderPreferedDelivery']);
        $order['preferedDeliveryLabels'] = implode(',', Delivery::getLabels($ordEnt->getOrderPreferedDelivery()));
      }
      else
      {
        $order['preferedDeliveryLabels'] = '';
      }
      // order delivery address
      if($order['orderBuyerAddress'] > 0)
      {
        $address = $this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->getUserAddress($order['orderBuyerAddress'], $order['buyerId']);
        $order['addressFirstName'] = $address[0]['addressFirstName'];
        $order['addressLastName'] = $address[0]['addressLastName'];
        $order['addressStreet'] = $address[0]['addressStreet'];
        $order['addressPostalCode'] = $address[0]['addressPostalCode'];
        $order['countryName'] = $address[0]['countryName'];
        $order['addressInfos'] = $address[0]['addressInfos'];
        $order['addressCity'] = $address[0]['addressCity'];
        $order['id_co'] = $address[0]['id_co'];
        $order['id_ua'] = $order['orderBuyerAddress'];
      }
      else
      {
        $order['addressFirstName'] = '';
        $order['addressLastName'] = '';
        $order['addressStreet'] = '';
        $order['addressPostalCode'] = '';
        $order['countryName'] = '';
        $order['addressInfos'] = '';
        $order['addressCity'] = '';
        $order['id_co'] = 0;
        $order['id_ua'] = 0;
      }
      // determine connected user role (seller or buyer)
      $role = 'buyer';
      $whoNotes = 'vendeur';
      if($userAttr['id'] == $order['id_us'])
      { 
        $role = 'seller';
        $whoNotes = 'acheteur';
      }
      $ordEnt->setWho($role);
      $ordEnt->setOrderState($order['orderState']);
      $lastState = false;
      $formView = null;
      $formView2 = null;
      // check if we need the form (only when state is 1 => Delivery address filled up, 2 => Delivery address incompleted,
      // 3 => Delivery address completed, 4 => Payment informations filled up, 5 => Payment informations incompleted,
      // 6 => Payment informations completed, 
      $showForm = $ordEnt->canShowForm();
      $showFormPay = true;
      if(!$ordEnt->isTheLastState())
      {
        if($showForm && $order['orderNextAction'] == $userAttr['id'])
        {
          Orders::setSessionToken($this->sessionTicket);
          $ordEnt->setTicket($this->sessionTicket);
          // create form which fill up initial data
          if($role == 'seller')
          {
            // seller has only one form : delivery and payment data
            if(count($flashData = $flashSess->getFlash('formData')) > 0 && isset($flashData['OrderNextStep']))
            {
              $orderNextStepData = $flashData['OrderNextStep'];
              unset($orderNextStepData['orderState']);
            } 
            else
            {
              $orderNextStepData = array('orderPackRef' => $order['orderPackRef'],
              'orderDelivery' => $order['orderDelivery'], 'orderCarrier' => $order['orderCarrier']);
            } 
            $ordEnt->setFirstStepData($orderNextStepData);
            if($ordEnt->getOrderState() == $ordEnt->getOrderStateDelivery())
            {
              $form = $this->createForm(new OrderDelivery(), $ordEnt);
              $showFormPay = false;
            }
            else
            {
              $form = $this->createForm(new OrderNextStep(), $ordEnt);
            }
          }
          elseif($role == 'buyer')
          {
            // buyer has two forms : address and delivery
            UsersAddresses::$em = $this->enMan;
            UsersAddresses::$staticId = $userAttr['id'];
            $uasEnt = new UsersAddresses;
            $adsEnt->setCountriesList($this->enMan->getRepository('GeographyCountriesBundle:Countries')->getCountries());
            $uasEnt->setCountriesList($adsEnt->getCountriesList()); 
            $ordEnt->setDeliveryTypes(Delivery::getDeliveryTypes());
            $ordEnt->setPaymentTypes($adsEnt->getPayments());
            // if flash session data
            if(count($flashData = $flashSess->getFlash('formData')) > 0)
            {
              $firstStepData = $flashData['FirstStep'];
              $orderFirstStepData = $flashData['OrderFirstStep'];
            } 
            else
            {
              $firstStepData = array(
                'addressFirstName' => $order['addressFirstName'], 'addressLastName' => $order['addressLastName'], 'addressPostalCode' => $order['addressPostalCode'],
                'addressCity' => $order['addressCity'], 'addressStreet' => $order['addressStreet'], 'addressInfos' => $order['addressInfos'],
                'addressCountry' => $order['id_co']
              );
              $orderFirstStepData = array('orderPreferedDelivery' => $ordEnt->getOrderPreferedDelivery(), 'orderPayment' => $order['orderPayment']);
            } 
            unset($orderFirstStepData['orderState']);
            $uasEnt->setFirstStepData($firstStepData);
            $ordEnt->setFirstStepData($orderFirstStepData); 
            UsersAddresses::setSessionToken($this->sessionTicket);
            $uasEnt->setTicket($this->sessionTicket);
            $ordEnt->setTicket($this->sessionTicket);
            $form = $this->createForm(new OrderFirstStep(), $ordEnt);
            $form2 = $this->createForm(new FirstStep(), $uasEnt);
            $formView2 = $form2->createView();
          }
          $formView = $form->createView();
        }
        elseif($order['orderNextAction'] == $userAttr['id'])
        {
          $showForm = false;
          $form = $this->createForm(new OrderFormState(), $ordEnt);
          $formView = $form->createView();
        }
        else
        {
          $showForm = false;
        }
      }
      else
      {
        $showForm = false;
        $lastState = true;
      }
      // submit request
      $data = $request->request->all('OrderNextStep');
      if($request->getMethod() == 'POST')
      {
        // form binding and checking 
        $form->bindRequest($request);
        if($role == 'buyer' && $showForm) 
        {
          $form2->bindRequest($request);
        }
        $formValid = true;
        if($ordEnt->isErrorState())
        {
          $showForm = false;
          $formValid = true;
        }
        else
        {
          $formValid = (bool)($form->isValid());
          // for buyer, we have to check the second form too
          if($role == 'buyer' && $formValid && $showForm)
          {
            $formValid = (bool)($form2->isValid()); 
          }
        }
        if($formValid)
        {
          // start transaction
          $this->enMan->getConnection()->beginTransaction();
          try
          {
            $nextUser = (int)$offer['id_us'];
            if($nextUser == $order['orderNextAction'])
            {
              $nextUser = (int)$order['buyerId'];
            }
            // order comment (any user type)
            $comments = 0;
            $adRef = $this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $id);
            $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
            if((isset($data['OrderNextStep']['orderComment']) && trim($data['OrderNextStep']['orderComment']) != '') || (isset($data['FirstStep']['orderComment']) && trim($data['FirstStep']['orderComment']) != '') || 
            (isset($data['OrderFirstStep']['orderComment']) && trim($data['OrderFirstStep']['orderComment']) != '') || 
            (isset($data['OrderFormState']['orderComment']) && trim($data['OrderFormState']['orderComment']) != ''))
            {
              if(isset($data['OrderNextStep']['orderComment']) && trim($data['OrderNextStep']['orderComment']) != '')
              {
                $commentText = trim($data['OrderNextStep']['orderComment']);
              }
              elseif(isset($data['FirstStep']['orderComment']) && trim($data['FirstStep']['orderComment']) != '')
              {
                $commentText = trim($data['FirstStep']['orderComment']);
              }
              elseif(isset($data['OrderFirstStep']['orderComment']) && trim($data['OrderFirstStep']['orderComment']) != '')
              {
                $commentText = trim($data['OrderFirstStep']['orderComment']);
              }
              elseif(isset($data['OrderFormState']['orderComment']) && trim($data['OrderFormState']['orderComment']) != '')
              {
                $commentText = trim($data['OrderFormState']['orderComment']);
              }
              $orcEnt = new OrdersComments;
              $orcEnt->setData(array('commentAd' => $adRef, 'commentAuthor' => $userRef, 'commentText' => $commentText,
              'commentDate' => ''));
              $this->enMan->persist($orcEnt);
              $this->enMan->flush();
              $comments = 1;
            }
            // it's clear now, seller can only fill up payment informations
            // and buyer can only correct delivery informations
            if($showForm && $role == 'seller')
            {
              $user1 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
              $user2 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$order['buyerId']); 
              $tplVals = array('{USER}', '{ORDER}');
              $realVals = array($this->user->getUser(), $id);
              if(!$showFormPay)
              {
                // otherwise, fill up order with the rest of the data
                $q = $this->enMan->createQueryBuilder()->update('Order\OrdersBundle\Entity\Orders', 'o')
                ->set('o.orderComments', 'o.orderComments + '.$comments)
                ->set('o.orderState', (int)$data['OrderDelivery']['orderState'])
                ->set('o.orderNextAction', $nextUser)
                ->set('o.orderPackRef', '?1')
                ->where('o.orderAd = ?2')
                ->setParameter(1, (string)$data['OrderDelivery']['orderPackRef'])
                ->setParameter(2, $id)
                ->getQuery()
                ->execute(); 
 
                // notify (PM) about sending of payment informations 
                $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/notify_delivery_infos.message'));

                // Insert private message
                $messageVals = array(
                  'title' => "La référence de l'envoi envoyée ".$id,
                  'content' => $template,
                  'type' => 2,
                  'state' => 1
                ); 
                // send informations about payment too (mail only)
                $tplVals = array('{USER}', '{TRACKER_NUMBER}', '{ORDER_URL}', '{ORDER}');
                $realVals = array($this->user->getUser(), $data['OrderDelivery']['orderPackRef'], $formUrl, $id);
                $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/send_delivery_infos.maildoc'));
                $titleMail = "Informations sur le suivi pour la commande no".$id;
              }
              else
              {
                // with delivery amount, calculate new order total amount
                $orderTotal = (float)$order['adOfferPrice'] + (float)$data['OrderNextStep']['orderDelivery'];
                // otherwise, fill up order with the rest of the data
                $q = $this->enMan->createQueryBuilder()->update('Order\OrdersBundle\Entity\Orders', 'o')
                ->set('o.orderTotal', (float)$orderTotal)
                ->set('o.orderDelivery', (float)$data['OrderNextStep']['orderDelivery'])
                ->set('o.orderState', (int)$data['OrderNextStep']['orderState'])
                ->set('o.orderCarrier', (int)$data['OrderNextStep']['orderCarrier'])
                ->set('o.orderComments', 'o.orderComments + '.$comments)
                ->set('o.orderNextAction', $nextUser)
                ->where('o.orderAd = ?1')
                ->setParameter(1, $id)
                ->getQuery()
                ->execute(); 
 
                // notify (PM) about sending of payment informations 
                $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/notify_payment_infos.message'));

                // Insert private message
                $messageVals = array(
                  'title' => "Informations de payement envoyées pour la commande no ".$id,
                  'content' => $template,
                  'type' => 2,
                  'state' => 1
                ); 
                // send informations about payment too (mail only)
                $tplVals = array('{USER}', '{PAYMENT_DATA}', '{ORDER_URL}', '{ORDER}');
                $realVals = array($this->user->getUser(), $data['OrderNextStep']['paymentInfos'], $formUrl, $id);
                $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'mails/send_payment_infos.maildoc'));
                $titleMail = "Informations de payement pour la commande no".$id;
              }
              $message = \Swift_Message::newInstance()
              ->setSubject($titleMail)
              ->setFrom($this->from['mail'])
              ->setTo($order['buyerEmail'])
              ->setContentType("text/html")
              ->setBody($template); 
            }
            elseif($showForm && $role == 'buyer')
            {
              $addressId = (int)$order['id_ua'];
              $newAddressId = (int)$data['FirstStep']['addressId'];
              if($addressId != $newAddressId && $newAddressId > 0)
              {
                $addressId = $newAddressId;
              }
              else
              {
                // compare two string hashes, if differents make update or insert if this address is already used to other order
                $hashOld = sha1(mb_strtolower($order['addressFirstName'].$order['addressLastName'].$order['addressPostalCode'].
                            $order['addressCity'].$order['id_co'].$order['addressStreet'].$order['addressInfos'], 'UTF-8'));
                $hashNew = sha1(mb_strtolower($data['FirstStep']['addressFirstName'].$data['FirstStep']['addressLastName'].$data['FirstStep']['addressPostalCode'].
                            $data['FirstStep']['addressCity'].$data['FirstStep']['addressCountry'].$data['FirstStep']['addressStreet'].$data['FirstStep']['addressInfos'], 'UTF-8'));
                if($hashOld != $hashNew)
                {
                  if(!$this->enMan->getRepository('UserAddressesBundle:UsersAddresses')->isUsedWithoutOneOrder($order['id_ua'], $order['id_ad']))
                  {
                    // update user address info, only when this address isn't used by other order
                    $q = $this->enMan->createQueryBuilder()->update('User\AddressesBundle\Entity\UsersAddresses', 'ua')
                    ->set('ua.addressFirstName', '?1')
                    ->set('ua.addressLastName', '?2')
                    ->set('ua.addressPostalCode', '?3')
                    ->set('ua.addressCity', '?4')
                    ->set('ua.addressStreet', '?5')
                    ->set('ua.addressInfos', '?6')
                    ->set('ua.addressCountry', (int)$data['FirstStep']['addressCountry'])
                    ->where('ua.id_ua = ?7')
                    ->setParameter(1, (string)$data['FirstStep']['addressFirstName'])
                    ->setParameter(2, (string)$data['FirstStep']['addressLastName'])
                    ->setParameter(3, (string)$data['FirstStep']['addressPostalCode'])
                    ->setParameter(4, (string)$data['FirstStep']['addressCity'])
                    ->setParameter(5, (string)$data['FirstStep']['addressStreet'])
                    ->setParameter(6, (string)$data['FirstStep']['addressInfos'])
                    ->setParameter(7, $addressId)
                    ->getQuery()
                    ->execute();
                  }
                  else
                  {
                    // otherwise, insert a new address and update orders table
                    // $userRef = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
                    $uasEnt->setData(array('addressState' => 1, 'addressUser' => $userRef, 'addressCountry' => $this->enMan->getReference('Geography\CountriesBundle\Entity\Countries', (int)$data['FirstStep']['addressCountry']), 'addressFirstName' => $data['FirstStep']['addressFirstName'], 'addressLastName' => $data['FirstStep']['addressLastName']
                    , 'addressPostalCode' => $data['FirstStep']['addressPostalCode'], 'addressCity' => $data['FirstStep']['addressCity'], 'addressStreet' => $data['FirstStep']['addressStreet'], 'addressInfos' => $data['FirstStep']['addressInfos']));
                    $this->enMan->persist($uasEnt);
                    $this->enMan->flush();
                    $addressId = $uasEnt->getIdUa();
                  }
                }
              }
              // update orders informations too
              $q = $this->enMan->createQueryBuilder()->update('Order\OrdersBundle\Entity\Orders', 'o')
              ->set('o.orderPreferedDelivery', '?1')
              ->set('o.orderPayment', (int)$data['OrderFirstStep']['orderPayment'])
              ->set('o.orderState', (int)$data['OrderFirstStep']['orderState'])
              ->set('o.orderNextAction', $nextUser)
              ->set('o.orderBuyerAddress', $addressId)
              ->set('o.orderComments', 'o.orderComments + '.$comments)
              ->where('o.orderAd = ?2')
              ->setParameter(1, implode(';', $data['OrderFirstStep']['orderPreferedDelivery']))
              ->setParameter(2, $id)
              ->getQuery()
              ->execute();

              // notify (PM) about sending of payment informations 
              $user2 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
              $user1 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$order['buyerId']);
              $tplVals = array('{USER}', '{ORDER}', '{ORDER_URL}');
              $realVals = array($this->user->getUser(), $id, $formUrl);
              $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/notify_address_modified.message'));

              // Insert private message
              $messageVals = array(
                'title' => "Informations de livraison corrigées pour la commande no ".$id,
                'content' => $template,
                'type' => 2,
                'state' => 1
              );

              // send informations about delivery address modification
              $message = \Swift_Message::newInstance()
              ->setSubject($messageVals['title'])
              ->setFrom($this->from['mail'])
              ->setTo($order['email'])
              ->setContentType("text/html")
              ->setBody($template); 
            }
            elseif(!$showForm)
            {
              if(!isset($data['OrderFormState']['orderState']))
              {
                if($role == "seller")
                {
                  $orderState = $data['OrderNextStep']['orderState'];
                  if(!$showFormPay)
                  {
                    $orderState = $data['OrderDelivery']['orderState'];
                  }
                }
                elseif($role == "buyer")
                {
                  $orderState = $data['OrderFirstStep']['orderState'];
                }
              }
              else
              {
                $orderState = $data['OrderFormState']['orderState'];
              }
			  // prevent always about state change
              // supplementary, if the state indicates that some informations are incompleted, add needed notices about that
		      $q = $this->enMan->createQueryBuilder()->update('Order\OrdersBundle\Entity\Orders', 'o')
              ->set('o.orderState', (int)$orderState)
              ->set('o.orderNextAction', $nextUser)
              ->where('o.orderAd = ?1')
              ->set('o.orderComments', 'o.orderComments + '.$comments)
              ->setParameter(1, $id)
              ->getQuery()
              ->execute();

              // notify (PM) about sending of payment informations
              $user1 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
              $user2 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$order['buyerId']);
              $emailAddress = $order['buyerEmail'];
              if($role == "buyer")
              {
                $user1 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$order['buyerId']);
                $user2 = $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']);
                $emailAddress = $order['email'];
              }
              $tplVals = array('{USER}', '{ORDER}', '{ORDER_URL}', '{OLD_STATE}', '{NEW_STATE}');
              $realVals = array($this->user->getUser(), $id, $formUrl, $ordEnt->getOrderStateLabel($orderState), $ordEnt->getOrderStateLabel((int)$orderState));
              $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/notify_order_state_update.message'));

              // Insert private message
              $messageVals = array(
                'title' => "Nouvel état de la commande no ".$id,
                'content' => $template,
                'type' => 2,
                'state' => 1
              );

              // send informations about new state
              $message = \Swift_Message::newInstance()
              ->setSubject($messageVals['title'])
              ->setFrom($this->from['mail'])
              ->setTo($emailAddress)
              ->setContentType("text/html")
              ->setBody($template); 
            }
            // Add new private message
            $this->enMan->getRepository('MessageMessagesBundle:Messages')->sendPm($user1, $user2, $messageVals);
            // commit SQL transaction
            $this->enMan->getConnection()->commit();
            // Send e-mail notyfication
            $this->get('mailer')->send($message); 
            $flashSess->setFlash('stepOrderSuccess', 1);
            return $this->redirect($formUrl);
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
          $formErrors = $this->getAllFormErrors($form);
          if($role == "buyer" && $showForm)
          {
            $formErrors = array_merge($formErrors, $this->getAllFormErrors($form2));
          }
          $flashSess->setFlash('formData', $data);
          $flashSess->setFlash('formErrors', $formErrors);
        } 
        return $this->redirect($formUrl);      
      }
      return $this->render('AdItemsBundle:Items:endAd.html.php', array('orderPage' => true, 'hasOffer' => true, 'offer' => $offer, 
      'ad' => $order, 'states' => $adsEnt->getObjetStates(), 'userTypes' => $useEnt->getUserTypesAliases(), 'spanClass' => '',
      'orderStates' => $ordEnt->getOrderLabels(), 'formClass' => 'hidden', 'form' => $formView, 'formUrl' => $formUrl, 'formType' => $formType, 'page' => 'step'.$order['orderState'],
      'formErrors' => $flashSess->getFlash('formErrors', array()), 'form2' => $formView2, 'success' => (int)$flashSess->getFlash('stepOrderSuccess'),
      'role' => $role, 'showForm' => $showForm, 'lastState' => $lastState, 'whoNotes' => $whoNotes, 
      'commentsList' => $commentsList, 'pager' => $pager->setPages(), 'showFormPay' => $showFormPay, 'errorStates' => $ordEnt->getErrorOrderStates(true),
      'stepDescription' => $ordEnt->getStepDescription()));
    }
    elseif($isTest == 0)
    {
      // ad was ended but any offer was selected
      return $this->render('OrderOrdersBundle:Orders:showNotOfferOrder.html.php', array());
    }
    else
    {
      // access tests case
      return new Response(parent::testAccess($testResult, 0), 200);
    }
    // return $this->redirect($this->generateUrl('badElement'));
  }

  /**
   * Gets order list by user.
   * @access public
   * @return Displayed template.
   */
  public function listByUserAction(Request $request)
  {
    $page = (int)$request->attributes->get('page');
    $isPartial = $this->checkIfPartial();
    $how = $request->attributes->get('how');
    $column = $request->attributes->get('column');
    $userAttr = $this->user->getAttributes();
    $ordEnt = new Orders;
    $orders = $this->enMan->getRepository('OrderOrdersBundle:Orders')->getOrdersByUser(array('column' => $column, 'how' => $how,'maxResults' => $this->config['pager']['perPage'],
    'start' => $this->config['pager']['perPage']*($page-1), 'date' => $this->config['sql']['dateFormat']), (int)$userAttr['id']);
	$pager = new Pager(array('before' => $this->config['pager']['before'],
	                 'after' => $this->config['pager']['after'], 'all' => $userAttr['stats']['orders'],
					 'page' => $page, 'perPage' => $this->config['pager']['perPage']
				 ));
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('OrderOrdersBundle:Orders:userOrdersTable.html.php', array('states' => $ordEnt->getOrderStates(),'orders' => $orders, 'pager' => $pager->setPages(),
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
    }
    return $this->render('OrderOrdersBundle:Orders:listByUser.html.php', array('orders' => $orders, 'pager' => $pager->setPages(),
    'states' => $ordEnt->getOrderStates(), 'ticket' => $this->sessionTicket, 'class' => $helper->getClassBySorter($how), 'how' => $how, 'column' => $column));
  }

}