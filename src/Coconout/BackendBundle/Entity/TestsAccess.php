<?php
namespace Coconout\BackendBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Database\MainEntity;

/**
 * @ORM\Table(name="tests_access")
 * @ORM\Entity(repositoryClass="Coconout\BackendBundle\Repository\TestsAccessRepository")
 */
class TestsAccess extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ta", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ta;

  /**
   * @ORM\Column(name="label_ta", type="string", length="100", nullable=false)
   */
  protected $testsLabel;

  /**
   * @ORM\Column(name="type_ta", type="integer", length="1", nullable=false)
   */
  protected $testsType;

  /**
   * @ORM\Column(name="last_ta", type="datetime", nullable=false)
   */
  protected $testsLastExecution;

  /**
   * @ORM\Column(name="result_ta", type="integer", length="1", nullable=false)
   */
  protected $testsLastResult;

  /**
   * @ORM\Column(name="config_ta", type="text", nullable=false)
   */
  protected $testsConfig;

  private $results = array(0 => 'error', 1 => 'ok');

  /**
   * Getters.
   */
  public function getIdTa()
  {
    return $this->id_ta;
  }
  public function getTestsLabel()
  {
    return $this->testsLabel;
  }
  public function getTestsType()
  {
    return $this->testsType;
  }
  public function getTestsLastExecution()
  {
    return $this->testsLastExecution;
  }
  public function getTestsLastResult()
  {
    return $this->testsLastResult;
  }
  public function getTestsConfig()
  {
    return unserialize($this->testsConfig);
  }
  public function getResultLabel($result)
  {
    return $this->results[$result];
  }

  /**
   * Setters
   */
  public function setTestsLabel($value)
  {
    $this->testsLabel = $value;
  }
  public function setTestsType($value)
  {
    $this->testsType = $value;
  }
  public function setTestsLastExecution($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->testsLastExecution = $value;
  }
  public function setTestsLastResult($value)
  {
    $this->testsLastResult = $value;
  }
  public function setTestsConfig($value)
  {
    $this->testsConfig = $value;
  }

}