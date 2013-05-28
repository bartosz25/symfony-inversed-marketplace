<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class OffersDeliveryZonesRepository extends EntityRepository 
{
  
  /**
   * Gets delivery fees by $offer.
   * @access public
   * @param int $offer Offer's id.
   * @return int List with delivery zones.
   */
  public function getFeesByOffer($offer)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT dz.id_dz, odz.zonePrice
    FROM CatalogueOffersBundle:OffersDeliveryZones odz
    JOIN odz.delivery_zones_id_dz dz
    WHERE odz.offers_id_of = :offer")
    ->setParameter('offer', (int)$offer); 
    $rows = $query->getResult();
    if(isset($rows[0]['id_dz']))
    {
      return $rows;
    }
    return array();
  }

  /**
   * Deletes delivery fees by $offer.
   * @access public
   * @param int $offer Offer's id.
   * @return void
   */
  public function deletePricingForOffer($offer)
  {
    $this->getEntityManager()->createQueryBuilder()->delete('Catalogue\OffersBundle\Entity\OffersDeliveryZones', 'odz')
    ->where('odz.offers_id_of = ?1')
    ->setParameter(1, (int)$offer)
    ->getQuery()
    ->execute();
  }
}