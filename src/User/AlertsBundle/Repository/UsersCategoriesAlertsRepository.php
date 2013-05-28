<?php
namespace User\AlertsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class UsersCategoriesAlertsRepository extends EntityRepository 
{

  /**
   * Gets user id by code.
   * @access public
   * @param string $code Code to find.
   * @param int $type Code type.
   * @return array Array with found data
   */
  public function alreadySubscribed($user, $category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT uca.alertDate
    FROM UserAlertsBundle:UsersCategoriesAlerts uca
    WHERE uca.alertUser = :user AND  uca.alertCategory = :category ")
    ->setParameter("user", $user)
    ->setParameter("category", $category); 
    $result = $query->getResult();
    return (bool)(isset($result[0]['alertDate']));
  }

  /**
   * Gets categories for user.
   * @access public
   * @param array $options Options array.
   * @param int $user User's id.
   * @return boolean True if subsbscribed, false otherwise
   */
  public function getSubscribedCategories($options, $user)
  {
    $order = "a.id_ad DESC";
    $columns = array("nom" => array("c.categoryName"), "date" => "uaa.alertDate");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.categoryName, c.id_ca, DATE_FORMAT(uaa.alertDate, '".$options['date']."') AS aboDate 
    FROM UserAlertsBundle:UsersCategoriesAlerts uaa
    JOIN uaa.alertCategory cm
    JOIN cm.modifiedCategory c
    WHERE uaa.alertUser = :user
    ORDER BY $order")
    ->setParameter("user", $user); 
    return $query->getResult();
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
    FROM UserAlertsBundle:UsersCategoriesAlerts uca
    JOIN uca.alertUser u
    WHERE uca.alertState = 0")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }
}