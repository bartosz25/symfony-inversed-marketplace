<?php
namespace Geography\ZonesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="delivery_zones")
 * @ORM\Entity(repositoryClass="Geography\ZonesBundle\Repository\DeliveryZonesRepository")
 */
class DeliveryZones
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_dz", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_dz;

  /**
   * @ORM\Column(name="name_dz", type="string", length="20", nullable=false)
   */
  protected $zoneName;

  /**
   * Getters.
   */
  public function getIdDz()
  {
    return $this->id_dz;
  }
  public function getZoneName()
  {
    return $this->zoneName;
  }
  /**
   * Setters.
   */
  public function setIdDz($value)
  {
    $this->id_dz = $value;
  }
  public function setZoneName($value)
  {
    $this->zoneName = $value;
  }

}