<?php
namespace User\AlertsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Frontend\FrontBundle\Controller\FrontController;
use User\AlertsBundle\Entity\UsersAdsAlerts;
use Database\MainEntity;
use Frontend\FrontBundle\Helper\FrontendHelper;

class UsersAlertsController extends FrontController
{

  /**
   * List subscribed elements.
   * @access public
   * @return Displayed template
   */
  public function listByUserAction(Request $request)
  {
    $type = $request->attributes->get('type');
    $column = $request->attributes->get('column');
    $how = $request->attributes->get('how');
    $isPartial = $this->checkIfPartial();
    $userAttr = $this->user->getAttributes();
    switch($type)
    {
      case 'annonces':
        $elements =  $this->enMan->getRepository('UserAlertsBundle:UsersAdsAlerts')
        ->getSubscribedAds(array('column' => $column, 'how' => $how, 'date' => $this->config['sql']['onlyDateFormat']), (int)$userAttr['id']);
        $all = (int)$userAttr['subscription']['ads'];
        $file = "tableAds";
      break;
      case 'categories':
        $elements = $this->enMan->getRepository('UserAlertsBundle:UsersCategoriesAlerts')
        ->getSubscribedCategories(array('column' => $column, 'how' => $how, 'date' => $this->config['sql']['onlyDateFormat']), (int)$userAttr['id']);
        $all = (int)$userAttr['subscription']['cats'];
        $file = "tableCategories";
      break;
    }
    $helper = new FrontendHelper;
    if($isPartial)
    {
      return $this->render('UserAlertsBundle:UsersAlerts:'.$file.'.html.php', array('elements' => $elements, 'type' => $type,
      'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('nom', 'date')), 'how' => $how, 'column' => $column));
    }
    return $this->render('UserAlertsBundle:UsersAlerts:listByUser.html.php', array('elements' => $elements, 'type' => $type,
    'ticket' => $this->sessionTicket, 'class' => $helper->getClassesBySorter($how, $column, array('nom', 'date')), 'how' => $how, 'column' => $column));
  }

  /**
   * Delete from category or ad.
   * @access public
   * @return Displayed template
   */
  public function deleteAction(Request $request)
  {
    $isTest = (int)$request->attributes->get('test');
    $testResult = (int)$request->attributes->get('result');
    $type = $request->attributes->get('type');
    $id = (int)$request->attributes->get('id');
    $validCSRF = $this->validateCSRF();
    // $userAttr = $this->user->getAttributes();
    $result = array();
    if($isTest == 0)
    {
      $userAttr = $this->user->getAttributes();
    }
    elseif($isTest == 1 && $testResult == 0)
    {
      $alert = $this->enMan->getRepository('User\AlertsBundle\Entity\UsersAdsAlerts')->find(array('alertAd' => $id, 'alertUser' => (int)$request->attributes->get('user')));
      $result = 1;
      if($alert == null)
      {
        $result = 0;
      }
      return new Response(parent::testAccess($testResult, $result), 200);
    }
    elseif($isTest == 1 && $testResult == 1)
    {
      $alert = $this->enMan->getRepository('User\AlertsBundle\Entity\UsersAdsAlerts')->find(array('alertAd' => $id, 'alertUser' => (int)$request->attributes->get('elUser1')));
      $result = 0;
      if($alert->getAlertAd()->getAdName() != '')
      {
        $result = 1;
      }
      return new Response(parent::testAccess($testResult, $result), 200); 
    }
    // checks if ad belongs to user
    if($isTest == 0 && (
      ($type == "annonces" && !$this->enMan->getRepository('User\AlertsBundle\Entity\UsersAdsAlerts')->alreadySubscribed($userAttr['id'], $id)) ||
      ($type == "categories" && !$this->enMan->getRepository('User\AlertsBundle\Entity\UsersCategoriesAlerts')->alreadySubscribed($userAttr['id'], $id))    
    ))
    {
      return json_encode(array('isError' => 1, 'message' => "Une erreur s'est produite pendant la suppression de cette alerte"));
    }
    if($validCSRF === true)
    {
      // start transaction
      $this->enMan->getConnection()->beginTransaction();
      try
      {
        switch($type)
        {
          case 'annonces':
            $field = 'aboAds';
            $q2 = $this->enMan->createQueryBuilder()->delete('User\AlertsBundle\Entity\UsersAdsAlerts', 'uaa')
            ->where('uaa.alertUser = ?1 AND uaa.alertAd = ?2')
            ->setParameter(1, $userAttr['id'])
            ->setParameter(2, $id)
            ->getQuery()
            ->execute();
            $what = "l'annonce";
          break;
          case 'categories':
            $field = 'aboCats';
            $q2 = $this->enMan->createQueryBuilder()->delete('User\AlertsBundle\Entity\UsersCategoriesAlerts', 'uca')
            ->where('uca.alertUser = ?1 AND uca.alertCategory = ?2')
            ->setParameter(1, $userAttr['id'])
            ->setParameter(2, $id)
            ->getQuery()
            ->execute();
            $what = 'la catégorie';
          break;
        }

        // update categories subscribtions
        $q = $this->enMan->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
        ->set('u.'.$field, 'u.'.$field.' - 1')
        ->where('u.id_us = ?1')
        ->setParameter(1, (int)$userAttr['id'])
        ->getQuery()
        ->execute();

        // commit SQL transaction
        $this->enMan->getConnection()->commit();
        $result['isError'] = 0;
        $result['message'] = "Vous êtes correctement désabonné de $what";
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
      $result['result'] = 1;
      $result['message'] = MainEntity::getTicketMessage()." Veuillez réessayer";
    }
    echo json_encode($result);
    die();
  }

}