<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_offers_propositions")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsOffersPropositionsRepository")
 */
class AdsOffersPropositions
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $ads_id_ad;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Offers")
   * @ORM\JoinColumn(name="offers_id_of", referencedColumnName="id_of")
   */
  protected $offers_id_of;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $users_id_us;

  /**
   * @ORM\Column(name="date_aop", type="datetime", nullable=false)
   */
  protected $propositionDate;

  /**
   * Getters.
   */
  public function getAdsIdAd()
  {
    return $this->ads_id_ad;
  }
  public function getOffersIdOf()
  {
    return $this->offers_id_of;
  }
  public function getUsersIdUs()
  {
    return $this->users_id_us;
  }
  public function getPropositionDate()
  {
    return $this->propositionDate;
  } 
  /**
   * Setters
   */
  public function setAdsIdAd($value)
  {
    $this->ads_id_ad = $value;
  }
  public function setOffersIdOf($value)
  {
    $this->offers_id_of = $value;
  }
  public function setUsersIdUs($value)
  {
    $this->users_id_us = $value;
  }
  public function setPropositionDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->propositionDate = $value;
  }

}