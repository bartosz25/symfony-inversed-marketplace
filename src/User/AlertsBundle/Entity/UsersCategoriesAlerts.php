<?php
namespace User\AlertsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
 
/**
 * @ORM\Table(name="users_categories_alerts")
 * @ORM\Entity(repositoryClass="User\AlertsBundle\Repository\UsersCategoriesAlertsRepository")
 */
class UsersCategoriesAlerts extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $alertUser;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\CategoriesModified")
   * @ORM\JoinColumn(name="categories_id_ca", referencedColumnName="categories_id_ca")
   */
  protected $alertCategory;

  /**
   * @ORM\Column(name="last_uca", type="datetime", nullable=false)
   */
  protected $alertDate;

  /**
   * @ORM\Column(name="state_uca", type="integer", length="1", nullable=false)
   */
  protected $alertState;

  /**
   * Getters.
   */
  public function getAlertUser()
  {
    return $this->alertUser;
  }
  public function getAlertCategory()
  {
    return $this->alertCategory;
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
  public function setAlertCategory($value)
  {
    $this->alertCategory = $value;
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