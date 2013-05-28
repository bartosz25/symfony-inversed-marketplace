<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class EbayTemporaryRepository extends EntityRepository 
{

  /**
   * Gets data for user synchronization profile.
   * @access public
   * @param int $user User's id.
   * @param array $options SQL options.
   * @return array Synchronization data.
   */
  public function getByUser($user, $options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT et.ebayCatalogue, et.ebayData, et.ebayItemId,
    DATE_FORMAT(et.ebayDate, '".$options['dateFormat']."') AS dateOfSync
    FROM CatalogueOffersBundle:EbayTemporary et
    WHERE et.ebayUser = :user")
    ->setParameter('user', (int)$user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $row = $query->getResult();
    if(isset($row[0]['ebayItemId']))
    {
      return $row;
    }
    return array();
  }

  /**
   * Checks if item exists in the database.
   * @access public
   * @param int $item Item's id.
   * @return bool True if exists, false otherwise
   */
  public function itemExists($item)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT et.ebayItemId, et.ebayData, et.ebayContent
    FROM CatalogueOffersBundle:EbayTemporary et
    WHERE et.ebayItemId = :item")
    ->setParameter('item', $item); 
    $row = $query->getResult();
    if(isset($row[0]['ebayItemId']))
    {
      return $row[0];
    }
    return array();
  }

}