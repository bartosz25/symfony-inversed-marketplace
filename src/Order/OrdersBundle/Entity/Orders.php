<?php
namespace Order\OrdersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Database\MainEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Min;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MaxLength;

/**
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="Order\OrdersBundle\Repository\OrdersRepository")
 */
class Orders extends MainEntity
{ 

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $orderAd;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users") 
   * @ORM\JoinColumn(name="seller_id_ua", referencedColumnName="id_us")
   */
  protected $orderSeller;

  /**
   * @ORM\ManyToOne(targetEntity="User\ProfilesBundle\Entity\Users")
   * @ORM\JoinColumn(name="buyer_id_us", referencedColumnName="id_us")
   */
  protected $orderBuyer;

  /**
   * @ORM\Column(name="buyer_address_ua", type="integer", nullable=false)
   */
  protected $orderBuyerAddress;

  /**
   * @ORM\Column(name="total_or", type="float", nullable=false)
   */
  protected $orderTotal;

  /**
   * @ORM\Column(name="delivery_or", type="float", nullable=false)
   */
  protected $orderDelivery;

  /**
   * @ORM\Column(name="tax_or", type="float", nullable=false)
   */
  protected $orderTax;

  /**
   * @ORM\Column(name="state_or", type="integer", length="2", nullable=false)
   */
  protected $orderState;

  /**
   * @ORM\Column(name="pref_delivery_or", type="string", length="10", nullable=false)
   */
  protected $orderPreferedDelivery;
 
  /**
   * @ORM\Column(name="payment_or", type="integer", length="1", nullable=false)
   */
  protected $orderPayment;

  /**
   * @ORM\Column(name="carrier_or", type="integer", length="2", nullable=false)
   */
  protected $orderCarrier;

  /**
   * @ORM\Column(name="next_user_or", type="integer", length="11", nullable=false)
   */
  protected $orderNextAction;

  /**
   * @ORM\Column(name="package_ref_or", type="string", length="255", nullable=false)
   */
  protected $orderPackRef;

  /**
   * @ORM\Column(name="comments_or", type="integer", length="5", nullable=false)
   */
  protected $orderComments;
// TODO : supprimer $orderProblem 
  /**
   * @ ORM\Column(name="problem_or", type="integer", length="1", nullable=false)
   */
  // protected $orderProblem;

  protected $deliveryTypes;
  protected $paymentTypes;
  protected $paymentInfos;
  protected $orderComment;
  protected $orderDeliveryFalse;
  protected $showFormStates = array('seller' => array(1, 2, 3, 4, 5, 6, 7),
  'buyer' => array(0, 1, 2, 3));
  protected $statesWithoutValidation = array('seller' => array(),
  'buyer' => array(0, 1, 3, 5));
  protected $orderStatesRelations = array(
    'seller' => array(1 => array(2, 4),
    3 => array(2, 4), 5 => array(6),
    7 => array(8), 8 => array(9)
    ),
    'buyer' => array(0 => array(1),
    2 => array(3), 4 => array(5, 7), 
    6 => array(5, 7), 8 => array(10, 11),
    10 => array(10, 11)
    ),
  );
  protected $orderStates = array(
    0 => '',
    1 => 'Informations sur livraison renseignées',
    2 => 'Informations sur livraison incomplète',
    3 => 'Informations sur livraison corrigée',
    4 => 'Informations sur paiement renseignées',
    5 => 'Informations sur paiement incomplètes', 
    6 => 'Informations sur paiement corrigées', 
    7 => 'Paiement effectué', 
    8 => 'Confirmation de paiement et expédition du produit',
    // 9 => 'Confirmation de paiement sans expédition du produit',
    9 => 'Expédition du produit',
    10 => 'Réception de colis et confirmation de la commande',
    11 => 'Réception de colis et annulation de la commande'
  );
  protected $orderLabels = array(
    "0;1;2;3;" => "Adresse de livraison",
    "4;5;6;7;" => "Paiement",
    "8;9;" => "Expédition du produit",
    "10;11;" => "Commande terminée"
  );
  protected $who;
  protected $descriptions = array(
    0 => "<a href=\"#DELIVERY\">Renseignez l'adresse</a> à laquelle sera livrée le produit achetée.",
    1 => 'Les informations de livraison ont été renseignées.',
    2 => 'Les informations de livraison sont incomplètes. Afin de garantir un bon acheminement du produit, veuillez les compléter.',
    3 => 'Les informations de livraison ont été complétées.',
    4 => 'Les informations de paiement ont été renseignées.',
    5 => 'Les informations de livraison sont incomplètes. Afin de permettre un paiement rapide, veuillez les compléter.', 
    6 => 'Les informations de paiement ont été complétées.', 
    7 => "Le paiement a été effectué. Dès la réception de l'argent, le vendeur va expédier le produit à l'adresse indiquée lors de l'une des précédentes étapes.", 
    8 => "Le vendeur a confirmé la réception du paiement. Il va procéder à l'expédition du produit",
    // 9 => 'Confirmation de paiement sans expédition du produit',
    9 => "Le produit vient d'être expédié par le vendeur.",
    10 => "Le produit a été reçu par l'acheteur. La commande vient d'être positivement terminée.",
    11 => "Le produit a été reçu par l'acheteur. Cependant, une anomalie s'est produite lors de la transaction. La commande vient d'être annulée."
  );

