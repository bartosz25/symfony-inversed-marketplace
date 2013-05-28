<?php
namespace Ad\ItemsBundle\Entity;
  
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Table(name="ads_form_fields")
 * @ORM\Entity(repositoryClass="Ad\ItemsBundle\Repository\AdsFormFieldsRepository")
 */
class AdsFormFields
{

  /**
   * @ORM\Id
   * @ ORM\Column(name="ads_id_ad", type="integer")
   * @ORM\ManyToOne(targetEntity="Ad\ItemsBundle\Entity\Ads")
   * @ORM\JoinColumn(name="ads_id_ad", referencedColumnName="id_ad")
   */
  protected $ads_id_ad;

  /**
   * @ORM\Id
   * @ ORM\Column(name="form_fields_id_ff", type="integer")
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
   * @ORM\Column(name="value_aff", type="text", nullable=false)
   */
  protected $fieldValue;

  /**
   * Getters.
   */
  public function getFormFieldsIdFf()
  {
    return $this->form_fields_id_ff;
  }
  public function getAdsIdAd()
  {
    return $this->ads_id_ad;
  } 
  /**
   * Setters
   */
  public function setAdsIdAd($value)
  {
    $this->ads_id_ad = $value;
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