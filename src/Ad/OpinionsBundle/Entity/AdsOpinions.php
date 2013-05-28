<?php
namespace Ad\OpinionsBundle\Entity;
  
use Database\MainEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints\NotBlank; 
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\MaxLength;

/**
 * @ORM\Table(name="ads_opinions")
 * @ORM\Entity(repositoryClass="Ad\OpinionsBundle\Repository\AdsOpinionsRepository")
 */
class AdsOpinions extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $opinionAd;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $opinionAuthor;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="for_user_ao", referencedColumnName="id_us")
   */
  protected $opinionReceiver;

  /**
   * @ORM\Column(name="title_ao", type="string", length="150", nullable=false)
   */
  protected $opinionTitle;

  /**
   * @ORM\Column(name="text_ao", type="text", nullable=false)
   */
  protected $opinionText;

  /**
   * @ORM\Column(name="date_ao", type="datetime", nullable=false)
   */
  protected $opinionDate;

  /**
   * @ORM\Column(name="note_ao", type="integer", length="1", nullable=false)
   */
  protected $opinionNote;

  protected $notes = array(1, 2, 3, 4, 5, 6);

  /**
   * Getters.
   */
  public function getOpinionAd()
  {
    return $this->opinionAd;
  }
  public function getOpinionAuthor()
  {
    return $this->opinionAuthor;
  }
  public function getOpinionReceiver()
  {
    return $this->opinionReceiver;
  }
  public function getOpinionTitle()
  {
    return $this->opinionTitle;
  }
  public function getOpinionText()
  {
    return $this->opinionText;
  }
  public function getOpinionDate()
  {
    return $this->opinionDate;
  }
  public function getOpinionNote()
  {
    return $this->opinionNote;
  }
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * Setters
   */
  public function setOpinionAuthor($value)
  {
    $this->opinionAuthor = $value;
  }
  public function setOpinionAd($value)
  {
    $this->opinionAd = $value;
  }
  public function setOpinionReceiver($value)
  {
    $this->opinionReceiver = $value;
  }
  public function setOpinionTitle($value)
  {
    $this->opinionTitle = $value;
  }
  public function setOpinionText($value)
  {
    $this->opinionText = $value;
  } 
  public function setOpinionDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->opinionDate = $value;
  }
  public function setOpinionNote($value)
  {
    $this->opinionNote = $value;
  }
  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // title constraints
    $metadata->addPropertyConstraint('opinionTitle', new NotBlank(array('message' => "Veuillez indiquer le titre."
    , 'groups' => array('write'))));
    $metadata->addPropertyConstraint('opinionTitle', new MaxLength(array('limit' => 150, 'message' => "Le titre peut avoir au maximum 150 caractères."
    , 'groups' => array('write'))));
    // text constraints
    $metadata->addPropertyConstraint('opinionText', new NotBlank(array('message' => "Veuillez indiquer le contenu."
    , 'groups' => array('write'))));
    // note constraints
    $metadata->addPropertyConstraint('opinionNote', new Min(array('limit' => 1, 'message' => "Veuillez indiquer la note."
    , 'groups' => array('write'))));
  }
 
}