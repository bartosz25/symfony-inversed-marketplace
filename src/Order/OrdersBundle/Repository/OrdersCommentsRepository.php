<?php
namespace Order\OrdersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class OrdersCommentsRepository extends EntityRepository 
{

  /**
   * Gets orders comments list by user id.
   * @access public
   * @param array $options Options used to the query.
   * @param int $order Order's id.
   * @return array Orders data.
   */
  public function getCommentsByOrder($options, $order)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oc.commentText, DATE_FORMAT(oc.commentDate, '".$options['date']."') AS addedDate
    FROM OrderOrdersBundle:OrdersComments oc
    WHERE oc.commentAd = :order")
    ->setParameter('order', $order)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);  
    return $query->getResult();
  }

}