  /**
   * Getters.
   */
  public function getOrderTotal()
  {
    return $this->orderTotal;
  }
  public function getOrderState()
  {
    return $this->orderState;
  }
  public function getOrderDelivery()
  {
    return $this->orderDelivery;
  }
  public function getOrderTax()
  {
    return $this->orderTax;
  }
  public function getOrderPreferedDelivery()
  {
    $deliveries = $this->orderPreferedDelivery;
    if(!is_array($this->orderPreferedDelivery))
    {
      $deliveries = explode(';', $this->orderPreferedDelivery);
    }
    $dels = array();
    foreach($deliveries as $d => $delivery)
    {
      $dels[] = (int)$delivery;
    }
    return $dels;
  }
  public function getOrderPayment()
  {
    return $this->orderPayment;
  }
  public function getOrderCarrier()
  {
    return $this->orderCarrier;
  }
  public function getOrderNextAction()
  {
    return $this->orderNextAction;
  }
  public function getOrderPackRef()
  {
    return $this->orderPackRef;
  }
  public function getPaymentInfos()
  {
    return $this->paymentInfos;
  }
  public function getOrderDeliveryFalse()
  {
    return $this->orderDeliveryFalse;
  }
  public function getOrderProblem()
  {
    return $this->orderProblem;
  }
  public function getOrderStates()
  {
    return $this->orderStates;
  }
  public function getOrderComments()
  {
    return $this->orderComments;
  }
  public function getOrderStateLabel($i)
  {
    return $this->orderStates[$i];
  }
  public function getOrderStatesToSelect()
  {
    $types = array();
    foreach($this->orderStatesRelations[$this->who] as $s => $states)
    {
      if($s >= $this->orderState)
      {
        foreach((array)$states as $st => $state)
        {
          $types[$state] = $this->orderStates[$state];
        }
        break;
      }
    }
    return parent::makeSelectList($types, '');
  }
  public function getWho()
  {
    return $this->who;
  }
  public function getOrderComment()
  {
    return $this->orderComment;
  }
  public function getErrorOrderStates($makeString = false)
  {
    $states = array(2, 5);
    if($makeString)
    {
      $states = implode(',', $states);
    }
    return $states;
  }
  public function getOrderLabels()
  {
    return $this->orderLabels;
  }
  /**
   * Setters
   */
  public function setOrderAd($value)
  {
    $this->orderAd = $value;
  }
  public function setOrderSeller($value)
  {
    $this->orderSeller = $value;
  }
  public function setOrderBuyer($value)
  {
    $this->orderBuyer = $value;
  }
  public function setOrderBuyerAddress($value)
  {
    $this->orderBuyerAddress = $value;
  }
  public function setOrderTotal($value)
  {
    $this->orderTotal = $value;
  }
  public function setOrderState($value)
  {
    $this->orderState = $value;
  }
  public function setOrderDelivery($value)
  {
    $this->orderDelivery = $value;
  }
  public function setOrderTax($value)
  {
    $this->orderTax = $value;
  }
  public function setOrderPreferedDelivery($value = array())
  {
    if(is_array($value)) 
    {
      $vals = array();
      foreach($value as $v => $val)
      {
        $vals[] = (int)$val;
      }
      $this->orderPreferedDelivery = $vals;
    }
	else
    {
      $this->orderPreferedDelivery = $value;
    }
  }
  public function setOrderPayment($value)
  {
    $this->orderPayment = $value;
  }
  public function setOrderCarrier($value)
  {
    $this->orderCarrier = $value;
  }
  public function setOrderNextAction($value)
  {
    $this->orderNextAction = $value;
  }
  public function setOrderPackRef($value)
  {
    $this->orderPackRef = $value;
  }
  public function setPaymentInfos($value)
  {
    $this->paymentInfos = $value;
  }
  public function setOrderDeliveryFalse($value)
  {
    $this->orderDeliveryFalse = $value;
  }
  public function setOrderProblem($value)
  {
    $this->orderProblem = $value;
  }
  public function setDeliveryTypes($value)
  {
    $this->deliveryTypes = $value;
  }
  public function setWho($value)
  {
    $this->who = $value;
  }
  public function setOrderComments($value)
  {
    $this->orderComments = $value;
  }
  public function getDeliveryTypes()
  {
    return $this->deliveryTypes;
  }
  public function setPaymentTypes($value)
  {
    $this->paymentTypes = $value;
  }
  public function getPaymentTypes($removeFirst)
  {
    if($removeFirst)
    {
      unset($this->paymentTypes[1]);
    }
    return $this->paymentTypes;
  }
  public function getOrderStateDelivery()
  {
    return 7;
  }
  public function setOrderComment($value)
  {
    $this->orderComment = $value;
  }
  
