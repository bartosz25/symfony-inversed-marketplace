<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Security;

use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class AuthenticationFormListener extends AbstractAuthenticationListener
{

  private $csrfProvider;

  /**
   * {@inheritdoc}
   */
  public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, array $options = array(), AuthenticationSuccessHandlerInterface $successHandler = null, AuthenticationFailureHandlerInterface $failureHandler = null, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfProviderInterface $csrfProvider = null)
  {
    parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, array_merge(array(
    'username_parameter' => '_username',
    'password_parameter' => '_password',
    'csrf_parameter'     => '_csrf_token',
    'intention'          => 'authenticate',
    'post_only'          => true,
    ), $options), $successHandler, $failureHandler, $logger, $dispatcher);

    $this->csrfProvider = $csrfProvider;
  }

  /**
   * {@inheritdoc}
   */
  protected function attemptAuthentication(Request $request)
  {
    // Detect which authentication method is supported
    if($this->options['post_only'] && 'post' !== strtolower($request->getMethod())) 
    {
      if(null !== $this->logger) 
      {
        $this->logger->debug(sprintf('Authentication method not supported: %s.', $request->getMethod()));
      }
      return null;
    }

    if(null !== $this->csrfProvider) 
    {
      $csrfToken = $request->get($this->options['csrf_parameter'], null, true);

      if(false === $this->csrfProvider->isCsrfTokenValid($this->options['intention'], $csrfToken)) 
      {
        throw new InvalidCsrfTokenException('Invalid CSRF token.');
      }
    }

    // Clean form data with XSS filter
    $data = $this->prepareData(array('username' => trim($request->get($this->options['username_parameter'], null, true)), 
    'password' => $request->get($this->options['password_parameter'], null, true)));

    // Initialize new token
    $token = new AuthenticationToken($data['login'], $data['password'], $this->providerKey);
    $token->firstLogin = true;
    // $request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $username);
    $this->logger->debug('[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[AVANT AUTHENTICATE CALLING]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]');
    return $this->authenticationManager->authenticate($token);
  }

  /**
   * Handle form submit for AuthenticationToken (makes login, password).
   * @access private
   * @param array $data Form data.
   * @return array Prepared data.
   */
  private function prepareData($data)
  {
    $filter = new FilterXss(FilterXss::STRICT_MODE, array());
    return array('login' => $filter->doFilterXss($data['username']), 'password' => $filter->doFilterXss($data['password']));
  }

}