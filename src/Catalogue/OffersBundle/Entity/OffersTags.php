<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="offers_tags")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\OffersTagsRepository")
 */
class OffersTags
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Offers")
   * @ORM\JoinColumn(name="offers_id_of", referencedColumnName="id_of")
   */
  protected $offers_id_of;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Frontend\FrontBundle\Entity\Tags")
   * @ORM\JoinColumn(name="tags_id_ta", referencedColumnName="id_ta")
   */
  protected $tags_id_ta;

  /**
   * Getters.
   */
  public function getOffersIdOf()
  {
    return $this->offers_id_of;
  }
  public function getTagsIdTa()
  {
    return $this->tags_id_ta;
  } 
  /**
   * Setters
   */
  public function setOffersIdOf($value)
  {
    $this->offers_id_of = $value;
  }
  public function setTagsIdTa($value)
  {
    $this->tags_id_ta = $value;
  }

}