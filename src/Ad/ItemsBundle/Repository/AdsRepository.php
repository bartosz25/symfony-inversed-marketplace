<?php
namespace Ad\ItemsBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Ad\ItemsBundle\Entity\Ads;
use Ad\ItemsBundle\Entity\AdsPayments;
use Ad\ItemsBundle\Entity\AdsHomepage;
use Ad\ItemsBundle\Entity\AdsTags;
use Ad\ItemsBundle\Entity\AdsFormFields;
use Ad\ItemsBundle\Entity\AdsModified;
use Frontend\FrontBundle\Entity\Tags;
use Order\OrdersBundle\Entity\Tax;
use Category\CategoriesBundle\Entity\FormFields; 
use Others\Tools;
use Database\MainEntity;

class AdsRepository extends EntityRepository 
{

  /**
   * Gets all ads by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Ads list.
   */
  public function getAdsListByUser($options, $user)
  {
    $order = "a.adName ASC";
    $columns = array("date" => "a.id_ad", "titre" => "a.adName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $queryPart = '';
    if(isset($options['strict']) && $options['strict'] == true)
    {
      $adsEnt = new Ads;
      $queryPart = 'AND a.adState = '.$adsEnt->getActiveState();
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState
    FROM AdItemsBundle:Ads a
    WHERE a.adAuthor = :user ".$queryPart." 
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets ad by id without joins and other things.
   * @access public
   * @param int $ad Ad's id.
   * @param int $user User's id.
   * @return array Ad informations.
   */
  public function getAdData($ad, $user)
  {
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adText, a.adVisits, a.adStart, a.adOffer, a.adTax,
    a.adEnd, a.adMinOpinion, a.adOfferPrice, a.adObjetState, a.adSellerType, a.adState, a.adBuyTo, a.adSellerGeo,
    c.id_ci, co.id_co, u.id_us, ca.id_ca, ca.categoryName, r.id_re
    FROM AdItemsBundle:Ads a
    JOIN a.adAuthor u
    JOIN a.adCategory ca
    JOIN a.adCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    WHERE a.id_ad = :ad AND u.id_us = :user AND a.adState = :state")
    ->setParameter('ad', $ad)
    ->setParameter('user', $user) 
    ->setParameter('state', $adsEnt->getActiveState()); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets ad by id.
   * @access public
   * @param int $ad Ad's id.
   * @return array Ad informations.
   */
  public function getOneAd($ad, $strict = true)
  {
    $adsEnt = new Ads;
    $whereClause = " AND a.adState = ".$adsEnt->getActiveState();
    if(!$strict)
    {
      $whereClause = ""; 
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adText, a.adVisits, a.adStart, a.adReplies, a.adOffer, a.adTax,
    a.adEnd, a.adMinOpinion, a.adObjetState, a.adSellerType, a.adState, a.adBuyTo, a.adSellerGeo,
    c.id_ci, c.cityAds, co.id_co, u.id_us, ca.id_ca, ca.categoryAds, u.id_us, u.login, u.email, ca.categoryName, ca.categoryUrl, c.cityUrl, c.cityName, 
    r.regionUrl, r.id_re, r.regionAds, r.regionName, co.countryName, co.id_co,
    DATE_FORMAT(a.adStart, '%d-%m-%Y') AS startTime, DATE_FORMAT(a.adEnd, '%d-%m-%Y') AS endTime
    FROM AdItemsBundle:Ads a
    JOIN a.adAuthor u
    JOIN a.adCategory ca
    JOIN a.adCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    WHERE a.id_ad = :ad ".$whereClause)
    ->setParameter('ad', $ad); 
    $rows = $query->getResult();
    if(isset($rows[0]['id_ad']))
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets ads list.
   * @access public
   * @param array $options Options used to SQL request.
   * @param array $where Condition array.
   * @return array Ads list.
   */
  public function getAdsList($options, $where)
  {
    $keys = array_keys($where);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, c.categoryUrl
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    WHERE a.".$keys[0]." = :".$keys[0]." AND a.adState > 0
    ORDER BY a.id_ad DESC")
    ->setParameter($keys[0], $where[$keys[0]])
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }


  /**
   * Checks if ad is correct (activated) and if the $user isn't author of this ad.
   * @access public
   * @param int $ad Ad's id.
   * @param int $user User's id.
   * @return array User informations.
   */
  public function isCorrectAd($ad, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad
    FROM AdItemsBundle:Ads a
    WHERE a.id_ad = :ad AND a.adState > 0 AND a.adAuthor != :user")
    ->setParameter('ad', $ad)
    ->setParameter('user', $user);
    $rows = $query->getResult();
    if(isset($rows[0]['id_ad']))
    {
      return true;
    }
    return false;
  }

  /**
   * Counts all questions for user's ad. 
   * @access public
   * @param int $user User's id.
   * @return array User informations.
   */
  public function countAllQuestions($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT SUM(a.adQuestions)
    FROM AdItemsBundle:Ads a
    WHERE a.adAuthor = :user")
    ->setParameter('user', $user); 
    return (int)$query->getSingleScalarResult();
  }

  /**
   * Counts all replies for user's ad. 
   * @access public
   * @param int $user User's id.
   * @return array User informations.
   */
  public function countAllReplies($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT SUM(a.adReplies)
    FROM AdItemsBundle:Ads a
    WHERE a.adAuthor = :user")
    ->setParameter('user', $user); 
    return (int)$query->getSingleScalarResult();
  }

  /**
   * Updates offers quantity for this ad.
   * @access public
   * @param int $i Decrements or increments value (often -1 or 1).
   * @param int $ad Ad's id.
   * @return void
   */
  public function updateOffersQuantity($i, $ad)
  {
    $query = $this->getEntityManager()->createQueryBuilder()
    ->update('Ad\ItemsBundle\Entity\Ads', 'a')
    ->set('a.adOffers', 'a.adOffers + '.$i)
    ->where('a.id_ad = ?1')
    ->setParameter(1, $ad)
    ->getQuery()->execute();
  }

  /**
   * Gets ads list by category.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $category Category id.
   * @return array Ads list.
   */
  public function getByCategory($options, $category)
  {
    $adsEnt = new Ads;
    $order = "a.adName ASC";
    $columns = array("titre" => "a.adName", 
    "date" => "a.adStart", "fourchette-a" => "a.adBuyTo", "ville" => "ci.cityName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, a.adBuyTo, a.adOffers,
    SUBSTRING_INDEX(a.adText, '.', 4) AS shortDesc, DATE_FORMAT(a.adStart, '".$options['date']."') AS dateStart,c.cityName
    FROM AdItemsBundle:Ads a
    JOIN a.adCity c
    WHERE a.adCategory = :category AND a.adState = :state
    ORDER BY $order")
    ->setParameter('category', $category)
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    if($options['cacheName'] != "") 
    {
      $query->useResultCache(true, 0, $options['cacheName']);
    }
    $result = $query->getResult();
    if($options['cacheName'] != "") 
    {
      $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    }
    return $result;
  }

  /**
   * Gets ads list by region's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $region Region id.
   * @return array Ads list.
   */
  public function getByRegion($options, $region)
  {
// TODO : voir, peut-être ce sera mieux de faire une requête avec IN($villesId) ?
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, ca.categoryUrl
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory ca
    JOIN a.adCity c
    WHERE c.cityRegion = :region AND a.adState > 0
    ORDER BY a.id_ad DESC")
    ->setParameter('region', $region)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }

  /**
   * Gets $limit ads.
   * @access public
   * @param int $limit Ads to show.
   * @return array Ads list.
   */
  public function getLastAds($limit)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, SUBSTRING_INDEX(a.adText, '.', 4) AS shortDesc, c.categoryUrl
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    ORDER BY a.id_ad DESC")
    ->setMaxResults($limit);
    return $query->getResult();
  }

  /**
   * Gets all ads list.
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Ads list.
   */
  public function getAllAds($options)
  {
    $order = "a.adName ASC";
    $columns = array("titre" => "a.adName", 
    "date" => "a.adStart", "categorie" => "c.categoryName", "ville" => "ci.cityName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adState, c.categoryName, c.categoryUrl,
    DATE_FORMAT(a.adStart, '".$options['date']."') AS dateStart, ci.cityName
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    JOIN a.adCity ci
    WHERE a.adState > 0
    ORDER BY $order")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }

  /**
   * Gets ads which are ending.
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Ads list.
   */
  public function getAdsToEnd($options)
  {
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adOffer, a.adName, a.adStart, a.adEnd, a.adState, c.id_ca, ci.id_ci, r.id_re, u.id_us, u.login
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    JOIN a.adCity ci
    JOIN ci.cityRegion r
    JOIN a.adAuthor u
    WHERE a.adState = :state AND DATE_FORMAT(a.adEnd, '%d-%m-%Y') <= '".date('d-m-Y')."'
    ORDER BY a.id_ad DESC")
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $result = $query->getResult();
    return $result;
  }

  /**
   * Gets all ads list.
   * + is backend method
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Ads list.
   */
  public function getAllAdsBackend($options)
  {
    $whereClause = '';
    if($options['type'] == 'new')
    {
      $whereClause = "WHERE a.adState = 0";
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, c.categoryName, c.categoryUrl, ci.cityName, ci.id_ci, u.login
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    JOIN a.adAuthor u
    JOIN a.adCity ci
    ".$whereClause." 
    ORDER BY a.id_ad DESC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Checks if $offer exists in the offers_id_of column.
   * @access public
   * @param int $offer Offer's id.
   * @return bool True if exists, false otherwise.
   */
  public function isValidOfferForAd($offer)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(a.id_ad) AS adsWithOffer
    FROM AdItemsBundle:Ads a
    WHERE a.adOffer = :offer")
    ->setParameter('offer', $offer); 
    $rows = $query->getResult();
    return (bool)($rows[0]['adsWithOffer'] > 0);
  }

  /**
   * Counts user's ads by ad state. 
   * @access public
   * @param int $user User's id.
   * @param int $state Ad's state.
   * @return int Ads counter.
   */
  public function countAdsByUserAndState($user, $state)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(a.id_ad)
    FROM AdItemsBundle:Ads a
    WHERE a.adAuthor = :user AND a.adState = :state")
    ->setParameter('user', (int)$user)
    ->setParameter('state', (int)$state); 
    return (int)$query->getSingleScalarResult();
  }

  /**
   * Add new ads.
   * @access public
   * @param array $referencies Array with referencies.
   * @param array $data Array with inserted data
   * @return void
   */
  public function addAd($referencies, $data)
  {
    // set relations objects
    $adsEnt = new Ads;
    $adsEnt->setAdValidity($data['ad']['validity']);
    $adsEnt->setAdName($data['ad']['name']);
    $adsEnt->setAdText($data['ad']['text']);
    $adsEnt->setAdMinOpinion($data['ad']['minOpinion']);
    $adsEnt->setAdObjetState($data['ad']['objetState']);
    $adsEnt->setAdSellerType($data['ad']['seller']);
    $adsEnt->setAdLength($data['ad']['length']);
    $adsEnt->setAdAtHomePage($data['ad']['home']);
    // $adsEnt->setAdBuyFrom(Tools::normalizePrice($data['ad']['buyFrom']));
    $adsEnt->setAdBuyTo(Tools::normalizePrice($data['ad']['buyTo']));
    $adsEnt->setAdSellerGeo($data['ad']['sellerGeo']);
    $adsEnt->setAdAuthor($referencies['user']);
    $adsEnt->setAdCategory($referencies['category']);
    $adsEnt->setAdCity($referencies['city']);
    $adsEnt->setAdStart();
    $adsEnt->setAdEnd();
    $adsEnt->setAdOffer(0);
    $adsEnt->setAdTax(Tax::getTaxValue((int)$data['ad']['tax']));
    $adsEnt->setAddData();
    $this->getEntityManager()->persist($adsEnt);
    $this->getEntityManager()->flush();
    // add to ads form fields table
    $affEnt = new AdsFormFields;
    $ffiEnt = new FormFields;
    $formCategory = $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\FormFieldsCategories', (int)$data['ad']['category']);
    foreach($data['formFields'] as $field)
    {
      $ffiEnt = $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\FormFields', (int)$field['id_ff']);
      $cloneAff = clone $affEnt;
      $cloneAff->setNewValues(array('ads_id_ad' => $adsEnt, 
      'form_fields_id_ff' => $ffiEnt,
      'categories_id_ca' => $formCategory,
      'fieldValue' => $data['others'][$field['codeName']]));
      $this->getEntityManager()->persist($cloneAff);
      $this->getEntityManager()->flush();
    }
    // get written tags
    if(count($data['tags']) > 0)
    {
      $tagsCount = 0;
      $tagEnt = new Tags;
      $adtEnt = new AdsTags;
      $dbTags = $this->getEntityManager()->getRepository('FrontendFrontBundle:Tags')->getTagsIn($data['tags']);
      // if not exists, insert a new one
      // if exists, update quantity
      foreach($data['tags'] as $tag)
      {
        if(in_array($tag, $dbTags))
        {
          // update quantity and get id
          $keys = array_keys($dbTags, $tag);
          $idTag = (int)$keys[0];
          $cloneTag = $this->getEntityManager()->getReference('Frontend\FrontBundle\Entity\Tags', $idTag);
          // $qb = $this->getEntityManager()->createQueryBuilder();
          // $q = $qb->update('Frontend\FrontBundle\Entity\Tags', 't')
          // ->set('t.tagAds', 't.tagAds + 1')
          // ->where('t.id_ta = ?1')
          // ->setParameter(1, $idTag)
          // ->getQuery();
          // $p = $q->execute();
        }
        else
        {
          $tagsCount++;
          $cloneTag = clone $tagEnt;
          $cloneTag->setTagName($tag);
          // $cloneTag->setTagAds(1);
          $cloneTag->setTagAds(0);
          $cloneTag->setTagOffers(0);
          $this->getEntityManager()->persist($cloneTag);
          $this->getEntityManager()->flush();
          $idTag = $cloneTag->getIdTa();
        }
        // insert new tags relations
        $cloneAdt = clone $adtEnt;
        $cloneAdt->setAdsIdAd($adsEnt);
        $cloneAdt->setTagsIdTa($cloneTag);
        $this->getEntityManager()->persist($cloneAdt);
        $this->getEntityManager()->flush();
      }    
      $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ '.$tagsCount, 'tags');
    }
    // insert new ads payments methods
    $adpEnt = new AdsPayments;
    foreach($data['payments'] as $payment)
    {
      $cloneAdp = clone $adpEnt;
      $cloneAdp->setAdsIdAd($adsEnt);
      $cloneAdp->setPaymentsIdPa($payment);
      $this->getEntityManager()->persist($cloneAdp);
      $this->getEntityManager()->flush();
    }
    // stats : update regions, cities, categories quantity
// TODO : transporter ce fragment dans le backoffice où l'on active les annonces
    // $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity((int)$data['ad']['city']);
    // $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('+ 1', $cityRow[0]['id_re'], 'ads');
    // $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('+ 1', (int)$data['ad']['city'], 'ads');
    // $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('+ 1', (int)$data['ad']['category'], 'ads');
    // $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'adsa');
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'adsn');
    // if show at the home page, insert to ads_frontpage
    if((int)$data['ad']['home'] == 1)
    {
      $adfEnt = new AdsHomepage;
      $adfEnt->setAdsIdAd($adsEnt);
      $adfEnt->setEndHomepage($adsEnt->getAdEnd());
      $this->getEntityManager()->persist($adfEnt);
      $this->getEntityManager()->flush();
    }
  }

  /**
   * Edits an ad.
   * @access public
   * @param int $id Ad's id.
   * @param array $data Edited data (new - contains new informations, old - contains old parameters)
   * @return void
   */
  public function editAd($id, $data)
  {
    // get ad reference
    $adEntity = $this->getEntityManager()->getReference('Ad\ItemsBundle\Entity\Ads', $id);
    // update ad data
    $q = $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
    ->set('a.adCategory', (int)$data['ad']['new']['category'])
    ->set('a.adCity', (int)$data['ad']['new']['city'])
    ->set('a.adName', '?1')
    ->set('a.adText', '?2')
    ->set('a.adMinOpinion', (int)$data['ad']['new']['minOpinion'])
    ->set('a.adObjetState', (int)$data['ad']['new']['objetState'])
    ->set('a.adSellerType', (int)$data['ad']['new']['sellerType'])
    // ->set('a.adBuyFrom', (float)Tools::normalizePrice($data['ad']['new']['buyFrom']))
    ->set('a.adBuyTo', (float)Tools::normalizePrice($data['ad']['new']['buyTo']))
    ->set('a.adSellerGeo', (int)$data['ad']['new']['sellerGeo'])
    ->set('a.adTax', Tax::getTaxValue((int)$data['ad']['new']['tax']))
    ->where('a.id_ad = ?3')
    ->setParameter(1, $data['ad']['new']['name'])
    ->setParameter(2, $data['ad']['new']['text'])
    ->setParameter(3, $id)
    ->getQuery()
    ->execute();
    // if categories are differents
    if($data['ad']['new']['category'] != $data['ad']['old']['category'])
    {
      $q2 = $this->getEntityManager()->createQueryBuilder()->delete('Ad\ItemsBundle\Entity\AdsFormFields', 'aff')
      ->where('aff.ads_id_ad = ?1')
      ->setParameter(1, $id)
      ->getQuery()
      ->execute();
      $affEnt = new AdsFormFields;
      $ffiEnt = new FormFields;
      $category = $this->getEntityManager()->getRepository('Category\CategoriesBundle\Entity\FormFieldsCategories')->findBy(array('categories_id_ca' => (int)$data['ad']['new']['category']));
      foreach($data['formFields']['new'] as $field)
      {
        $cloneAff = clone $affEnt;
        $cloneAff->setNewValues(array('ads_id_ad' => $adEntity, 
        'form_fields_id_ff' => $this->getEntityManager()->find('Category\CategoriesBundle\Entity\FormFields', (int)$field['id_ff']), 
        'categories_id_ca' => $category[0],
        'fieldValue' => $data['others'][$field['codeName']]));
        $this->getEntityManager()->persist($cloneAff);
        $this->getEntityManager()->flush();
      }
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('- 1', (int)$data['ad']['old']['category'], 'ads');
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('+ 1', (int)$data['ad']['new']['category'], 'ads');

      // update category state = delete label to old, new label to new category 
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:CategoriesModified')->categoryModified((int)$data['ad']['old']['category'], 'delete', array('adName' => $data['ad']['new']['name']));
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:CategoriesModified')->categoryModified((int)$data['ad']['new']['category'], 'add', array('adName' => $data['ad']['new']['name']));
    }
    else
    {
      foreach($data['formFields']['new'] as $field)
      {
        $q = $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\AdsFormFields', 'aff')
        ->set('aff.fieldValue', '?1')
        ->where('aff.ads_id_ad = ?2 AND aff.form_fields_id_ff= ?3 AND aff.categories_id_ca = ?4')
        ->setParameter(1, $data['others'][$field['codeName']])
        ->setParameter(2, $id)
        ->setParameter(3, (int)$field['id_ff'])
        ->setParameter(4, (int)$data['ad']['new']['category'])
        ->getQuery()
        ->execute();
      }
      // modify categories_modified
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:CategoriesModified')->categoryModified((int)$data['ad']['new']['category'], 'content', array('adName' => $data['ad']['new']['name']));
    }
    // if city changed, update ads quantity for city and region
    if($data['ad']['new']['city'] != $data['ad']['old']['city'])
    {
      $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity((int)$data['ad']['new']['city']);
      $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('+ 1', $cityRow[0]['id_re'], 'ads');
      $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('+ 1', (int)$data['ad']['new']['city'], 'ads');

      $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity($data['ad']['old']['city']);
      $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('- 1', $cityRow[0]['id_re'], 'ads');
      $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('- 1', $data['ad']['old']['city'], 'ads');
    }
    $adpEnt = new AdsPayments;
    // remove all old payment rows and insert the new ones
    $this->getEntityManager()->getRepository('AdItemsBundle:AdsPayments')->deleteByAd($id);
    foreach($data['payments'] as $payment)
    {
      $cloneAdp = clone $adpEnt;
      $cloneAdp->setAdsIdAd($adEntity);
      $cloneAdp->setPaymentsIdPa($payment);
      $this->getEntityManager()->persist($cloneAdp);
      $this->getEntityManager()->flush();
    }
    // update ads_modified table with the last modification
    $this->getEntityManager()->getRepository('AdItemsBundle:AdsModified')->adModified($id, 'content');
  }  

  /**
   * Accept ad.
   * @access public
   * @param int $id Ad's id
   * @param array $data Ad's data
   * @return void
   */
  public function acceptAd($id, $data)
  {
    $adsEnt = new Ads;
    // change ad state
    $q = $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
    ->set('a.adState', $adsEnt->getActiveState())
    ->where('a.id_ad = ?1')
    ->setParameter(1, $id)
    ->getQuery()
    ->execute();
// TODO : incrémenter les tags ici, au lieu de addAd()
    $tags = $this->getEntityManager()->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($id);
    foreach($tags as $t => $tag)
    {
      $this->getEntityManager()->createQueryBuilder()
      ->update('Frontend\FrontBundle\Entity\Tags', 't')
      ->set('t.tagAds', 't.tagAds + 1')
      ->where('t.id_ta = ?1')
      ->setParameter(1, $tag['id_ta'])
      ->getQuery()
      ->execute();
    }

    // stats : update regions, cities, categories quantity
    $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity((int)$data['city']);
    $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('+ 1', $cityRow[0]['id_re'], 'ads');
    $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('+ 1', (int)$data['city'], 'ads');
    $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('+ 1', (int)$data['category'], 'ads');
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ 1', 'adsa');
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'adsn');
    // if show at the home page, insert to ads_frontpage

    // update ads_modified table with the last modification
    $this->getEntityManager()->getRepository('AdItemsBundle:AdsModified')->adModified($id, 'ad_accepted');
  }

  /**
   * Delete ad from the database.
   * @access public
   * @param int $id Deleted offer id.
   * @param array $offerRow Offer data.
   * @param array $config Config data.
   * @return void
   */
  public function deleteAd($id, $ad, $config)
  {
    $cacheClass = $this->getEntityManager()->getConfiguration()->getResultCacheImpl();
    $dirs = $cacheClass->getCacheStructure();
    
    // check if offer is used as accepted offer
    $offers = $this->getEntityManager()->getRepository('AdItemsBundle:AdsOffers')->getOffersByAd(array('cacheName' => $config['cacheName'], 'date' => $config['dateFormat']), $id);
    $adsEnt = new Ads;
    // make the ad deleted
    $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
    ->set('a.adName', '?1')
    ->set('a.adState', '?2')
    ->where('a.id_ad = ?3')
    ->setParameter(1, $config['title'])
    ->setParameter(2, $adsEnt->getDeletedState())
    ->setParameter(3, (int)$id)
    ->getQuery()
    ->execute();
    // clean /ads/all 
    $cacheClass->cleanDirCache($dirs['ads'].'all/');
    
    // stats : update regions, cities, categories quantity
    $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('- 1', (int)$ad['id_re'], 'ads');
    $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('- 1', (int)$ad['id_ci'], 'ads');
    $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('- 1', (int)$ad['id_ca'], 'ads');
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'adsa');
    // clean other cache files
    if(isset($ad))
    {
      $cacheClass->cleanDirCache($dirs['categories'].$ad['id_ca'].'/ads/');
      $cacheClass->cleanDirCache($dirs['cities'].$ad['id_ci'].'/ads/');
      $cacheClass->cleanDirCache($dirs['regions'].$ad['id_re'].'/ads/');
    }
    // if ad was activated
    if($ad['adState'] == $adsEnt->getActiveState())
    {
      $this->getEntityManager()->getRepository('UserProfilesBundle:Users')->updateQuantity('- 1', $ad['id_us'], 'userAds');  
    }
    elseif($ad['adState'] == $adsEnt->getNotAcceptedState())
    {
      $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'adsn');
    }
    if($ad['adState'] != $adsEnt->getNotAcceptedState())
    {
      $tags = $this->getEntityManager()->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($id);
      if(count($tags) > 0)
      { 
        $tagsIds = array();
        foreach($tags as $tag)
        {
          $tagsIds[] = (int)$tag['id_ta'];
          // clean cache for tag
          $cacheClass->cleanDirCache($dirs['tags'].$tag['id_ta'].'/ads/');
        }
        $this->getEntityManager()->getRepository('FrontendFrontBundle:Tags')->updateQuantity('- 1', 'tagAds', $tagsIds);
      }
    }
    // update ads_modified table with the last modification
    $this->getEntityManager()->getRepository('AdItemsBundle:AdsModified')->adModified($id, 'ad_deleted');
  }

  /**
   * Deny ad.
   * @access public
   * @param int $id Ad's id
   * @return void
   */
  public function denyAd($id)
  {
    $adsEnt = new Ads;
    // change ad state
    $q = $this->getEntityManager()->createQueryBuilder()->delete('Ad\ItemsBundle\Entity\Ads', 'a')
    ->where('a.id_ad = ?1')
    ->setParameter(1, $id)
    ->getQuery()
    ->execute();
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'adsn');
  }

  /**
   * End ad.
   * @access public
   * @param int $id Ended ad.
   * @param array $ad Ad data.
   * @param array $config Config data.
   * @return void
   */
  public function endAd($id, $ad, $config)
  {
    $cacheClass = $this->getEntityManager()->getConfiguration()->getResultCacheImpl();
    $dirs = $cacheClass->getCacheStructure();
    
    // get all offers authors, with e-mail; send the notyfication
    $cacheName = $config['ads'].$id.'/add_offers';
    $offers = $this->getEntityManager()->getRepository('AdItemsBundle:AdsOffers')->getOffersByAd(array('cacheName' => $cacheName, 'date' => $config['dateFormat']), $id);
    $mailsBCC = array();
    foreach($offers as $o => $offer)
    {
      $mailsBCC[] = $offer['email'];
    }
    $vars = array('{AD_TITLE}');
    $template = file_get_contents(rootDir.'mails/ad_ended_no_offer.maildoc');
    $urls = array($ad['adName']);
    $parsedTpl = str_replace($vars, $urls, $template);
    $message = \Swift_Message::newInstance()
    ->setSubject("Annonce ".$ad['adName']." vient d'être terminée")
    ->setFrom($config['from'])
    ->setTo($config['from'])
    ->setBcc($mailsBCC)
    ->setContentType("text/html")
    ->setBody($parsedTpl);

    // make the ad deleted
    $adsEnt = new Ads;
    $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
    ->set('a.adState', '?1')
    ->where('a.id_ad = ?2')
    ->setParameter(1, $adsEnt->getEndedState())
    ->setParameter(2, (int)$id)
    ->getQuery()
    ->execute();
    // clean /ads/all 
    $cacheClass->cleanDirCache($dirs['ads'].'all/');
    
    // stats : update regions, cities, categories quantity
    $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('- 1', (int)$ad['id_re'], 'ads');
    $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('- 1', (int)$ad['id_ci'], 'ads');
    $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('- 1', (int)$ad['id_ca'], 'ads');
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'adsa');
    // clean other cache files
    if(isset($ad))
    {
      $cacheClass->cleanDirCache($dirs['categories'].$ad['id_ca'].'/ads/');
      $cacheClass->cleanDirCache($dirs['cities'].$ad['id_ci'].'/ads/');
      $cacheClass->cleanDirCache($dirs['regions'].$ad['id_re'].'/ads/');
    }
    if($ad['adState'] != $adsEnt->getNotAcceptedState())
    {
      $tags = $this->getEntityManager()->getRepository('AdItemsBundle:AdsTags')->getTagsByAd($id);
      if(count($tags) > 0)
      { 
        $tagsIds = array();
        foreach($tags as $tag)
        {
          $tagsIds[] = (int)$tag['id_ta'];
          // clean cache for tag
          $cacheClass->cleanDirCache($dirs['tags'].$tag['id_ta'].'/ads/');
        }
        $this->getEntityManager()->getRepository('FrontendFrontBundle:Tags')->updateQuantity('- 1', 'tagAds', $tagsIds);
      }
    }
    // send mail
    $config['mailer']->send($message);
    // update ads_modified table with the last modification
    $this->getEntityManager()->getRepository('AdItemsBundle:AdsModified')->adModified($id, 'ad_ended');
  }

  /**
   * Decrements ads offers counter.
   * @access public
   * @param mixed $ads Array with ads ids or simple integer for one ad's id.
   * @return void
   */
  public function decrementOffers($ads)
  {
    if(is_array($ads))
    {
      $qb = $this->getEntityManager()->createQueryBuilder();
      $qb->update('AdItemsBundle:Ads', 'a')
      ->set('a.adOffers', 'a.adOffers - 1') 
      ->add('where', $qb->expr()->in('a.id_ad', $ads))
      ->getQuery()
      ->getResult();
    }
    else
    {
      $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
      ->set('a.adOffers', 'a.adOffers - 1')
      ->where('a.id_ad = ?1')
      ->setParameter(1, (int)$ads)
      ->getQuery()
      ->execute();
    }
  }

  /**
   * Gets ads for error page.
   * @access public
   * @param int $limit Ads limit.
   * @return array Ads list.
   */
  public function getAdsRand($limit)
  {
    $allAds = $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->getStats('adsa');
    $start = rand(1, $allAds);
    $start = round($start/$limit);
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, c.categoryUrl, c.id_ca
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    WHERE a.adState = :state
    ORDER BY a.id_ad DESC")
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($limit)
    ->setFirstResult($start);
    return $query->getResult();
  }


  /**
   * Gets rand ad.
   * @access public
   * @return array Ads list.
   */
  public function getForTest()
  {
    $stats = $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->find('adsa');
    $rand = rand(1, $stats->getStatValue());
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, u.id_us
    FROM AdItemsBundle:Ads a
    JOIN a.adAuthor u
    WHERE (a.id_ad BETWEEN :param1 AND :param2) AND a.adState = 1")
    ->setParameter('param1', 1)
    ->setParameter('param2', $rand)
    ->setMaxResults(1); 
    $row = $query->getResult();
    return array('id' => $row[0]['id_ad'], 'id2' => '', 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us'], 'data' => $row[0]);
  }

  /**
   * Checks if ad is correct (activated) and if the $user isn't author of this ad.
   * @access public
   * @param int $ad Ad's id.
   * @param int $user User's id.
   * @return array User informations.
   */
  public function testIfIsCorrectAd()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, u.id_us
    FROM AdItemsBundle:Ads a
    JOIN a.adAuthor u
    WHERE a.adState > 0")
    ->setMaxResults(1);
    $row = $query->getResult();
    return array('id' => $row[0]['id_ad'], 'id2' => '', 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']); 
  }

  /**
   * Gets random ads list.
   * @access public
   * @param int $limit Limit of ads to load.
   * @param int $max Number of all ads.
   * @return array Ads list.
   */
  public function getRandomAds($limit, $max)
  {
    $adsEnt = new Ads;
    $start = Tools::getStart($limit, $max);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, c.categoryUrl, c.categoryName
    FROM AdItemsBundle:Ads a
    JOIN a.adCategory c
    WHERE a.adState = :state
    ORDER BY a.id_ad DESC")
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($max)
    ->setFirstResult($start); 
    return $query->getResult();
  }

  /**
   * Gets ads by $word and $categories ids.
   * @access public
   * @param string $word Searched word.
   * @param array $categories Categories ids array.
   * @param array $options Options used to SQL request.
   * @return array Ads list.
   */
  public function searchAd($word, $categories, $options)
  {
    $categoriesWhere = "";
    if(count($categories) > 0)
    {
      $categoriesWhere = "AND a.adCategory IN(".implode(",", $categories).")";
    }
    $adsEnt = new Ads;
    $order = "a.adName ASC";
    $columns = array("titre" => "a.adName", 
    "date" => "a.adStart", "fourchette-a" => "a.adBuyTo", "ville" => "ci.cityName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, a.adBuyTo, a.adOffers,
    SUBSTRING_INDEX(a.adText, '.', 4) AS shortDesc, DATE_FORMAT(a.adStart, '".$options['date']."') AS dateStart, ci.cityName,
    c.id_ca, c.categoryName, c.categoryUrl
    FROM AdItemsBundle:Ads a
    JOIN a.adCity ci
    JOIN a.adCategory c
    WHERE a.adName LIKE :word $categoriesWhere AND a.adState = :state
    ORDER BY $order")
    ->setParameter('word', "%".$word."%")
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }

  /**
   * Counts ads by $word and $categories ids.
   * @access public
   * @param string $word Searched word.
   * @param array $categories Categories ids array.
   * @return array Ads list.
   */
  public function countAds($word, $categories)
  {
    $categoriesWhere = "";
    if(count($categories) > 0)
    {
      $categoriesWhere = "AND a.adCategory IN(".implode(",", $categories).")";
    }
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(a.id_ad)
    FROM AdItemsBundle:Ads a
    WHERE a.adName LIKE :word $categoriesWhere AND a.adState = :state")
    ->setParameter('word', "%".$word."%")
    ->setParameter('state', $adsEnt->getActiveState());
    return (int)$query->getSingleScalarResult();
  }

  
  /**
   * Gets ads for index page
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Ads list.
   */
  public function getForIndex($options)
  {
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, a.adName, a.adStart, a.adState, a.adBuyTo, a.adOffers,
    SUBSTRING(SUBSTRING_INDEX(a.adText, '.', 4), 1, 227) AS shortDesc, DATE_FORMAT(a.adStart, '".$options['date']."') AS dateStart,c.cityName,
    ca.id_ca, ca.categoryUrl, ca.categoryName, u.login, u.id_us,
    DATEDIFF(a.adEnd, NOW()) AS daysToEnd
    FROM AdItemsBundle:Ads a
    JOIN a.adCity c
    JOIN a.adCategory ca
    JOIN a.adAuthor u
    WHERE a.adState = :state
    ORDER BY a.id_ad DESC")
    ->setParameter('state', $adsEnt->getActiveState())
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    if($options['cacheName'] != "") 
    {
      $query->useResultCache(true, 0, $options['cacheName']);
    }
    $result = $query->getResult();
    if($options['cacheName'] != "") 
    {
      $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    }
    return $result;
  }
  
  /**
   * Gets best ads.
   * @access public
   * @param array $options Options array.
   * @return array Ads list.
   */
  public function getBestAds($options)
  {
    $conn = $this->getEntityManager()->getConnection();
    $query = "SELECT a.id_ad, a.name_ad, (a.buy_to_ad - o.price_of) AS gain, c.url_ca
    FROM ads a 
    JOIN offers o ON o.id_of = a.offers_id_of
    JOIN categories c ON a.categories_id_ca = c.id_ca
    ORDER BY gain DESC LIMIT 50";
    $stmt = $conn->prepare($query);
    // $stmt->bindValue(1, $state);
    // $stmt->bindValue(2, $category);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  /**
   * Gets economized money by UMO users.
   * @access public
   * @return float Economized amount
   */
  public function countEconomized()
  {
    $adsEnt = new Ads;
    $query = $this->getEntityManager()
    ->createQuery("SELECT SUM((a.adBuyTo - a.adOfferPrice))
    FROM AdItemsBundle:Ads a WHERE a.adState = :state")
    ->setParameter('state', $adsEnt->getEndedState()); 
    return (float)$query->getSingleScalarResult();
  }

}