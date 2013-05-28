<?php
namespace Catalogue\ImagesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Catalogue\ImagesBundle\Entity\OffersImages;
use Database\MainEntity;

class OffersImagesRepository extends EntityRepository 
{

  /**
   * Gets images by offer id.
   * @access public
   * @param int $offer Offer's id.
   * @return array Files list.
   */
  public function getImagesByOffer($offer, $options)
  {
    $order = "o.offerName ASC";
    $columns = array("offre" => "o.offerName", "date" => "oi.id_oi");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName, DATE_FORMAT(oi.imageDate, '".$options['dateFormat']."') AS dateAdd
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE oi.imageOffer = :offer ORDER BY $order")
    ->setParameter('offer', $offer); 
    $row = $query->getResult();
    if(isset($row[0]['id_oi']))
    {
      return $row;
    }
    return array();
  }

  /**
   * Gets images by offer and user id.
   * @access public
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @return array Files list.
   */
  public function getImagesByOfferAndUser($offer, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE oi.imageOffer = :offer AND o.offerAuthor = :user
    ORDER BY oi.id_oi ASC")
    ->setParameter('offer', (int)$offer) 
    ->setParameter('user', (int)$user); 
    $row = $query->getResult();
    if(isset($row[0]['id_oi']))
    {
      return $row;
    }
    return array();
  }

  /**
   * Gets images by user id.
   * @access public
   * @param array $options Options used by the request.
   * @param int $user User's id.
   * @return array Files list.
   */
  public function getAllImages($options, $user)
  {
    $order = "o.offerName ASC";
    $columns = array("offre" => "o.offerName", "date" => "oi.id_oi");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName, DATE_FORMAT(oi.imageDate, '".$options['dateFormat']."') AS dateAdd
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE o.offerAuthor = :user ORDER BY $order")
    ->setParameter('user', (int)$user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    $row = $query->getResult();
    if(isset($row[0]['id_oi']))
    {
      return $row;
    }
    return array();
  }

  /**
   * Gets image by user id and image id.
   * @access public
   * @param int $image Image's id.
   * @param int $user User's id.
   * @return array Files list.
   */
  public function getImageByUserAndId($image, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE oi.id_oi = :image AND o.offerAuthor = :user")
    ->setParameter('image', (int)$image)
    ->setParameter('user', (int)$user); 
    $row = $query->getResult();
    if(isset($row[0]['id_oi']))
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Gets last added image for $offer.
   * @access public
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @return int Last file name.
   */
  public function getLastNameByOffer($offer, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE o.id_of = :offer AND o.offerAuthor = :user ORDER BY oi.id_oi DESC")
    ->setParameter('offer', (int)$offer)
    ->setParameter('user', (int)$user)
    ->setMaxResults(1); 
    $row = $query->getResult();
    if(isset($row[0]['id_oi']))
    {
      $exp = explode('.', $row[0]['imageName']);
      return (int)$exp[0];
    }
    return 0;
  }

  /**
   * Counts images for $offer.
   * @access public
   * @param int $offer Offer's id.
   * @param int $user User's id.
   * @return int Images count.
   */
  public function countByOffer($offer, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(oi.id_oi) AS allImages
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE o.id_of = :offer AND o.offerAuthor = :user")
    ->setParameter('offer', (int)$offer)
    ->setParameter('user', (int)$user); 
    $row = $query->getResult();
    if(isset($row[0]['allImages']))
    {
      return (int)$row[0]['allImages'];
    }
    return 0;
  }

  /**
   * Gets image for test.
   * @access public
   * @return array Test informations.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName, u.id_us
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    JOIN o.offerAuthor u
    ")
    ->setMaxResults(1);
    $rows = $query->getResult();
    return array('id' => $rows[0]['id_oi'], 'id2' => 0, 'user1' => $rows[0]['id_us'], 'user2' => $rows[0]['id_us']);
  }

  /**
   * Gets images by user id.
   * @access public
   * @param array $options Options used by the request.
   * @return array Files list.
   */
  public function getAllDatabaseImages($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets one image..
   * @access public
   * @param int $image Image's id.
   * @return array Files list.
   */
  public function getOneImage($image)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oi.id_oi, oi.imageName, o.id_of, o.offerName
    FROM CatalogueImagesBundle:OffersImages oi
    JOIN oi.imageOffer o
    WHERE oi.id_oi = :image")
    ->setParameter('image', (int)$image); 
    return $query->getResult();
  }

  /**
   * Delete images by IN clause
   * @access public
   * @param array $images Images array.
   * @return void.
   */
  public function deleteImagesIn($images)
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->delete('from', 'CatalogueImagesBundle:OffersImages oi')
    ->add('where', $qb->expr()->in('oi.id_oi', $images))
    ->getQuery()
    ->execute();
  }

  /**
   * Inserts new images for inserted offer.
   * @access public
   * @param Catalogue\OffersBundle\Entity\Offers $offer Offer's reference.
   * @param array $images Images to insert.
   * @param array $config Config with directories, images prefixes, files limit by offer.
   * @return void
   */
  public function uploadImages($offer, $images, $config)
  {
    if(count($images) == 0 )
    {
      return null;
    }
    $oimEnt = new OffersImages;
    $ids = array();
    $uploaded = 0;
    foreach($images as $i => $image)
    {
      if(count($ids) < $config['maxImages'])
      {
        $newName = $uploaded+1;
        $oimClone = clone $oimEnt;
        $oimClone->setData(array('imageOffer' => $offer, 'imageName' => $newName.'.'.$image['tmpExt'], 'imageDate' => ''));
        $this->getEntityManager()->persist($oimClone);
        $this->getEntityManager()->flush();
        // move images from temporary to final directory
        foreach($config['prefixes'] as $p => $prefix)
        {
          @rename($config['temporary'].$prefix.$image['tmpName'].'.'.$image['tmpExt'], $config['final'].$prefix.$newName.'.'.$image['tmpExt']);
        }
        $uploaded++;
      }
      else
      {
        foreach($config['prefixes'] as $p => $prefix)
        {
          @unlink($config['temporary'].$prefix.$image['tmpName'].'.'.$image['tmpExt']);
        }
      }
      $ids[] = (int)$image['id_oit'];
    }
    // update number of offer images
    $this->getEntityManager()->createQueryBuilder()
    ->update('Catalogue\OffersBundle\Entity\Offers', 'o')
    ->set('o.offerImages', $uploaded)
    ->where('o.id_of = ?1')
    ->setParameter(1, $offer->getIdOf())
    ->getQuery()
    ->execute();
    $this->getEntityManager()->getRepository('FrontendFrontBundle:Stats')->updateQuantity('+ '.$uploaded, 'ofim');

    // remove all temporary files
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb->delete('from', 'CatalogueImagesBundle:OffersImagesTmp oit');
    $qb->add('where', $qb->expr()->in('oit.id_oit', $ids));
    $query = $qb->getQuery();
    $query->execute();
  }


}