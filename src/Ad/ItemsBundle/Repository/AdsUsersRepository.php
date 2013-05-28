<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class AdsUsersRepository extends EntityRepository 
{

  /**
   * Checks if user've already seen this $ad.
   * @access public
   * @param int $user User's id.
   * @param int $ad Ad's id.
   * @return boolean true if he saw this ad; false otherwise.
   */
  public function alreadySeen($user, $ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT au.counterDate
    FROM AdItemsBundle:AdsUsers au
    WHERE au.counterUser = :user AND au.counterAd = :ad")
    ->setParameter('user', $user)
    ->setParameter('ad', $ad); 
    $row = $query->getResult();
    return (boolean)(isset($row[0]['counterDate']));
  }

}