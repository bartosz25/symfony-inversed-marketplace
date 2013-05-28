<?php
namespace Category\CategoriesBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="form_fields_categories")
 * @ORM\Entity(repositoryClass="Category\CategoriesBundle\Repository\FormFieldsCategoriesRepository")
 */
class FormFieldsCategories
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\Categories")
   * @ORM\JoinColumn(name="categories_id_ca", referencedColumnName="id_ca")
   */
  protected $categories_id_ca;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\FormFields")
   * @ORM\JoinColumn(name="form_fields_id_ff", referencedColumnName="id_ff")
   */
  protected $form_fields_id_ff;

  /**
   * @ORM\Column(name="label_ffc", type="string", length="300", nullable=false)
   */
  protected $labelForm;

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
public function __toString() { return 'FormFieldsCategories';}
}