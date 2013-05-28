<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_homepage")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsHomepageRepository")
 */
class AdsHomepage
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $ads_id_ad;

  /**
   * @ORM\Column(name="end_ah", type="date", nullable=false)
   */
  protected $endHomepage;

  /**
   * Getters.
   */
  public function getAdsIdAd()
  {
    return $this->ads_id_ad;
  }
  public function getEndHomepage()
  {
    return $this->endHomepage;
  } 
  /**
   * Setters
   */
  public function setAdsIdAd($value)
  {
    $this->ads_id_ad = $value;
  }
  public function setEndHomepage($value)
  {
    $value->add(new \DateInterval('P5D'));
    $this->endHomepage = $value;
  }

}