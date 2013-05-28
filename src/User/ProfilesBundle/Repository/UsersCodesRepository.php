<?php
namespace User\ProfilesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class UsersCodesRepository extends EntityRepository 
{

  /**
   * Gets user id by code.
   * @access public
   * @param string $code Code to find.
   * @param int $type Code type.
   * @return array Array with found data
   */
  public function getUser($code, $type)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.email, u.login, u.registeredDate
    FROM UserProfilesBundle:UsersCodes uc
    JOIN uc.user u
    WHERE uc.code = :code AND  uc.type = :type ")
    ->setParameter("code", $code)
    ->setParameter("type", $type); 
    return $query->getResult();
  }

  /**
   * Checks if the user generated the code.
   * @access public
   * @param string $email Users' e-mail.
   * @param int $type Code type.
   * @return bool True if cocde exists, false if not.
   */
  public function hasCode($email, $type)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.email, u.login
    FROM UserProfilesBundle:UsersCodes uc
    JOIN uc.user u
    WHERE u.email = :email AND  uc.type = :type ")
    ->setParameter("email", $email)
    ->setParameter("type", $type);
    if(count($query->getResult()) > 0)
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
  public function getUserWithCode($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.email, u.registeredDate, u.userType, u.userFriends, u.userMessages,
    u.userAds, u.userOffers, u.userCatalogues, u.userOrders, u.userState, u.userActivityType, u.userProfile,
    uc.code
    FROM UserProfilesBundle:UsersCodes uc
    JOIN uc.user u
    WHERE u.id_us = :user AND uc.type = 1")
    ->setParameter('user', $user); 
    $result = $query->getResult();
    if(isset($result[0]['id_us']))
    {
      return $result[0];
    }
    return array();
  }


}