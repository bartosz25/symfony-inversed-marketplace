<?php
namespace Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;

class AuthenticationEntity extends EntityUserProvider
{
  private $class;
  private $repository;
  private $property;

  /**
   * Overrides parent constructor for be able to use $repository var.
   * @access public
   * @return void
   */
  public function __construct(EntityManager $em, $class, $property = null)
  {
    parent::__construct($em, $class, $property);
    $this->repository = $em->getRepository($class);
    $this->class = $class;
  }

  /**
   * Gets class repository.
   * @access public
   * @return Doctrine\ORM\EntityRepository 
   */
  public function getRepository()
  {
    return $this->repository;
  }

  /** 
   * Method called by authentication provider.
   * @access public
   * @param string $username User's login.
   * @return Doctrine\ORM\EntityRepository Repository object.
   */
  public function loadUserByUsername($username)
  {
    return $this->repository->loadUserByUsername($username);
  }
}