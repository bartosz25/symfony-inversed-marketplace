<?php
namespace Catalogue\OffersBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Database\MainEntity;

class CataloguesRepository extends EntityRepository 
{

  /**
   * Gets all ads by user's id.
   * @access public
   * @param array $options Options used to SQL request.
   * @param int $user User's id.
   * @return array Ads list.
   */
  public function getCataloguesByUser($options, $user)
  {
    $order = "c.catalogueName ASC";
    $columns = array("nom" => array("c.catalogueName"), "nombre_offres" => "c.catalogueOffers");
    $order = MainEntity::makeOrderClause($columns, $options, $order);
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_cat, c.catalogueName, c.catalogueOffers
    FROM CatalogueOffersBundle:Catalogues c
    WHERE c.catalogueProp = :user AND c.catalogueDeleted = 0
    ORDER BY $order ")
    ->setParameter('user', (int)$user)
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets catalogue data to edit by his author.
   * @access public
   * @param int $catalogue Catalogue's id.
   * @param int $user Author's id.
   * @return array Catalogue's data.
   */
  public function getCatalogueData($catalogue, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_cat, c.catalogueName, c.catalogueDesc, c.catalogueOffers
    FROM CatalogueOffersBundle:Catalogues c
    WHERE c.id_cat = :catalogue AND c.catalogueProp = :user AND c.catalogueDeleted = 0")
    ->setParameter('user', (int)$user)
    ->setParameter('catalogue', (int)$catalogue); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Gets offers number for one catalogue.
   * @access public
   * @param int $catalogue Catalogue's id.
   * @return int Offers quantity.
   */
  public function getOffersCount($catalogue)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.catalogueOffers
    FROM CatalogueOffersBundle:Catalogues c
    WHERE c.id_cat = :catalogue AND c.catalogueDeleted = 0")
    ->setParameter('catalogue', $catalogue); 
    $rows = $query->getResult();
    return (int)$rows[0]['catalogueOffers'];
  }

  /**
   * Checks if $catalogue belongs to $user.
   * @access public
   * @param int $catalogue Catalogue's id.
   * @param int $user User's id.
   * @return boolean True if belongs, false otherwise.
   */
  public function ifBelongs($catalogue, $user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_cat
    FROM CatalogueOffersBundle:Catalogues c
    WHERE c.id_cat = :catalogue AND c.catalogueProp = :user AND c.catalogueDeleted = 0")
    ->setParameter('user', $user)
    ->setParameter('catalogue', $catalogue); 
    $rows = $query->getResult();
    return (bool)isset($rows[0]['id_cat']);
  }

  /**
   * Gets all catalogues.
   * + backend
   * @access public
   * @param array $options Options used to SQL request.
   * @return array Catalogues list.
   */
  public function getAllCatBackend($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_cat, c.catalogueName, c.catalogueOffers, c.catalogueDesc, u.id_us, u.login
    FROM CatalogueOffersBundle:Catalogues c
    JOIN c.catalogueProp u
    WHERE c.catalogueDeleted = 0
    ORDER BY c.id_cat DESC")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets catalogue data to edit by his author.
   * @access public
   * @param int $catalogue Catalogue's id.
   * @return array Catalogue's data.
   */
  public function getOneCatalogueData($catalogue)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_cat, c.catalogueName, c.catalogueDesc
    FROM CatalogueOffersBundle:Catalogues c
    WHERE c.id_cat = :catalogue AND c.catalogueDeleted = 0")
    ->setParameter('catalogue', $catalogue); 
    $rows = $query->getResult();
    if($rows)
    {
      return $rows[0];
    }
    return array();
  }

  /**
   * Updates catalogue stats.
   * @access public
   * @param string $how Increment/decrement (+1 or -1).
   * @param int $catalogue Catalogue's id.
   * @param string $field Field to update.
   * @return void.
   */
  public function updateQuantity($how, $catalogue, $field)
  {
    $this->getEntityManager()->createQueryBuilder()->update('Catalogue\OffersBundle\Entity\Catalogues', 'c')
    ->set('c.'.$field, 'c.'.$field.' '.$how)
    ->where('c.id_cat = ?1')
    ->setParameter(1, (int)$catalogue)
    ->getQuery()
    ->execute();
  }


  /**
   * Gets random catalogue.   
   * @access public
   * @return array Catalogue's data.
   */
  public function getForTest()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_cat, c.catalogueName, c.catalogueDesc, u.id_us
    FROM CatalogueOffersBundle:Catalogues c
    JOIN c.catalogueProp u");
    $row = $query->getResult();
    return array('id' => $row[0]['id_cat'], 'id2' => '', 'user1' => $row[0]['id_us'], 'user2' => $row[0]['id_us']);
  }

}