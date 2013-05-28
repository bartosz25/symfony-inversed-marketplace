<?php
namespace User\ProfilesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
/**
 * @ORM\Table(name="users_sessions")
 * @ORM\Entity(repositoryClass="User\ProfilesBundle\Repository\UsersSessionsRepository")
 */
class UsersSessions extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $users_id_us;

  /**
   * @ORM\Column(name="key_use", type="string", length="255", nullable=false)
   */
  protected $keyUse;

  /**
   * @ORM\Column(name="session_use", type="string", length="255", nullable=false)
   */
  protected $sessionUse;

  /**
   * @ORM\Column(name="server_use", type="text", nullable=false)
   */
  protected $serverUse;

  /**
   * @ORM\Column(name="expires_use", type="datetime", nullable=false)
   */
  protected $expiresUse;

  public function setUsers_id_us($value)
  {
    $this->users_id_us = $value;
  }

  public function setKeyUse($value)
  {
    $this->keyUse = $value;
  }
  public function setSessionUse($value)
  {
    $this->sessionUse = $value;
  }
  public function setServerUse($value)
  {
    $this->serverUse = $value;
  }
  public function setExpiresUse($value)
  {
    $this->expiresUse = $value;
  }
}