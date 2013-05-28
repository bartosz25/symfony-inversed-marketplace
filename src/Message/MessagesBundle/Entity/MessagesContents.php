<?php
namespace Message\MessagesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\NotBlank;
use Database\MainEntity;

/**
 * @ORM\Table(name="messages_contents")
 * @ORM\Entity(repositoryClass="Message\MessagesBundle\Repository\MessagesContentsRepository")
 */
class MessagesContents extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_mc", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_mc;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $contentAuthor;

  /**
   * @ORM\Column(name="title_mc", type="string", length="150", nullable=false)
   */
  protected $contentTitle;

  /**
   * @ORM\Column(name="text_mc", type="text", nullable=false)
   */
  protected $contentMessage;

  /**
   * @ORM\Column(name="date_mc", type="datetime", nullable=false)
   */
  protected $contentDate;

  /**
   * @ORM\Column(name="type_mc", type="integer", length="1", nullable=false)
   */
  protected $contentType;

  private $recipersList;
  private $recipersLogins;
  private $isProfile;
  private $message;
  public $typesAliases = array(0 => 'message privé', 1 => 'invitation', 2 => 'question annonce');

  /**
   * Getters.
   */
  public function getIdMc()
  {
    return $this->id_mc;
  }
  public function getContentAuthor()
  {
    return $this->contentAuthor;
  }
  public function getContentTitle()
  {
    return $this->contentTitle;
  }
  public function getContentMessage()
  {
    return $this->contentMessage;
  }
  public function getContentDate()
  {
    return $this->contentDate;
  }
  public function getContentType()
  {
    return $this->contentType;
  }
  public function getRecipersList()
  {
    return $this->recipersList;
  }
  public function getRecipersLogins()
  {
    return $this->recipersLogins;
  }
  public function getIsProfile()
  {
    return $this->isProfile;
  }
  public function getMessage()
  {
    return $this->message;
  }
  public function getSystemMessage()
  {
    return 2;
  }

  /**
   * Setters
   */
  public function setIdMt($value)
  {
    $this->id_mt = $value;
  }
  public function setContentAuthor($value)
  {
    $this->contentAuthor = $value;
  }
  public function setContentTitle($value)
  {
    $this->contentTitle = $value;
  }
  public function setContentMessage($value)
  {
    $this->contentMessage = $value;
  }
  public function setContentDate($value)
  {
    $this->contentDate = $value;
  }
  public function setContentType($value)
  {
    $this->contentType = $value;
  }
  public function setRecipersList($value)
  {
    $this->recipersList = $value;
  }
  public function setIsProfile($value)
  {
    $this->isProfile = $value;
  }
  public function setMessage($value)
  {
    $this->message = $value;
  }
  public function setRecipersLogins($value)
  {
    $this->recipersLogins = $value;
  }

  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // name constraints
    $metadata->addPropertyConstraint('contentTitle', new NotBlank(array('message' => "Veuillez indiquer le titre."
    , 'groups' => array('write'))));
    $metadata->addPropertyConstraint('contentTitle', new MaxLength(array('limit' => 150, 'message' => "Le titre peut avoir au maximum 150 caractères."
    , 'groups' => array('write'))));
    $metadata->addPropertyConstraint('contentMessage', new NotBlank(array('message' => "Veuillez indiquer le message."
    , 'groups' => array('write'))));
  }

}