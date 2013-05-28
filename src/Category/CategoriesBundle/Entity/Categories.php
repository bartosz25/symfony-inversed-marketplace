<?php
namespace Category\CategoriesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="categories")
 * @ORM\Entity(repositoryClass="Category\CategoriesBundle\Repository\CategoriesRepository")
 */
class Categories
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ca", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ca;

  /**
   * @ORM\Column(name="categories_id_ca", type="integer", length="11", nullable=false)
   */
  protected $categoryParent;

  /**
   * @ORM\Column(name="name_ca", type="string", length="150", nullable=false)
   */
  protected $categoryName;

  /**
   * @ORM\Column(name="url_ca", type="string", length="150", nullable=false)
   */
  protected $categoryUrl;

  /**
   * @ORM\Column(name="ads_ca", type="integer", length="4", nullable=false)
   */
  protected $categoryAds;

  /**
   * @ORM\Column(name="offers_ca", type="integer", length="4", nullable=false)
   */
  protected $categoryOffers;

  // public function __construct() 
  // {
    // $this->parentCategory = new \Doctrine\Common\Collections\ArrayCollection();
  // }

public function setIdCa($v){$this->id_ca = $v;}
public function getIdCa() { return $this->id_ca;}
public function __toString() { return 'Categories';}
}