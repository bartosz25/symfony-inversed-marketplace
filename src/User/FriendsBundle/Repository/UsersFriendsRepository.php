<?php
namespace User\FriendsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class UsersFriendsRepository extends EntityRepository 
{

  /**
   * Gets tags list for one ad.
   * @access public
   * @param int $user1 First user's id
   * @param int $user2 Second user's id
   * @param bool $strict If true, check if contact is activated
   * @return boolean True if they are contact; false otherwise.
   */
  public function areContacts($user1, $user2, $strict = false)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u1.id_us AS user1, u2.id_us AS user2, uf.friendState
    FROM UserFriendsBundle:UsersFriends uf
    JOIN uf.users_id_us_1 u1
    JOIN uf.users_id_us_2 u2
    WHERE (u1.id_us = :user1 AND u2.id_us = :user2) OR (u1.id_us = :user2 AND u2.id_us = :user1) ")
    ->setParameter('user1', $user1)
    ->setParameter('user2', $user2);
    $row = $query->getResult();
    if(count($row) > 0 && $strict && $row[0]['friendState'] == 1)
    {
      return true;
    }
    elseif(count($row) > 0 && $strict && $row[0]['friendState'] != 1)
    {
      return false;
    }
    return (bool)(count($row) > 0);
  }

  /**
   * Checks if the both users have a commun invitation.
   * @access public
   * @param int $user1 First user's id
   * @param int $user2 Second user's id
   * @param boolean $strict If true, checks invitation state.
   * @return boolean True if they have an invitation; false otherwise.
   */
  public function isInvitation($user1, $user2, $strict = true)
  {
    $stateCheck = " AND uf.friendState = 0 ";
    if(!$strict)
    {
      $stateCheck = "";
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT u1.id_us AS user1, u2.id_us AS user2
    FROM UserFriendsBundle:UsersFriends uf
    JOIN uf.users_id_us_1 u1
    JOIN uf.users_id_us_2 u2
    WHERE ((u1.id_us = :user1 AND u2.id_us = :user2) OR (u1.id_us = :user2 AND u2.id_us = :user1)) ".$stateCheck)
    ->setParameter('user1', $user1)
    ->setParameter('user2', $user2);
    $row = $query->getResult();
    return (bool)(count($row) > 0);
  }

  /**
   * Get users contacts to administrate.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Contacts list.
   */
  public function getUserContacts($options, $user)
  {
    $order = "sortedLogin ASC";
    $columns = array("login" => array("sortedLogin"), "date" => "uaa.alertDate");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT u1.id_us AS user1Id, u1.login AS user1Login, u2.id_us AS user2Id, u2.login AS user2Login,
    REPLACE(CONCAT_WS('', u1.login, u2.login), '".$options['login']."', '') AS sortedLogin
    FROM UserFriendsBundle:UsersFriends uf
    JOIN uf.users_id_us_1 u1
    JOIN uf.users_id_us_2 u2
    WHERE (u1.id_us = :user OR u2.id_us = :user) AND uf.friendState = 1
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Get for test.
   * @access public
   * @return boolean True if they have an invitation; false otherwise.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u1.id_us AS user1, u2.id_us AS user2
    FROM UserFriendsBundle:UsersFriends uf
    JOIN uf.users_id_us_1 u1
    JOIN uf.users_id_us_2 u2")
    ->setMaxResults(1);
    $row = $query->getResult();
    return array('id' => '', 'id2' => '', 'user1' => $row[0]['user1'], 'user2' => $row[0]['user2']);
  }

}