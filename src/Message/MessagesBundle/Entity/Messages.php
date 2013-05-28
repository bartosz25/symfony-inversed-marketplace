<?php
namespace Message\MessagesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;

/**
 * @ORM\Table(name="messages")
 * @ORM\Entity(repositoryClass="Message\MessagesBundle\Repository\MessagesRepository")
 */
class Messages extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_me", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_me;

  /** 
   * @ORM\ManyToOne(targetEntity="Message\MessagesBundle\Entity\MessagesContents")
   * @ORM\JoinColumn(name="messages_contents_id_mc", referencedColumnName="id_mc")
   */
  protected $messageContent;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $messageReciper;

  /**
   * @ORM\Column(name="state_me", type="integer", length="1", nullable=false)
   */
  protected $messageState;

  public $messagesAliases = array(0 => '', 1 => 'nouveau', 2 => 'important'); 

  /**
   * Getters.
   */
  public function getIdMe()
  {
    return $this->id_me;
  }
  public function getMessageContent()
  {
    return $this->messageContent;
  }
  public function getMessageReciper()
  {
    return $this->messageReciper;
  }
  public function getMessageState()
  {
    return $this->messageState;
  }
  /**
   * Setters
   */
  public function setIdMe($value)
  {
    $this->id_me = $value;
  }
  public function setMessageContent($value)
  {
    $this->messageContent = $value;
  }
  public function setMessageReciper($value)
  {
    $this->messageReciper = $value;
  }
  public function setMessageState($value)
  {
    $this->messageState = $value;
  }
}