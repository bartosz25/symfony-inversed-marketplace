<?php
namespace Catalogue\ImagesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;

class OffersImagesTmpRepository extends EntityRepository 
{

  /**
   * Gets last file in the database.
   * @access public
   * @return string Last filename.
   */
  public function getLastFile()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oit.id_oit
    FROM CatalogueImagesBundle:OffersImagesTmp oit ORDER BY oit.id_oit DESC")
    ->setMaxResults(1); 
    $row = $query->getResult();
    if(isset($row[0]['id_oit']))
    {
      return (int)$row[0]['id_oit'];
    }
    return 0;
  }

  /**
   * Gets files by temporary id.
   * @access public
   * @param string $offer Alphabnumerical offer code.
   * @return array Files list.
   */
  public function getByOfferId($offer)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oit.id_oit, oit.tmpName, oit.tmpExt, oit.tmpSize, oit.tmpDate
    FROM CatalogueImagesBundle:OffersImagesTmp oit WHERE oit.tmpOffer = :offer")
    ->setParameter('offer' , $offer); 
    $row = $query->getResult();
    if(isset($row[0]['id_oit']))
    {
      return $row;
    }
    return array();
  }

  /**
   * Gets files by his $id and $user id.
   * @access public
   * @param int $id Image's id.
   * @param int $user User's id.
   * @return array Files list or empty if isn't correct.
   */
  public function getByIdAndUser($id, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oit.id_oit, oit.tmpName, oit.tmpExt, oit.tmpSize, oit.tmpDate
    FROM CatalogueImagesBundle:OffersImagesTmp oit WHERE oit.id_oit = :id AND oit.tmpAuthor = :author")
    ->setParameter('id' , (int)$id)
    ->setParameter('author' , (int)$user);
    $row = $query->getResult();
    if(isset($row[0]['id_oit']))
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Gets files by temporary id and connected user id.
   * @access public
   * @param string $offer Alphabnumerical offer code.
   * @param int $user User's id.
   * @return array Files list.
   */
  public function getByOfferIdAndUser($offer, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT oit.id_oit, oit.tmpName, oit.tmpExt, oit.tmpSize, oit.tmpDate
    FROM CatalogueImagesBundle:OffersImagesTmp oit 
    WHERE oit.tmpOffer = :offer AND oit.tmpAuthor = :user")
    ->setParameter('offer', $offer)
    ->setParameter('user', $user); 
    $row = $query->getResult();
    if(isset($row[0]['id_oit']))
    {
      return $row;
    }
    return array();
  }

  /**
   * Count uploaded files by temporary id.
   * @access public
   * @param string $offer Alphabnumerical offer code.
   * @return int Uploaded images for this offer.
   */
  public function countUploaded($offer)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT COUNT(oit.id_oit) AS counted
    FROM CatalogueImagesBundle:OffersImagesTmp oit
    WHERE oit.tmpOffer = :offer")
    ->setParameter('offer', $offer); 
    $row = $query->getResult();
    if(isset($row[0]['counted']))
    {
      return (int)$row[0]['counted'];
    }
    return 0;
  }

}