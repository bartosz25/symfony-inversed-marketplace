<?php
namespace Coconout\BackendBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="admins")
 * @ORM\Entity(repositoryClass="Coconout\BackendBundle\Repository\AdminsRepository")
 */
class Admins
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ad", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ad;

  /**
   * @ORM\Column(name="in_ad", type="string", length="10", nullable=false)
   */
  protected $login;

  /**
   * @ORM\Column(name="ass_ad", type="text", length="255", nullable=false)
   */
  protected $password;

  /**
   * @ORM\Column(name="reg_ad", type="datetime", nullable=false)
   */
  protected $registeredDate;

  /**
   * @ORM\Column(name="last_ad", type="datetime", nullable=false)
   */
  protected $lastLogin;

  /**
   * @ORM\Column(name="ip_ad", type="text", length="20", nullable=false)
   */
  protected $adminIp;

  public static $em;
  public static $staticLogin;
  public static $saltData; 
  public $fingerprinting;
  public $pass1;
  public $pass2;
  private $fingerHash = array('start' => '%pei*OS93q', 'end' => '_8soz.qu_2U8Sjw..%%');

  public function setLogin($value)
  {
    $this->login = $value;
  }
  public function setPassword($value)
  {
    $this->password = $value;
  }  
  public function getIdUs()
  {
    return $this->id_ad;
  }
  public function getLogin()
  {
    return $this->login;
  }
  public function getPassword()
  {
    return $this->password;
  }
  public function getLastLogin()
  {
    return $this->lastLogin->format('Y-m-d');
  }
  public function getRegisteredDate()
  {
    return $this->registeredDate->format('Y-m-d');
  }
  public function getRoles()
  {
    return array('ROLE_ADMIN_BACKEND');
  }
  public function getUsername()
  {
    return $this->login;
  }
  public function getUserState()
  {
    return 1;
  }
  public function getActiveState()
  {
    return 1;
  }
  public function getAttributes()
  {
    return array('id' => $this->getIdUs(), 'fingerprinting' => $this->fingerprinting);
  }
  public function eraseCredentials()
  {
  }
 
  /**
   * Makes fingerprinting proof of connected user.
   * @access public
   * @return void
   */
  public function makeFingerprinting()
  {
    $this->fingerprinting = sha1($this->fingerHash['start'].$_SERVER['HTTP_USER_AGENT']."".$_SERVER['SERVER_ADDR']."".$_SERVER['SERVER_PROTOCOL']."zjablkiem".$_SERVER['HTTP_ACCEPT_ENCODING'].$this->fingerHash['end']);
  }

  /**
   * Compres to fingerprinting proofs (connected user and based on physically user's data).
   * @access public
   * @return bool True if the proofs are correct, false otherwise
   */
  public function checkFingerprinting($fingerprinting)
  {
    $this->makeFingerprinting();
    return (bool)($this->fingerprinting == $fingerprinting);
  }

}