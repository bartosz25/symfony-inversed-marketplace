<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_payments")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsPaymentsRepository")
 */
class AdsPayments
{

  /**
   * @ORM\Id
   * @ ORM\Column(name="ads_id_ad", type="integer")
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $ads_id_ad;

  /**
   * @ORM\Id
   * @ORM\Column(name="payments_id_pa", type="integer")
   */
  protected $payments_id_pa;

  /**
   * Getters.
   */
  public function getAdsIdAd()
  {
    return $this->ads_id_ad;
  }
  public function getPaymentsIdPa()
  {
    return $this->payments_id_pa;
  } 
  /**
   * Setters
   */
  public function setAdsIdAd($value)
  {
    $this->ads_id_ad = $value;
  }
  public function setPaymentsIdPa($value)
  {
    $this->payments_id_pa = $value;
  }

}