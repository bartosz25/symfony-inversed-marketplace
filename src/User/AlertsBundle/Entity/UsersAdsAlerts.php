<?php
namespace User\AlertsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
 
/**
 * @ORM\Table(name="users_ads_alerts")
 * @ORM\Entity(repositoryClass="User\AlertsBundle\Repository\UsersAdsAlertsRepository")
 */
class UsersAdsAlerts extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $alertUser;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\AdsModified")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="ads_id_ad")
   */
  protected $alertAd;

  /**
   * @ORM\Column(name="last_uaa", type="datetime", nullable=false)
   */
  protected $alertDate;

  /**
   * @ORM\Column(name="state_uaa", type="integer", length="1", nullable=false)
   */
  protected $alertState;

  /**
   * Getters.
   */
  public function getAlertUser()
  {
    return $this->alertUser;
  }
  public function getAlertAd()
  {
    return $this->alertAd;
  }
  public function getAlertDate()
  {
    return $this->alertDate;
  }
  public function getAlertState()
  {
    return $this->alertState;
  }

  /**
   * Setters.
   */
  public function setAlertUser($value)
  {
    $this->alertUser = $value;
  }
  public function setAlertAd($value)
  {
    $this->alertAd = $value;
  }
  public function setAlertDate($value)
  {
    $this->alertDate = $value;
  }
  public function setAlertState($value)
  {
    $this->alertState = $value;
  }

}