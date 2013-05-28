<?php
namespace User\ProfilesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UsersRepository extends EntityRepository implements UserProviderInterface 
{

  /**
   * Checks if login or e-mail is already used. 
   * @access public
   * @param string $column Column to find.
   * @param string $value Value to check.
   * @return True if is used, false otherwise.
   */
  public function isUsed($column, $value)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us 
    FROM UserProfilesBundle:Users u
    WHERE u.".$column." = :".$column." ")
    ->setParameter($column, $value); 
    $result = $query->getResult();
    if(count($result) > 0)
    {
      return true;
    }
    return false;
  }

  /**
   * Checks if login or e-mail is already used. It checks only active users.
   * @access public
   * @param string $column Column to find.
   * @param string $value Value to check.
   * @return True if is used, false otherwise.
   */
  public function isUsedActive($column, $value)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us 
    FROM UserProfilesBundle:Users u
    WHERE u.".$column." = :".$column." AND u.userState = 1")
    ->setParameter($column, $value); 
    $result = $query->getResult();
    if(count($result) > 0)
    {
      return true;
    }
    return false;
  }

  /**
   * Gets user stats.
   * @access public
   * @param string $column Column to find.
   * @param int $user User's id.
   * @return int The quantity of $column value.
   */
  public function getStats($column, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.".$column." 
    FROM UserProfilesBundle:Users u
    WHERE u.id_us = :user")
    ->setParameter('user', $user); 
    $result = $query->getResult();
    return $result[0][$column];
  }

  /**
   * Gets user id by login and password.
   * @access public
   * @param string $login User's login.
   * @param string $password User's password.
   * @return True if is correct, false otherwise.
   */
  public function checkPassword($login, $password)
  {
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
   * Gets users list.
   * @access public
   * @param array $options Options array.
   * @return array Users list.
   */
  public function getUsersList($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login
    FROM UserProfilesBundle:Users u
    WHERE u.userState = 1
    ORDER BY u.login ASC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }

  /**
   * Get user data.
   * @access public
   * @param int $user User's id.
   * @return array List of user's data.
   */
  public function getUser($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.email, u.registeredDate, u.userType, u.userFriends, u.userEbayLogin, u.userMessages,
    u.userAds, u.userOffers, u.userCatalogues, u.userOrders, u.userState, u.userPrestashopStore, u.userActivityType, u.userProfile,
    (u.userNotes/u.userNotesQuantity) AS average
    FROM UserProfilesBundle:Users u
    WHERE u.id_us = :user")
    ->setParameter('user', (int)$user); 
    $result = $query->getResult();
    return $result[0];
  }

  /**
   * Get users by id's list.
   * @access public
   * @param array $user User's ids.
   * @return array List of user's data.
   */
  public function getUsersByIds($users)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->add('select', 'u.id_us, u.login, u.email, u.userFriends, u.userMessages')
    ->add('from', 'UserProfilesBundle:Users u');
    $qb->add('where', 'u.userState = 1');
    $qb->add('where', $qb->expr()->in('u.id_us', $users));
    $query = $qb->getQuery();
    return $query->getResult();
  }

  /**
   * Checks if user is active. 
   * @access public
   * @param int $user User's id.
   * @return True if is active, false otherwise.
   */
  public function isActive($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.email
    FROM UserProfilesBundle:Users u
    WHERE u.id_us = :user AND u.userState = 1")
    ->setParameter('user', $user); 
    $row = $query->getResult();
    $mail = '';
    if(count($row) > 0)
    {
      $mail = $row[0]['email'];
    }
    return $mail;
  }

  /** 
   * Gets users list with all informations.
   * @access public
   * @param array $options Options array.
   * @return array Users list.
   */
  public function getCompletList($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.email, u.registeredDate,
    u.lastLogin, u.userProfile, u.userState
    FROM UserProfilesBundle:Users u
    ORDER BY u.login ASC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
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
    ->createQuery("UPDATE UserProfilesBundle:Users u SET
    u.userIp = :userIp ,
    u.lastLogin = :lastLogin
    WHERE u.id_us = :id_us")
    ->setParameter("id_us", (int)$id)
    ->setParameter("userIp", $_SERVER['REMOTE_ADDR'])
    ->setParameter("lastLogin", $date->format('Y-m-d H:i:s')); 
    $query->execute();
  }
 
  /**
   * Gets user by not like.
   * @access public
   * @param int $user1 User's id.
   * @param int $user2 User's id.
   * @return int The quantity of $column value.
   */
  public function getNotLike($user1, $user2)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us
    FROM UserProfilesBundle:Users u
    WHERE u.id_us != :user1 AND u.id_us != :user2")
    ->setParameter('user1', $user1)
    ->setParameter('user2', $user2)
    ->setMaxResults(1);
    return $query->getResult();
  }

  /**
   * Updates user stats.
   * @access public
   * @param string $how Increment/decrement (+1 or -1).
   * @param int $user User's id.
   * @param string $field Field to update.
   * @return void.
   */
  public function updateQuantity($how, $user, $field)
  {
    $this->getEntityManager()->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
    ->set('u.'.$field, 'u.'.$field.' '.$how)
    ->where('u.id_us = ?1')
    ->setParameter(1, (int)$user)
    ->getQuery()
    ->execute();
  }


  /** 
   * Gets subscribers list.
   * @access public
   * @param array $options Options array.
   * @return array Users list.
   */
  public function getAllSubscribers($options)
  {
    $where = "u.aboCats > 0 OR u.aboAds > 0";
    if($options['type'] == "cats")
    {
      $where = "u.aboCats > 0";
    }
    elseif($options['type'] == "ads")
    {
      $where = "u.aboAds > 0";
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.aboCats, u.aboAds
    FROM UserProfilesBundle:Users u
    WHERE $where
    ORDER BY u.login ASC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }

  /** 
   * Counts all subscribers.
   * @access public
   * @param string $type Count type.
   * @return integer Subscribed users.
   */
  public function countAllSubscribers($type)
  {
    $where = "u.aboCats > 0 OR u.aboAds > 0";
    if($type == "cats")
    {
      $where = "u.aboCats > 0";
    }
    elseif($type == "ads")
    {
      $where = "u.aboAds > 0";
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(u.id_us) AS counter
    FROM UserProfilesBundle:Users u
    WHERE $where");
    $row = $query->getResult();
    return (int)$row[0]['counter'];
  }

  public function supportsClass($class)
  {
    return true;
  }

  function refreshUser(UserInterface $user) 
  {
  }

}