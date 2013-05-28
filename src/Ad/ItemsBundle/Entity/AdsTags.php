<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_tags")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsTagsRepository")
 */
class AdsTags
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $ads_id_ad;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Frontend\FrontBundle\Entity\Tags")
   * @ORM\JoinColumn(name="tags_id_ta", referencedColumnName="id_ta")
   */
  protected $tags_id_ta;

  /**
   * Getters.
   */
  public function getAdsIdAd()
  {
    return $this->ads_id_ad;
  }
  public function getTagsIdTa()
  {
    return $this->tags_id_ta;
  } 
  /**
   * Setters
   */
  public function setAdsIdAd($value)
  {
    $this->ads_id_ad = $value;
  }
  public function setTagsIdTa($value)
  {
    $this->tags_id_ta = $value;
  }

}