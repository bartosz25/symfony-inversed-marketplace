<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\CallbackValidator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotBlankValidator;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\MaxLengthValidator;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\MinValidator;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\RegexValidator;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\TrueValidator;

use Ad\ItemsBundle\Entity\AdsParentEntity;

/**
 * @ORM\Table(name="offers")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\OffersRepository")
 */
class Offers extends AdsParentEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_of", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_of;

  /**
   * @ORM\ManyToOne(targetEntity="Geography\CitiesBundle\Entity\Cities")
   * @ORM\JoinColumn(name="cities_id_ci", referencedColumnName="id_ci")
   */
  protected $offerCity;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $offerAuthor;

  /**
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\Categories")
   * @ORM\JoinColumn(name="categories_id_ca", referencedColumnName="id_ca")
   */
  protected $offerCategory;

  /**
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Catalogues")
   * @ORM\JoinColumn(name="catalogues_id_cat", referencedColumnName="id_cat")
   */
  protected $offerCatalogue;

  /**
   * @ORM\Column(name="name_of", type="string", length="150", nullable=false)
   */
  protected $offerName;

  /**
   * @ORM\Column(name="text_of", type="text", nullable=false)
   */
  protected $offerText;

  /**
   * @ORM\Column(name="date_of", type="datetime", nullable=false)
   */
  protected $offerDate;

  /**
   * @ORM\Column(name="objet_state_of", type="integer", length="1", nullable=false)
   */
  protected $offerObjetState;

  /**
   * @ORM\Column(name="price_of", type="float", nullable=false)
   */
  protected $offerPrice;

  /**
   * @ORM\Column(name="tax_of", type="float", nullable=false)
   */
  protected $offerTax;

  /**
   * @ORM\Column(name="external_id_of", type="string", length="20", nullable=false)
   */
  protected $offerExternalId;

  /**
   * @ORM\Column(name="external_system_of", type="string", length="25", nullable=false)
   */
  protected $offerExternalSystem;

  /**
   * @ORM\Column(name="images_of", type="integer", length="1", nullable=false)
   */
  protected $offerImages;

  /**
   * @ORM\Column(name="deleted_of", type="integer", length="1", nullable=false)
   */
  protected $offerDeleted; // 0 - active, 1 - is deleted

  public $states = array("en attente d'activation", 'active', 'finie', 'supprimée');
  public $categoriesList = array();
  private $formFields = array();
  private $cataloguesList = array();
  private $countriesList = array();
  private $citiesList = array();
  private $offerCountry;
  private $offerAd;
  private $deliveryFees;
  private $deliveryYN;
  const NO_CATALOGUES = 1;
  private $messages = array(self::NO_CATALOGUES => "Aucun catalogue est disponible. Veuillez rajouter un catalogue avant de rajouter l'offre.");

  /**
   * Getters.
   */
  public function getId_of()
  {
    return $this->id_of;
  }
  public function getIdOf()
  {
    return $this->id_of;
  }
  public function getOfferAuthor()
  {
    return $this->offerAuthor;
  }
  public function getOfferCity()
  {
    return $this->offerCity;
  }
  public function getOfferCatalogue($forSelect = false)
  {
    return $this->offerCatalogue;
  }
  public function getOfferCategory()
  {
    return $this->offerCategory;
  }
  public function getOfferName()
  {
    return $this->offerName;
  }
  public function getOfferText()
  {
    return $this->offerText;
  }
  public function getOfferDate()
  {
    return $this->offerDate;
  }
  public function getOfferObjetState()
  {
    return $this->offerObjetState;
  }
  public function getOfferPrice()
  {
    return $this->offerPrice;
  }
  public function getOfferTax()
  {
    return $this->offerTax;
  }
  public function getOfferExternalId()
  {
    return $this->offerExternalId;
  }
  public function getOfferExternalSystem()
  {
    return $this->offerExternalSystem;
  }
  public function getOfferImages()
  {
    return $this->offerImages;
  }
  public function getOfferCountry()
  {
    return $this->offerCountry;
  }
  public function getOfferAd()
  {
    return $this->offerAd;
  }
  public function getOfferDeleted()
  {
    return $this->offerDeleted;
  }
  public function getNotDeletedState()
  {
    return 0;
  }
  public function getCataloguesList($forSelect = false)
  {
    $list = $this->cataloguesList;
    if($forSelect)
    {
      $list = array();
      foreach($this->cataloguesList as $c => $catalogue)
      {
        $list[$catalogue['id_cat']] = $catalogue['catalogueName'];
      }
    }
    return $list;
  }
  public function getDeliveryFeesYNList()
  {
    return array('non', 'oui');
  }
  public function getDeliveryYN()
  {
    return $this->deliveryYN;
  }
  public function getPreparedFees($fees)
  {
    $prices = array();
    foreach($fees as $f => $fee)
    {
      $prices[$fee['id_dz']] = (float)$fee['zonePrice'];
    }
    return $prices;
  }
  public function getReasonMessage($reason)
  {
    return $this->messages[$reason];
  }
  /**
   * Setters
   */
  public function setOfferCatalogue($value)
  {
    $this->offerCatalogue = $value;
  }
  public function setOfferAuthor($value)
  {
    $this->offerAuthor = $value;
  }
  public function setOfferCity($value)
  {
    $this->offerCity = $value;
  }
  public function setOfferCategory($value)
  {
    $this->offerCategory = $value;
  }
  public function setOfferName($value)
  {
    $this->offerName = $value;
  }
  public function setOfferText($value)
  {
    $this->offerText = $value;
  }
  public function setOfferDate($value)
  {
    if($value == '')
    {
      $value = new \DateTime();
    }
    $this->offerDate = $value;
  }
  public function setOfferObjetState($value)
  {
    $this->offerObjetState = $value;
  }
  public function setOfferPrice($value)
  {
    $this->offerPrice = $value;
  }
  public function setOfferTax($value)
  {
    $this->offerTax = (float)$value;
  }
  public function setOfferExternalId($value)
  {
    $this->offerExternalId = $value;
  }
  public function setOfferExternalSystem($value)
  {
    $this->offerExternalSystem = $value;
  }
  public function setOfferImages($value)
  {
    $this->offerImages = $value;
  }
  public function setCataloguesList($value)
  {
    $this->cataloguesList = $value;
  }
  public function setOfferCountry($value)
  {
    $this->offerCountry = $value;
  }
  public function setOfferAd($value)
  {
    $this->offerAd = $value;
  }
  public function setDeliveryYN($value)
  {
    $this->deliveryYN = $value;
  }
  public function setOfferDeleted($value)
  {
    $this->offerDeleted = $value;
  }
  /**
   * Sets data after submitting an addition form.
   * @access public
   * @param array $params Params list.
   * @return void
   */
  public function setDataAdded($params)
  {
    foreach($params as $p => $param)
    {
      // $method = 'set'.ucfirst($p);
      $this->$p = $param;
    }
  }
