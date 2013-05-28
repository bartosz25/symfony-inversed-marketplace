<?php
namespace Ad\QuestionsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints\CallbackValidator; 
use Symfony\Component\Validator\Constraints\NotBlank; 
use Symfony\Component\Validator\Constraints\MaxLength;
use Database\MainEntity;

/**
 * @ORM\Table(name="ads_questions")
 * @ORM\Entity(repositoryClass="Ad\QuestionsBundle\Repository\AdsQuestionsRepository")
 */
class AdsQuestions extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_aq", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_aq;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $questionAuthor;

  /**
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $questionAd;

  /**
   * @ORM\Column(name="title_aq", type="string", length="150", nullable=false)
   */
  protected $questionTitle;

  /**
   * @ORM\Column(name="text_aq", type="text", nullable=false)
   */
  protected $questionText;

  /**
   * @ORM\Column(name="date_aq", type="datetime", nullable=false)
   */
  protected $questionDate;

  /**
   * @ORM\Column(name="state_aq", type="integer", length="1", nullable=false)
   */
  protected $questionState;

  public $questionStates = array(0 => 'lue', 1 => 'nouvelle', 2 => 'déjà traitée');

  /**
   * Getters.
   */
  public function getIdAq()
  {
    return $this->id_aq;
  }
  public function getQuestionAuthor()
  {
    return $this->questionAuthor;
  }
  public function getQuestionAd()
  {
    return $this->questionAd;
  }
  public function getQuestionTitle()
  {
    return $this->questionTitle;
  }
  public function getQuestionText()
  {
    return $this->questionText;
  }
  public function getQuestionDate()
  {
    return $this->questionDate;
  }
  public function getQuestionState()
  {
    return $this->questionState;
  }
  /**
   * Setters
   */
  public function setQuestionAuthor($value)
  {
    $this->questionAuthor = $value;
  }
  public function setQuestionAd($value)
  {
    $this->questionAd = $value;
  }
  public function setQuestionTitle($value)
  {
    $this->questionTitle = $value;
  }
  public function setQuestionText($value)
  {
    $this->questionText = $value;
  } 
  public function setQuestionDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->questionDate = $value;
  }
  public function setQuestionState($value)
  {
    $this->questionState = $value;
  }
  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // name constraints
    $metadata->addPropertyConstraint('questionTitle', new NotBlank(array('message' => "Veuillez indiquer le titre."
    , 'groups' => array('write'))));
    $metadata->addPropertyConstraint('questionTitle', new MaxLength(array('limit' => 100, 'message' => "Le titre peut avoir au maximum 10 caractères."
    , 'groups' => array('write'))));
    $metadata->addPropertyConstraint('questionText', new NotBlank(array('message' => "Veuillez indiquer le titre."
    , 'groups' => array('write'))));
  }
 
}