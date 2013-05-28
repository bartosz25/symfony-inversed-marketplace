<?php
namespace Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationToken extends AbstractToken implements TokenInterface
{

  public $firstLogin = false;
  public $url = '';
  private $credentials;
  private $providerKey;
  private $user;
  public $tokenErrors = array('type' => '', 'message' => '');

  /**
  * Constructor.
  *
  * @param string $user        The username (like a nickname, email address, etc.)
  * @param string $credentials This usually is the password of the user
  * @param string $providerKey The provider key
  * @param array  $roles       An array of roles
  *
  * @throws \InvalidArgumentException
  */
  public function __construct($user, $credentials, $providerKey, array $roles = array())
  {
    parent::__construct($roles);

    if (empty($providerKey)) {
      throw new \InvalidArgumentException('$providerKey must not be empty.');
    }

    $this->setUser($user);
    $this->credentials = $credentials;
    $this->providerKey = $providerKey;

    parent::setAuthenticated(count($roles) > 0);
  }

	
  public function setAuthenticated($isAuthenticated)
  {
    if ($isAuthenticated) {
      throw new \LogicException('Cannot set this token to trusted after instantiation.');
    }

    parent::setAuthenticated(false);
  }

  public function getCredentials()
  {
    return $this->credentials;
  }
  public function eraseCredentials()
  { 
    return '';
  } 

  public function serialize()
  {
    return serialize(array($this->credentials, $this->providerKey, parent::serialize()));
  }

  public function unserialize($str)
  {
    list($this->credentials, $this->providerKey, $parentStr) = unserialize($str);
    parent::unserialize($parentStr);
  }

  public function getProviderKey()
  {
    return $this->providerKey;
  }

  public function setLogout()
  {
    $this->credentials = '';
    $this->attributes = array();
    $this->roles = array();
    parent::setAuthenticated(false);
  }

}