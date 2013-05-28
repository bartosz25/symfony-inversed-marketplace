<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Ad\ItemsBundle\Entity\AdsParentEntity;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Regex;
use Validators\ExtendedUrl;
/**
 * @ORM\Table(name="sync_prestashop")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\SyncPrestashopRepository")
 */
class SyncPrestashop extends AdsParentEntity
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $syncUserId;

  /**
   * @ORM\ManyToOne(targetEntity="Geography\CitiesBundle\Entity\Cities")
   * @ORM\JoinColumn(name="cities_id_ci", referencedColumnName="id_ci")
   */
  protected $syncCity;

  /**
   * @ORM\Column(name="categories_sp", type="text", nullable=false)
   */
  protected $syncCategories;

  /**
   * @ORM\Column(name="products_sp", type="text", nullable=false)
   */
  protected $syncProducts;

  /**
   * @ORM\Column(name="links_sp", type="text", nullable=false)
   */
  protected $syncLinks;

  /**
   * @ORM\Column(name="site_sp", type="string", length="255", nullable=false)
   */
  protected $syncSite;

  /**
   * @ORM\Column(name="key_sp", type="string", length="255", nullable=false)
   */
  protected $syncKey;  

  /**
   * @ORM\Column(name="objets_default_sp", type="integer", length="1", nullable=false)
   */
  protected $syncDefaultState;

  /**
   * @ORM\Column(name="tax_default_sp", type="float", nullable=false)
   */
  protected $syncTax;

  /**
   * @ORM\Column(name="date_sp", type="datetime", nullable=false)
   */
  protected $syncDate;

  private $syncCountry, $states;

  /**
   * Getters.
   */
  public function getSyncUserId()
  {
    return $this->syncUserId;
  }
  public function getSyncCategories()
  {
    return $this->syncCategories;
  }
  public function getSyncProducts()
  {
    return $this->syncProducts;
  }
  public function getSyncLinks()
  {
    return $this->syncLinks;
  }
  public function getSyncSite()
  {
    return $this->syncSite;
  }
  public function getSyncKey()
  {
    return $this->syncKey;
  }
  public function getSyncCity()
  {
    return $this->syncCity;
  } 
  public function getSyncDefaultState()
  {
    return $this->syncDefaultState;
  }
  public function getSyncTax()
  {
    return $this->syncTax;
  }
  public function getSyncDate()
  {
    return $this->syncDate;
  }
  public function getSyncCountry()
  {
    return (int)$this->syncCountry;
  }
  // public function getCountries()
  // {
    // return $this->countries;
  // }
  // public function getCities()
  // {
    // return $this->cities;
  // }
  public function getStates()
  {
    return $this->states;
  }

  /**
   * Setters.
   */
  public function setSyncUserId($value)
  {
    $this->syncUserId = $value;
  }
  public function setSyncCategories($value)
  {
    $this->syncCategories = $value;
  }
  public function setSyncProducts($value)
  {
    $this->syncProducts = $value;
  }
  public function setSyncLinks($value)
  {
    $this->syncLinks = $value;
  }
  public function setSyncSite($value)
  {
    $this->syncSite = $value;
  }
  public function setSyncKey($value)
  {
    $this->syncKey = $value;
  }
  public function setSyncCity($value)
  {
    $this->syncCity = $value;
  }
  public function setSyncDefaultState($value)
  {
    $this->syncDefaultState = $value;
  }
  public function setSyncTax($value)
  {
    $this->syncTax = $value;
  }
  public function setSyncDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    } 
    $this->syncDate = $value;
  }
  public function setSyncCountry($value)
  {
    $this->syncCountry = $value;
  }
  // public function setCountries($value)
  // {
    // $this->countries = $value;
  // }
  // public function setCities($value)
  // {
    // $this->cities = $value;
  // }
  public function setStates($value)
  {
    $this->states = $value;
  }


  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // site address constraints
    $metadata->addPropertyConstraint('syncSite', new NotBlank(array('message' => "Veuillez indiquer l'adresse."
    , 'groups' => array('synchronizePrestashopFirst'))));
    $metadata->addPropertyConstraint('syncSite', new MaxLength(array('limit' => 255, 'message' => "L'adresse peut avoir au maximum 255 caractères."
    , 'groups' => array('synchronizePrestashopFirst'))));
    $metadata->addPropertyConstraint('syncSite', new ExtendedUrl(array('message' => "L'adresse n'existe pas ou votre site est temporairement indisponible."
    , 'groups' => array('synchronizePrestashopFirst'))));
    // key constraints
    $metadata->addPropertyConstraint('syncKey', new NotBlank(array('message' => "Veuillez indiquer la clé."
    , 'groups' => array('synchronizePrestashopFirst'))));
    $metadata->addPropertyConstraint('syncKey', new MaxLength(array('limit' => 255, 'message' => "La clé peut avoir au maximum 255 caractères."
    , 'groups' => array('synchronizePrestashopFirst'))));
    $metadata->addPropertyConstraint('syncKey', new Regex(array('pattern' => '/^([A-Za-z0-9])+$/i', 'message' => "La clé peut contenir seulement les chiffres et les lettres."
    , 'groups' => array('synchronizePrestashopFirst'))));
    // country constraints
    $metadata->addPropertyConstraint('syncCountry', new Min(array('limit' => 1, 'message' => "Veuillez indiquer le pays de votre activité."
    , 'groups' => array('synchronizePrestashopFirst'))));
    // city constraints
    $metadata->addPropertyConstraint('syncCity', new Min(array('limit' => 1, 'message' => "Veuillez indiquer la ville de votre activité."
    , 'groups' => array('synchronizePrestashopFirst'))));
    // objects default state constraints
    $metadata->addPropertyConstraint('syncDefaultState', new NotBlank(array('message' => "Veuillez indiquer l'état par défaut des objets."
    , 'groups' => array('synchronizePrestashopFirst'))));
    // tax default constraints
    $metadata->addPropertyConstraint('syncTax', new NotBlank(array('message' => "Veuillez indiquer la taxe à appliquer."
    , 'groups' => array('synchronizePrestashopFirst'))));
  }



}