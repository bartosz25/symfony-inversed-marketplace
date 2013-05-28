<?php
namespace Ad\QuestionsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class AdsQuestionsRepository extends EntityRepository 
{

  /**
   * Gets all questions by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Questions list.
   */
  public function getQuestionsList($options, $user)
  {
    $order = "aq.questionState DESC, aq.id_aq DESC";
    $columns = array("titre" => array("aq.questionTitle"), "date" => "aq.id_aq", "etat" => "aq.questionState", "auteur" => "u.login");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle, SUBSTRING_INDEX(aq.questionText, '.', 2) AS shortContent, DATE_FORMAT(aq.questionDate, '".$options['date']."') AS date, aq.questionState,
    u.id_us, u.login, a.id_ad, a.adName
    FROM AdQuestionsBundle:AdsQuestions aq
    JOIN aq.questionAd a
    JOIN aq.questionAuthor u
    WHERE a.adAuthor = :user
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets $question for $user.
   * @access public
   * @param array $options Option's list.
   * @param int $question Question's id.
   * @param int $user User's id.
   * @return array Option's data.
   */
  public function getQuestion($options, $question, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle,  aq.questionText , DATE_FORMAT(aq.questionDate, '".$options['date']."') AS date, aq.questionState,
    u.id_us, u.login, u.email, a.id_ad, a.adName, c.categoryUrl
    FROM AdQuestionsBundle:AdsQuestions aq
    JOIN aq.questionAd a
    JOIN a.adCategory c
    JOIN aq.questionAuthor u
    WHERE aq.id_aq = :question AND a.adAuthor = :user")
    ->setParameter('question', $question)
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
   * Gets all questions from the database.
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Questions list.
   */
  public function getCompletList($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle, SUBSTRING_INDEX(aq.questionText, '.', 11) AS shortContent, DATE_FORMAT(aq.questionDate, '".$options['date']."') AS date, aq.questionState,
    u.id_us, u.login, a.id_ad, a.adName
    FROM AdQuestionsBundle:AdsQuestions aq
    JOIN aq.questionAd a
    JOIN aq.questionAuthor u
    ORDER BY aq.id_aq DESC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets $question by $id.
   * @access public
   * @param int $id Question's id.
   * @return array Option's data.
   */
  public function getQuestionNormal($id)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle, aq.questionText ,  aq.questionState,
    u.id_us, u.login, u.email, a.id_ad, a.adName
    FROM AdQuestionsBundle:AdsQuestions aq
    JOIN aq.questionAd a
    JOIN aq.questionAuthor u
    WHERE aq.id_aq = :question")
    ->setParameter('question', $id); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets question for test.
   * @access public
   * @return array Option's data.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aq.id_aq, aq.questionTitle,  aq.questionText  , aq.questionState,
    u.id_us, u.login, u.email, a.id_ad, a.adName
    FROM AdQuestionsBundle:AdsQuestions aq
    JOIN aq.questionAd a
    JOIN a.adAuthor u")
    ->setMaxResults(1); 
    $rows = $query->getResult();
    return array('id' => $rows[0]['id_aq'], 'id2' => '', 'user1' => $rows[0]['id_us'], 'user2' => $rows[0]['id_us']);
  }
}