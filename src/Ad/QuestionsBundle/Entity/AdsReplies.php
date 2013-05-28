<?php
namespace Ad\QuestionsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints\NotBlank;  
use Database\MainEntity;

/**
 * @ORM\Table(name="ads_replies")
 * @ORM\Entity(repositoryClass="Ad\QuestionsBundle\Repository\AdsRepliesRepository")
 */
class AdsReplies extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ar", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ar;

  /**
   * @ORM\ManyToOne(targetEntity="Ad\QuestionsBundle\Entity\AdsQuestions")
   * @ORM\JoinColumn(name="ads_questions_id_aq", referencedColumnName="id_aq")
   */
  protected $replyQuestion;
 
  /**
   * @ORM\Column(name="text_ar", type="text", nullable=false)
   */
  protected $replyText;

  /**
   * @ORM\Column(name="date_ar", type="datetime", nullable=false)
   */
  protected $replyDate;

  private $replyType;
  public $types = array(0 => "publique (apparaîtra sur la page d'annonce)", 
  1 => "privée (envoyée en tant que le message privé à l'auteur de la question)");

  /**
   * Getters.
   */
  public function getIdAr()
  {
    return $this->id_ar;
  }
  public function getReplyQuestion()
  {
    return $this->replyQuestion;
  }
  public function getReplyText()
  {
    return $this->replyText;
  }
  public function getReplyDate()
  {
    return $this->replyDate;
  }
  public function getReplyType()
  {
    return $this->replyType;
  }
  /**
   * Setters
   */
  public function setReplyQuestion($value)
  {
    $this->replyQuestion = $value;
  }
  public function setReplyText($value)
  {
    $this->replyText = $value;
  }
  public function setReplyDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->replyDate = $value;
  }
  public function setReplyType($value)
  {
    $this->replyType = $value;
  }
  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // name constraints
    $metadata->addPropertyConstraint('replyText', new NotBlank(array('message' => "Veuillez indiquer la réponse."
    , 'groups' => array('reply'))));
  }
 
}