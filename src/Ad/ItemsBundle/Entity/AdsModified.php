<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
/**
 * @ORM\Table(name="ads_modified")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsModifiedRepository")
 */
class AdsModified extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $modifiedAd;

  /**
   * @ORM\Column(name="modifs_am", type="text", nullable=false)
   */
  protected $modifiedText;

  /**
   * @ORM\Column(name="first_modif_am", type="datetime", nullable=false)
   */
  protected $modifiedFirstModif;

  /**
   * @ ORM\Column(name="send_modif_am", type="date", nullable=false)
   */
  // protected $modifiedSendDate;

  /**
   * @ORM\Column(name="last_user_am", type="integer", length="11", nullable=false)
   */
  protected $modifiedLastUser;

  protected $typeLabels = array('content' => "Modification des caractéristiques", 'offer_deleted' => "Une offre a été supprimée de l'annonce",
  'ad_deleted' => "Une annonce a été supprimée", 'ad_accepted' => "Une annonce a été ajoutée", 'offer_accepted' => "Une offre a été acceptée", 'offer_deleted' => "Une offre a été supprimée",
  'ad_ended' => "Une annonce a été terminée");

  /**
   * Getters.
   */
  public function getModifiedAd()
  {
    return $this->modifiedAd;
  }
  public function getModifiedText()
  {
    return $this->modifiedText;
  }
  public function getModifiedFirstModif()
  {
    return $this->modifiedFirstModif;
  }
  // public function getModifiedSendDate()
  // {
    // return $this->modifiedSendDate;
  // }
  public function getModifiedLastUser()
  {
    return $this->modifiedLastUser;
  }
  public function getTypeLabel($type)
  {
    return $this->typeLabels[$type];
  }
  public function getFrequency()
  {
    return 1;
  }
  /**
   * Setters.
   */
  public function setModifiedAd($value)
  {
    $this->modifiedAd = $value;
  }
  public function setModifiedText($value)
  {
    $this->modifiedText = $value;
  }
  public function setModifiedFirstModif($value)
  {
    $this->modifiedFirstModif = $value;
  }
  // public function setModifiedSendDate($value)
  // {
    // $this->modifiedSendDate = $value;
  // } 
  public function setModifiedLastUser($value)
  {
    $this->modifiedLastUser = $value;
  }

}