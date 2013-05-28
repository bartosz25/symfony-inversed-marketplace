<?php
namespace Ad\ItemsBundle\Entity;
  
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
 * @ORM\Table(name="ads")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsRepository")
 */
class Ads extends AdsParentEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ad", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ad;

// TODO : offers_id_of which can be null
  /**
   * @ORM\Column(name="offers_id_of", type="integer", length="11", nullable=false)
   */
  protected $adOffer;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="users_id_us", referencedColumnName="id_us")
   */
  protected $adAuthor;

  /**
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\Categories")
   * @ORM\JoinColumn(name="categories_id_ca", referencedColumnName="id_ca")
   */
  protected $adCategory;

  /**
   * @ORM\ManyToOne(targetEntity="Geography\CitiesBundle\Entity\Cities")
   * @ORM\JoinColumn(name="cities_id_ci", referencedColumnName="id_ci")
   */
  protected $adCity;

  /**
   * @ORM\Column(name="name_ad", type="string", length="200", nullable=false)
   */
  protected $adName;

  /**
   * @ORM\Column(name="desc_ad", type="text", nullable=false)
   */
  protected $adText;

  /**
   * @ORM\Column(name="visits_ad", type="integer", length="8", nullable=false)
   */
  protected $adVisits;

  /**
   * @ORM\Column(name="start_ad", type="date", nullable=false)
   */
  protected $adStart;

  /**
   * @ORM\Column(name="end_ad", type="date", nullable=false)
   */
  protected $adEnd;
 
  /**
   * @ORM\Column(name="minimun_opinions_ad", type="float", nullable=false)
   */
  protected $adMinOpinion;

  /**
   * @ORM\Column(name="objets_state_ad", type="integer", length="1", nullable=false)
   */
  protected $adObjetState;

  /**
   * @ORM\Column(name="seller_type_ad", type="integer", length="1", nullable=false)
   */
  protected $adSellerType;

  /**
   * @ORM\Column(name="state_ad", type="integer", length="1", nullable=false)
   */
  protected $adState;

  // /**
   // * @ORM\Column(name="buy_from_ad", type="float", nullable=false)
   // */
  // protected $adBuyFrom;

  /**
   * @ORM\Column(name="buy_to_ad", type="float", nullable=false)
   */
  protected $adBuyTo;

  /**
   * @ORM\Column(name="seller_geo_ad", type="integer", length="1", nullable=false)
   */
  protected $adSellerGeo;

  /**
   * @ORM\Column(name="questions_ad", type="integer", length="3", nullable=false)
   */
  protected $adQuestions;

  /**
   * @ORM\Column(name="replies_ad", type="integer", length="3", nullable=false)
   */
  protected $adReplies;

  /**
   * @ORM\Column(name="offers_ad", type="integer", length="3", nullable=false)
   */
  protected $adOffers;

  /**
   * @ORM\Column(name="tax_ad", type="float", nullable=false)
   */
  protected $adTax;

  /**
   * @ORM\Column(name="offer_price_ad", type="float", nullable=false)
   */
  protected $adOfferPrice;

  // private $objetStates = array('peu importe', 'neuf', 'usagé');
  private $sellerGeo = array('partout', 'le même pays que moi', 'la même région que moi', 'la même ville que moi');
  private $payments = array(1 => 'tout accepté', 2 => 'carte bancaire', 3 => 'virement', 4 => 'chèque', 5 => 'liquide');
  private $validityTime = array(1 => '1 semaine', 2 => '2 semaines', 3 => '3 semaines', 4 => '4 semaines', 5 => '5 semaines');
  private $formFields = array();
  public $states = array("en attente d'activation", 'active', 'finie', 'supprimée');
  private $adAtHomePage; // show or not ad at the home page
  private $adCountry;
  private $adPayments = array();
  private $adValidity;
  protected $dynamicFields = array(); // form fields added dynamically

  public function getIdAd()
  {
    return $this->id_ad;
  }
  public function getAdName()
  {
    return $this->adName;
  }
  public function getAdText()
  {
    return $this->adText;
  }
  public function getAdAuthor()
  {
    return $this->adAuthor;
  }
  public function getAdCategory()
  {
    return $this->adCategory;
  }
  public function getAdMinOpinion()
  {
    return $this->adMinOpinion;
  }
  public function getAdObjetState()
  {
    return $this->adObjetState;
  }
  public function getAdSellerType()
  {
    return $this->adSellerType;
  }
