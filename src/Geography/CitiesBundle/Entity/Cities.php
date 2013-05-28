<?php
namespace Geography\CitiesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cities")
 * @ORM\Entity(repositoryClass="Geography\CitiesBundle\Repository\CitiesRepository")
 */
class Cities
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ci", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ci;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Geography\RegionsBundle\Entity\Regions")
   * @ORM\JoinColumn(name="regions_id_re", referencedColumnName="id_re")
   */
  protected $cityRegion;

  /**
   * @ORM\Column(name="name_ci", type="string", length="150", nullable=false)
   */
  protected $cityName;

  /**
   * @ORM\Column(name="url_ci", type="string", length="150", nullable=false)
   */
  protected $cityUrl;

  /**
   * @ORM\Column(name="ads_ci", type="integer", length="5", nullable=false)
   */
  protected $cityAds;

  /**
   * @ORM\Column(name="offers_ci", type="integer", length="5", nullable=false)
   */
  protected $cityOffers;

  /**
   * @ORM\Column(name="long_ci", type="text", length="15", nullable=false)
   */
  protected $cityLong;

  /**
   * @ORM\Column(name="lat_ci", type="text", length="15", nullable=false)
   */
  protected $cityLat;

  /**
   * Getters.
   */
  public function getIdCi()
  {
    return $this->id_ci;
  }
  public function getCityRegion()
  {
    return $this->cityRegion;
  }
  public function getCityName()
  {
    return $this->cityName;
  }
  public function getCityLong()
  {
    return $this->cityLong;
  }
  public function getCityLat()
  {
    return $this->cityLat;
  }
  public function getCityAds()
  {
    return $this->cityAds;
  }
  public function getCityOffers()
  {
    return $this->cityOffers;
  }
}