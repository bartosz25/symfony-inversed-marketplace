<?php
namespace Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Security\AuthenticationToken;
use Security\SaltCellar;
 
class AuthenticationProvider implements AuthenticationProviderInterface
{
  private $userProvider; 

// TEMPORARY !
  public $saltData = array(
    'years' => array(2011 => 'sdq32&', 2012 => 'Ctr%"', 2013 => 'µ*zny'),
    'months' => array(1 => '"""jz23', 2 => '02^sSE', 3 => '^d920^q', 4 => 'GzSSW<<>szoea', 5 => '$ssseC2;9', 
    6 => '|o9gdl;s', 7 => '&;$s$$q', 8 => '[sxdi', 9 => ':KFDe', 10 => '%orueh', 11 => '*soepz*$GzSSW<<>sGzSSW<<>s', 12 => '?,zzezosqQS'),
    'days' => array(1 => '@sdq_', 2 => 'nsdq&"', 3 => '%PzMpiny', 4 => '@34Ezez', 5 => '=*µµ**ds', 6 => '=JsozEEE', 7 => '!osuSD', 8 => '§dsq_E6_---', 9 => '/seok6|--|6|__|-',
    10 => '^tess^', 11 => '/T__^\42&\\', 12 => '%bv', 13 => '+oksq44923', 14 => ')odjnfhdfµµµ387', 15 => '$$dfssed==JS', 16 => '^dsqk44?S', 17 => '..:dsine2', 18 => '_JS2---2UIne', 19 => '.--:2ckssnSS2', 20 => '---PJNsuyz222', 21 => '!djsq-___$s',
    22 => '.24R909dzq^', 23 => '\vcx', 24 => '&n,n,,$', 25 => ',:nbfj', 26 => '!SDQ:!;ii', 27 => '$kmhhggfez', 28 => '%eksu2398', 29 => '++=KJSUY', 30 => '|USH23', 31 => '??sdqsd§!24')
  ); 
  // private $fingerHash = array('start' => 'bi$^nsdo29', 'end' => 'bongsiqo3%%1*');


  public function __construct(UserProviderInterface $userProvider)
  {
// DEBUG echo 'AuthenticationProvider : __construct <br />';
    $this->userProvider = $userProvider;
  }

  /**
   * Authenticates AuthenticationToken user.
   * @access public
   * @param TokenInterface $token Token of logged user.
   * @return TokenInterface if authentication was done correctly. Otherwise, it throwns an exception.
   */
  public function authenticate(TokenInterface $token)
  {
// DEBUG echo 'AuthenticationProvider : authenticate <br />';
    $result = false;
    // regenerate session
    if(time()%2 == 0 && !isset($_POST['sid']))
    {
      session_regenerate_id(false);
    }
    $userLogin = $token->getUsername();
    if(!preg_match('/^([A-Za-z0-9\-_])+$/i', $userLogin))
    {
      $userLogin = '';
    }
    $userRow = $this->userProvider->loadUserByUsername($token->getUsername());
    // first login (directly from form login)
    if(isset($userRow))
    {
      if($token->firstLogin)
      {
        $regDate = strtotime($userRow->getRegisteredDate()); 
        // make salt password
        $cellar = new SaltCellar($this->saltData);
        $salt = $cellar->getSalt(date('Y-m-d', $regDate));
        $passSalt = sha1($cellar->setHash(array('salt' => $salt, 'mdp' => $token->getCredentials(), 'login' => $token->getUsername()), date('n', $regDate)));
        $result = (bool)($userRow->getLogin() == $token->getUsername() && $userRow->getPassword() == $passSalt);
      }
      else
      {
        // check fingerprinting
        $attributes = $token->getAttributes();
        $result = $userRow->checkFingerprinting($attributes['fingerprinting']);
        if($result)
        {
          $result = (bool)($userRow->getPassword() == $token->getCredentials());
        }
      }
// DEBUG echo $passSalt.'<br />'.$userRow->getPassword();die();

      if($result)
      {
        if($userRow->getUserState() != $userRow->getActiveState())
        {
          $token->tokenErrors = array('type' => 'notActivated', 'message' => "Le compte n'a pas été activé.");
          throw new BadCredentialsException("Account wasn't activated.");
        }
// DEBUG echo 'AuthenticationProvider : authenticate : TRUE <br />'; 
        if($token->firstLogin)
        {
          $this->setFirstLogin($userRow->getIdUs());
        }
        $userRow->makeFingerprinting();
        $securityToken = new AuthenticationToken($userRow->getLogin(), $userRow->getPassword(), $token->getProviderKey(),$userRow->getRoles());
        $securityToken->setAttributes($userRow->getAttributes());
        return $securityToken;
      }
    }
    $token->tokenErrors = array('type' => 'invalidData', 'message' => "Les données de connexion ne sont pas correctes.");
    throw new BadCredentialsException('Incorrect login data.');
  }
  
  /**
   * Checks if this is the provider for AuthenticationToken.
   * @access public
   * @param TokenInterface $token User token.
   * @return boolean True if it is the provider for AuthenticationToken, false otherwise.
   */
  public function supports(TokenInterface $token)
  {
// DEBUG echo 'AuthenticationProvider : supports() <br />';
    return $token instanceof AuthenticationToken;
  }

  /**
   * Notifies that user is connected and updates field in the database.
   * @access public
   * @param int $id Id of user to update.
   * @return void
   */
  public function setFirstLogin($id)
  {
    $this->userProvider->getRepository()->updateLastLogin($id);
  }

  // /**
   // * Makes fingerprinting proove of connected user.
   // * @access public
   // * @return void
   // */
  // public function makeFingerprinting()
  // {
    // return sha1($this->fingerHash['start'].$_SERVER['HTTP_USER_AGENT']."".$_SERVER['SERVER_ADDR']."".$_SERVER['SERVER_PROTOCOL']."zjablkiem".$_SERVER['HTTP_ACCEPT_ENCODING'].$this->fingerHash['end']);
  // }

  // /**
   // * Compres to fingerprinting proofs (connected user and based on physically user's data).
   // * @access public
   // * @return bool True if the proofs are correct, false otherwise
   // */
  // public function checkFingerprinting($fingerprinting)
  // {
    // return (bool)($this->makeFingerprinting() == $fingerprinting);
  // }

  public function getProviderKey()
  {
    return $this->providerKey;
  }
 
}