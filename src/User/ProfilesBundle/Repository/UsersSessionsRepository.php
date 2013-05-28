<?php
namespace User\ProfilesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use User\ProfilesBundle\Entity\UsersSessions;

class UsersSessionsRepository extends EntityRepository 
{

  /**
   * Deletes all invalid entries : expired rows and old rows for the $user id.
   * @access public
   * @param int $user Logged user's id.
   * @return void
   */
  public function deleteInvalid($user)
  {
    $this->getEntityManager()->createQueryBuilder()->delete('User\ProfilesBundle\Entity\UsersSessions', 'us')
    ->where('us.users_id_us = ?1')
    ->orWhere('us.expiresUse < ?2')
    ->setParameter(1, (int)$user)
    ->setParameter(2, date('Y-m-d H:i:s'))
    ->getQuery()
    ->execute();
  }

  /**
   * Adds new random session relation.
   * @access public
   * @param int $user Logged user's id.
   * @return string Random id key.
   */
  public function setNewRandom($user)
  {
    if(($random = $this->getEntityManager()->getRepository("UserProfilesBundle:UsersSessions")->getRandomByUser($user)) != "") return $random;
    $random = sha1(time().$_SERVER['REMOTE_ADDR'].rand(0, 19999));
    $useEnt = new UsersSessions;
    $expired = time() + (30*60); // lifetime setted to 30 minutes
    $useEnt->setData(array('users_id_us' => $this->getEntityManager()->getReference('User\ProfilesBundle\Entity\Users', $user), 
      'keyUse' => $random, 
      'sessionUse' => session_id(), 
      'serverUse' => serialize(array('REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'], 
        'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],
        'HTTP_ACCEPT_ENCODING' => $_SERVER['HTTP_ACCEPT_ENCODING'])),
      'expiresUse' => new \DateTime(date('Y-m-d H:i:s', $expired))
    ));
    $this->getEntityManager()->persist($useEnt);
    $this->getEntityManager()->flush();
    return $random;
  }


  /**
   * Get users session data by $random id.
   * @access public
   * @param string $random Alphanumerical random id.
   * @return array List with session data.
   */
  public function getByRandom($random)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, us.keyUse, us.sessionUse, us.serverUse 
    FROM UserProfilesBundle:UsersSessions us
    JOIN us.users_id_us u
    WHERE us.keyUse = :random ")
    ->setParameter("random", $random);
    $result = $query->getResult();
    // if(count($result) > 0)
    // {
      return $result[0];
    // }
    return array();
  }


  /**
   * Get random number by user id.
   * @access public
   * @param int $user User's id.
   * @return string Random data.
   */
  public function getRandomByUser($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT us.keyUse
    FROM UserProfilesBundle:UsersSessions us
    WHERE us.users_id_us = :user")
    ->setParameter("user", $user);
    $result = $query->getResult();
    if(count($result) > 0)
    {
      return $result[0]['keyUse'];
    }
    return "";
  }

}