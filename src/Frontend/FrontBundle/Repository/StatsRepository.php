<?php
namespace Frontend\FrontBundle\Repository;

use Doctrine\ORM\EntityRepository;
    
class StatsRepository extends EntityRepository 
{
  /**
   * Gets value for one stat.
   * @access public
   * @param string $key Key to retreive.
   * @return string Retreived value.
   */
  public function getStats($key)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT s.statValue
    FROM FrontendFrontBundle:Stats s
    WHERE s.key_st = :key ")
    ->setParameter("key", $key);
    $result = $query->getResult();
    return $result[0]['statValue'];
  }

  /**
   * Update stats quantity.
   * @access public
   * @param string $how Increment/decrement (+1 or -1).
   * @param string $field Field id.
   * @return void.
   */
  public function updateQuantity($how, $field)
  {
    $this->getEntityManager()->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Stats', 's')
    ->set('s.statValue', 's.statValue '.$how)
    ->where('s.key_st = ?1')
    ->setParameter(1, $field)
    ->getQuery()
    ->execute();
  }

  /**
   * Puts new number of users who will receive the newsletter.
   * @access public
   * @return void
   */
  public function setNewsletterCount()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us
    FROM UserAlertsBundle:UsersAdsAlerts uaa
    JOIN uaa.alertUser u
    WHERE uaa.alertState = 0");
    $rowA = $query->getResult();

    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us
    FROM UserAlertsBundle:UsersCategoriesAlerts uca
    JOIN uca.alertUser u
    WHERE uca.alertState = 0");
    $rowC = $query->getResult();
    $merged = array_merge($rowA, $rowC);
    $count = 0;
    $used = array();
    foreach($merged as $e => $element)
    {
      if(!in_array($element['id_us'], $used))
      {
        $count++;
        $used[] = $element['id_us'];
      }
    }
    $this->getEntityManager()->createQueryBuilder()->update('Frontend\FrontBundle\Entity\Stats', 's')
    ->set('s.statValue', $count)
    ->where('s.key_st = ?1')
    ->setParameter(1, 'news')
    ->getQuery()
    ->execute();
  }

}