  /**
   * Gets step description
   * @access public
   * @return string Step description
   */
  public function getStepDescription()
  {
    return $this->descriptions[$this->orderState];
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

  /**
   * Checks if can show order's step form.
   * @access public
   * @return boolean True if can show form, false otherwise.
   */
  public function canShowForm()
  {
    return (bool)in_array($this->orderState, $this->showFormStates[$this->who]);
  }

  /**
   * Checks if order is at the last state.
   * @access public
   * @return boolean true If it's the last state, false otherwise
   */
  public function isTheLastState()
  {
    return (bool)($this->orderState > 9);
  }

  /**
   * Checks if order is the error state. If it is, the form isn't validated.
   * @access public
   * @return boolean true If it's the last state, false otherwise
   */
  public function isErrorState()
  {
    return (bool)($this->orderState == 2 || $this->orderState == 5);
  }

  /**
   * Form constraints.
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // prefered delivery
    $metadata->addPropertyConstraint('orderPreferedDelivery', new Choice(array('choices' => Delivery::getDeliveryTypes(true), 'multiple' => true, 'min' => 1, 
    'multipleMessage' => "Veuillez choisir au moins un mode de livraison préféré."
    , 'groups' => array('orderFirstStep'))));
    // payment method
    $metadata->addPropertyConstraint('orderPayment', new Min(array('limit' => 2, 'message' => "Veuillez choisir le mode de payement."
    , 'groups' => array('orderFirstStep'))));
    // payment informations (send only by mail)
    $metadata->addPropertyConstraint('paymentInfos', new NotBlank(array('message' => "Veuillez indiquer les informations sur le payement."
    , 'groups' => array('orderNextStep'))));
    // delivery price
    $metadata->addPropertyConstraint('orderDelivery', new NotBlank(array('message' => "Veuillez induqer le montant de livraison."
    , 'groups' => array('orderNextStep'))));
    // carrierOrderFormState
    $metadata->addPropertyConstraint('orderCarrier', new NotBlank(array('message' => "Veuillez indiquer le transporteur."
    , 'groups' => array('orderNextStep'))));
    // order state
    $metadata->addPropertyConstraint('orderState', new NotBlank(array('message' => "Veuillez indiquer l'état de la commande."
    , 'groups' => array('orderNextStep', 'orderFormState'))));
    // order comment
    $metadata->addPropertyConstraint('orderComment', new MaxLength(array('limit' => 300, 'message' => "La longueur maximale du commentaire est 300 caractères."
    , 'groups' => array('orderFirstStep', 'orderNextStep', 'orderFormState'))));
    // tracking number
    $metadata->addPropertyConstraint('orderPackRef', new NotBlank(array('message' => "Veuillez indiquer le numéro de suivi."
    , 'groups' => array('orderDelivery'))));
  }

}