<?php
namespace User\AlertsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use User\AlertsBundle\Entity\UsersAdsAlerts;
use Ad\ItemsBundle\Entity\AdsModified;
use Database\MainEntity;

class UsersAdsAlertsController extends FrontController
{

  /**
   * Subscribe to one ad. If is called with AJAX, returns response in JSON. Otherwise, show template.
   * @access public
   * @return Displayed template if not called with AJAX, JSON otherwise.
   */
  public function subscribeAdAction(Request $request)
  {
    $ad = (int)$request->attributes->get('ad');
    $userAttr = $this->user->getAttributes();
    $response['isError'] = 1;
    $validCSRF = $this->validateCSRF();
    // First, check user's categories subscribe limit
    if($validCSRF === true && $userAttr['subscription']['ads'] < $this->config['subscribe']['ads'])
    {
      if(!$this->enMan->getRepository('UserAlertsBundle:UsersAdsAlerts')->alreadySubscribed($userAttr['id'], $ad))
      {
        // start transaction
        $this->enMan->getConnection()->beginTransaction();
        try
        {
          // check if ad exists in ad_modified
          if(!$this->enMan->getRepository('AdItemsBundle:AdsModified')->ifExists($ad))
          {
            $admEnt = new AdsModified;
            // insert new modified element
            $admEnt->setData(array('modifiedAd' => $this->enMan->getReference('Ad\ItemsBundle\Entity\Ads', $ad),
            'modifiedText' => serialize(array()), 'modifiedFirstModif' => new \DateTime(),
            'modifiedLastUser' => 0
            ));
            $this->enMan->persist($admEnt);
            $this->enMan->flush();
          }
          else
          {
            $admEnt = $this->enMan->getReference('Ad\ItemsBundle\Entity\AdsModified', $ad);
          }
          $uaaEnt = new UsersAdsAlerts;
          $uaaEnt->setData(array('alertUser' => $this->enMan->getReference('User\ProfilesBundle\Entity\Users', (int)$userAttr['id']), 
          'alertState' => 0, 'alertAd' => $admEnt, 'alertDate' => new \DateTime()));
          $this->enMan->persist($uaaEnt);
          $this->enMan->flush();

          // update categories subscribtions
          $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
          ->set('u.aboAds', 'u.aboAds + 1')
          ->where('u.id_us = ?1')
          ->setParameter(1, (int)$userAttr['id'])
          ->getQuery()
          ->execute();

          // commit SQL transaction
          $this->enMan->getConnection()->commit();

          $response['isError'] = 0;
          $response['message'] = "Vous vous êtes correctement abonné à cette annonce";
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
        $response['message'] = "Vous êtes déjà abonné à cette annonce";
      }
    }
    elseif($validCSRF === false)
    {
      $response['message'] = MainEntity::getTicketMessage();
    }
    else
    {
      $response['message'] = "La limite d'abonnements a été atteinte.";
    } 
    // $isAjax = (int)$request->request->get('isAjax');die();
    // In fonction of call type, return adapted response
    if($request->request->get('isAjax') !== null)
    {
      echo json_encode($response);
      die();
    }
    return $this->render('UserAlertsBundle:UsersAdsAlerts:subscribeAd.html.php', array('response' => $response)); 
  }

}