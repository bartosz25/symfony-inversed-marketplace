<?php
namespace User\FriendsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="users_friends")
 * @ORM\Entity(repositoryClass="User\FriendsBundle\Repository\UsersFriendsRepository")
 */
class UsersFriends
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us_1", referencedColumnName="id_us")
   */
  protected $users_id_us_1;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us_2", referencedColumnName="id_us")
   */
  protected $users_id_us_2;
 
  /**
   * @ORM\Column(name="state_uf", type="integer", length="1", nullable=false)
   */
  protected $friendState;


  /**
   * Setters.
   */
  public function setUsersIdUs1($value)
  {
    $this->users_id_us_1 = $value;
  }
  public function setUsersIdUs2($value)
  {
    $this->users_id_us_2 = $value;
  }
  public function setFriendState($value)
  {
    $this->friendState = $value;
  }
  /**
   * Getters.
   */
  public function getUsersIdUs1()
  {
    return $this->users_id_us_1;
  }
  public function getUsersIdUs2()
  {
    return $this->users_id_us_2;
  }
  public function getFriendState()
  {
    return $this->friendState;
  }

}