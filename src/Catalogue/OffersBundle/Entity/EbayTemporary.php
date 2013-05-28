<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Regex;
use Validators\ExtendedUrl;
use Ad\ItemsBundle\Entity\AdsParentEntity;

/**
 * @ORM\Table(name="ebay_temporary")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\EbayTemporaryRepository")
 */
class EbayTemporary extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_et", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_et;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $ebayUser;

  /**
   * @ORM\Column(name="catalogues_id_cat", type="integer", nullable=false)
   */
  protected $ebayCatalogue;

  /**
   * @ORM\Column(name="ebay_id_et", type="integer", length="15", nullable=false)
   */
  protected $ebayItemId;

  /**
   * @ORM\Column(name="data_et", type="text", nullable=false)
   */
  protected $ebayData;

  /**
   * @ORM\Column(name="content_et", type="text", nullable=false)
   */
  protected $ebayContent;

  /**
   * @ORM\Column(name="date_et ", type="datetime", nullable=false)
   */
  protected $ebayDate;


  /**
   * Getters.
   */
  public function getIdEt()
  {
    return $this->id_et;
  }
  public function getEbayUser()
  {
    return $this->ebayUser;
  }
  public function getEbayItemId()
  {
    return $this->ebayItemId;
  }
  public function getEbayCatalogue()
  {
    return $this->ebayCatalogue;
  }
  public function getEbayData()
  {
    return $this->ebayData;
  }
  public function getEbayContent()
  {
    return $this->ebayContent;
  }
  public function getEbayDate()
  {
    return $this->ebayDate;
  }

  /**
   * Setters.
   */
  public function setIdEt($value)
  {
    $this->id_et = $value;
  }
  public function setEbayUser($value)
  {
    $this->ebayUser = $value;
  }
  public function setEbayItemId($value)
  {
    $this->ebayItemId = $value;
  }
  public function setEbayCatalogue($value)
  {
    $this->ebayCatalogue = $value;
  }
  public function setEbayData($value)
  {
    $this->ebayData = $value;
  }
  public function setEbayContent($value)
  {
    $this->ebayContent = $value;
  }
  public function setEbayDate($value)
  {
    $this->ebayDate = parent::getDate($value);
  }

  /**
   * Gets mapped object state.
   * @access public
   * @param int $state eBay object state.
   * @return int UneMeilleureOffre object state.
   */
  public function getCorrespondedState($state)
  {
    $adpEnt = new AdsParentEntity;
    if($state == 1000)
    {
      $method = 'getNewObjectState';
    }
    else
    {
      $method = 'getUsedObjectState';
    }
    return $adpEnt->$method();
  }

}