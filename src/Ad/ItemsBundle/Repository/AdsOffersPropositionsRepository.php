<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class AdsOffersPropositionsRepository extends EntityRepository 
{

  /**
   * Gets all offers by ad's id.
   * @access public
   * @param array $options Options used by the query.
   * @param int $ad Ad's id.
   * @return array Offers list.
   */
  public function exists($offer, $ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aop.propositionDate
    FROM AdItemsBundle:AdsOffersPropositions aop
    WHERE aop.ads_id_ad = :ad AND aop.offers_id_of = :offer")
    ->setParameter('offer', $offer)
    ->setParameter('ad', $ad);
    $result = $query->getResult();
    $return = false;
    if(count($result) > 0)
    {
      $return = true;
    }
	return $return;
  }

  /**
   * Gets proposition with ad, offer and ad's author.
   * @access public
   * @param int $ad Ad's id.
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function propositonExists($ad, $offer, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aop.propositionDate, a.id_ad, a.adName, o.id_of, o.offerName,
    u.id_us, u.login, u.email
    FROM AdItemsBundle:AdsOffersPropositions aop
    JOIN aop.ads_id_ad a
    JOIN aop.offers_id_of o
    JOIN aop.users_id_us u
    WHERE aop.ads_id_ad = :ad AND aop.offers_id_of = :offer AND
    aop.users_id_us = :user")
    ->setParameter('offer', $offer)
    ->setParameter('user', $user)
    ->setParameter('ad', $ad);
    $row = $query->getResult();
    if(isset($row[0]['id_ad']))
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Gets all propositions by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Ads list.
   */
  public function getPropositionsListByUser($options, $user)
  {
    $order = "ao.addedDate DESC";
    $columns = array("date" => "ao.addedDate", "annonce" => "a.adName", "offre" => "o.offerName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName,o.id_of, o.offerName
    FROM AdItemsBundle:AdsOffersPropositions aop
    JOIN aop.ads_id_ad a
    JOIN aop.offers_id_of o
    WHERE aop.users_id_us = :user
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets all propositions by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Ads list.
   */
  public function countForUser($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(aop.propositionDate) AS allELements
    FROM AdItemsBundle:AdsOffersPropositions aop
    WHERE aop.users_id_us = :user
    ORDER BY aop.propositionDate DESC")
    ->setParameter('user', $user); 
    $row = $query->getResult();
    if(isset($row[0]['allELements']))
    {
      return $row[0]['allELements'];
    }
    return 0;
  }

  /**
   * Gets proposition with ad, offer and ad's author.
   * @access public
   * @param int $ad Ad's id.
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT aop.propositionDate, a.id_ad, a.adName, o.id_of, o.offerName,
    u.id_us, u.login, u.email
    FROM AdItemsBundle:AdsOffersPropositions aop
    JOIN aop.ads_id_ad a
    JOIN aop.offers_id_of o
    JOIN aop.users_id_us u")
    ->setMaxResults(1);
    $row = $query->getResult();
    return array('id' => $row[0]['id_ad'], 'id2' => $row[0]['id_of'], 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }

}