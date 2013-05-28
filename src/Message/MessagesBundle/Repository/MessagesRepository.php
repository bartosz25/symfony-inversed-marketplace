<?php
namespace Message\MessagesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Message\MessagesBundle\Entity\Messages;
use Message\MessagesBundle\Entity\MessagesContents;
use Database\MainEntity;

class MessagesRepository extends EntityRepository
{

  /**
   * Gets contact's invitation message.
   * @access public
   * @param int $user1 Id of user which invites.
   * @param int $user2 Id of invited user.
   * @return array Params of invitation message.
   */
  public function getInviteMessage($user1, $user2)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT m.id_me, mc.id_mc
    FROM MessageMessagesBundle:Messages m
    JOIN m.messageContent mc
    JOIN m.messageReciper r
    JOIN mc.contentAuthor a
    WHERE a.id_us = :user1 AND r.id_us = :user2 AND mc.contentType = 1")
    ->setParameter('user1', $user1)
    ->setParameter('user2', $user2); 
    return $query->getResult();
  }

  /**
   * Gets all messages by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Messages list.
   */
  public function getMessagesListByUser($options, $user)
  {
    $order = "m.messageState DESC, m.id_me DESC";
    $columns = array("titre" => array("mc.contentTitle"), 
    "date" => "m.id_me", "etat" => array("m.messageState" , "m.id_me"), "auteur" => "u.login");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.login, u.id_us, mc.contentTitle, SUBSTRING_INDEX(mc.contentMessage, '.', 2) AS shortContent, DATE_FORMAT(mc.contentDate, '".$options['date']."') AS messageDate, 
    mc.contentType, m.messageState, m.id_me
    FROM MessageMessagesBundle:Messages m
    JOIN m.messageContent mc
    JOIN mc.contentAuthor u
    WHERE m.messageReciper = :user
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets message by id and user.
   * @access public
   * @param array $options Options array.
   * @param int $message Message's id.
   * @param int $user User's id.
   * @return array Message's data.
   */
  public function getMessage($options, $message, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.login, u.id_us, mc.id_mc, mc.contentTitle, SUBSTRING_INDEX(mc.contentMessage, '.', 2) AS shortContent, DATE_FORMAT(mc.contentDate, '".$options['date']."') AS messageDate, 
    mc.contentType, mc.contentMessage AS contentText, m.messageState, m.id_me
    FROM MessageMessagesBundle:Messages m
    JOIN m.messageContent mc
    JOIN mc.contentAuthor u
    WHERE m.id_me = :message AND m.messageReciper = :user")
    ->setParameter('user', $user)
    ->setParameter('message', $message);
    return $query->getResult();
  }

  /**
   * Checks if content message has user's message.
   * @access public
   * @param int $content Content's id.
   * @return boolean True if has, false otherwise.
   */
  public function hasMessages($content)
  {
    $query = $this->getEntityManager()->createQueryBuilder()
    ->select("COUNT(m.id_me)")
    ->from("MessageMessagesBundle:Messages", "m")
    ->where('m.messageContent = '.(int)$content.'')
    ->getQuery();
    if($query->getSingleScalarResult() > 1)
    {
      return true;
    }
    return false;
  }


  /**
   * Sends a new private message
   * @access public
   * @param User\Profiles\Entity\Users $author Instance of Users class for message sender.
   * @param User\Profiles\Entity\Users $receiver Instance of Users class for message receiver.
   * @param array $message Message data (title, type, content).
   * @return Message\MessagesBundle\Entity\Messages Entity of inserted message.
   */
  public function sendPm($author, $receiver, $message)
  {
// TODO : voir si $receiver->getIdUs() retourne quelque chose !
    // first, insert into messages content table
    $mecEnt = new MessagesContents;
    $mecEnt->setContentAuthor($author);
    $mecEnt->setContentTitle($message['title']);
    $mecEnt->setContentMessage($message['content']);
    $mecEnt->setContentDate(new \DateTime());
    $mecEnt->setContentType($message['type']);
    $this->getEntityManager()->persist($mecEnt);
    $this->getEntityManager()->flush();
    // secondly, push it into messages table
    $mesDb = new Messages;
    $mesDb->setMessageContent($mecEnt);
    $mesDb->setMessageReciper($receiver);
    $mesDb->setMessageState($message['state']);
    $this->getEntityManager()->persist($mesDb);
    $this->getEntityManager()->flush();
    // update messages quantity in the users table
    $messageField = 'userMessages';
    if($message['type'] == 2 || $message['type'] == 1)
    {
      $messageField = 'userMessagesSystem';
    }
    $this->getEntityManager()->createQueryBuilder()->update('User\ProfilesBundle\Entity\Users', 'u')
    ->set('u.userNewMessages', 'u.userNewMessages + 1')
    ->set('u.'.$messageField, 'u.'.$messageField.' + 1')
    ->where('u.id_us = ?1')
    ->setParameter(1, $receiver->getIdUs())
    ->getQuery()
    ->execute();

    return $mesDb;
  }

  /**
   * Gets random message.
   * @access public
   * @return array Message's data.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.login, u.id_us, mc.id_mc, mc.contentTitle,  mc.contentType, m.messageState, m.id_me
    FROM MessageMessagesBundle:Messages m
    JOIN m.messageContent mc
    JOIN m.messageReciper u
    ORDER BY m.id_me DESC");
    $row = $query->getResult();
    return array('id' => $row[0]['id_me'], 'id2' => '', 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }

}