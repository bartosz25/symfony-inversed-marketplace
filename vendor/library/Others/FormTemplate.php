<?php
namespace Others;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Security\FilterXss;

/**
 * Form template used to generate form fields dynamically.
 */
class FormTemplate extends AbstractType
{

  /**
   * Form's name.
   * @access private
   * @var string
   */
  private $name;

  /**
   * List of form's elements.
   * @access private
   * @var array
   */
  private $fields;

  public function __construct($name, $fields)
  {
    $this->name = $name;
    $this->fields = $fields;
  }

  /**
   * Builds form fields.
   */
  public function buildForm(FormBuilder $builder, array $options)
  {
    foreach($this->fields as $field)
    {
      $options = (array)unserialize($field['typeOptionsForm']);
      $builder->add($field['codeName'], $field['typeForm'], $options);
    }
  }

  public function getDefaultOptions(array $options) 
  {
    return array('validation_groups' => array($this->name));
  }

  public function getName()
  {
    return $this->name;
  }
}