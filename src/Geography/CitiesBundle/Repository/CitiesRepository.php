<?php
namespace Geography\CitiesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class CitiesRepository extends EntityRepository 
{

  /**
   * Gets all cities.
   * @access public
   * @param integer $start Start limit.
   * @return array List of found cities.
   */
  public function getAllCities($options)
  {
    // $cacheName = 'global/news_index';
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.cityName, r.regionUrl
    FROM GeographyCitiesBundle:Cities c
    JOIN c.cityRegion r
    ORDER BY c.cityName ASC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    // $query->useResultCache(true, 0, $cacheName);
    $result = $query->getResult(); // saved in given result cache id.
    // $query->getQueryCacheDriver()->save($cacheName, $result);
    return $result;
  }

  /**
   * Gets cities for one country.
   * @access public
   * @param integer $country Cities of choosen country.
   * @return array Cities's list.
   */
  public function getCities($country)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ci, c.cityName, r.id_re, r.regionName
    FROM GeographyCitiesBundle:Cities c
    JOIN c.cityRegion r
    WHERE r.regionCountry = :country
    ORDER BY c.cityName ASC ")
    ->setParameter('country', $country); 
    return $query->getResult();
  }

  /**
   * Update ads or offers quantity.
   * @access public
   * @param string $how Increment/decrement (+1 or -1).
   * @param int $city City id.
   * @param string $type Type (ads/offers).
   * @return void.
   */
  public function updateQuantity($how, $city, $type)
  {
    if($type == 'ads')
    {
      $field = 'cityAds';
    }
    elseif($type == 'offers')
    {
      $field = 'cityOffers';
    }
    $this->getEntityManager()->createQueryBuilder()->update('Geography\CitiesBundle\Entity\Cities', 'c')
    ->set('c.'.$field, 'c.'.$field.' '.$how)
    ->where('c.id_ci = ?1')
    ->setParameter(1, $city)
    ->getQuery()
    ->execute();
  }

  /**
   * Gets cities for one country.
   * @access public
   * @param integer $country Cities of choosen country.
   * @return array Cities's list.
   */
  public function getCitiesAll()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ci, c.cityName, c.cityUrl, r.id_re, r.regionUrl, r.regionName, co.id_co, co.countryName
    FROM GeographyCitiesBundle:Cities c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    ORDER BY co.countryName ASC, r.regionName ASC, c.cityName ASC "); 
    return $query->getResult();
  }

  /**
   * Gets $city with region stats
   * @access public
   * @param integer $city City id.
   * @return array City data.
   */
  public function getCityWithRegion($city)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ci, c.cityName, c.cityUrl, c.cityAds, c.cityOffers, r.id_re, r.regionUrl, r.regionName, r.regionAds, r.regionOffers,
    co.id_co, co.countryName
    FROM GeographyCitiesBundle:Cities c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    WHERE c.id_ci = :city")
    ->setParameter('city', $city); 
    $row = $query->getResult();
    if(isset($row[0]['id_ci']))
    {
      return $row[0];
    }
    return array();
  }

}