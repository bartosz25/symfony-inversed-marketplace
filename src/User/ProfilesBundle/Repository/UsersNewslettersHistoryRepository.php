<?php
namespace User\ProfilesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class UsersNewslettersHistoryRepository extends EntityRepository 
{

  /**
   * Updates visits for $key.
   * @access public
   * @param string $field Updated field's name.
   * @param string $key Key of newsletter.
   * @return void
   */
  public function updateVisit($field, $key)
  {
    $this->getEntityManager()->createQueryBuilder()->update('User\ProfilesBundle\Entity\UsersNewslettersHistory', 'unh')
    ->set('unh.'.$field, 'unh.'.$field. ' + 1')
    ->where('unh.historyKey = ?1')
    ->setParameter(1, $key)
    ->getQuery()
    ->execute();
  }

  /**
   * Makes newsletter as read.
   * @access public
   * @param string $key Key of newsletter.
   * @return void
   */
  public function makeAsRead($key)
  {
    $this->getEntityManager()->createQueryBuilder()->update('User\ProfilesBundle\Entity\UsersNewslettersHistory', 'unh')
    ->set('unh.historyReceived', '1')
    ->where('unh.historyKey = ?1')
    ->setParameter(1, $key)
    ->getQuery()
    ->execute();
  }

}