// TODO : calculate the difference between two dates
  public function getAdLength()
  {
    return 0;
  }
  public function getFormFields()
  {
    return $this->formFields;
  }
  public function getAdAtHomePage()
  {
    return (int)$this->adAtHomePage;
  }
  // public function getAdBuyFrom()
  // {
    // return (float)$this->adBuyFrom;
  // }
  public function getAdBuyTo()
  {
    return (float)$this->adBuyTo;
  }
  public function getAdCountry()
  {
    return $this->adCountry;
  }
  public function getAdCity()
  {
    return $this->adCity;
  }
  public function getSellerGeo()
  {
    return $this->sellerGeo;
  }
  public function getAdSellerGeo()
  {
    return $this->adSellerGeo;
  }
  public function getPayments()
  {
    return $this->payments;
  }
  public function getAdPayments()
  {
    return $this->adPayments;
  }
  public function getAdValidity()
  {
    return $this->adValidity;
  }
  public function getValidityTime()
  {
    return $this->validityTime;
  }
  public function getAdStart()
  {
    return $this->adStart;
  }
  public function getAdEnd()
  {
    return $this->adEnd;
  }
  public function getPaymentLabel($i)
  {
    return $this->payments[$i];
  }
  public function getAdOffers()
  {
    return $this->adOffers;
  }
  public function getAdOfferPrice()
  {
    return $this->adOfferPrice;
  }
  public function getAdTax()
  {
    return $this->adTax;
  }
  public function getNotAcceptedState()
  {
    return 0;
  }
  public function getActiveState()
  {
    return 1;
  }
  public function getEndedState()
  {
    return 2;
  }
  public function getDeletedState()
  {
    return 3;
  }
  public function getAdObjectState($v)
  {
    return $this->objetStates[$v];
  }
  /**
   * Setters
   */
  public function setAdName($value)
  {
    $this->adName = $value;
  }
  public function setAdText($value)
  {
    $this->adText = $value;
  }
  public function setAdCategory($value)
  {
    $this->adCategory = $value;
  }
  public function setAdAuthor($value)
  {
    $this->adAuthor = $value;
  }
  public function setAdMinOpinion($value)
  {
    $this->adMinOpinion = $value;
  }
  public function setAdObjetState($value)
  {
    $this->adObjetState = $value;
  }
  public function setAdSellerType($value)
  {
    $this->adSellerType = $value;
  }
