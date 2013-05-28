<?php
namespace Coconout\BackendBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;

/**
 * @ORM\Table(name="tests_access_history")
 * @ORM\Entity(repositoryClass="Coconout\BackendBundle\Repository\TestsAccessHistoryRepository")
 */
class TestsAccessHistory extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_tah", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_tah;

  /**
   * @ORM\ManyToOne(targetEntity="Coconout\BackendBundle\Entity\TestsAccess")
   * @ORM\JoinColumn(name="tests_access_id_ta", referencedColumnName="id_ta")
   */
  protected $testsAccess;

  /**
   * @ORM\Column(name="params_tah", type="string", length="255", nullable=false)
   */
  protected $historyParams;

  /**
   * @ORM\Column(name="date_tah", type="datetime", nullable=false)
   */
  protected $historyDate;

  /**
   * @ORM\Column(name="result_tah", type="integer", length="1", nullable=false)
   */
  protected $historyResult; 

  /**
   * @ORM\Column(name="tested_result_tah", type="integer", length="1", nullable=false)
   */
  protected $historyTestedResult;

  /**
   * Getters.
   */
  public function getIdTah()
  {
    return $this->id_tah;
  }
  public function getTestsAccess()
  {
    return $this->testsAccess;
  }
  public function getHistoryParams()
  {
    return $this->historyParams;
  }
  public function getHistoryDate()
  {
    return $this->historyDate;
  }
  public function getHistoryResult()
  {
    return $this->historyResult;
  }
  public function getHistoryTestedResult()
  {
    return $this->historyTestedResult;
  }

  /**
   * Setters
   */
  public function setHistoryParams($value)
  {
    $this->historyParams = $value;
  }
  public function setTestsAccess($value)
  {
    $this->testsAccess = $value;
  }
  public function setHistoryDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->historyDate = $value;
  }
  public function setHistoryResult($value)
  {
    $this->historyResult = $value;
  }
  public function setHistoryTestedResult($value)
  {
    $this->historyTestedResult = $value;
  }

}