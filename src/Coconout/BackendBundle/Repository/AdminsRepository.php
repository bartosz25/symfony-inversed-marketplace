<?php
namespace Coconout\BackendBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminsRepository extends EntityRepository implements UserProviderInterface 
{

   /**
   * Gets user id by login and password.
   * @access public
   * @param string $login User's login.
   * @param string $password User's password.
   * @return True if is correct, false otherwise.
   */
  public function checkPassword($login, $password)
  { echo 'check pass';die();
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us
    FROM UserProfilesBundle:Users u
    WHERE u.password = :password AND u.login = :login AND u.userState = 1")
    ->setParameter('password', $password)
    ->setParameter('login', $login); 
    $result = $query->getResult();
    if(count($result) > 0)
    {
      return true;
    }
    return false;
  }
 

  /**
   * Get user data.
   * @access public
   * @param int $user User's id.
   * @return array List of user's data.
   */
  public function getUser($user)
  {echo 'get user';die();
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.email, u.registeredDate, u.userType, u.userAvg, u.userFriends, u.userMessages,
    u.userAds, u.userOffers, u.userCatalogues, u.userOrders, u.userState, u.userActivityType, u.userProfile
    FROM UserProfilesBundle:Users u
    WHERE u.id_us = :user")
    ->setParameter('user', $user); 
    $result = $query->getResult();
    return $result[0];
  }
 

  /**
   * Loads user by his login.
   * @access public
   * @param string $login User's login.
   * @return User\ProfilesBundle\Entity\User
   */
  public function loadUserByUsername($username)
  {
    return $this->findOneBy(array('login' => $username));
  }

  /**
   * Updates last login informations.
   * @access public
   * @param int $id User's id.
   * @return void
   */
  public function updateLastLogin($id)
  {
    $date = new \DateTime();
    $query = $this->getEntityManager()
    ->createQuery("UPDATE CoconoutBackendBundle:Admins a SET
    a.adminIp = :adminIp,
    a.lastLogin = :lastLogin
    WHERE a.id_ad = :id_ad")
    ->setParameter("id_ad", (int)$id)
    ->setParameter("adminIp", $_SERVER['REMOTE_ADDR'])
    ->setParameter("lastLogin", $date->format('Y-m-d H:i:s')); 
    $query->execute();
  }
 
  public function supportsClass($class)
  {
    return true;
  }

  function refreshUser(UserInterface $user) 
  {
  }

}