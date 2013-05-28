<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class AdsTagsRepository extends EntityRepository 
{

  /**
   * Gets tags list for one ad.
   * @access public
   * @param int $ad Ad's id
   * @return array Tags list.
   */
  public function getTagsByAd($ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT t.id_ta, t.tagName, t.tagAds
    FROM AdItemsBundle:AdsTags at
    JOIN at.tags_id_ta t
    WHERE at.ads_id_ad = :ad")
    ->setParameter('ad', $ad);
    return $query->getResult();
  }

  /**
   * Gets ads list.
   * @access public
   * @param array $options Options used to SQL request.
   * @param array $tag Tag's id.
   * @return array Ads list.
   */
  public function getAdsList($options, $tag)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName,  DATE_FORMAT(a.adStart, '".$options['date']."') AS dateStart, a.adStart, a.adState, c.categoryUrl,
    c.categoryName, c.id_ca, ci.id_ci, ci.cityName
    FROM AdItemsBundle:AdsTags at
    JOIN at.ads_id_ad a
    JOIN a.adCategory c
    JOIN a.adCity ci
    WHERE at.tags_id_ta = :tag AND a.adState > 0
    ORDER BY a.id_ad DESC")
    ->setParameter('tag', $tag)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    // $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }
}