public $fieldsValues;
  /**
   * Validates ad addition form.
   * @access public
   * @param $context Symfony\Component\Validator\ExecutionContext Currenct context.
   * @return void
   */
  public function validAdd(ExecutionContext $context)
  {
    $contextPath = $context->getPropertyPath();
    foreach($this->formFields as $field)
    {
      $constraints = (array)unserialize($field['constraintsForm']);
      foreach($constraints as $constraint)
      {
        $constraintValidator = $constraint['constraint'].'Validator';
        $cons = new $constraint['constraint']($constraint['options']);
        $validator = new $constraintValidator;
        if(!$validator->isValid($this->$field['codeName'], $cons))
        {
          $property_path = $contextPath.'.'.$field['codeName'];
          $context->setPropertyPath($property_path);
          $context->addViolation($cons->message, array(), null);
        }
      }
    }
  }
  /**
   * Checkers.
   */
  public function hasCatalogues()
  {
    return (bool)(count($this->cataloguesList) > 0);
  }

public $siteweb;
public $technology;
  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    $metadata->addConstraint(new Callback(array('methods' => array('validAdd'), 'groups' => array('addOffer'))));
    // name constraints
    $metadata->addPropertyConstraint('offerName', new NotBlank(array('message' => "Veuillez indiquer le titre."
    , 'groups' => array('addOffer'))));
    $metadata->addPropertyConstraint('offerName', new MaxLength(array('limit' => 100, 'message' => "Le titre peut avoir au maximum 10 caractères."
    , 'groups' => array('addOffer'))));
    $metadata->addPropertyConstraint('offerPrice', new MaxLength(array('limit' => 10, 'message' => "Le titre peut avoir au maximum 10 caractères."
    , 'groups' => array('addOffer'))));
    // categories select constraint
    $metadata->addPropertyConstraint('offerCategory', new Min(array('limit' => 1, 'message' => "Veuillez choisir la catégorie."
    , 'groups' => array('addOffer'))));
    // countries select constraint
    $metadata->addPropertyConstraint('offerCountry', new Min(array('limit' => 1, 'message' => "Veuillez choisir le pays."
    , 'groups' => array('addOffer'))));
    // cities select constraint
    $metadata->addPropertyConstraint('offerCity', new Min(array('limit' => 1, 'message' => "Veuillez choisir la ville."
    , 'groups' => array('addOffer'))));
    // tax constraint
    $metadata->addPropertyConstraint('offerTax', new NotBlank(array('message' => "Veuillez indiquer la taxe."
    , 'groups' => array('addOffer'))));
    // tags constraints
    for($i=1; $i < 11; $i++)
    {
      $metadata->addPropertyConstraint('tag'.$i, new MaxLength(array('limit' => 20, 'message' => "Un tag peut avoir au maximum 20 caractères."
      , 'groups' => array('addOffer'))));
    }
  } 

  /**
   * Sets data when ad is edited.
   * @access public
   * @param array $params Params list.
   * @return void
   */
  public function setEditedData($params)
  {
    if(count($params) > 0)
    {
      foreach($params as $p => $param)
      {
        $this->$p = $param;
      }
      $this->offerCategory = $params['id_ca'];
      $this->offerCatalogue = $params['id_cat'];
      $this->offerCountry = $params['id_co'];
      $this->offerCity = $params['id_ci'];
      $this->offerAuthor = $params['id_us'];
    }
  }

}