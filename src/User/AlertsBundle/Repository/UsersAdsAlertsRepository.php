<?php
namespace User\AlertsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class UsersAdsAlertsRepository extends EntityRepository 
{

  /**
   * Checks if user is already subscribed to this ad.
   * @access public
   * @param int $user User's id.
   * @param int $ad Ad's id.
   * @return boolean True if subsbscribed, false otherwise
   */
  public function alreadySubscribed($user, $ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT uaa.alertDate
    FROM UserAlertsBundle:UsersAdsAlerts uaa
    WHERE uaa.alertUser = :user AND  uaa.alertAd = :ad ")
    ->setParameter("user", $user)
    ->setParameter("ad", $ad); 
    $result = $query->getResult();
    return (bool)(isset($result[0]['alertDate']));
  }

  /**
   * Gets ads for user.
   * @access public
   * @param array $options Options array.
   * @param int $user User's id.
   * @return boolean True if subsbscribed, false otherwise
   */
  public function getSubscribedAds($options, $user)
  {
    $order = "a.id_ad DESC";
    $columns = array("nom" => array("a.adName"), "date" => "uaa.alertDate");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.adName, a.id_ad, DATE_FORMAT(uaa.alertDate, '".$options['date']."') AS aboDate 
    FROM UserAlertsBundle:UsersAdsAlerts uaa
    JOIN uaa.alertAd am
    JOIN am.modifiedAd a
    WHERE uaa.alertUser = :user
    ORDER BY $order")
    ->setParameter("user", $user); 
    return $query->getResult();
  }

  /**
   * Gets random alert.
   * @access public
   * @return array Data list.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.adName, a.id_ad, u.id_us 
    FROM UserAlertsBundle:UsersAdsAlerts uaa
    JOIN uaa.alertAd a
    JOIN uaa.alertUser u"); 
    $row = $query->getResult();
    return array('id' => $row[0]['id_ad'], 'id2' => '', 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }

  /**
   * Gets user who didn't receive newsletter.
   * @access public
   * @return array Data list.
   */
  public function getNotSendSubscribers($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login
    FROM UserAlertsBundle:UsersAdsAlerts uaa
    JOIN uaa.alertUser u
    WHERE uaa.alertState = 0")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }

}