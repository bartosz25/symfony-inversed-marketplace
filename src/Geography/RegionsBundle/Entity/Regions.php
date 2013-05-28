<?php
namespace Geography\RegionsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="regions")
 * @ORM\Entity(repositoryClass="Geography\RegionsBundle\Repository\RegionsRepository")
 */
class Regions
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_re", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_re;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Geography\CountriesBundle\Entity\Countries")
   * @ORM\JoinColumn(name="countries_id_co", referencedColumnName="id_co")
   */
  protected $regionCountry;
  /**
   * @ORM\Column(name="name_re", type="string", length="100", nullable=false)
   */
  protected $regionName;

  /**
   * @ORM\Column(name="url_re", type="text", length="120", nullable=false)
   */
  protected $regionUrl;

  /**
   * @ORM\Column(name="alias_re", type="text", length="4", nullable=false)
   */
  protected $regionAlias;

  /**
   * @ORM\Column(name="ads_re", type="integer", length="4", nullable=false)
   */
  protected $regionAds;

  /**
   * @ORM\Column(name="offers_re", type="integer", length="4", nullable=false)
   */
  protected $regionOffers;
}