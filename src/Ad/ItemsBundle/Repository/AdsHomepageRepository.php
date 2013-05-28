<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class AdsHomepageRepository extends EntityRepository 
{

  /**
   * Checks if $ad will be displayed at home page.
   * @access public
   * @param int $ad Ad id.
   * @return boolean True if exists, false otherwise
   */
  public function isForHomepage($ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad
    FROM AdItemsBundle:AdsHomepage ah
    JOIN ah.ads_id_ad a
    WHERE a.id_ad = :ad")
    ->setParameter('ad', $ad); 
    $row = $query->getResult();
    return (bool)(isset($row[0]['id_ad']));
  }

}