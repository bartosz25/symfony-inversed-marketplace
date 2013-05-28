<?php
namespace Cron\TasksBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use Order\OrdersBundle\Entity\Orders;
use User\AddressesBundle\Entity\UsersAddresses;

class AdsController extends FrontController
{

  /**
   * End ads.
   * @access public
   * @return Empty template.
   */
  public function endAdsAction(Request $request)
  {
    $cacheDirs = array();
    // ads to delete par call
    $perQueue = 5;
    // first, get 5 ads to end
    $ads = $this->enMan->getRepository('AdItemsBundle:Ads')->getAdsToEnd(array('start' => 0, 'maxResults' => $perQueue));
    // make a loop and end the ads
    foreach($ads as $a => $ad)
    {
      // start transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        if((int)$ad['adOffer'] == 0)
        {
          $this->enMan->getRepository('AdItemsBundle:Ads')->endAd($ad['id_ad'],
            array('adName' => $ad['adName'], 'id_re' => $ad['id_re'], 'id_ci' => $ad['id_ci'], 'id_ca' => $ad['id_ca'], 'adState' => $ad['adState']),
            array('ads' => $this->config['cache']['ads'], 'mailer' => $this->get('mailer'), 'from' => $this->from['mail'], 'dateFormat' => $this->config['sql']['dateFormat'])
          );
        }
        else
        {
          $ordEnt = new Orders;
          $uasEnt = new UsersAddresses;
          // get the offer
          $offer = $this->enMan->getRepository('CatalogueOffersBundle:Offers')->getOneOffer($ad['adOffer']);
          // instead of end this ad, make the order
          $this->enMan->getRepository('OrderOrdersBundle:Orders')->initNewOrder($ad['id_ad'], array(
              'FirstStep' => array('addressId' => 0, 'addressCountry' => self::FRANCE_ID, 'addressFirstName' => '',
                'addressLastName' => '', 'addressPostalCode' => '', 'addressCity' => 0, 'addressStreet' => '', 'addressInfos' => ''
              ),
              'OrderFirstStep' => array('orderComment' => '', 'orderPreferedDelivery' => array(), 'orderPayment' => '',)
            ), 
            array('userId' => $ad['id_us'], 'sellerId' => $offer['id_us'], 'userLogin' => $ad['login'], 'offerName' => $offer['offerName'], 'offerPrice' => $offer['offerPrice'],
              'email' => $offer['email'], 'from' => $this->from['mail'],  'insertBuyerAddress' => false, 
              'url' => $this->generateUrl('orderUpdateData', array('id' => $ad['id_ad'])), 'mailer' => $this->get('mailer')
            ), 
            $ordEnt,
            $uasEnt 
          );
        }
        $this->enMan->getConnection()->commit();
      }
      catch(Exception $e)
      {
        $this->enMan->getConnection()->rollback();
        $this->enMan->close();
        throw $e;
      }
    }
    return new Response('');
  }

}