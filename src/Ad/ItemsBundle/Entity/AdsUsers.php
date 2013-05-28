<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_users")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsUsersRepository")
 */
class AdsUsers
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $counterAd;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $counterUser;

  /**
   * @ORM\Column(name="date_au", type="datetime", nullable=false)
   */
  protected $counterDate;

  /**
   * Getters.
   */
  public function getCounterAd()
  {
    return $this->counterAd;
  }
  public function getCounterUser()
  {
    return $this->counterUser;
  } 
  public function getCounterDate()
  {
    return $this->counterDate;
  } 
  /**
   * Setters
   */
  public function setCounterAd($value)
  {
    $this->counterAd = $value;
  }
  public function setCounterUser($value)
  {
    $this->counterUser = $value;
  } 
  public function setCounterDate($value)
  {
    $this->counterDate = $value;
  } 
}