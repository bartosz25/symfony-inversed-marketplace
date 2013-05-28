<?php
namespace Security;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Security\AuthenticationToken;
use Security\AuthenticationProvider;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationListener implements ListenerInterface
{

  protected $securityContext;
  protected $authenticationManager;
  protected $providerKey;
  private $logger;

  /**
   * Constructor for listener. The parameters are defined in security.xml.
   */
  public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, $providerKey, LoggerInterface $logger = null)
  {
// DEBUG echo 'AuthenticationListener : __construct <br />'; 
    $this->securityContext = $securityContext;
    $this->authenticationManager = $authenticationManager;
    $this->providerKey = $providerKey;
    $this->logger = $logger;
  }

  /**
   * Handles login request.
   * @access public
   * @param GetResponseEvent $event Handled event
   */
  public function handle(GetResponseEvent $event)
  {
    $request = $event->getRequest();
    $session = $request->getSession();
    $securityToken = $this->securityContext->getToken(); //var_dump($securityToken);//die();
    if($securityToken instanceof AuthenticationToken && $securityToken->isAuthenticated())
    {
      try
      {
        $token = $this->authenticationManager->authenticate($securityToken);	  
        // set new AuthenticationToken
        $this->securityContext->setToken($token);
      }
      catch(BadCredentialsException $e)
      {
        $this->securityContext->setToken(null);
        $session->set('autherror', $securityToken->tokenErrors['message']);
        $this->logger->debug(sprintf('Authentication failed for user: %s. Catched exception : %s',
        $securityToken->getUser(), $e->getMessage()));
      }
    }
  }

}