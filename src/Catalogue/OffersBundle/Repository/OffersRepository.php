<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\DriverManager;

use Catalogue\OffersBundle\Entity\Offers;
use Catalogue\OffersBundle\Entity\OffersFormFields;
use Catalogue\OffersBundle\Entity\OffersTags;
use Catalogue\OffersBundle\Entity\OffersDeliveryZones;
use Category\CategoriesBundle\Entity\FormFields;
use Frontend\FrontBundle\Entity\Tags;
use Ad\ItemsBundle\Entity\Ads;
use Others\Tools;
use Database\MainEntity;

class OffersRepository extends EntityRepository 
{

  /**
   * Gets all offers by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function getOffersListByUser($options, $user)
  {
    $order = "o.offerName ASC";
    $columns = array("nom" => "o.offerName", "date" => "o.id_of");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, u.id_us, c.id_ci, r.id_re, ca.id_ca, cat.id_cat,
    DATE_FORMAT(o.offerDate, '".$options['dateFormat']."') AS dateOffer
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN o.offerCategory ca
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    WHERE o.offerAuthor = :user AND o.offerDeleted = 0
    ORDER BY $order")
    ->setParameter('user', $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets offers list by catalogue.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $catalogue Catalogue's id.
   * @return array Offers list.
   */
  public function getOffersByCatalogue($options, $catalogue)
  {
    $order = "o.offerName ASC";
    $columns = array("titre" => array("o.offerName"), "date" => "o.id_of", "categorie" => "c.categoryName", "prix" => "o.offerPrice");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerPrice, c.categoryName, c.categoryUrl, DATE_FORMAT(o.offerDate, '".$options['date']."') AS addedDate
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCategory c
    WHERE o.offerCatalogue = :catalogue AND o.offerDeleted = 0
    ORDER BY $order")
    ->setParameter('catalogue', $catalogue)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);  
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }

  /**
   * Gets offer data to edit by his author.
   * @access public
   * @param int $offer Offer's id.
   * @param int $user Author's id.
   * @return array Offer's data.
   */
  public function getOfferData($offer, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, u.id_us, c.id_ca, cat.id_cat,
    o.offerName, o.offerText, o.offerDate, o.offerObjetState, o.offerPrice, o.offerTax, o.offerImages,
    ci.id_ci, ci.cityName, r.id_re, r.regionName, cou.id_co, cou.countryName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerAuthor u
    JOIN o.offerCategory c
    JOIN o.offerCatalogue cat
    JOIN o.offerCity ci
    JOIN ci.cityRegion r
    JOIN r.regionCountry cou
    WHERE o.id_of = :offer AND o.offerAuthor = :user AND o.offerDeleted = 0")
    ->setParameter('user', (int)$user)
    ->setParameter('offer', (int)$offer); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets offer data by offer id.
   * @access public
   * @param int $offer Offer's id.
   * @return array Offer's data.
   */
  public function getOneOffer($offer)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, u.id_us, u.login, u.email, c.id_ca, c.categoryName, c.categoryUrl, cat.id_cat, cat.catalogueName,
    o.offerName, o.offerText, o.offerDate, o.offerObjetState, o.offerPrice, o.offerTax,
    ci.id_ci, ci.cityName, r.id_re, cou.id_co
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerAuthor u
    JOIN o.offerCategory c
    JOIN o.offerCatalogue cat
    JOIN o.offerCity ci
    JOIN ci.cityRegion r
    JOIN r.regionCountry cou
    WHERE o.id_of = :offer AND o.offerDeleted = 0")
    ->setParameter('offer', (int)$offer); 
    $rows = $query->getResult();
    if(isset($rows[0]['id_of']))
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets offers list by catalogue.
   * @access public
   * @param array $options Options used to SQL request.
   * @param string $query WHERE query.
   * @param array $params Parameters used by the WHERE query.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function getOffersByFilterAndUser($options, $query, $params, $user)
  {
    $where = 'WHERE u.id_us = :user';
    if($query != '')
    {
      $where .= " AND ".$query;
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, u.id_us
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerAuthor u
    JOIN o.offerCategory cat
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    ".$where." AND o.offerDeleted = 0
    ORDER BY o.offerName ASC")
    ->setParameter('user' , $user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    foreach($params as $p => $param)
    {
      $query->setParameter($p, $param);
    }
    return $query->getResult();
  }

  /**
   * Gets offers list by catalogue.
   * @access public
   * @param string $query WHERE query.
   * @param array $params Parameters used by the WHERE query.
   * @param int $user User's id.
   * @return array Offers list.
   */
  public function countOffersByFilterAndUser($query, $params, $user)
  {
    $where = 'WHERE u.id_us = :user';
    if($query != '')
    {
      $where .= " AND ".$query;
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(o.id_of)
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerAuthor u
    JOIN o.offerCategory cat
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    ".$where." AND o.offerDeleted = 0
    ORDER BY o.offerName ASC")
    ->setParameter('user' , $user);
    foreach($params as $p => $param)
    {
      $query->setParameter($p, $param);
    }
    return $query->getSingleScalarResult();
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
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, SUBSTRING_INDEX(o.offerText, '.', 4) AS shortDesc, cat.id_cat, cat.catalogueName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    WHERE o.offerCategory = :category AND o.offerDeleted = 0
    ORDER BY o.id_of DESC")
    ->setParameter('category', $category)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }

  /**
   * Gets offers list by region's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $region Region id.
   * @return array Offers list.
   */
  public function getByRegion($options, $region)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, cat.id_cat, cat.catalogueName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    JOIN o.offerCity c
    WHERE c.cityRegion = :region AND o.offerDeleted = 0
    ORDER BY o.id_of DESC")
    ->setParameter('region', $region)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }

  /**
   * Gets offers list by city's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $city City id.
   * @return array Offers list.
   */
  public function getByCity($options, $city)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, cat.id_cat, cat.catalogueName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    WHERE o.offerCity = :city AND o.offerDeleted = 0
    ORDER BY o.id_of DESC")
    ->setParameter('city', $city)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $query->useResultCache(true, 0, $options['cacheName']);
    $result = $query->getResult();
    $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    return $result;
  }


  /**
   * Gets $limit last added offers.
   * @access public
   * @param int $limit Offer's limit.
   * @return array Offers list.
   */
  public function getLastOffers($limit)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, SUBSTRING_INDEX(o.offerText, '.', 4) AS shortDesc, c.id_cat, c.catalogueName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue c
    WHERE o.offerDeleted = 0
    ORDER BY o.id_of DESC")
    ->setMaxResults($limit); 
    return $query->getResult();
  }


  /**
   * Gets external offers (from user's store for exemple) for user.
   * @access public
   * @param int $user User's id.
   * @param string $source Source's alias.
   * @param boolean $makeArray Sets if we do an unidimensional array.
   * @return array Offers list.
   */
  public function getExternalOffersByUser($user, $source, $makeArray = false)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerExternalId, cat.id_cat, c.id_ci, ca.id_ca, r.id_re, co.id_co
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCategory ca
    JOIN o.offerCatalogue cat 
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    WHERE o.offerAuthor = :user AND o.offerExternalSystem = :source AND o.offerDeleted = 0
    ORDER BY o.id_of DESC")
    ->setParameter('user', (int)$user) 
    ->setParameter('source', $source); 
    $rows = $query->getResult();
    if(!$makeArray)
    {
      return $rows;
    }
    $offers = array();
    $externals = array();
    foreach($rows as $r => $row)
    {
      $offers[$row['id_of']] = array('id' => $row['id_of'], 'category' => $row['id_ca'], 'catalogue' => $row['id_cat'],
      'city' => $row['id_ci'], 'region' => $row['id_re'], 'country' => $row['id_co']);
      $externals[$row['id_of']] = $row['offerExternalId'];
    }
    return array('externals' => $externals, 'offers' => $offers);
  }

  /**
   * Gets one external offer
   * @access public
   * @param int $user User's id.
   * @param string $source Source's alias.
   * @param string $id External id.
   * @return array Offers list.
   */
  public function getExternalOfferByUser($user, $source, $id)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerExternalId, cat.id_cat, c.id_ci, ca.id_ca, r.id_re, co.id_co
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCategory ca
    JOIN o.offerCatalogue cat 
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN r.regionCountry co
    WHERE o.offerAuthor = :user AND o.offerExternalId = :id AND o.offerExternalSystem = :source
    ORDER BY o.id_of DESC")
    ->setParameter('user', (int)$user)
    ->setParameter('id', $id)
    ->setParameter('source', $source); 
    $rows = $query->getResult();
    if(isset($rows[0]['id_of']))
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets offers list.
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Offers list.
   */
  public function getAllOffers($options)
  {
    $order = "o.offerName ASC";
    $columns = array("titre" => "o.offerName", 
    "date" => "o.id_of", "prix" => "o.offerPrice", "categorie" => "c.categoryName", "catalogue" => "cat.catalogueName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerExternalSystem, cat.id_cat, cat.catalogueName, c.categoryName, c.categoryUrl, c.id_ca, 
    o.offerPrice, DATE_FORMAT(o.offerDate, '".$options['date']."') AS addedDate, u.login, u.id_us
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    JOIN o.offerCategory c
    WHERE o.offerDeleted = 0
    ORDER BY $order")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    if(isset($options['cacheName']))
    {
      $query->useResultCache(true, 0, $options['cacheName']);
      $result = $query->getResult();
      $query->getQueryCacheDriver()->save($options['cacheName'], $result);
    }
    else
    {
      $result = $query->getResult();
    }
    return $result;
  }

  /**
   * Gets offers when adding new image.
   * @access public
   * @param int $limit Images limit by offer.
   * @param int $user Author's id.
   * @return array Offer's data.
   */
  public function getOffersForImageAdd($limit, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerText, o.offerDate, o.offerObjetState, o.offerPrice, o.offerTax, o.offerImages 
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerAuthor u
    WHERE o.offerAuthor = :user AND o.offerDeleted = 0 AND o.offerImages < :limit ")
    ->setParameter('limit', (int)$limit)
    ->setParameter('user', (int)$user); 
    return $query->getResult();
  }

  /**
   * Adds all images.
   * @access public
   * @param int $user Author's id.
   * @return int All images added by $user.
   */
  public function sumAllImages($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT SUM(o.offerImages) AS allImages
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerAuthor u
    WHERE o.offerAuthor = :user AND o.offerDeleted = 0")
    ->setParameter('user', (int)$user); 
    $row = $query->getResult();
    if(isset($row[0]['allImages']))
    {
      return (int)$row[0]['allImages'];
    }
    return 0;
  }

  /**
   * Checks if offer exists in the ad offers_id_of field.
   * @access public
   * @param int $catalogue Offer's catalogue id.
   * @return bool True if exists, false otherwise
   */
  public function catalogueOfferInAd($catalogue)
  {
    $connection = $this->getEntityManager()->getConnection();
    $statement = $connection->prepare("SELECT COUNT(o.id_of) AS allOffers FROM offers o
    JOIN catalogues c ON c.id_cat = o.catalogues_id_cat
    JOIN ads a ON o.id_of = a.offers_id_of WHERE c.id_cat = :idCatalogue AND o.deleted_of = 0");
    $statement->execute(array(':idCatalogue' => (int)$catalogue));
    $row = $statement->fetch();
    return $row['allOffers'];
  }

  /**
   * Adds new offer into database.
   * @access public
   * @param array Array with referencies.
   * @param array Array with some datas.
   * @return Catalogue\OffersBundle\Entity\Offers Offers entity.
   */
  public function addNewOffer($referencies, $data)
  {
    // add offer into database
    $offEnt = new Offers;
    $offEnt->setOfferAuthor($referencies['user']);
    $offEnt->setOfferCatalogue($referencies['catalogue']);
    $offEnt->setOfferCategory($referencies['category']);
    $offEnt->setOfferCity($referencies['city']);
    $offEnt->setOfferPrice((float)Tools::normalizePrice($data['offer']['price']));
    $offEnt->setOfferName($data['offer']['name']);
    $offEnt->setOfferText($data['offer']['description']);
    $offEnt->setOfferTax((float)$data['offer']['tax']);
    $offEnt->setOfferObjetState((int)$data['offer']['state']);
    if(ctype_alnum($data['offer']['external']))
    {
      $offEnt->setOfferExternalId($data['offer']['external']);
    }
    else
    {
      $offEnt->setOfferExternalId('');
    }
    $offEnt->setOfferExternalSystem($data['offer']['system']);
    $offEnt->setOfferDate('');
    $offEnt->setOfferImages(0);
    $offEnt->setOfferDeleted(0);
    $this->getEntityManager()->persist($offEnt);
    $this->getEntityManager()->flush();
    // insert offer characteristics
    $ofoEnt = new OffersFormFields;
    foreach($data['formFields'] as $field)
    {
      $value = '';
      if(isset($field['codeName']) && isset($data['others'][$field['codeName']]))
      {
        $value = $data['others'][$field['codeName']];
      }
      $ffiEnt = $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\FormFields', (int)$field['id_ff']);
      $ffcEnt = $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\FormFieldsCategories',  (int)$data['offer']['categoryId']);
      $cloneOff = clone $ofoEnt;
      $cloneOff->setNewValues(array('offers_id_of' => $offEnt, 
      'form_fields_id_ff' => $ffiEnt,
      'categories_id_ca' => $ffcEnt, 'fieldValue' => $value));
      $this->getEntityManager()->persist($cloneOff); 
      $this->getEntityManager()->flush();
    }
    if(count($data['tags']) > 0)
    {
      // get written tags 
      $formTags = array();
      for($i = 1; $i < 11; $i++)
      {
// TODO : for be sure, pass by SQL injection filter
        if($data['tags']['tag'.$i] != '' && !in_array($data['tags']['tag'.$i], $formTags))
        {
          $formTags[] = $data['tags']['tag'.$i];
        }
      }
      // if tags have to be added
      if(count($formTags) > 0)
      {
        $tagsCount = 0;
        $tagEnt = new Tags;
        $oftEnt = new OffersTags;
        $dbTags = $this->getEntityManager()->getRepository('FrontendFrontBundle:Tags')->getTagsIn($formTags);
        // if not exists, insert a new one
        // if exists, update quantity
        foreach($formTags as $tag)
        {
          if(in_array($tag, $dbTags))
          {
            // update quantity and get id
            $keys = array_keys($dbTags, $tag);
            $idTag = (int)$keys[0];
            $cloneTag = $this->getEntityManager()->getReference('Frontend\FrontBundle\Entity\Tags', $idTag);
            $qb = $this->getEntityManager()->createQueryBuilder();
            $q = $qb->update('Frontend\FrontBundle\Entity\Tags', 't')
            ->set('t.tagOffers', 't.tagOffers + 1')
            ->where('t.id_ta = ?1')
            ->setParameter(1, $idTag)
            ->getQuery();
            $p = $q->execute();
          }
          else
          {
            $tagsCount++;
            $cloneTag = clone $tagEnt;
            $cloneTag->setTagName($tag);
            $cloneTag->setTagAds(0);
            $cloneTag->setTagOffers(1);
            $this->getEntityManager()->persist($cloneTag);
            $this->getEntityManager()->flush();
          }
          // insert new tags relations
          $cloneAdt = clone $oftEnt;
          $cloneAdt->setOffersIdOf($offEnt);
          $cloneAdt->setTagsIdTa($cloneTag);
          $this->getEntityManager()->persist($cloneAdt);
          $this->getEntityManager()->flush(); 
          $tagEnt->resetStaticTag(array('id_ta', 'tagName', 'tagAds', 'tagOffers'));
        }
        $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ '.$tagsCount, 'tags');
      }
    }
    // if delivery zones added
    if(count($data['delivery']) > 0)
    {
      $odzEnt = new OffersDeliveryZones();
      foreach($data['delivery'] as $z => $zone)
      {
        $zoneRef = $this->getEntityManager()->getReference('Geography\ZonesBundle\Entity\DeliveryZones', $z);
        $cloned = clone $odzEnt;
        $cloned->setData(array('deliveryZonesIdDz' => $zoneRef, 'offersIdOf' => $offEnt, 'zonePrice' => (float)$zone));
        $this->getEntityManager()->persist($cloned);
        $this->getEntityManager()->flush(); 
      }
    }
    // Update offers number in catalogue
    $q = $this->getEntityManager()->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Catalogues', 'c')
    ->set('c.catalogueOffers', 'c.catalogueOffers + 1')
    ->where('c.id_cat = ?1')
    ->setParameter(1, (int)$data['offer']['catalogueId'])
    ->getQuery()
    ->execute();
    // stats : update regions, cities, categories quantity
    $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity((int)$data['offer']['cityId']);
    $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('+ 1', $cityRow[0]['id_re'], 'offers');
    $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('+ 1', (int)$data['offer']['cityId'], 'offers');
    $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('+ 1', (int)$data['offer']['categoryId'], 'offers');
    return $offEnt;
 }

  /**
   * Edits an offer.
   * @access public
   * @param integer $offer Offer's id.
   * @param array $data Data to update (also old data; the both separated to different arrays).
   * @return void
   */
  public function editOffer($offer, $data)
  {
    // update offer
    $queryBuilder = $this->getEntityManager()->createQueryBuilder();
    $q = $queryBuilder->update('Catalogue\OffersBundle\Entity\Offers', 'o')
    ->set('o.offerCategory', (int)$data['offer']['new']['offerCategory'])
    ->set('o.offerCatalogue', (int)$data['offer']['new']['offerCatalogue'])
    ->set('o.offerName', '?1')
    ->set('o.offerText', '?2')
    ->set('o.offerCity', (int)$data['offer']['new']['offerCity'])
    ->set('o.offerPrice', (float)Tools::normalizePrice($data['offer']['new']['offerPrice']))
    ->set('o.offerObjetState', (int)$data['offer']['new']['offerObjetState'])
    ->where('o.id_of = ?3')
    ->setParameter(1, $data['offer']['new']['offerName'])
    ->setParameter(2, $data['offer']['new']['offerText'])
    ->setParameter(3, $offer)
    ->getQuery()
    ->execute();
    // get offer reference
    $offerStatic = $this->getEntityManager()->getReference('Catalogue\OffersBundle\Entity\Offers', $offer);
    // if category changed, delete form fields relations, insert the new ones
    if($data['offer']['new']['offerCategory'] != $data['offer']['old']['category'])
    {
      $q2 = $this->getEntityManager()->createQueryBuilder()->delete('Catalogue\OffersBundle\Entity\OffersFormFields', 'offd')
      ->where('offd.offers_id_of = ?1')
      ->setParameter(1, $offer)
      ->getQuery()
      ->execute();
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('+ 1', (int)$data['offer']['new']['offerCategory'], 'offers');
      $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('- 1', (int)$data['offer']['old']['category'], 'offers');

      $ofiEnt = new OffersFormFields;
      $ffiEnt = new FormFields;
      foreach($data['formField']['new'] as $field)
      {
        $value = '';
        if(isset($field['codeName']) && isset($data['others'][$field['codeName']]))
        {
          $value = $data['others'][$field['codeName']];
        }
        $ffiEnt = $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\FormFields', (int)$field['id_ff']);
        $ffcEnt = $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\FormFieldsCategories',  (int)$data['offer']['new']['offerCategory']);
        $cloneOff = clone $ofiEnt;
        $cloneOff->setNewValues(array('offers_id_of' => $offerStatic, 
        'form_fields_id_ff' => $ffiEnt, 
        'categories_id_ca' => $ffcEnt,
        'fieldValue' => $value));
        $this->getEntityManager()->persist($cloneOff);
        $this->getEntityManager()->flush();
      }
    }
    else
    {
      foreach($data['formField']['old'] as $field)
      {
        $value = '';
        if(isset($field['codeName']) && isset($data['others'][$field['codeName']]))
        {
          $value = $data['others'][$field['codeName']];
        }
        $q = $this->getEntityManager()->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\OffersFormFields', 'off')
        ->set('off.fieldValue', '?1')
        ->where('off.offers_id_of = ?2 AND off.form_fields_id_ff= ?3 AND off.categories_id_ca = ?4')
        ->setParameter(1, $value)
        ->setParameter(2, $offer)
        ->setParameter(3, (int)$field['id_ff'])
        ->setParameter(4, (int)$data['offer']['old']['category'])
        ->getQuery()
        ->execute();
      }
    }
    // if catalogue changes, modify the counter
    if($data['offer']['old']['catalogue'] != $data['offer']['new']['offerCatalogue'])
    {
      // -1
      $q = $this->getEntityManager()->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Catalogues', 'c')
      ->set('c.catalogueOffers', 'c.catalogueOffers - 1')
      ->where('c.id_cat = ?1')
      ->setParameter(1, (int)$data['offer']['old']['catalogue'])
      ->getQuery()
      ->execute();
      // +1
      $q = $this->getEntityManager()->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Catalogues', 'c')
      ->set('c.catalogueOffers', 'c.catalogueOffers + 1')
      ->where('c.id_cat = ?1')
      ->setParameter(1, (int)$data['offer']['new']['offerCatalogue'])
      ->getQuery()
      ->execute();
    }
    // if cities are differents
    if($data['offer']['old']['city'] != $data['offer']['new']['offerCity'])
    {
      $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity((int)$data['offer']['old']['city']);
      $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('- 1', $cityRow[0]['id_re'], 'offers');
      $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('- 1', (int)$data['offer']['old']['city'], 'offers');

      $cityRow = $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->getByCity((int)$data['offer']['new']['offerCity']);
      $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('+ 1', $cityRow[0]['id_re'], 'offers');
      $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('+ 1', (int)$data['offer']['new']['offerCity'], 'offers');
    }
    // if delivery zones added
    $this->getEntityManager()->getRepository('CatalogueOffersBundle:OffersDeliveryZones')->deletePricingForOffer($offer);
    if(count($data['delivery']) > 0)
    {
      $odzEnt = new OffersDeliveryZones();
      foreach($data['delivery'] as $z => $zone)
      {
        $zoneRef = $this->getEntityManager()->getReference('Geography\ZonesBundle\Entity\DeliveryZones', $z);
        $cloned = clone $odzEnt;
        $cloned->setData(array('deliveryZonesIdDz' => $zoneRef, 'offersIdOf' => $offerStatic, 'zonePrice' => (float)$zone));
        $this->getEntityManager()->persist($cloned);
        $this->getEntityManager()->flush(); 
      }
    }
  }

  /**
   * Delete offer from the database.
   * @access public
   * @param int $id Deleted offer id.
   * @param array $offerRow Offer data.
   * @param array $config Config data.
   * @return void
   */
  public function deleteOffer($id, $offerRow, $config)
  {
    $cacheClass = $this->getEntityManager()->getConfiguration()->getResultCacheImpl();
    $dirs = $cacheClass->getCacheStructure();
    $message = null;
    // check if offer is used as accepted offer
    $adsEnt = new Ads;
    $isInAd = $this->getEntityManager()->getRepository('AdItemsBundle:Ads')->isValidOfferForAd($id);
    $tags = $this->getEntityManager()->getRepository('CatalogueOffersBundle:OffersTags')->getTagsByOffer($id);
    $adsList = $this->getEntityManager()->getRepository('AdItemsBundle:AdsOffers')->getAllAdsByOffer($id);
    $images = $this->getEntityManager()->getRepository('CatalogueImagesBundle:OffersImages')->getImagesByOffer($id);
    if(!$isInAd)
    {
      $this->getEntityManager()->createQueryBuilder()->delete('Catalogue\OffersBundle\Entity\Offers', 'o')
      ->where('o.id_of = ?1')
      ->setParameter(1, (int)$id)
      ->getQuery()
      ->execute();
      $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- 1', 'offa');
    }
    // clean /offers/all 
    $cacheClass->cleanDirCache($dirs['offers'].'all/');

    // decrement ads counters
    $adsIds = array();
    $adsDelAo = array();
    $adsDelMails = array();
    foreach($adsList as $ad)
    {
      $adsIds[] = (int)$ad['id_ad'];
      // if ad is not ended, remove the offer from this ad too
      if($ad['adState'] == $adsEnt->getActiveState())
      {
        $adsDelAo[] = (int)$ad['id_ad'];
        // add mail to prevent ad authors too
        $adsDelMails[] = $ad['email'];
      }
      $cacheClass->cleanDirCache($dirs['ads'].$ad['id_ad'].'/');
    }
    if(count($adsIds) > 0)
    {
      $this->getEntityManager()->getRepository('AdItemsBundle:Ads')->decrementOffers($adsIds);
      $this->getEntityManager()->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Offers', 'o')
      ->set('o.offerName', '?1')
      ->set('o.offerDeleted', '?2')
      ->where('o.id_of = ?3')
      ->setParameter(1, $config['title'])
      ->setParameter(2, 1)
      ->setParameter(3, (int)$id)
      ->getQuery()
      ->execute();
      $this->getEntityManager()->createQueryBuilder()->update('Ad\ItemsBundle\Entity\Ads', 'a')
      ->set('a.adOffer', '?1')
      ->where('a.adOffer = ?2 AND a.adState = ?3')
      ->setParameter(1, 0)
      ->setParameter(2, (int)$id)
      ->setParameter(3, $adsEnt->getActiveState())
      ->getQuery()
      ->execute();
      // send e-mail
      $vars = array('{OFFER_NAME}');
      $template = file_get_contents(rootDir.'mails/offer_deleted_ad_prevent.maildoc');
      $urls = array($offerRow['offerName']);
      $parsedTpl = str_replace($vars, $urls, $template);
      $message = \Swift_Message::newInstance()
      ->setSubject("Offre ".$ad['adName']." vient d'être supprimée")
      ->setFrom($config['from'])
      ->setTo($config['from'])
      ->setBcc($adsDelMails)
      ->setContentType("text/html")
      ->setBody($parsedTpl);
    }
    // stats : update regions, cities, categories quantity
    $this->getEntityManager()->getRepository('GeographyRegionsBundle:Regions')->updateQuantity('- 1', $offerRow['id_re'], 'offers');
    $this->getEntityManager()->getRepository('GeographyCitiesBundle:Cities')->updateQuantity('- 1', (int)$offerRow['id_ci'], 'offers');
    $this->getEntityManager()->getRepository('CategoryCategoriesBundle:Categories')->updateQuantity('- 1', (int)$offerRow['id_ca'], 'offers');
    $this->getEntityManager()->getRepository('UserProfilesBundle:Users')->updateQuantity('- 1', $offerRow['id_us'], 'userOffers');
    $this->getEntityManager()->getRepository('CatalogueOffersBundle:Catalogues')->updateQuantity('- 1', $offerRow['id_cat'], 'catalogueOffers');
    // clean other cache files
    $cacheClass->cleanDirCache($dirs['categories'].$offerRow['id_ca'].'/offers/');
    $cacheClass->cleanDirCache($dirs['cities'].$offerRow['id_ci'].'/offers/');
    $cacheClass->cleanDirCache($dirs['regions'].$offerRow['id_re'].'/offers/');
    $cacheClass->cleanDirCache($dirs['catalogues'].$offerRow['id_cat'].'/');

    // delete from ads_offers
    if($isInAd && count($adsDelAo) > 0)
    {
      $this->getEntityManager()->getRepository('AdItemsBundle:AdsOffers')->deleteOffers($adsDelAo);
    }
    // decrement tags
    if(count($tags) > 0)
    {
      $tagsIds = array();
      foreach($tags as $tag)
      {
        $tagsIds[] = (int)$tag['id_ta'];
        // clean cache for tag
        $cacheClass->cleanDirCache($dirs['tags'].$tag['id_ta'].'/offers/');
      }
      $this->getEntityManager()->getRepository('FrontendFrontBundle:Tags')->updateQuantity('- 1', 'tagOffers', $tagsIds);        
    }
    // delete images too
    if(count($images) > 0)
    {
      $imagesIds = array();
      foreach($images as $i => $image)
      {
        $imagesIds[] = (int)$image['id_oi'];
      }
      $allImages = count($imagesIds);
      $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('- '.$allImages, 'ofim');
      $this->getEntityManager()->getRepository('CatalogueImagesBundle:OffersImages')->deleteImagesIn($imagesIds);
      $dir = $config['offersDir'].'/'.$id.'/';
      foreach($images as $image)
      {
        foreach($config['prefix'] as $p => $prefix)
        {
          @unlink($dir.$prefix.$image['imageName']);
        }
        @unlink($dir.$image['imageName']);
      }
    }
    @unlink($dir);
    if($message != null)
    {
      $config['mailer']->send($message);
    }
  }

  /**
   * Gets offers for error page.
   * @access public
   * @param int $limit Offers limit.
   * @return array Offers list.
   */
  public function getOffersRand($limit)
  {
    $allOffers = $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->getStats('offa');
    $start = rand(1, $allOffers);
    $start = round($start/$limit);
    $offEnt = new Offers;
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerExternalSystem, cat.id_cat, cat.catalogueName, c.categoryName, c.categoryUrl, c.id_ca
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCategory c
    JOIN o.offerCatalogue cat
    WHERE o.offerDeleted = :deleted")
    ->setParameter('deleted', $offEnt->getNotDeletedState())
    ->setMaxResults($limit)
    ->setFirstResult($start);
    return $query->getResult();
  }

  /**
   * Gets offer to test.
   * @access public
   * @return array Offers list.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerExternalSystem, cat.id_cat, cat.catalogueName, c.categoryName, c.categoryUrl, c.id_ca, 
    u.login, u.id_us
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    JOIN o.offerCategory c")
    ->setMaxResults(1);  
    $rows = $query->getResult();
    return array('id' => $rows[0]['id_of'], 'id2' => 0, 'user1' => $rows[0]['id_us'], 'user2' => $rows[0]['id_us']);
  }

  /**
   * Gets offer to test.
   * @access public
   * @return array Offers list.
   */
  public function getForTestSendProposition()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerExternalSystem, cat.id_cat, cat.catalogueName, c.categoryName, c.categoryUrl, c.id_ca, 
    u.login, u.id_us
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    JOIN o.offerCategory c")
    ->setMaxResults(1);  
    $rows = $query->getResult();

    $query2 = $this->getEntityManager()
    ->createQuery("SELECT a.id_ad, u.id_us
    FROM AdItemsBundle:Ads a
    JOIN a.adAuthor u
    WHERE u.id_us != :user ")
    ->setParameter('user', $rows[0]['id_us'])
    ->setMaxResults(1);  
    $ads = $query2->getResult();

    return array('id' => $rows[0]['id_of'], 'id2' => $ads[0]['id_ad'], 'user1' => $rows[0]['id_us'], 'user2' => $ads[0]['id_us']);
  }

  /**
   * Gets random offers.
   * @access public
   * @param int $limit Limit of offers to load.
   * @param int $max Number of offers.
   * @return array Offers list.
   */
  public function getRandomOffers($limit, $max)
  {
    $start = Tools::getStart($limit, $max);
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, u.id_us, c.id_ci, r.id_re, ca.id_ca, cat.id_cat, cat.catalogueName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN o.offerCategory ca
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    WHERE o.offerDeleted = 0
    ORDER BY o.id_of DESC")
    ->setMaxResults($max)
    ->setFirstResult($start); 
    return $query->getResult();
  }

  /**
   * Search offers.
   * @access public
   * @param string $word Searched word.
   * @param array $categories Categories ids array.
   * @param array $options Options used to SQL request.
   * @return array Offers list.
   */
  public function searchOffer($word, $categories, $options)
  {
    $categoriesWhere = "";
    if(count($categories) > 0)
    {
      $categoriesWhere = "AND o.offerCategory IN(".implode(",", $categories).")";
    }
    $order = "o.offerName ASC";
    $columns = array("titre" => "o.offerName", 
    "date" => "o.id_of", "prix" => "o.offerPrice", "categorie" => "c.categoryName", "catalogue" => "cat.catalogueName");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerExternalSystem, cat.id_cat, cat.catalogueName, c.categoryName, c.categoryUrl, c.id_ca, 
    o.offerPrice, DATE_FORMAT(o.offerDate, '".$options['date']."') AS addedDate, u.login, u.id_us
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    JOIN o.offerCategory c
    WHERE o.offerName LIKE :word $categoriesWhere AND o.offerDeleted = 0
    ORDER BY $order")
    ->setParameter('word', "%".$word."%")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']);
    return $query->getResult();
  }
  /**
   * Search offers.
   * @access public
   * @param string $word Searched word.
   * @param array $categories Categories ids array.
   * @return array Offers list.
   */
  public function countOffers($word, $categories)
  {
    $categoriesWhere = "";
    if(count($categories) > 0)
    {
      $categoriesWhere = "AND o.offerCategory IN(".implode(",", $categories).")";
    }
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(o.id_of) FROM CatalogueOffersBundle:Offers o
    WHERE o.offerName LIKE :word $categoriesWhere AND o.offerDeleted = 0")
    ->setParameter('word', "%".$word."%");
    return (int)$query->getSingleScalarResult();
  }

  /**
   * Gets offers for index page.
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Offers list.
   */
  public function getForIndex($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT o.id_of, o.offerName, o.offerPrice, u.id_us, c.id_ci, r.id_re, ca.id_ca, cat.id_cat,
    DATE_FORMAT(o.offerDate, '".$options['date']."') AS dateOffer, cat.id_cat, cat.catalogueName
    FROM CatalogueOffersBundle:Offers o
    JOIN o.offerCity c
    JOIN c.cityRegion r
    JOIN o.offerCategory ca
    JOIN o.offerCatalogue cat
    JOIN o.offerAuthor u
    WHERE o.offerDeleted = 0
    ORDER BY o.id_of DESC")
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
  public function getBestOffers($options)
  {
    $conn = $this->getEntityManager()->getConnection();
    $query = "SELECT o.name_of, o.id_of, o.price_of
    FROM ads a 
    JOIN offers o ON o.id_of = a.offers_id_of
    ORDER BY o.price_of  DESC LIMIT 50";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

}