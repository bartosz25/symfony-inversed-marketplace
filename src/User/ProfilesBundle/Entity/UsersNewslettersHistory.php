<?php
namespace User\ProfilesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
 
/**
 * @ORM\Table(name="users_newsletters_history")
 * @ORM\Entity(repositoryClass="User\ProfilesBundle\Repository\UsersNewslettersHistoryRepository")
 */
class UsersNewslettersHistory extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $historyUser;

  /**
   * @ORM\Column(name="key_unh", type="string", length="255", nullable=false)
   */
  protected $historyKey;

  /**
   * @ORM\Column(name="date_unh", type="datetime", nullable=false)
   */
  protected $historyDate;

  /**
   * @ORM\Column(name="received_unh", type="integer", length="1", nullable=false)
   */
  protected $historyReceived;

  /**
   * @ORM\Column(name="ads_visits_unh", type="integer", length="4", nullable=false)
   */
  protected $historyAdsVisits;

  /**
   * @ORM\Column(name="categories_visits_unh", type="integer", length="4", nullable=false)
   */
  protected $historyCatsVisits;

  /**
   * Getters.
   */
  public function getHistoryUser()
  {
    return $this->historyUser;
  }
  public function getHistoryKey()
  {
    return $this->historyKey;
  }
  public function getHistoryDate()
  {
    return $this->historyDate;
  }
  public function getHistoryReceived()
  {
    return $this->historyReceived;
  }
  public function getHistoryAdsVisits()
  {
    return $this->historyAdsVisits;
  }
  public function getHistoryCatsVisits()
  {
    return $this->historyCatsVisits;
  }
  /**
   * Setters.
   */
  public function setHistoryUser($value)
  {
    $this->historyUser = $value;
  }
  public function setHistoryKey($value)
  {
    $this->historyKey = $value;
  }
  public function setHistoryDate($value)
  {
    $this->historyDate = parent::getDate('');
  }
  public function setHistoryReceived($value)
  {
    $this->historyReceived = $value;
  }
  public function setHistoryAdsVisits($value)
  {
    $this->historyAdsVisits = $value;
  }
  public function setHistoryCatsVisits($value)
  {
    $this->historyCatsVisits = $value;
  }

}