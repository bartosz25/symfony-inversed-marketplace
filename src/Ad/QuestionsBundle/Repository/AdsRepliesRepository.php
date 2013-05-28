<?php
namespace Ad\QuestionsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class AdsRepliesRepository extends EntityRepository 
{

  /**
   * Gets all questions by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Questions list.
   */
  public function getRepliesList($options, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ar.id_ar, aq.questionTitle, SUBSTRING_INDEX(ar.replyText, '.', 2) AS shortContent, DATE_FORMAT(ar.replyDate, '".$options['date']."') AS date,
    a.id_ad, a.adName
    FROM AdQuestionsBundle:AdsReplies ar
    JOIN ar.replyQuestion aq
    JOIN aq.questionAd a
    WHERE a.adAuthor = :user
    ORDER BY  ar.id_ar DESC")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets $reply for $user.
   * @access public
   * @param array $options Option's list.
   * @param int $question Question's id.
   * @param int $user User's id.
   * @return array Option's data.
   */
  public function getReply($reply, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle, ar.id_ar, ar.replyText, ar.replyDate, a.id_ad
    FROM AdQuestionsBundle:AdsReplies ar
    JOIN ar.replyQuestion aq
    JOIN aq.questionAd a
    WHERE ar.id_ar = :reply AND a.adAuthor = :user")
    ->setParameter('reply', $reply)
    ->setParameter('user', $user); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets $question with an eventual response for $user.
   * @access public
   * @param array $options Option's list.
   * @param int $question Question's id.
   * @param int $user User's id.
   * @return array Option's data.
   */
  public function getQuestionWithReply($question, $user)
  {
    $rsm = new ResultSetMapping;
    $rsm->addEntityResult('Ad\QuestionsBundle\Entity\AdsQuestions', 'aq');
    $rsm->addFieldResult('aq', 'id_aq', 'id_aq');
    $rsm->addEntityResult('Ad\QuestionsBundle\Entity\AdsReplies', 'ar');
    $rsm->addFieldResult('ar', 'id_ar', 'id_ar');
    $rsm->addEntityResult('Ad\ItemsBundle\Entity\Ads', 'a');
    $rsm->addFieldResult('a', 'id_ad', 'id_ad');

    $query = $this->getEntityManager()->createNativeQuery('SELECT 
    aq.id_aq, ar.id_ar, a.id_ad
    FROM ads_questions aq 
    JOIN ads a ON a.id_ad = aq.ads_id_ad
    LEFT JOIN ads_replies ar ON ar.ads_questions_id_aq = aq.id_aq
    WHERE aq.id_aq = ? AND a.users_id_us = ?', $rsm);
    $query->setParameter(1, $question);
    $query->setParameter(2, $user);
    $question = $query->getResult();
    if(count($question) > 0)
    {
      return array('id_aq' => $question[0]->getIdAq(), 'id_ar' => $question[1]->getIdAr(),
      'id_ad' => $question[2]->getIdAd());
    }
    return array();
  }

  /**
   * Count replies by $question id.
   * @access public
   * @param int $id Question's id.
   * @return int Replies count.
   */
  public function countReplies($id)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(ar.id_ar)
    FROM AdQuestionsBundle:AdsReplies ar
    WHERE ar.replyQuestion = :question")
    ->setParameter('question', $id); 
    return $query->getSingleScalarResult();
  }

  /**
   * Gets all replies.
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Replies list.
   */
  public function getCompletList($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ar.id_ar, aq.questionTitle, SUBSTRING_INDEX(ar.replyText, '.', 10) AS shortContent, DATE_FORMAT(ar.replyDate, '".$options['date']."') AS date,
    a.id_ad, a.adName
    FROM AdQuestionsBundle:AdsReplies ar
    JOIN ar.replyQuestion aq
    JOIN aq.questionAd a
    ORDER BY  ar.id_ar DESC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets $reply with corresponding ad.
   * @access public
   * @param int $reply Reply's id.
   * @return array Option's data.
   */
  public function getReplyOne($reply)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle, ar.id_ar, ar.replyText, ar.replyDate, a.id_ad
    FROM AdQuestionsBundle:AdsReplies ar
    JOIN ar.replyQuestion aq
    JOIN aq.questionAd a
    WHERE ar.id_ar = :reply")
    ->setParameter('reply', $reply); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets reply for test.
   * @access public
   * @return array Reply data.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle, ar.id_ar, ar.replyText, ar.replyDate, a.id_ad, u.id_us
    FROM AdQuestionsBundle:AdsReplies ar
    JOIN ar.replyQuestion aq
    JOIN aq.questionAd a
    JOIN a.adAuthor u")
    ->setMaxResults(1); 
    $rows = $query->getResult();
    return array('id' => $rows[0]['id_ar'], 'id2' => '', 'user1' => $rows[0]['id_us'], 'user2' => $rows[0]['id_us']);
  }
}