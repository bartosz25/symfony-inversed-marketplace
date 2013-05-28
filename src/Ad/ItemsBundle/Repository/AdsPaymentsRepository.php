<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class AdsPaymentsRepository extends EntityRepository 
{

  /**
   * Gets accepted payments for one ad.
   * @access public
   * @param int $ad Ad's id.
   * @return array Ads list.
   */
  public function getPaymentsByAd($ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ap.payments_id_pa
    FROM AdItemsBundle:AdsPayments ap
    WHERE ap.ads_id_ad = :ad")
    ->setParameter('ad', $ad); 
    return $query->getResult();
  }

  /**
   * Removes ad's payments by $ad id.
   * @access public
   * @param int $ad Ad's id.
   * @return void
   */
  public function deleteByAd($ad)
  {
    $this->getEntityManager()->createQueryBuilder()->delete('Ad\ItemsBundle\Entity\AdsPayments', 'ap')
    ->where('ap.ads_id_ad = ?1')
    ->setParameter(1, $ad)
    ->getQuery()
    ->execute();
  }

}