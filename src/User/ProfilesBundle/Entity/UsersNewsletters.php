<?php
namespace User\ProfilesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
/**
 * @ORM\Table(name="users_newsletters")
 * @ORM\Entity(repositoryClass="User\ProfilesBundle\Repository\UsersNewslettersRepository")
 */
class UsersNewsletters extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $newsletterUser;

  /**
   * @ORM\Column(name="created_un", type="datetime", nullable=false)
   */
  protected $newsletterCreated;

  /**
   * @ORM\Column(name="send_un", type="datetime", nullable=false)
   */
  protected $newsletterSend;

  /**
   * @ORM\Column(name="state_un", type="integer", length="1", nullable=false)
   */
  protected $newsletterState;

  /**
   * @ORM\Column(name="visits_un", type="integer", length="3", nullable=false)
   */
  protected $newsletterVisits;

  /**
   * Getters.
   */
  public function getNewsletterUser()
  {
    return $this->newsletterUser;
  }
  public function getNewsletterCreated()
  {
    return $this->newsletterCreated;
  }
  public function getNewsletterSend()
  {
    return $this->newsletterSend;
  }
  public function getNewsletterState()
  {
    return $this->newsletterState;
  }
  public function getNewsletterVisits()
  {
    return $this->newsletterVisits;
  }
  /**
   * Setters.
   */
  public function setNewsletterUser($value)
  {
    $this->newsletterUser = $value;
  }
  public function setNewsletterCreated($value)
  {
    $this->newsletterCreated = parent::getDate('');
  }
  public function setNewsletterSend($value)
  {
    $this->newsletterSend = parent::getDate('');
  }
  public function setNewsletterState($value)
  {
    $this->newsletterState = $value;
  }
  public function setNewsletterVisits($value)
  {
    $this->newsletterVisits = $value;
  }

}