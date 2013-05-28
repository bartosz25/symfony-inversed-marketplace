<?php
namespace Category\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository; 
use Doctrine\ORM\Query\ResultSetMapping;
use Others\Tools; 

class CategoriesRepository extends EntityRepository 
{

  /**
   * Gets all categories.
   * @access public
   * @param boolean $makeTree Indicates if we make a tree with parents.
   * @return array Categorie's list.
   */
  public function getCategories($makeTree)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ca, c.categoryName, c.categoryUrl, c.categoryParent
    FROM CategoryCategoriesBundle:Categories c
    ORDER BY c.categoryParent ASC, c.categoryName ASC "); 
    $categories = $query->getResult();
    if($makeTree)
    {
      $categories = Tools::makeTree($categories, array('parent' => 'id_ca', 'children' => 'categoryParent'));
    }
    return $categories;
  }

  /**
   * Gets only child categories.
   * @access public
   * @return array Categorie's list.
   */
  public function getChildCategories()
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ca, c.categoryName, c.categoryUrl, c.categoryParent
    FROM CategoryCategoriesBundle:Categories c
    WHERE c.categoryParent > 0
    ORDER BY c.categoryName ASC "); 
    return $query->getResult();
  }

  /**
   * Gets all categories with page separator.
   * @access public
   * @param array $options Options used to SQL query.
   * @return array Categorie's list.
   */
  public function getCategoriesList($options)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ca, c.categoryName, c.categoryUrl
    FROM CategoryCategoriesBundle:Categories c
    ORDER BY c.categoryName ASC ")
    ->setMaxResults($options['maxResults'])
    ->setFirstResult($options['start']); 
    return $query->getResult();
  }

  /**
   * Gets category by url.
   * @access public
   * @param string $category Category url.
   * @return array Category data.
   */
  public function getByUrl($category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ca, c.categoryName, c.categoryUrl, c.categoryAds, c.categoryOffers
    FROM CategoryCategoriesBundle:Categories c
    WHERE c.categoryUrl = :category")
    ->setParameter('category', $category); 
    return $query->getResult();
  }

  /**
   * Update ads or offers quantity.
   * @access public
   * @param string $how Increment/decrement (+1 or -1).
   * @param int $category Category id.
   * @param string $type Type (ads/offers).
   * @return void.
   */
  public function updateQuantity($how, $category, $type)
  {
    if($type == 'ads')
    {
      $field = 'categoryAds';
    }
    elseif($type == 'offers')
    {
      $field = 'categoryOffers';
    }
    $this->getEntityManager()->createQueryBuilder()->update('Category\CategoriesBundle\Entity\Categories', 'c')
    ->set('c.'.$field, 'c.'.$field.' '.$how)
    ->where('c.id_ca = ?1')
    ->setParameter(1, $category)
    ->getQuery()
    ->execute();
  }

  /**
   * Gets category by id.
   * @access public
   * @param integer $category Category id.
   * @return array Category data.
   */
  public function getById($category)
  {
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ca, c.categoryName, c.categoryUrl, c.categoryAds, c.categoryOffers
    FROM CategoryCategoriesBundle:Categories c
    WHERE c.id_ca = :category")
    ->setParameter('category', $category); 
    $row = $query->getResult();
    if(isset($row[0]['id_ca'])) 
    {
      return $row[0];
    }
    return array();
  }

  /**
   * Gets random categories.
   * @access public
   * @param int $limit Limit of categories to load.
   * @param int $max Number of all categories.
   * @param bool $onlyChild True if get only subcategories.
   * @return array Categorie's list.
   */
  public function getRandomCategories($limit, $max, $onlyChild = true)
  {
    $where = "";
    if($onlyChild)
    {
      $where = " WHERE c.categoryParent > 0";
    }
    $start = Tools::getStart($limit, $max);
    $query = $this->getEntityManager()
    ->createQuery("SELECT c.id_ca, c.categoryName, c.categoryUrl, c.categoryParent
    FROM CategoryCategoriesBundle:Categories c ".$where."
    ORDER BY c.categoryName ASC")
    ->setMaxResults($limit)
    ->setFirstResult($start);
    return $query->getResult();
  }

}