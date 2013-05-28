<?php
namespace Catalogue\OffersBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="offers_form_fields")
 * @ORM\Entity(repositoryClass="Catalogue\OffersBundle\Repository\OffersFormFieldsRepository")
 */
class OffersFormFields
{

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Catalogue\OffersBundle\Entity\Offers")
   * @ORM\JoinColumn(name="offers_id_of", referencedColumnName="id_of")
   */
  protected $offers_id_of;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\FormFields")
   * @ORM\JoinColumn(name="form_fields_id_ff", referencedColumnName="id_ff")
   */
  protected $form_fields_id_ff;

  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Category\CategoriesBundle\Entity\FormFieldsCategories")
   * @ORM\JoinColumn(name="categories_id_ca", referencedColumnName="categories_id_ca")
   */
  protected $categories_id_ca;

  /**
   * @ORM\Column(name="value_off", type="text", nullable=false)
   */
  protected $fieldValue;

  /**
   * Getters.
   */
  public function getFormFieldsIdFf()
  {
    return $this->form_fields_id_ff;
  }
  public function getOffersIdOf()
  {
    return $this->offers_id_of;
  } 
  public function getCategoriesIdCa()
  {
    return $this->categories_id_ca;
  } 
  /**
   * Setters
   */
  public function setOffersIdOf($value)
  {
    $this->offers_id_of = $value;
  }
  public function setFormFieldsIdFf($value)
  {
    $this->form_fields_id_ff = $value;
  }
  /**
   * Sets values to a new ads - form field association.
   * @access public
   * @param array $values List of values.
   * @return void
   */
  public function setNewValues($values)
  {
    foreach($values as $v => $value)
    {
      $this->$v = $value;
    }
  }
}