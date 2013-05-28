<?php
namespace Geography\RegionsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class RegionsRepository extends EntityRepository 
{

  /**
   * Gets all regions for one country.
   * @access public
   * @param integer $country Country id.
   * @return array List of found regions.
   */
  public function getAllRegions($country)
  {
    // $cacheName = 'global/news_index';
    $query = $this->getEntityManager()
    ->createQuery("SELECT r.regionName, r.regionUrl, c.countryName 
    FROM GeographyRegionsBundle:Regions r
    JOIN r.regionCountry c
    WHERE r.regionCountry = :country
    ORDER BY r.regionName ASC")
    ->setParameter('country', (int)$country); 
    // $query->useResultCache(true, 0, $cacheName);
    $result = $query->getResult(); // saved in given result cache id.
    // $query->getQueryCacheDriver()->save($cacheName, $result);
    return $result;
  }

  /**
   * Gets region by url.
   * @access public
   * @param string $url Region's url.
   * @return array List of found regions.
   */
  public function getByUrl($url)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT r.id_re, r.regionName, r.regionUrl, r.regionAds, r.regionOffers
    FROM GeographyRegionsBundle:Regions r
    WHERE r.regionUrl = :url")
    ->setParameter('url', $url); 
    return $query->getResult();
  }

  /**
   * Gets region by city.
   * @access public
   * @param int $city City id.
   * @return array Region data.
   */
  public function getByCity($city)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT r.id_re, r.regionName, r.regionUrl, r.regionAds, r.regionOffers
    FROM GeographyCitiesBundle:Cities c
    JOIN c.cityRegion r
    WHERE c.id_ci = :city")
    ->setParameter('city', $city); 
    return $query->getResult();
  }

  /**
   * Update ads or offers quantity.
   * @access public
   * @param string $how Increment/decrement (+1 or -1).
   * @param int $region Region id.
   * @param string $type Type (ads/offers).
   * @return void.
   */
  public function updateQuantity($how, $region, $type)
  {
    if($type == 'ads')
    {
      $field = 'regionAds';
    }
    elseif($type == 'offers')
    {
      $field = 'regionOffers';
    }
    $this->getEntityManager()->createQueryBuilder()->update('Geography\RegionsBundle\Entity\Regions', 'r')
    ->set('r.'.$field, 'r.'.$field.' '.$how)
    ->where('r.id_re = ?1')
    ->setParameter(1, $region)
    ->getQuery()
    ->execute();
  }

  /**
   * Gets all regions.
   * @access public
   * @return array List of found regions.
   */
  public function getRegions()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT r.id_re, r.regionName, r.regionUrl, c.countryName, c.id_co
    FROM GeographyRegionsBundle:Regions r
    JOIN r.regionCountry c
    ORDER BY r.regionName ASC");
    return $query->getResult();
  }

}