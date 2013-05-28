<?php
namespace Catalogue\ImagesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;

/**
 * @ORM\Table(name="offers_images_tmp")
 * @ORM\Entity(repositoryClass="Catalogue\ImagesBundle\Repository\OffersImagesTmpRepository")
 */
class OffersImagesTmp extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_oit", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_oit;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $tmpAuthor;

  /**
   * @ORM\Column(name="offer_oit", type="string", length="255", nullable=false)
   */
  protected $tmpOffer;

  /**
   * @ORM\Column(name="name_oit", type="string", length="255", nullable=false)
   */
  protected $tmpName;

  /**
   * @ORM\Column(name="ext_oit", type="string", length="4", nullable=false)
   */
  protected $tmpExt;

  /**
   * @ORM\Column(name="size_oit", type="integer", length="10", nullable=false)
   */
  protected $tmpSize;

  /**
   * @ORM\Column(name="date_oit", type="datetime", nullable=false)
   */
  protected $tmpDate;

  /**
   * Getters.
   */
  public function getIdOit()
  {
    return $this->id_oit;
  }
  public function getTmpOffer()
  {
    return $this->tmpOffer;
  }
  public function getTmpAuthor()
  {
    return $this->tmpAuthor;
  }
  public function getTmpName()
  {
    return $this->tmptmpName;
  }
  public function getTmpExt()
  {
    return $this->tmpExt;
  }
  public function getTmpSize()
  {
    return $this->tmpSize;
  }
  public function getTmpDate()
  {
    return $this->tmpDate;
  }

  /**
   * Setters
   */
  public function setIdOit($value)
  {
    $this->id_oit = $value;
  }
  public function setTmpOffer($value)
  {
    $this->tmpOffer = $value;
  }
  public function setTmpAuthor($value)
  {
    $this->tmpAuthor = $value;
  }
  public function setTmpName($value)
  {
    $this->tmpName = $value;
  }
  public function setTmpExt($value)
  {
    $this->tmpExt = $value;
  }
  public function setTmpSize($value)
  {
    $this->tmpSize = $value;
  }
  public function setTmpDate($value)
  {
    $this->tmpDate = parent::getDate($value);
  }

}