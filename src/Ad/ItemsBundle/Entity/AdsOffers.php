<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_offers")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsOffersRepository")
 */
class AdsOffers
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ao", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ao;

  /**
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $ads_id_ad;

  /**
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Offers")
   * @ORM\JoinColumn(name="offers_id_of", referencedColumnName="id_of")
   */
  protected $offers_id_of;

  /**
   * @ORM\Column(name="date_ao", type="datetime", nullable=false)
   */
  protected $addedDate;

  /**
   * Getters.
   */
  public function getIdAo()
  {
    return $this->id_ao;
  }
  public function getAdsIdAd()
  {
    return $this->ads_id_ad;
  }
  public function getOffersIdOf()
  {
    return $this->offers_id_of;
  } 
  public function getAddedDate()
  {
    return $this->addedDate;
  } 
  /**
   * Setters
   */
  public function setIdAo($value)
  {
    $this->id_ao = $value;
  }
  public function setAdsIdAd($value)
  {
    $this->ads_id_ad = $value;
  }
  public function setOffersIdOf($value)
  {
    $this->offers_id_of = $value;
  }
  public function setAddedDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->addedDate = $value;
  }

}