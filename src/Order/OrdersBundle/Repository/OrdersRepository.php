<?php
namespace Order\OrdersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Order\OrdersBundle\Entity\OrdersComments;
use Database\MainEntity;

class OrdersRepository extends EntityRepository 
{

  /**
   * Retreives order data by seller or buyer id and order id.
   * @access public
   * @param int $order Order's id.
   * @param int $user User's id.
   * @return array Order data.
   */
  public function getOrderByUsers($order, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.email, us.id_us AS buyerId, us.login AS buyerLogin, us.email as buyerEmail,
    a.id_ad, a.adName, a.adName, a.adText, a.adVisits, a.adStart, a.adOffer,
    a.adEnd, a.adMinOpinion, a.adOfferPrice, a.adObjetState, a.adSellerType, a.adState, a.adBuyTo, a.adSellerGeo,
    ca.id_ca, ca.categoryName, o.orderTotal, o.orderDelivery, o.orderTax, o.orderState, o.orderPreferedDelivery, o.orderPackRef,
    o.orderPayment, o.orderCarrier, o.orderNextAction, o.orderComments, o.orderBuyerAddress
    FROM OrderOrdersBundle:Orders o
    JOIN o.orderAd a
    JOIN a.adCategory ca
    JOIN o.orderSeller u
    JOIN o.orderBuyer us
    WHERE a.id_ad = :order AND (us.id_us = :user OR u.id_us = :user)")
    ->setParameter('order', $order)
    ->setParameter('user', $user); 
    $row = $query->getResult();
    if(isset($row[0]))
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Gets orders list by user id.
   * @access public
   * @param array $options Options used to the query.
   * @param int $user User's id.
   * @return array Orders data.
   */
  public function getOrdersByUser($options, $user)
  {
    $order = "a.id_ad";
    $columns = array("numero" => "a.id_ad", "titre" => "a.adName", "etat" => "o.orderState");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, o.orderTotal, o.orderState
    FROM OrderOrdersBundle:Orders o
    JOIN o.orderAd a
    JOIN a.adCategory ca
    JOIN o.orderSeller u
    JOIN o.orderBuyer us
    WHERE us.id_us = :user OR u.id_us = :user ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);  
    return $query->getResult();
  }

