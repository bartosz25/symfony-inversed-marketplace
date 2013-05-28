<?php
namespace Geography\CountriesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class CountriesRepository extends EntityRepository 
{

  /**
   * Gets all countries.
   * @access public
   * @return array Cities's list.
   */
  public function getCountries()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT co.id_co, co.countryName
    FROM GeographyCountriesBundle:Countries co
    ORDER BY co.countryName ASC "); 
    return $query->getResult();
  }

}