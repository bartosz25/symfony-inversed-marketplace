<?php
namespace User\ProfilesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
 
 
/**
 * @ORM\Table(name="users_codes")
 * @ORM\Entity(repositoryClass="User\ProfilesBundle\Repository\UsersCodesRepository")
 */
class UsersCodes
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $user;

  /**
   * @ORM\Column(name="code_uc", type="string", length="255", nullable=false)
   */
  protected $code;

  /**
   * @ORM\Column(name="type_uc", type="integer", length="1", nullable=false)
   */
  protected $type;

  /** 
   * Sets new code for user.
   * @access public
   * @param array $data Array with user data.
   * @return void
   */
  public function setNewCode($data)
  {
    foreach($data as $k => $value)
    {
      $this->$k = $value;
    }
  }

}