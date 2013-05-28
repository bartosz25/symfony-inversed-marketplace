<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;
use Ad\ItemsBundle\Entity\Ads;

class AdsOffersRepository extends EntityRepository 
{

  /**
   * Gets all offers by ad's id.
   * @access public
   * @param array $options Options used by the query.
   * @param int $ad Ad's id.
   * @return array Offers list.
   */
  public function getOffersByAd($options, $ad)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, u.email, o.id_of, o.offerName, o.offerPrice, c.id_cat, c.catalogueName, DATE_FORMAT(ao.addedDate, '".$options['date']."') AS submitted
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN o.offerCatalogue c
    JOIN o.offerAuthor u
    WHERE ao.ads_id_ad = :ad
    ORDER BY o.offerPrice ASC, o.id_of ASC")
    ->setParameter('ad', $ad); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }

  /**
   * Gets all offers by user's id. We return only offers of activated ads.
   * @access public
   * @param array $options Options used by the query.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function getOffersListByUser($options, $user)
  {
    $order = "ao.addedDate DESC";
    $columns = array("date" => "ao.addedDate", "annonce" => "a.adName", "offre" => "o.offerName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, DATE_FORMAT(ao.addedDate, '".$options['date']."') AS submitted,
    a.id_ad, a.adName, a.adState
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN ao.ads_id_ad a
    WHERE o.offerAuthor = :user AND a.adState = :state
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);  
    return $query->getResult();
  }

  /**
   * Counts all offers by user's id. We return only offers of activated ads.
   * @access public
   * @param int $user User's id.
   * @return int Offers quantity.
   */
  public function countActivedOffers($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(o.id_of) AS allOffers
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN ao.ads_id_ad a
    WHERE o.offerAuthor = :user AND a.adState = 1
    ORDER BY ao.addedDate DESC")
    ->setParameter('user', $user); 
    $row = $query->getResult();
    return (int)$row[0]['allOffers'];
  }

  /**
   * Checks if offer belongs to user and if ad is activated.
   * @access public
   * @param int $ad Ad's id.
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @return boolean True if offer belongs to user and ad is activated, false otherwise.
   */
  public function checkOffer($ad, $offer, $user, $strict = true)
  {
    $stateClause = " AND a.adState = 1";
    if(!$strict)
    {
      $stateClause = "";
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, a.id_ad, a.adOffer, a.adName, u.id_us, u.login, u.email
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN ao.ads_id_ad a
    JOIN a.adAuthor u
    WHERE ao.offers_id_of = :offer AND ao.ads_id_ad = :ad AND o.offerAuthor = :user ".$stateClause)
    ->setParameter('offer', $offer)
    ->setParameter('ad', $ad)
    ->setParameter('user', $user); 
    $row = $query->getResult();
    if(isset($row[0]['id_of']))
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Checks if ad belongs to user and offer not.
   * @access public
   * @param int $ad Ad's id.
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @param string $dateFormat Format of SQL date.
   * @return boolean True if offer belongs to user and ad is activated, false otherwise.
   */
  public function checkOfferAd($ad, $offer, $user, $dateFormat)
  {
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerTax, o.offerPrice, a.adName, a.id_ad, u.login, u.email, u.id_us, a.adOffer, DATE_FORMAT(a.adEnd, '".$dateFormat."') AS dateEnd
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN o.offerAuthor u
    JOIN ao.ads_id_ad a
    WHERE a.id_ad = :ad AND o.id_of = :offer AND u.id_us != :user AND a.adAuthor = :user AND a.adState = :state")
    ->setParameter('offer', $offer)
    ->setParameter('ad', $ad)
    ->setParameter('state', $adsEnt->getActiveState()) 
    ->setParameter('user', $user); 
    $row = $query->getResult();
    if(isset($row[0]['id_of']))
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Gets all offers by user's id. We return only offers of activated ads.
   * @access public
   * @param array $options Options used by the query.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function getOffersListByUserAds($options, $user)
  {
    $order = "o.offerName ASC";
    $columns = array("annonce" => "a.adName", "offre" => "o.offerName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, DATE_FORMAT(ao.addedDate, '".$options['date']."') AS submitted,
    a.id_ad, a.adName, a.adState
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN ao.ads_id_ad a
    WHERE a.adAuthor = :user AND a.adState = :state
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);  
    return $query->getResult();
  }

  /**
   * Counts all offers for user's ads. We return only offers of activated ads.
   * @access public
   * @param int $user User's id.
   * @return int Offers quantity.
   */
  public function countOffersForUserAds($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(o.id_of) AS allOffers
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.offers_id_of o
    JOIN ao.ads_id_ad a
    WHERE a.adAuthor = :user AND a.adState = 1
    ORDER BY ao.addedDate DESC")
    ->setParameter('user', $user); 
    $row = $query->getResult();
    return (int)$row[0]['allOffers'];
  }

  /**
   * Gets all ads by offer's id.
   * @access public
   * @param int $offer Offer's id.
   * @return array Ads list.
   */
  public function getAllAdsByOffer($offer)
  {
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, u.login, u.email
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.ads_id_ad a
    JOIN a.adAuthor u
    WHERE ao.offers_id_of = :offer")
    ->setParameter('offer', $offer); 
    return $query->getResult();
  }

  /**
   * Delete one offer from $ads.
   * @access public
   * @param array $ads Array with ads ids or simple integer for one ad's id.
   * @return void
   */
  public function deleteOffers($ads)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->delete('AdItemsBundle:AdsOffers', 'ao')
    ->add('where', $qb->expr()->in('ao.ads_id_ad', $ads))
    ->getQuery()
    ->getResult();
  }

  /**
   * Gets offer to test.
   * @access public
   * @return array Offers list.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, a.id_ad, o.id_of, o.offerName, o.offerPrice, ao.id_ao
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.ads_id_ad a
    JOIN ao.offers_id_of o
    JOIN o.offerAuthor u"); 
    $row = $query->getResult();
    return array('id' => $row[0]['id_of'], 'id2' => $row[0]['id_ad'], 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }


  /**
   * Gets offer to test.
   * @access public
   * @return array Offers list.
   */
  public function getForTestAd()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT u.id_us, u.login, a.id_ad, o.id_of, o.offerName, o.offerPrice, ao.id_ao
    FROM AdItemsBundle:AdsOffers ao
    JOIN ao.ads_id_ad a
    JOIN a.adAuthor u
    JOIN ao.offers_id_of o"); 
    $row = $query->getResult();
    return array('id' => $row[0]['id_ao'], 'id2' => $row[0]['id_ad'], 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }

}