<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class SyncPrestashopRepository extends EntityRepository 
{

  /**
   * Gets data for user synchronization profile.
   * @access public
   * @param int $user User's id.
   * @param array $options SQL options.
   * @return array Synchronization data.
   */
  public function getForUser($user, $options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT sp.syncCategories, sp.syncProducts, c.id_ci, co.id_co, sp.syncTax, sp.syncDefaultState, sp.syncSite, sp.syncKey,
    DATE_FORMAT(sp.syncDate, '".$options['dateFormat']."') AS dateOfSync
    FROM CatalogueOffersBundle:SyncPrestashop sp
    JOIN sp.syncCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    WHERE sp.syncUserId = :user")
    ->setParameter('user', $user); 
    $row = $query->getResult();
    if(isset($row[0]['syncSite']))
    {
      return $row[0];
    }
    return array();
  }

}