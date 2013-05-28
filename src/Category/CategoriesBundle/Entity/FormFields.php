<?php
namespace Category\CategoriesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="form_fields")
 * @ORM\Entity(repositoryClass="Category\CategoriesBundle\Repository\FormFieldsRepository")
 */
class FormFields
{

  /**
   * @ORM\Id
   * @ORM\Column(name="id_ff", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id_ff;

  /**
   * @ ORM\OneToMany(targetEntity="Category\CategoriesBundle\Entity\FormFields", mappedBy="parentCategory")
   */
  // protected $childrenCategory;

  /**
   * @ ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\FormFields", inversedBy="childrenCategory")
   * @ ORM\JoinColumn(name="form_fields_id_ff", referencedColumnName="id_ff")
   */
  // protected $parentCategory;

  /**
   * @ORM\Column(name="fullname_ff", type="string", length="200", nullable=false)
   */
  protected $fullName;

  /**
   * @ORM\Column(name="codename_ff", type="string", length="30", nullable=false)
   */
  protected $codeName;

  /**
   * @ORM\Column(name="type_ff", type="string", length="11", nullable=false)
   */
  protected $typeForm;

  /**
   * @ORM\Column(name="type_options_ff", type="text", nullable=false)
   */
  protected $typeOptionsForm;

  /**
   * @ORM\Column(name="constraints_ff", type="text", nullable=false)
   */
  protected $constraintsForm;

  /**
   * @ORM\Column(name="dependencies_php_ff", type="text", nullable=false)
   */
  protected $dependenciesPhpForm;

  /**
   * @ORM\Column(name="dependencies_js_ff", type="text", nullable=false)
   */
  protected $dependenciesJsForm;

  /**
   * @ORM\Column(name="entity_ff", type="string", length="150", nullable=false)
   */
  protected $entityForm;

  // public function __construct() 
  // {
    // $this->parentCategory = new \Doctrine\Common\Collections\ArrayCollection();
  // }

  /**
   * Sets a new temporary object without connect into database.
   * @access public
   * @params $params Array with params.
   * @return void
   */
  public function setNewObject($params)
  {
    foreach($params as $p => $param)
    {
      $this->$p = $param;
    }
  }
 
public function getIdFf()
{
  return $this->id_ff;
}
public function __toString() { return 'FormFields';}
}