// TODO : calculate the difference between two dates
  public function setAdLength($value)
  {
    $this->adLength = $value;
  }
  public function setAdAtHomePage($value)
  {
    $this->adAtHomePage = $value;
  }
  public function setFormFields($value)
  {
    $this->formFields = $value;
  }
  // public function setAdBuyFrom($value)
  // {
    // $this->adBuyFrom = $value;
  // }
  public function setAdBuyTo($value)
  {
    $this->adBuyTo = $value;
  }
  public function setAdCountry($value)
  {
    $this->adCountry = $value;
  }
  public function setAdCity($value)
  {
    $this->adCity = $value;
  }
  public function setAdOffer($value)
  {
    $this->adOffer = $value;
  }
  public function setAdSellerGeo($value)
  {
    $this->adSellerGeo = $value;
  }
  public function setAdPayments($value)
  {
    $this->adPayments[] = $value;
  }
  public function setAdValidity($value)
  {
    $this->adValidity = $value;
  }
  public function setAdStart()
  {
    $this->adStart = new \DateTime();
  }
  public function setAdEnd($value = '')
  {
    if($value == '' && array_key_exists($this->getAdValidity(), $this->validityTime))
    {
      $weeksDays = (int)$this->getAdValidity()*7;
      $date = new \DateTime();
      $date->add(new \DateInterval('P'.$weeksDays.'D'));
      $this->adEnd = $date;
    }
    else
    {
      $this->adEnd = $value;
    }
  }
  public function setAdOffers($value)
  {
    $this->adOffers = $value;
  }
  public function setAdOfferPrice($value)
  {
    $this->adOfferPrice = $value;
  }
  public function setAdTax($value)
  {
    $this->adTax = $value;
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
      $method = 'set'.ucfirst($p);
      if(method_exists($this, $method)) $this->$method($param);
      else $this->__set($p, $param);
    }
  }

  /**
   * Dynamic fields handlers
   */
  public function __set($key, $value)
  {
    $this->dynamicFields[$key] = $value;
  }

  public function __get($key)
  {
    $result = "";
    if(isset($this->dynamicFields[$key])) $result = $this->dynamicFields[$key];
    return $result;
  }

  /**
   * Validates ad addition form.
   * @access public
   * @param $context Symfony\Component\Validator\ExecutionContext Currenct context.
   * @return void
   */
  public function validAdd(ExecutionContext $context)
  {
    $contextPath = $context->getPropertyPath();

    if(count($this->adPayments) == 0 || !$this->validatePayments())
    {
      $context->setPropertyPath($contextPath.'.adPayments');
      $context->addViolation("Veuillez indiquer au moins un moyen de paiement accepté.", array(), null);
    }
    // if($this->getAdBuyFrom() > $this->getAdBuyTo())
    // {
      // $property_path = $contextPath.'.adBuyFrom';echo 'error for '.$property_path;
      // $context->setPropertyPath($property_path);
      // $context->addViolation("Le premier prix de la fourchette doit être inférieur au premier", array(), null);
    // }
    foreach($this->formFields as $field)
    {
      $constraints = (array)unserialize($field['constraintsForm']);
      foreach($constraints as $constraint)
      {
        $constraintValidator = $constraint['constraint'].'Validator';
        $cons = new $constraint['constraint']($constraint['options']);
        $validator = new $constraintValidator;
        // $fieldGetter = 'get'.ucfirst($field['codeName']);
        // if(!$validator->isValid($this->$fieldGetter(), $cons))
// echo $field['codeName']."======>".$this->dynamicFields[$field['codeName']]."<br /><br />";
        if(!$validator->isValid($this->dynamicFields[$field['codeName']], $cons))
        {
          $property_path = $contextPath.'.'.$field['codeName'];
// echo 'error for '.$property_path." and validator {$constraintValidator}";
// print_r($constraint['options']);
          $context->setPropertyPath($property_path);
          $context->addViolation($cons->message, array(), null);
        }
      }
    }
  }

  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    $metadata->addConstraint(new Callback(array('methods' => array('validAdd'), 'groups' => array('addAd'))));
    // name constraints
    $metadata->addPropertyConstraint('adName', new NotBlank(array('message' => "Veuillez indiquer le nom de l'annonce."
    , 'groups' => array('addAd'))));
    $metadata->addPropertyConstraint('adName', new MaxLength(array('limit' => 100, 'message' => "Le login peut avoir au maximum 10 caractères."
    , 'groups' => array('addAd'))));
    // categories select constraint
    $metadata->addPropertyConstraint('adCategory', new Min(array('limit' => 1, 'message' => "Veuillez choisir la catégorie."
    , 'groups' => array('addAd'))));
    // countries select constraint
    $metadata->addPropertyConstraint('adCountry', new Min(array('limit' => 1, 'message' => "Veuillez choisir le pays."
    , 'groups' => array('addAd'))));
    // cities select constraint
    $metadata->addPropertyConstraint('adCity', new Min(array('limit' => 1, 'message' => "Veuillez choisir la ville."
    , 'groups' => array('addAd'))));
    // price from
    // $metadata->addPropertyConstraint('adBuyFrom', new Regex(array('pattern' => '/^([0-9.,])+$/i','message' => "Veuillez introduire un chiffre correct"
    // , 'groups' => array('addAd'))));
    // price to 
    $metadata->addPropertyConstraint('adBuyTo', new Regex(array('pattern' => '/^([0-9.,])+$/i','message' => "Veuillez introduire un chiffre correct."
    , 'groups' => array('addAd'))));
    // $metadata->addGetterConstraint('adBuyTo', new True(array('message' => "Le deuxième prix de la fourchette doit être inférieur au premier"  
    // , 'groups' => array('addAd'))));
    // name constraints
    // $metadata->addPropertyConstraint('adPayments', new Min(array('limit' => 1, 'message' => "Veuillez indiquer les moyens de paiement acceptés"
    // , 'groups' => array('addAd'))));
    // tags constraints
    for($i=1; $i < 11; $i++)
    {
      $metadata->addPropertyConstraint('tag'.$i, new MaxLength(array('limit' => 20, 'message' => "Un tag peut avoir au maximum 20 caractères."
      , 'groups' => array('addAd'))));
    }
  }

  /**
   * Gets objet states.
   * @access public
   * @return array List of states.
   */
  public function getObjetStates()
  {
    return $this->objetStates;
  }

  /** 
   * Gets geography condition to SQL query.
   * @access public
   * @param int $geo Geo's condition id.
   * @param array $values Array with values (city, region, country).
   * @return array List with field name as a key.
   */
  public function getGeoToQuery($geo, $values)
  {
    switch($geo)
    {
      case 1:
        return array('co.id_co' => $values['country'], 'label' => $values['countryName'], 'offer' => $values['offerCountry']);
      break;
      case 2:
        return array('r.id_re' => $values['region'], 'label' => $values['regionName'], 'offer' => $values['offerRegion']);
      break;
      case 3:
        return array('c.id_ci' => $values['city'], 'label' => $values['cityName'], 'offer' => $values['offerCity']);
      break;
    }
    return array();
  }

  /**
   * Sets data before insert it into database.
   * @access public
   * @return void
   */
  public function setAddData()
  {
    $this->adVisits = 0;
    $this->setAdStart();
    $this->setAdEnd('');
    $this->adState = 0;
    $this->adReplies = 0;
    $this->adQuestions = 0;
    $this->adOffers = 0;
    $this->adOfferPrice = 0;
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
      $this->adCity = $params['id_ci'];
      $this->adCountry = $params['id_co'];
      $this->adCategory = $params['id_ca'];
    }
  }

  /**
   * Setter for form fields values.
   * @access public
   * @param array $params Params list.
   * @return void
   */
  public function setFormFieldsData($params)
  {
    foreach($params as $p => $param)
    {
      $this->$param['codeName'] = $param['fieldValue'];
    }
    $this->setFormFields($params);
  }

  /**
   * Setter for payment methods.
   * @access public
   * @param array $params Params list.
   * @return void
   */
  public function setPaymentMethods($params)
  {
    foreach($params as $p => $param)
    {
      $this->adPayments[] = (int)$param['payments_id_pa'];
    }
  }

  private function validatePayments()
  {
    foreach($this->adPayments as $payment)
    {
      if(is_array($payment))
      {
        foreach($payment as $payMethod)
        {
          if(!array_key_exists($payMethod, $this->payments))
          {
            return false;
          }
        }
      }
      else
      {
        if(!array_key_exists($payment, $this->payments))
        {
          return false;
        }
      }
    }
    return true;
  }

}