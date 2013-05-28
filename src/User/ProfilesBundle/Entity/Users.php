<?php
namespace User\ProfilesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\True;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Validators\IsUsed;
use Validators\CheckPassword;
use Validators\Csrf;
use Database\MainEntity;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="User\ProfilesBundle\Repository\UsersRepository")
 */
class Users extends MainEntity
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_us", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_us;

  /**
   * @ORM\Column(name="login_us", type="string", length="19", nullable=false)
   */
  protected $login;

  /**
   * @ORM\Column(name="pass_us", type="text", length="255", nullable=false)
   */
  protected $password;

  /**
   * @ORM\Column(name="mail_us", type="text", length="200", nullable=false)
   */
  protected $email;

  /**
   * @ORM\Column(name="registered_us", type="datetime", nullable=false)
   */
  protected $registeredDate;

  /**
   * @ORM\Column(name="last_us", type="datetime", nullable=false)
   */
  protected $lastLogin;

  /**
   * @ORM\Column(name="ip_us", type="text", length="40", nullable=false)
   */
  protected $userIp;

  /**
   * @ORM\Column(name="type_us", type="integer", length="1", nullable=false)
   */
  protected $userType;

  /**
   * @ORM\Column(name="abo_ads_us", type="integer", length="2", nullable=false)
   */
  protected $aboAds;

  /**
   * @ORM\Column(name="abo_cat_us", type="integer", length="2", nullable=false)
   */
  protected $aboCats;

  /**
   * @ORM\Column(name="notes_us", type="integer", length="5", nullable=false)
   */
  protected $userNotes;

  /**
   * @ORM\Column(name="notes_quant_us", type="integer", length="5", nullable=false)
   */
  protected $userNotesQuantity;

  /**
   * @ORM\Column(name="friends_us", type="integer", length="5", nullable=false)
   */
  protected $userFriends;

  /**
   * @ORM\Column(name="system_messages_us", type="integer", length="6", nullable=false)
   */
  protected $userMessagesSystem;

  /**
   * @ORM\Column(name="messages_us", type="integer", length="6", nullable=false)
   */
  protected $userMessages;

  /**
   * @ORM\Column(name="new_messages_us", type="integer", length="6", nullable=false)
   */
  protected $userNewMessages;

  /**
   * @ORM\Column(name="ads_us", type="integer", length="6", nullable=false)
   */
  protected $userAds;

  /**
   * @ORM\Column(name="offers_us", type="integer", length="6", nullable=false)
   */
  protected $userOffers;

  /**
   * @ORM\Column(name="catalogues_us", type="integer", length="6", nullable=false)
   */
  protected $userCatalogues;

  /**
   * @ORM\Column(name="orders_us", type="integer", length="6", nullable=false)
   */
  protected $userOrders;

  /**
   * @ORM\Column(name="addresses_us", type="integer", length="3", nullable=false)
   */
  protected $userAddresses;

  /**
   * @ORM\Column(name="state_us", type="integer", length="1", nullable=false)
   */
  protected $userState;

  /**
   * @ORM\Column(name="activity_type_us", type="integer", length="1", nullable=false)
   */
  protected $userActivityType;

  /**
   * @ORM\Column(name="profile_us", type="text", nullable=false)
   */
  protected $userProfile;

  /**
   * @ORM\Column(name="ebay_login_us", type="string", length="64", nullable=false)
   */
  protected $userEbayLogin;

  /**
   * @ORM\Column(name="prestashop_store_us", type="string", length="255", nullable=false)
   */
  protected $userPrestashopStore;

  public static $em;
  public static $isText;
  public static $staticLogin;
  public static $saltData; 
  public $fingerprinting;
  public $pass1;
  public $pass2;
  private $fingerHash = array('start' => 'dgte%%8nsdo29', 'end' => '%ziqaze*eqw*98*z*%%1*');
  private $averagesAliases = array(0 => "pas d'opinion", 1 => 'niveau 1', 2 => 'niveau 2', 3 => 'niveau 3',
  4 => 'niveau 4', 5 => 'niveau 5', 6 => 'niveau 6');
  private $userTypesAliases = array('pas précisé', 'professionnel', 'particulier');


  public function setLogin($value)
  {
    $this->login = $value;
  }
  public function setEmail($value)
  {
    $this->email = $value;
  } 
  public function setPassword($value)
  {
    $this->password = $value;
  }  
  public function setUserState($value)
  {
    $this->userState = $value;
  }
  public function setUserProfile($value)
  {
    $this->userProfile = $value;
  }
  public function setUserEbayLogin($value)
  {
    $this->userEbayLogin = $value;
  }
  public function setUserType($value)
  {
    $this->userType = $value;
  }
  public function setUserNotes($value)
  {
    $this->userNotes = $value;
  }
  public function setUserNotesQuantity($value)
  {
    $this->userNotesQuantity = $value;
  }
  public function setUserOrders($value)
  {
    $this->userOrders = $value;
  }
  public function setUserAddresses($value)
  {
    $this->userAddresses = $value;
  }
  public function setUserPrestashopStore($value)
  {
    $this->userPrestashopStore = $value;
  }
  public function getIdUs()
  {
    return $this->id_us;
  }
  public function getLogin()
  {
    return $this->login;
  }
  public function getPassword()
  {
    return $this->password;
  }
  public function getEmail()
  {
    return $this->email;
  }
  public function getRegisteredDate()
  {
    return $this->registeredDate->format('Y-m-d');
  }
  public function getUserProfile()
  {
    return $this->userProfile;
  }
  public function getUserEbayLogin()
  {
    return $this->userEbayLogin;
  }
  public function getUserPrestashopStore()
  {
    return $this->userPrestashopStore;
  }
  public function getUserState()
  {
    return $this->userState;
  }
  public function getUserType()
  {
    return $this->userType;
  }
  public function getUserNotes()
  {
    return $this->userNotes;
  }
  public function getUserNotesQuantity()
  {
    return $this->userNotesQuantity;
  }
  public function getUserOrders()
  {
    return $this->userOrders;
  }
  public function getUserAddresses()
  {
    return $this->userAddresses;
  }
  public function getRoles()
  {
    return array('ROLE_ADMIN');
  }
  public function getUsername()
  {
    return $this->login;
  }
  public function getAttributes()
  {
    $average = 0;
    if($this->userNotesQuantity > 0)
    {
      $average = (float)($this->userNotes/$this->userNotesQuantity);
    }
    return array(
      'id' => $this->id_us,
      'email' => $this->email,
      'type' => $this->userType,
      'subscription' => array('ads' => $this->aboAds, 'cats' => $this->aboCats),
      'average' => $average,
      'profile' => $this->userProfile,
      'stats' => array('friends' => $this->userFriends, 'messages' => $this->userMessages, 'ads' => $this->userAds, 
                       'catalogues' => $this->userCatalogues, 'offers' => $this->userOffers, 'messages_system' => $this->userMessagesSystem,
                       'new_messages' => $this->userNewMessages, 'opinions' => $this->userNotesQuantity, 'orders' => $this->userOrders, 'addresses' => $this->userAddresses),
      'fingerprinting' => $this->fingerprinting
    );
  }
  public function getNotActivatedState()
  {
    return 0;
  }
  public function getDeletedState()
  {
    return 2;
  }
  public function getAliasType($v)
  {
    return $this->userTypesAliases[$v];
  }
  /**
   * Gets active state code.
   * @access public
   * @return integer Integer of active account.
   */
  public function getActiveState()
  {
    return 1;
  }

  /** 
   * Sets new user.
   * @access public
   * @param array $data Array with user data.
   * @return void
   */
  public function setRegisterUser($data)
  {
    foreach($data as $k => $value)
    {
      $this->$k = $value;
    }
  }

  /**
   * Form constraints (required constraint is for all fields) :
   * - the same passwords (pass1 and pass2) : register, forgotten password, new password
   * - regex for the passwords (pass1, pass2, password) : register, forgotten password, new password
   * - minimal length for the passwords (pass1, pass2, password) : register, forgotten password, new password
   * - if the password exists for user (password) : new password, login
   * - if the login is used (login) : register, login
   * - regex for the login (login) : register, login
   * - if the e-mail is used (email) : register (return false), forgotten password (return true)
   * - if the e-mail has a correct format (email) : register, forgotten password 
   * - profile is not empty
   */
  public static function loadValidatorMetadata(ClassMetadata $metadata)
  {
    // CSRF constraint
    // $m = new MainEntity();
    if(!self::$isText)
    {
      $metadata->addPropertyConstraint('ticket', new Csrf(array('field' => 'ticket', 'message' => parent::getTicketMessage(), 'sessionToken' => parent::getSessionToken()
      , 'groups' => array('registration', 'forgotten', 'forgottenNew', 'editUser', 'editEmail', 'editPassword'))));
    }
    // login constraints
    $metadata->addPropertyConstraint('login', new NotBlank(array('message' => "Veuillez indiquer le login."
    , 'groups' => array('registration', 'editUser'))));
    $metadata->addPropertyConstraint('login', new MaxLength(array('limit' => 10, 'message' => "Le login peut avoir au maximum 10 caractères."
    , 'groups' => array('registration', 'editUser'))));
    $metadata->addPropertyConstraint('login', new Regex(array('pattern' => '/^([A-Za-z0-9\-_])+$/i', 'message' => "Le login peut être composé de lettres (sans accents), chiffres, - et _."
    , 'groups' => array('registration', 'editUser'))));
    $metadata->addPropertyConstraint('login', new IsUsed(array('em' => self::$em, 'what' => 'register', 'type' => 'falseIfExists', 'field' => 'login', 'message' => "Ce login est déjà utilisé."
    , 'groups' => array('registration'))));
    // password constraints
    $metadata->addPropertyConstraint('password', new NotBlank(array('message' => "Veuillez indiquer le mot de passe."
    , 'groups' => array('editPassword'))));
    $metadata->addPropertyConstraint('pass1', new NotBlank(array('message' => "Veuillez indiquer le mot de passe."
    , 'groups' => array('registration', 'forgottenNew', 'editPassword'))));
    $metadata->addPropertyConstraint('pass2', new NotBlank(array('message' => "Veuillez indiquer le mot de passe."
    , 'groups' => array('registration', 'forgottenNew', 'editPassword'))));
    $metadata->addPropertyConstraint('password', new MinLength(array('limit' => 6, 'message' => "Le mot de passe doit avoir au minimum 6 caractères."
    , 'groups' => array('editPassword'))));
    $metadata->addPropertyConstraint('pass1', new MinLength(array('limit' => 6, 'message' => "Le mot de passe doit avoir au minimum 6 caractères."
    , 'groups' => array('registration', 'forgottenNew', 'editPassword'))));
    $metadata->addPropertyConstraint('password', new Regex(array('pattern' => '/^([A-Za-z0-9\-_.,])+$/i', 'message' => "Le mot de passe peut être composé de lettres (sans accents), chiffres, '_', '-', '.' et ',' ."
    , 'groups' => array())));
    $metadata->addPropertyConstraint('pass1', new Regex(array('pattern' => '/^([A-Za-z0-9\-_.,])+$/i', 'message' => "Le mot de passe peut être composé de lettres (sans accents), chiffres, '_', '-', '.' et ',' ."
    , 'groups' => array('registration', 'forgottenNew', 'editPassword'))));
    $metadata->addPropertyConstraint('password', new CheckPassword(array('em' => self::$em, 'login' => self::$staticLogin, 'saltData' => self::$saltData, 'message' => "L'ancien mot de passe n'est pas correct."
    , 'groups' => array('editPassword'))));
    $metadata->addGetterConstraint('pass2', new True(array('message' => "Les mots de passe doivent être les mêmes."  
    , 'groups' => array('registration', 'forgottenNew', 'editPassword'))));
    // e-mail constraints
    $metadata->addPropertyConstraint('email', new NotBlank(array('message' => "Veuillez indiquer l'adresse e-mail."
    , 'groups' => array('registration', 'forgotten', 'editEmail', 'editUser'))));
    $metadata->addPropertyConstraint('email', new Email(array('checkMX' => true, 'message' => "Veuillez indiquer l'adresse e-mail correcte."
    , 'groups' => array('registration', 'forgotten', 'editEmail', 'editUser'))));
    $metadata->addPropertyConstraint('email', new IsUsed(array('em' => self::$em, 'what' => 'register', 'type' => 'falseIfExists', 'field' => 'email', 'message' => "Cette adresse e-mail est déjà utilisée."
    , 'groups' => array('registration', 'editEmail'))));
    $metadata->addPropertyConstraint('email', new IsUsed(array('em' => self::$em, 'what' => 'code', 'type' => '', 'field' => 'email', 'message' => "Cette adresse e-mail n'existe pas."
    , 'groups' => array('forgotten', 'editUser'))));
    // eBay login
    $metadata->addPropertyConstraint('userEbayLogin', new NotBlank(array('message' => "Veuillez indiquer le login eBay."
    , 'groups' => array('editEbayLogin'))));
    $metadata->addPropertyConstraint('userEbayLogin', new MaxLength(array('limit' => 100, 'message' => "Le login eBay peut avoir au maximum 64 caractères."
    , 'groups' => array('editCard', 'editEbayLogin'))));
    // Prestashop store
    $metadata->addPropertyConstraint('userPrestashopStore', new MaxLength(array('limit' => 255, 'message' => "Le lien vers la boutique ne peut pas compter plus que 255 caractères."
    , 'groups' => array('editCard'))));
  }


  public function isPass2()
  {
    return (bool)($this->pass1 == $this->pass2);
  }

  public function eraseCredentials()
  {
  }
 
  /**
   * Makes fingerprinting proof of connected user.
   * @access public
   * @return void
   */
  public function makeFingerprinting()
  {
    // when uploadify call
    if(isset($_GET['sid']) && isset($_SESSION['serverData']))
    {
      foreach($_SESSION['serverData'] as $s => $session)
      {
        $_SERVER[$s] = $session;
      }
      // unset($_SESSION['serverData']);
    }
    $this->fingerprinting = sha1($this->fingerHash['start'].$_SERVER['HTTP_USER_AGENT']."".$_SERVER['SERVER_ADDR']."".$_SERVER['SERVER_PROTOCOL']."zjablkiem".$_SERVER['HTTP_ACCEPT_ENCODING'].$this->fingerHash['end']);
  }

  /**
   * Compres to fingerprinting proofs (connected user and based on physically user's data).
   * @access public
   * @return bool True if the proofs are correct, false otherwise
   */
  public function checkFingerprinting($fingerprinting)
  {
    $this->makeFingerprinting();
    return (bool)($this->fingerprinting == $fingerprinting);
  }

  /**
   * Gets aliases for user's averages.
   * @access public
   * @return array List with aliases.
   */
  public function getAveragesAliases()
  {
    return $this->averagesAliases;
  }

  /**
   * Gets aliases for user's types.
   * @access public
   * @return array List with aliases.
   */
  public function getUserTypesAliases()
  {
    return $this->userTypesAliases;
  }
}