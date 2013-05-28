<?php
namespace Geography\CountriesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="countries")
 * @ORM\Entity(repositoryClass="Geography\CountriesBundle\Repository\CountriesRepository")
 */
class Countries
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_co", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_co;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Geography\ZonesBundle\Entity\DeliveryZones")
   * @ORM\JoinColumn(name="delivery_zones_id_dz", referencedColumnName="id_dz")
   */
  protected $countryZone;
  /**
   * @ORM\Column(name="name_co", type="string", length="100", nullable=false)
   */
  protected $countryName;

  protected $cacheVar;

  /**
   * Handle cache
   */
  public function setSource($source)
  {
    $this->cacheVar = $source;
  }
  public function getCountries()
  {
    $countries = array();
    foreach($this->cacheVar as $c => $country)
    {
      $countries[] = array('id_co' => $country['id_co'], 'countryName' => $country['countryName']);
    }
    return $countries;
  }

  public function getRegions($country = 0)
  {
    $regions = array();
  }

  public function getCitiesByCountry($country = 0)
  {
    $cities = array();
    $result = $this->cacheVar;
    if($country > 0)
    {
      $result = array($this->cacheVar[$country]);
    }
    $i = 0;
    foreach($result as $c => $country)
    {
      foreach($country['regions'] as $r => $region)
      {
        foreach($region['cities'] as $c => $city)
        {
          $cities[$i] = $city;
          $i++;
        }
      }
    }
    return $cities;
  }

}