<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;

/**
 * @ORM\Table(name="offers_delivery_zones")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\OffersDeliveryZonesRepository")
 */
class OffersDeliveryZones extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Geography\ZonesBundle\Entity\DeliveryZones")
   * @ORM\JoinColumn(name="delivery_zones_id_dz", referencedColumnName="id_dz")
   */
  protected $delivery_zones_id_dz;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Offers")
   * @ORM\JoinColumn(name="offers_id_of", referencedColumnName="id_of")
   */
  protected $offers_id_of;

  /**
   * @ORM\Column(name="price_odz", type="float", nullable=false)
   */
  protected $zonePrice;

  /**
   * Getters.
   */
  public function getDeliveryZonesIdDz()
  {
    return $this->delivery_zones_id_dz;
  } 
  public function getOffersIdOf()
  {
    return $this->offers_id_of;
  }
  public function getZonePrice()
  {
    return $this->zonePrice;
  }
  /**
   * Setters
   */
  public function setDeliveryZonesIdDz($value)
  {
    $this->delivery_zones_id_dz = $value;
  }
  public function setOffersIdOf($value)
  {
    $this->offers_id_of = $value;
  }
  public function setZonePrice($value)
  {
    $this->zonePrice = $value;
  }

}