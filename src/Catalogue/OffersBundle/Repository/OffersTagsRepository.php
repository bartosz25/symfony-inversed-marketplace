<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class OffersTagsRepository extends EntityRepository 
{

  /**
   * Gets tags list for one offer.
   * @access public
   * @param int $offer Offer's id
   * @return array Tags list.
   */
  public function getTagsByOffer($offer)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT t.id_ta, t.tagName
    FROM CatalogueOffersBundle:OffersTags ot
    JOIN ot.tags_id_ta t
    WHERE ot.offers_id_of = :offer")
    ->setParameter('offer', $offer);
    return $query->getResult();
  }

  /**
   * Gets offers list.
   * @access public
   * @param array $options Options used to SQL request.
   * @param array $tag Tag's id.
   * @return array Ads list.
   */
  public function getOffersList($options, $tag)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, cat.id_cat, cat.catalogueName
    FROM CatalogueOffersBundle:OffersTags ot
    JOIN ot.offers_id_of o
    JOIN o.offerCatalogue cat
    WHERE ot.tags_id_ta = :tag
    ORDER BY o.id_of DESC")
    ->setParameter('tag', $tag)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);  
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }
}