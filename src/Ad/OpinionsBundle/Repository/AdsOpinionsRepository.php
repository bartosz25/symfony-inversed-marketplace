<?php
namespace Ad\OpinionsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class AdsOpinionsRepository extends EntityRepository 
{

  /**
   * Checks if user has already wrote an opinion for this ad.
   * @access public
   * @param int $ad Ad's id.
   * @param int $user Users's id.
   * @return array Offers list.
   */
  public function alreadyWrote($ad, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ao.opinionNote
    FROM AdOpinionsBundle:AdsOpinions ao
    WHERE ao.opinionAd = :ad AND ao.opinionAuthor = :user")
    ->setParameter('user', $user)
    ->setParameter('ad', $ad);
    $row = $query->getResult();
	return (bool)(isset($row[0]['opinionNote']));
  }

  /**
   * Counts opinions written by an user.
   * @access public
   * @param int $user Users's id.
   * @return int Written opinions.
   */
  public function countGivenOpinions($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(ao.opinionNote)
    FROM AdOpinionsBundle:AdsOpinions ao
    WHERE ao.opinionAuthor = :user")
    ->setParameter('user', $user);
    return $query->getSingleScalarResult();
  }

  /**
   * Gets opinions whether written or given by an user.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @param string $type Type of query (written opinions or received opinions)
   * @return array Opinions list.
   */
  public function getOpinionsByUser($options, $user, $type)
  {
    $order = "ao.opinionDate DESC ASC";
    $columns = array("date" => "ao.opinionDate", "titre" => "ao.opinionTitle",
    "note" => "ao.opinionNote", "commande" => "a.adName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $where = 'ao.opinionReceiver = :user';
    if($type == 'author')
    {
      $where = 'ao.opinionAuthor = :user';
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT SUBSTRING_INDEX(ao.opinionText, '.', 2) AS shortContent, 
    ao.opinionTitle, ao.opinionNote, DATE_FORMAT(ao.opinionDate, '".$options['date']."') AS addedDate,
    a.adName
    FROM AdOpinionsBundle:AdsOpinions ao
    JOIN ao.opinionAd a
    WHERE $where
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Checks if user has already wrote an opinion for this ad.
   * @access public
   * @param int $ad Ad's id.
   * @param int $user Users's id.
   * @return array Offers list.
   */
  public function getForTestWrote()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT ao.opinionNote
    FROM AdOpinionsBundle:AdsOpinions ao
    WHERE ao.opinionAd = :ad AND ao.opinionAuthor = :user")
    ->setParameter('user', $user)
    ->setParameter('ad', $ad);
    $row = $query->getResult();
	return (bool)(isset($row[0]['opinionNote']));
  }

}