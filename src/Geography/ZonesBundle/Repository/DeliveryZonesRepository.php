<?php
namespace Geography\ZonesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class DeliveryZonesRepository extends EntityRepository 
{

  /**
   * Get all delivery zones.
   * @access public
   * @return array List of found zones.
   */
  public function getDeliveryZones()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT dz.id_dz, dz.zoneName 
    FROM GeographyZonesBundle:DeliveryZones dz
    ORDER BY dz.zoneName ASC");
    return $query->getResult();
  }

}