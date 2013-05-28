<?php
namespace Frontend\FrontBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stats")
 * @ORM\Entity(repositoryClass="Frontend\FrontBundle\Repository\StatsRepository")
 */
class Stats
{

  /**
   * @ORM\Id
   * @ORM\Column(name="key_st", type="text", length="4")
   */
  protected $key_st;
 
  /**
   * @ORM\Column(name="value_st", type="string", length="10", nullable=false)
   */
  protected $statValue;

  /**
   * @ORM\Column(name="alias_st", type="text", length="30", nullable=false)
   */
  protected $statAlias;

  /**
   * Getters.
   */
  public function getStatValue()
  {
    return $this->statValue;
  }


}