  /**
   * Inserts new order.
   * @access public
   * @param int $id Ad (= order) id.
   * @param array $data Form data.
   * @param array $params Other params.
   * @param Order\OrdersBundle\Entity\Orders $ordEnt Order's class instance.
   * @param User\AdressesBundle\Entity\UsersAdresses $uasEnt User adress instance.
   * @return void
   */
  public function initNewOrder($id, $data, $params, $ordEnt, $uasEnt)
  {
    // set adState to "end"
    $q = $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
    ->set('a.adState', '2')
    ->where('a.id_ad = ?1')
    ->setParameter(1, $id)
    ->getQuery()
    ->execute();

    // insert address data
    // or put only id (if address is inserted => detect it with JavaScript events)
    $userRef = $this->getEntityManager()->getReference('User\ProfilesBundle\Entity\Users', (int)$params['userId']);
    $buyerAddress = 0;
    if($params['insertBuyerAddress'])
    {
      if((int)$data['FirstStep']['addressId'] > 0)
      {
        $uasEnt = $this->getEntityManager()->getReference('User\AddressesBundle\Entity\UsersAddresses', (int)$data['FirstStep']['addressId']);
      }
      else
      {
        $uasEnt->setFirstStepData(array('addressUser' => $userRef, 'addressCountry' => $this->getEntityManager()->getReference('Geography\CountriesBundle\Entity\Countries', (int)$data['FirstStep']['addressCountry']), 'addressFirstName' => $data['FirstStep']['addressFirstName'], 'addressLastName' => $data['FirstStep']['addressLastName']
        , 'addressPostalCode' => $data['FirstStep']['addressPostalCode'], 'addressCity' => $data['FirstStep']['addressCity'], 'addressStreet' => $data['FirstStep']['addressStreet'], 'addressInfos' => $data['FirstStep']['addressInfos'], 'addressState' => $uasEnt->getActiveState()));
        $this->getEntityManager()->persist($uasEnt);
        $this->getEntityManager()->flush();
        // update user's addresses quantity
        $q = $this->getEntityManager()->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.userAddresses', 'u.userAddresses + 1')
        ->where('u.id_us = ?1')
        ->setParameter(1, (int)$params['userId'])
        ->getQuery()
        ->execute();
      }
      $buyerAddress = $uasEnt->getIdUa();
    }
    // init order row (update order step too; put to 1)
    // step 1 : buyer and seller can modify his data (seller valids data and ships the order)
    // step 2 : only buy can modify that (buyer receives the order and changes the order's state to "ended")
    $comments = 0; 
    $adRef = $this->getEntityManager()->getReference('Ad\ItemsBundle\Entity\Ads', $id);
    $sellerRef = $this->getEntityManager()->getReference('User\ProfilesBundle\Entity\Users', (int)$params['sellerId']); 
     if(trim($data['OrderFirstStep']['orderComment']) != '')
    {
      $orcEnt = new OrdersComments;
      $orcEnt->setData(array('commentAd' => $adRef, 'commentAuthor' => $userRef, 'commentText' => trim($data['OrderFirstStep']['orderComment']),
      'commentDate' => ''));
      $this->getEntityManager()->persist($orcEnt);
      $this->getEntityManager()->flush();
      $comments = 1;
    }
    $ordEnt->setFirstStepData(array('orderAd' => $adRef, 'orderSeller' => $sellerRef, 'orderBuyer' => $userRef, 'orderBuyerAddress' => $buyerAddress, 
      'orderTotal' => (float)$params['offerPrice'], 'orderDelivery' => 0, 'orderTax' => 0, 'orderState' => 1, 'orderPreferedDelivery' => implode(';', $data['OrderFirstStep']['orderPreferedDelivery']), 'orderPayment' => (int)$data['OrderFirstStep']['orderPayment'], 
      'orderPackRef' => '', 'orderComments' => $comments, 'orderCarrier' => 0, 'orderNextAction' => (int)$params['sellerId'])
    );
    $this->getEntityManager()->persist($ordEnt);
    $this->getEntityManager()->flush();

    // update orders quantity for both users
    $q = $this->getEntityManager()->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
    ->set('u.userOrders', 'u.userOrders + 1')
    ->where('u.id_us = ?1 OR u.id_us = ?2')
    ->setParameter(1, $params['userId'])
    ->setParameter(2, (int)$params['sellerId'])
    ->getQuery()
    ->execute();	

    // send PM and mail to seller
    $tplVals = array('{USER}', '{OFFER_NAME}', '{URL_UPDATE}');
    $realVals = array($params['userLogin'], $params['offerName'], $params['url']);
    $template = str_replace($tplVals, $realVals, file_get_contents(rootDir.'messages/start_transaction.message'));

    // Insert private message
    $messageVals = array(
      'title' => "Commande de votre offre".$realVals[1],
      'content' => $template,
      'type' => 2,
      'state' => 1
    );
    $this->getEntityManager()->getRepository('MessageMessagesBundle:Messages')->sendPm($userRef, $sellerRef, $messageVals);

    $message = \Swift_Message::newInstance()
    ->setSubject("Commande de votre offre".$realVals[1])
    ->setFrom($params['from'])
    ->setTo($params['email'])
    ->setContentType("text/html")
    ->setBody($template);
    $params['mailer']->send($message);
  }

  /**
   * Retreives order data by seller or buyer id and order id.
   * @access public
   * @return array Order data.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, us.id_us AS buyerId, a.id_ad 
    FROM OrderOrdersBundle:Orders o
    JOIN o.orderAd a
    JOIN o.orderSeller u
    JOIN o.orderBuyer ua
    JOIN ua.addressCountry co
    JOIN ua.addressUser us")
    ->setMaxResults(1); 
    $row = $query->getResult();
    return array('id' => $row[0]['id_ad'], 'id2' => '', 'user1' => $row[0]['id_us'], 'user2' => $row[0]['buyerId']);
  }

}