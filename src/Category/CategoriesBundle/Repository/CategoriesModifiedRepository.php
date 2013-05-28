<?php
namespace Category\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Category\CategoriesBundle\Entity\CategoriesModified;

class CategoriesModifiedRepository extends EntityRepository 
{

  /**
   * Checks if $category exists in the table.
   * @access public
   * @param int $category Category id.
   * @return boolean True if exists, false otherwise
   */
  public function ifExists($category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT cm.modifiedText, c.id_ca
    FROM CategoryCategoriesBundle:CategoriesModified cm
    JOIN cm.modifiedCategory c
    WHERE c.id_ca = :category")
    ->setParameter('category', $category); 
    $row = $query->getResult();
    return (bool)(isset($row[0]['id_ca']));
  }

  /**
   * Updates or inserts modifications of one ad.
   * @access public
   * @param int $category Category's id.
   * @param string $type Modification's type.
   * @param array $options Supplementary options, may be used to get label.
   * @return void.
   */
  public function categoryModified($category, $type, $options)
  {
    $camEnt = new CategoriesModified;
    $text = array();
    $query = $this->getEntityManager()
    ->createQuery("SELECT cm.modifiedText, c.id_ca
    FROM CategoryCategoriesBundle:CategoriesModified cm
    JOIN cm.modifiedCategory c
    WHERE c.id_ca = :category")
    ->setParameter('category', $category); 
    $row = $query->getResult();
    $ifExists = $this->ifExists($category);
    if($ifExists)
    {
      $text = (array)(unserialize($row[0]['modifiedText']));
    }
    $text[] = array('type' => $type, 'text' => $camEnt->getTypeLabel($type, array($options['adName'])), 'date' => time());
    if($ifExists)
    {
      // make update
      $this->getEntityManager()->createQueryBuilder()->update('Category\CategoriesBundle\Entity\CategoriesModified', 'cm')
      ->set('cm.modifiedText', '?1')
      ->where('cm.modifiedCategory = ?2')
      ->setParameter(1, serialize($text))
      ->setParameter(2, $category)
      ->getQuery()
      ->execute();
    }
    else
    {
      // $date = new \DateTime();
      // $date->add(new \DateInterval('P'.$camEnt->getFrequency().'D'));
      // insert new modified element
      $camEnt->setData(array('modifiedCategory' => $this->getEntityManager()->getReference('Category\CategoriesBundle\Entity\Categories', $category),
      'modifiedText' => serialize($text), 'modifiedFirstModif' => new \DateTime(),
      'modifiedLastUser' => 0
      ));
      $this->getEntityManager()->persist($camEnt);
      $this->getEntityManager()->flush();
    }
  }

  /**
   * Gets new informations for user.
   * @access public
   * @param int $user User's id.
   * @return boolean True if subsbscribed, false otherwise
   */
  public function getNewsByCategory($user)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.categoryName, c.categoryUrl, c.id_ca, cm.modifiedText 
    FROM UserAlertsBundle:UsersCategoriesAlerts uca
    JOIN uca.alertCategory cm
    JOIN cm.modifiedCategory c
    WHERE uca.alertUser = :user")
    ->setParameter("user", $user); 
    return $query->getResult();
  }

}