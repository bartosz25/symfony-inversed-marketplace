<?php
namespace User\AddressesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Validators\BelongsToUser;
use Database\MainEntity;

/**
 * @ORM\Table(name="users_addresses")
 * @ORM\Entity(repositoryClass="User\AddressesBundle\Repository\UsersAddressesRepository")
 */
class UsersAddresses extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ua", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ua;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $addressUser;

  /**
   * @ORM\ManyToOne(targetEntity="Geography\CountriesBundle\Entity\Countries")
   * @ORM\JoinColumn(name="country_id_co", referencedColumnName="id_co")
   */
  protected $addressCountry;

  /**
   * @ORM\Column(name="fname_ua", type="string", length="30", nullable=false)
   */
  protected $addressFirstName;

  /**
   * @ORM\Column(name="lname_ua", type="string", length="50", nullable=false)
   */
  protected $addressLastName;

  /**
   * @ORM\Column(name="postalcode_ua", type="string", length="8", nullable=false)
   */
  protected $addressPostalCode;

  /**
   * @ORM\Column(name="city_ua", type="string", length="30", nullable=false)
   */
  protected $addressCity;

  /**
   * @ORM\Column(name="street_ua", type="string", length="200", nullable=false)
   */
  protected $addressStreet;

  /**
   * @ORM\Column(name="info_ua", type="string", length="300", nullable=false)
   */
  protected $addressInfos;

  /**
   * @ORM\Column(name="state_ua", type="string", length="300", nullable=false)
   */
  protected $addressState;

  private $setCountriesList;
  protected $addressId;
  protected $addressHash;
  protected $addressHashOld;
  protected $addressOldId;
  public static $em;
  public static $staticId;

  /**
   * Getters
   */
  public function getIdUa()
  {
    return $this->id_ua;
  }
  public function getAddressCountry()
  {
    return $this->addressCountry;
  }
  public function getAddressFirstName()
  {
    return $this->addressFirstName;
  }
  public function getAddressLastName()
  {
    return $this->addressLastName;
  }
  public function getAddressPostalCode()
  {
    return $this->addressPostalCode;
  }
  public function getAddressCity()
  {
    return $this->addressCity;
  }
  public function getAddressStreet()
  {
    return $this->addressStreet;
  }
  public function getAddressInfos()
  {
    return $this->addressInfos;
  }
  public function getAddressState()
  {
    return $this->addressState;
  }
  public function getCountriesList()
  {
    return $this->countriesList;
  }
  public function getAddressId()
  {
    return $this->addressId;
  }
  public function getAddressHash()
  {
    return $this->addressHash;
  }
  public function getAddressHashOld()
  {
    return $this->addressHashOld;
  }
  public function getAddressOldId()
  {
    return $this->addressOldId;
  }
  public function getActiveState()
  {
    return 1;
  }
  public function getDeletedState()
  {
    return 2;
  }
  /**
   * Setters
   */
  public function setAddressUser($value)
  {
    $this->addressUser = $value;
  }
  public function setAddressCountry($value)
  {
    $this->addressCountry = $value;
  }
  public function setAddressFirstName($value)
  {
    $this->addressFirstName = $value;
  }
  public function setAddressLastName($value)
  {
    $this->addressLastName = $value;
  }
  public function setAddressPostalCode($value)
  {
    $this->addressPostalCode = $value;
  }
  public function setAddressCity($value)
  {
    $this->addressCity = $value;
  }
  public function setAddressStreet($value)
  {
    $this->addressStreet = $value;
  }
  public function setAddressInfos($value)
  {
    $this->addressInfos = $value;
  }
  public function setAddressState($value)
  {
    $this->addressState = $value;
  }
  public function setCountriesList($value)
  {
    $this->countriesList = $value;
  }
  public function setAddressId($value)
  {
    $this->addressId = $value;
  }
  public function setAddressHash($value)
  {
    $this->addressHash = $value;
  }
  public function setAddressHashOld($value)
  {
    $this->addressHashOld = $value;
  }
  public function setAddressOldId($value)
  {
    $this->addressOldId = $value;
  }

  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // first name
    $metadata->addPropertyConstraint('addressFirstName', new NotBlank(array('message' => "Veuillez indiquer le prénom."
    , 'groups' => array('addAddress', 'firstStep'))));
    $metadata->addPropertyConstraint('addressFirstName', new MaxLength(array('limit' => 30, 'message' => "Le prénom peut avoir au maximum 30 caractères."
    , 'groups' => array('addAddress', 'firstStep'))));
    // last name
    $metadata->addPropertyConstraint('addressLastName', new NotBlank(array('message' => "Veuillez indiquer le nom."
    , 'groups' => array('addAddress', 'firstStep'))));
    $metadata->addPropertyConstraint('addressLastName', new MaxLength(array('limit' => 50, 'message' => "Le nom peut avoir au maximum 50 caractères."
    , 'groups' => array('addAddress', 'firstStep'))));
    // country
    $metadata->addPropertyConstraint('addressCountry', new Min(array('limit' => 1, 'message' => "Veuillez choisir le pays."
    , 'groups' => array('addAddress', 'firstStep'))));
    // postal code
    $metadata->addPropertyConstraint('addressPostalCode', new Regex(array('pattern' => '/^([A-Za-z0-9\-])+$/i', 'message' => "Le code postal peut être composé de : lettre(s), chiffre(s) ou tiret(s)."
    , 'groups' => array('addAddress', 'firstStep'))));
    $metadata->addPropertyConstraint('addressPostalCode', new MaxLength(array('limit' => 8, 'message' => "Le code postal peut avoir au maximum 8 caractères."
    , 'groups' => array('addAddress', 'firstStep'))));
    // city
    $metadata->addPropertyConstraint('addressCity', new NotBlank(array('message' => "Veuillez indiquer la ville."
    , 'groups' => array('addAddress', 'firstStep'))));
    $metadata->addPropertyConstraint('addressCity', new MaxLength(array('limit' => 30, 'message' => "La ville peut avoir au maximum 30 caractères."
    , 'groups' => array('addAddress', 'firstStep'))));
    // street
    $metadata->addPropertyConstraint('addressStreet', new NotBlank(array('message' => "Veuillez indiquer la rue."
    , 'groups' => array('addAddress', 'firstStep'))));
    $metadata->addPropertyConstraint('addressStreet', new MaxLength(array('limit' => 200, 'message' => "La rue peut avoir au maximum 200 caractères."
    , 'groups' => array('addAddress', 'firstStep'))));
    // complementary informations
    $metadata->addPropertyConstraint('addressInfos', new MaxLength(array('limit' => 300, 'message' => "Les informations supplémentaires peuvent avoir au maximum 300 caractères."
    , 'groups' => array('addAddress', 'firstStep'))));
    // address id
    $metadata->addPropertyConstraint('addressId', new BelongsToUser(array('em' => self::$em, 'userId' => self::$staticId, 'what' => 'address', 
    'message' => "Cette adresse n'existe pas."
    , 'groups' => array('firstStep'))));
  }

  /**
   * Sets data used by FirstStep's form.
   * @access public
   * @param array $data Data to set.
   * @return void
   */
  public function setFirstStepData($params)
  {
    foreach($params as $p => $param)
    {
      $setter = 'set'.ucfirst($p);
      $this->$setter($param);
    }
  }
}