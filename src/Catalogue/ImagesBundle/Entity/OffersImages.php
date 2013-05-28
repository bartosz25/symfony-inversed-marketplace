<?php
namespace Catalogue\ImagesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;
use Symfony\Component\Validator\Constraints\Min;

/**
 * @ORM\Table(name="offers_images")
 * @ORM\Entity(repositoryClass="Catalogue\ImagesBundle\Repository\OffersImagesRepository")
 */
class OffersImages extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_oi", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_oi;

  /**
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Offers")
   * @ORM\JoinColumn(name="offers_id_of", referencedColumnName="id_of")
   */
  protected $imageOffer;

  /**
   * @ORM\Column(name="name_oi", type="string", length="15", nullable=false)
   */
  protected $imageName;

  /**
   * @ORM\Column(name="date_oi", type="datetime", nullable=false)
   */
  protected $imageDate;

  private $offersList;
  
  /**
   * Getters.
   */
  public function getIdOi()
  {
    return $this->id_oi;
  }
  public function getImageOffer()
  {
    return $this->imageOffer;
  }
  public function getImageName()
  {
    return $this->imageName;
  }
  public function getImageDate()
  {
    return $this->imageDate;
  }
  public function getOffersList()
  {    
    $offers = array();
    foreach($this->offersList as $o => $offer)
    {
      $offers[$offer['id_of']] = $offer['offerName'];
    }
    return parent::makeSelectList($offers, "-- sélectionnez l'offre --");
  }

  /**
   * Setters
   */
  public function setIdOi($value)
  {
    $this->id_oi = $value;
  }
  public function setImageOffer($value)
  {
    $this->imageOffer = $value;
  }
  public function setImageName($value)
  {
    $this->imageName = $value;
  }
  public function setImageDate($value)
  {
    $this->imageDate = parent::getDate($value);
  }
  public function setOffersList($value)
  {
    $this->offersList = $value;
  }

  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // countries select constraint
    $metadata->addPropertyConstraint('imageOffer', new Min(array('limit' => 1, 'message' => "Veuillez choisir l'offre."
    , 'groups' => array('addImage'))));
  }

}